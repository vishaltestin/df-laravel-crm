<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MessageTemplate;
use App\Organization;
use Sentinel;

class MessageTemplateController extends Controller
{
    // ---------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------
    public function index()
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;

    $query = MessageTemplate::with('organization')->orderBy('id','desc');

    if ($role === 'root') {
        $templates = $query->get();   // all org
    } else {
        $templates = $query->where('organization_id',$orgId)->get();
    }

    // 🔒 ORGANIZATION DROPDOWN FIX
    //if ($role === 'root') {
    //    $organizations = Organization::orderBy('name')->get();
    //} else {
        $organizations = Organization::where('id',$orgId)->get();
   // }

    

    return view('FrontEnd.message_templates.index', compact(
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

    // ----------------------------
    // Permission check
    // ----------------------------
    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json(['message'=>'Permission denied'],403);
    }

    // ----------------------------
    // Validation
    // ----------------------------
    $request->validate([
        'title'   => 'required',
        'message' => 'required',
        'status'  => 'required|in:active,inactive',
        'organization_id' => 'nullable|exists:organizations,id'
    ]);

    // ----------------------------
    // Organization resolve
    // root → select org
    // others → session org only
    // ----------------------------
    $finalOrgId = ($role === 'root')
        ? ($request->organization_id ?? $orgId)
        : $orgId;

    // ----------------------------
    // Extract dynamic variables
    // ----------------------------
    preg_match_all('/\{[a-zA-Z0-9_]+\}/', $request->message, $matches);
    $variables = $matches[0] ?? [];

    // ----------------------------
    // CREATE TEMPLATE
    // ----------------------------
    $template = MessageTemplate::create([
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

    // ----------------------------
    // LOGS (same as WhatsApp)
    // ----------------------------
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
        'message' => 'Message Template Created Successfully'
    ]);
}

    // ---------------------------------------------------------
    // EDIT
    // ---------------------------------------------------------
    public function edit($id)
    {
        $user  = Sentinel::check();
        $role  = $user->roles()->first()->slug ?? null;
        $uid   = $user->id;
        $orgId = registeruser($uid)->organization;

        $template = MessageTemplate::findOrFail($id);

        if ($role !== 'root' && $template->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized access'],403);
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

    $template = MessageTemplate::findOrFail($id);

    // ----------------------------
    // Permission
    // ----------------------------
    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json(['message'=>'Permission denied'],403);
    }

    // admin / sadmin → only own org
    if ($role !== 'root' && $template->organization_id != $orgId) {
        return response()->json(['message'=>'Unauthorized organization'],403);
    }

    // ----------------------------
    // Validation
    // ----------------------------
    $request->validate([
        'title'   => 'required',
        'message' => 'required',
        'status'  => 'required|in:active,inactive'
    ]);

    // ----------------------------
    // Extract variables
    // ----------------------------
    preg_match_all('/\{[a-zA-Z0-9_]+\}/', $request->message, $matches);
    $variables = $matches[0] ?? [];

    // ----------------------------
    // Save old data for logs
    // ----------------------------
    $oldData = $template->toArray();

    // ----------------------------
    // UPDATE TEMPLATE
    // ----------------------------
    $template->update([
        'title'          => $request->title,
        'category'       => $request->category,
        'send_by'        => $request->send_by,
        'status'         => $request->status,
        'message'        => $request->message,
        'variables_json' => json_encode($variables),
        'organization_id'=> $template->organization_id // org change NOT allowed
    ]);

    // ----------------------------
    // LOGS (same pattern as WhatsApp)
    // ----------------------------
    logOrganizationActivity(
        $template->organization_id,
        $uid,
        'message_template',
        $template->id,
        'update_template',
        'Updated Message Template',
        [
            'old' => $oldData,
            'new' => $template->toArray()
        ]
    );

    logUserActivity(
        $template->organization_id,
        $uid,
        'message_template',
        $template->id,
        'update_template',
        'Updated Message Template',
        [
            'old' => $oldData,
            'new' => $template->toArray()
        ]
    );

    return response()->json([
        'status'  => 200,
        'message' => 'Message Template Updated Successfully'
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

    $template = MessageTemplate::findOrFail($id);

    // ----------------------------
    // Permission
    // ----------------------------
    if ($role === 'sadmin') {
        return response()->json(['message'=>'SADMIN cannot delete'],403);
    }

    if ($role !== 'root' && $template->organization_id != $orgId) {
        return response()->json(['message'=>'Unauthorized delete'],403);
    }

    // ----------------------------
    // OLD DATA (for logs)
    // ----------------------------
    $oldData = $template->toArray();

    // ----------------------------
    // DELETE
    // ----------------------------
    $template->delete();

    // ----------------------------
    // LOGS
    // ----------------------------
    logOrganizationActivity(
        $template->organization_id,
        $uid,
        'message_template',
        $id,
        'delete_template',
        'Deleted Message Template',
        $oldData
    );

    logUserActivity(
        $template->organization_id,
        $uid,
        'message_template',
        $id,
        'delete_template',
        'Deleted Message Template',
        $oldData
    );

    return response()->json([
        'status'=>200,
        'message'=>'Template Deleted Successfully'
    ]);
}

    // ---------------------------------------------------------
    // BULK DELETE
    // ---------------------------------------------------------
    public function bulkDelete(Request $request)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;

    if ($role === 'sadmin') {
        return response()->json(['message'=>'SADMIN cannot delete'],403);
    }

    $ids = $request->ids ?? [];

    if (empty($ids)) {
        return response()->json(['message'=>'No templates selected'],422);
    }

    // ----------------------------
    // ROOT → all org
    // ADMIN → only own org
    // ----------------------------
    $templates = ($role === 'root')
        ? MessageTemplate::whereIn('id',$ids)->get()
        : MessageTemplate::whereIn('id',$ids)
            ->where('organization_id',$orgId)
            ->get();

    foreach ($templates as $template) {

        $oldData = $template->toArray();

        $template->delete();

        // ---- LOGS PER TEMPLATE ----
        logOrganizationActivity(
            $template->organization_id,
            $uid,
            'message_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted Message Template',
            $oldData
        );

        logUserActivity(
            $template->organization_id,
            $uid,
            'message_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted Message Template',
            $oldData
        );
    }

    return response()->json([
        'status'=>200,
        'message'=>'Selected Message Templates Deleted Successfully'
    ]);
}


}
