<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EmailTemplate;
use App\Register;
use Sentinel;

class EmailTemplateController extends Controller
{
    protected function user()
    {
        return Sentinel::check();
    }

    protected function role()
    {
        return $this->user()->roles()->first()->slug ?? null;
    }

    protected function orgId()
    {
        $reg = Register::where('user_id', $this->user()->id)->first();
        return $reg->organization ?? null;
    }

    // ------------------------------------------------------------------
    // INDEX
    // ------------------------------------------------------------------
public function index()
{
   $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = registeruser($uid)->organization;

    if (!in_array($role, ['root', 'sadmin', 'admin'])) {
        abort(403, 'Unauthorized');
    }

    $query = EmailTemplate::orderBy('id', 'DESC');

    // ADMIN → only own organization
    if ($role === 'admin') {
        $query->where('organization_id', $orgId);
    }

    // ROOT / SADMIN → all organizations (no filter)

    $templates = $query->get();

    return view(
        'FrontEnd.email_templates.index',
        compact('templates')
    );
}

    // ------------------------------------------------------------------
    // STORE
    // ------------------------------------------------------------------
public function store(Request $request)
{
    $user  = Sentinel::check();
    $uid   = $user->id;
    $role  = $user->roles()->first()->slug ?? null;
    $orgId = $this->orgId();

    // -----------------------------
    // Permission
    // -----------------------------
    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json(['message'=>'Permission denied'], 403);
    }

    // -----------------------------
    // Validation
    // -----------------------------
    $request->validate([
        'title'    => 'required',
        'subject' => 'required',
        'content' => 'required',
        'status'  => 'required|in:active,inactive',
    ]);

    // -----------------------------
    // Extract variables
    // (subject + content दोनों से)
    // -----------------------------
    preg_match_all('/\{[a-zA-Z0-9_]+\}/', $request->subject . ' ' . $request->content, $matches);
    $variables = array_values(array_unique($matches[0] ?? []));

    // -----------------------------
    // Create Email Template
    // -----------------------------
    $template = EmailTemplate::create([
        'organization_id' => $orgId,          // ✅ session based
        'created_by'      => $uid,
        'title'            => $request->title,
        'subject'         => $request->subject,
        'message'         => $request->content,
        'variables_json'  => json_encode($variables),
        'status'          => $request->status,
    ]);

    // -----------------------------
    // LOGS (same pattern as SMS)
    // -----------------------------
    logOrganizationActivity(
        $orgId,
        $uid,
        'email_template',
        $template->id,
        'create_template',
        'Created Email Template',
        $template->toArray()
    );

    logUserActivity(
        $orgId,
        $uid,
        'email_template',
        $template->id,
        'create_template',
        'Created Email Template',
        $template->toArray()
    );

    return response()->json([
        'status'  => 200,
        'message' => 'Email Template Created Successfully'
    ]);
}


    // ------------------------------------------------------------------
    // EDIT
    // ------------------------------------------------------------------
public function edit($id)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $orgId = $this->orgId();

    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json([
            'status'  => false,
            'message' => 'Permission denied'
        ], 403);
    }

    $template = EmailTemplate::findOrFail($id);

    // ADMIN → only own organization
    if ($role === 'admin' && $template->organization_id != $orgId) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    return response()->json([
        'status'   => true,
        'template' => $template
    ]);
}


    // ------------------------------------------------------------------
    // UPDATE
    // ------------------------------------------------------------------
 public function update(Request $request, $id)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = $this->orgId();

    if (!in_array($role, ['root','sadmin','admin'])) {
        return response()->json([
            'status'  => false,
            'message' => 'Permission denied'
        ], 403);
    }

    $template = EmailTemplate::findOrFail($id);

    // ADMIN → only own org
    if ($role === 'admin' && $template->organization_id != $orgId) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    $request->validate([
        'title'    => 'required',
        'subject' => 'required',
        'content' => 'required',
    ]);

    // 🔹 OLD DATA (for logs)
    $oldData = $template->toArray();

    $template->update([
        'title'    => $request->title,
        'subject' => $request->subject,
        'message' => $request->content,
    ]);

    // 🔹 LOGS (same style as SMS / WhatsApp)
    logOrganizationActivity(
        $template->organization_id,
        $uid,
        'email_template',
        $template->id,
        'update_template',
        'Updated Email Template',
        [
            'old' => $oldData,
            'new' => $template->toArray()
        ]
    );

    logUserActivity(
        $template->organization_id,
        $uid,
        'email_template',
        $template->id,
        'update_template',
        'Updated Email Template',
        [
            'old' => $oldData,
            'new' => $template->toArray()
        ]
    );

    return response()->json([
        'status'  => true,
        'message' => 'Email Template Updated Successfully'
    ]);
}

    // ------------------------------------------------------------------
    // DELETE
    // ------------------------------------------------------------------
  public function destroy($id)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = $this->orgId();

    $template = EmailTemplate::findOrFail($id);

    // ❌ SADMIN delete नहीं कर सकता
    if ($role === 'sadmin') {
        return response()->json([
            'status'  => false,
            'message' => 'Super Admin cannot delete'
        ], 403);
    }

    // ❌ ADMIN → only own org
    if ($role === 'admin' && $template->organization_id != $orgId) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    // 🔹 OLD DATA for logs
    $oldData = $template->toArray();

    $template->delete();

    // 🔹 LOGS (same pattern as SMS / WhatsApp)
    logOrganizationActivity(
        $template->organization_id,
        $uid,
        'email_template',
        $id,
        'delete_template',
        'Deleted Email Template',
        $oldData
    );

    logUserActivity(
        $template->organization_id,
        $uid,
        'email_template',
        $id,
        'delete_template',
        'Deleted Email Template',
        $oldData
    );

    return response()->json([
        'status'  => true,
        'message' => 'Email Template Deleted Successfully'
    ]);
}
// ---------------------------------------------------------
// BULK DELETE (EMAIL TEMPLATES)
// ---------------------------------------------------------
public function bulkDelete(Request $request)
{
    $user  = Sentinel::check();
    $role  = $user->roles()->first()->slug ?? null;
    $uid   = $user->id;
    $orgId = $this->orgId();

    // ❌ SADMIN cannot delete
    if ($role === 'sadmin') {
        return response()->json([
            'status'  => false,
            'message' => 'Super Admin cannot delete'
        ], 403);
    }

    $ids = $request->ids ?? [];

    if (empty($ids)) {
        return response()->json([
            'status'  => false,
            'message' => 'No templates selected'
        ], 422);
    }

    // --------------------------------
    // ROOT  → all org templates
    // ADMIN → only own org templates
    // --------------------------------
    $templates = ($role === 'root')
        ? EmailTemplate::whereIn('id', $ids)->get()
        : EmailTemplate::whereIn('id', $ids)
            ->where('organization_id', $orgId)
            ->get();

    foreach ($templates as $template) {

        $oldData = $template->toArray();

        $template->delete();

        // 🔹 LOGS (per template)
        logOrganizationActivity(
            $template->organization_id,
            $uid,
            'email_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted Email Template',
            $oldData
        );

        logUserActivity(
            $template->organization_id,
            $uid,
            'email_template',
            $template->id,
            'bulk_delete_template',
            'Bulk Deleted Email Template',
            $oldData
        );
    }

    return response()->json([
        'status'  => 200,
        'message' => 'Selected Email Templates Deleted Successfully'
    ]);
}

}
