<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WhatsappTemplate;
use App\Organization;
use Sentinel;

class WhatsappTemplateController extends Controller
{
    // ---------------------------------------------------------
    protected function user() { return Sentinel::check(); }
    protected function role() { return $this->user()->roles()->first()->slug ?? null; }
    protected function userId() { return $this->user()->id; }
    protected function orgId() { return registeruser($this->userId())->organization ?? null; }

    // ---------------------------------------------------------
    public function index()
    {
        $role  = $this->role();
        $orgId = $this->orgId();

        $query = WhatsappTemplate::with('organization')->orderBy('id','desc');

      //  if ($role === 'root' || $role === 'sadmin') {
            $templates = $query->get();
      //  } else {
            $templates = $query->where('organization_id', $orgId)->get();
       // }

// 🔒 ORGANIZATION DROPDOWN FIX
    //if ($role === 'root') {
    //    $organizations = Organization::orderBy('name')->get();
    //} else {
        $organizations = Organization::where('id',$orgId)->get();
   // }
        

        return view('FrontEnd.whatsapp_templates.index', compact(
            'templates','organizations','role'
        ));
    }

    // ---------------------------------------------------------
    // STORE
    // ---------------------------------------------------------
  public function store(Request $request)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;

    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json(['message'=>'Permission denied'],403);
    }

    $request->validate([
        'title'   => 'required',
        'message' => 'required',
        'status'  => 'required|in:active,inactive'
    ]);

    // -----------------------------
    // ORGANIZATION RESOLUTION
    // -----------------------------
    if ($role === 'root') {
        if (!$request->organization_id) {
            return response()->json(['message'=>'Organization is required'],422);
        }
        $finalOrgId = $request->organization_id;
    } else {
        $finalOrgId = $orgId;
    }

    preg_match_all('/\{[a-zA-Z0-9_]+\}/', $request->message, $matches);
    $variables = $matches[0] ?? [];

    // -----------------------------
    // CREATE TEMPLATE
    // -----------------------------
    $template = WhatsappTemplate::create([
        'organization_id' => $finalOrgId,
        'created_by'      => $uid,
        'send_by'         => $request->send_by,
        'title'           => $request->title,
        'category'        => $request->category,
        'message'         => $request->message,
        'variables_json'  => json_encode($variables),
        'editable'        => 1,
        'sent_count'      => 0,
        'status'          => $request->status,
    ]);

    // -----------------------------
    // LOGGING (RESTORED ✅)
    // -----------------------------
    logOrganizationActivity(
        $finalOrgId,
        $uid,
        'message_template',
        $template->id,
        'create_template',
        'Created Message Template',
        $template->toArray()
    );

    logUserActivity(
        $finalOrgId,
        $uid,
        'message_template',
        $template->id,
        'create_template',
        'Created Message Template',
        $template->toArray()
    );

    return response()->json([
        'status'  => 200,
        'message' => 'Whatsapp Message Template Created Successfully'
    ]);
}


    // ---------------------------------------------------------
    // EDIT
    // ---------------------------------------------------------
    public function edit($id)
    {
        $role  = $this->role();
        $orgId = $this->orgId();

        $template = WhatsappTemplate::findOrFail($id);

        if ($role !== 'root' && $role !== 'sadmin' && $template->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized template'],403);
        }

        return response()->json(['template'=>$template]);
    }

    // ---------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------
  public function update(Request $request, $id)
{
     $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;
	
	
 
    if (!in_array($role,['root','sadmin','admin'])) {
        return response()->json(['message'=>'Permission denied'],403);
    }

    $template = WhatsappTemplate::findOrFail($id);

    // admin / sadmin → only own org
    if ($role !== 'root' && $template->organization_id != $orgId) {
        return response()->json(['message'=>'Unauthorized template'],403);
    }

    $oldData = $template->toArray();

    $request->validate([
        'title'            => 'required',
        'message'          => 'required',
        'organization_id'  => 'required|exists:organizations,id',
        'status'           => 'required|in:active,inactive'
    ]);

    // admin / sadmin cannot change org
    if ($role !== 'root' && $request->organization_id != $orgId) {
        return response()->json(['message'=>'Cannot assign template to another organization'],403);
    }

    preg_match_all('/\{[a-zA-Z0-9_]+\}/', $request->message, $matches);
    $variables = $matches[0] ?? [];

    $template->update([
        'organization_id' => $request->organization_id,
        'send_by'         => $request->send_by,
        'title'           => $request->title,
        'category'        => $request->category,
        'message'         => $request->message,
        'variables_json'  => json_encode($variables),
        'status'          => $request->status
    ]);

    // ✅ CORRECT LOGGING (template org based)
    logOrganizationActivity(
        $template->organization_id,
        $uid,
        'whatsapp_template',
        $template->id,
        'update_template',
        'Updated WhatsApp Template',
        ['old'=>$oldData,'new'=>$template->toArray()]
    );

    logUserActivity(
        $template->organization_id,
        $uid,
        'whatsapp_template',
        $template->id,
        'update_template',
        'Updated WhatsApp Template',
        ['old'=>$oldData,'new'=>$template->toArray()]
    );

    return response()->json([
        'status'=>200,
        'message'=>'Whatsapp Template Updated Successfully.'
    ]);
}


    // ---------------------------------------------------------
    // DELETE
    // ---------------------------------------------------------
    public function destroy($id)
    {
        $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;

        if (!in_array($role,['root','admin'])) {
            return response()->json(['message'=>'Delete not allowed for your role'],403);
        }

        $template = WhatsappTemplate::findOrFail($id);

        // admin → cannot delete others org template
        if ($role === 'admin' && $template->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized delete'],403);
        }

        $oldData = $template->toArray();

        $template->delete();

        logOrganizationActivity($orgId,$uid,'whatsapp_template',$id,'delete_template',"Deleted WhatsApp Template",$oldData);
        logUserActivity($orgId,$uid,'whatsapp_template',$id,'delete_template',"Deleted WhatsApp Template",$oldData);

        return response()->json(['status'=>200,'message'=>'Template Deleted Successfully.']);
    }
	
	
	
	// ---------------------------------------------------------
// BULK DELETE (WhatsApp Templates)
// ---------------------------------------------------------
public function bulkDelete(Request $request)
{
         $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;
    // SADMIN not allowed
    if ($role === 'sadmin') {
        return response()->json(['message'=>'SADMIN cannot delete'],403);
    }

    $ids = $request->ids ?? [];

    if (empty($ids)) {
        return response()->json(['message'=>'No templates selected'],422);
    }

    // ------------------------------------
    // ROOT → delete any org templates
    // ADMIN → only own org templates
    // ------------------------------------
    $templates = ($role === 'root')
        ? WhatsappTemplate::whereIn('id', $ids)->get()
        : WhatsappTemplate::whereIn('id', $ids)
            ->where('organization_id', $orgId)
            ->get();

    foreach ($templates as $template) {

        $oldData = $template->toArray();

        $template->delete();

        // -------- LOGS (template-org based) --------
        logOrganizationActivity(
            $template->organization_id,
            $uid,
            'whatsapp_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted WhatsApp Template',
            $oldData
        );

        logUserActivity(
            $template->organization_id,
            $uid,
            'whatsapp_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted WhatsApp Template',
            $oldData
        );
    }

    return response()->json([
        'status'  => 200,
        'message' => 'Selected WhatsApp Templates Deleted Successfully'
    ]);
}




}

