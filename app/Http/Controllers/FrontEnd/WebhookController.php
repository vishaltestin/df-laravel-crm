<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Webhook;

class WebhookController extends Controller
{
    // ============================
    // INDEX
    // ============================
    public function index()
    {
        $user = \Sentinel::check();
        $userId = $user->id;

        $reg     = registeruser($userId);
        $orgId   = $reg->organization;
        $role    = $user->roles()->first()->slug ?? null;

        // 👉 root & sadmin → सभी organizations के webhooks
        if ($role === 'root' || $role === 'sadmin') {
            $webhooks = Webhook::orderBy('id','DESC')->get();
        }
        // 👉 admin → अपने organization के webhooks
        elseif ($role === 'admin') {
            $webhooks = Webhook::where('organization_id', $orgId)
                               ->orderBy('id','DESC')
                               ->get();
        }
        // 👉 manager/operator/student → deny
        else {
            abort(403, "You are not allowed to access Webhooks");
        }

        return view('FrontEnd.webhooks.index', compact('webhooks','role'));
    }

    // ============================
    // CREATE VIEW
    // ============================
    public function create()
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        if (!in_array($role, ['root','sadmin','admin'])) {
            abort(403, 'Not Allowed.');
        }

        return view('FrontEnd.webhooks.create');
    }

    // ============================
    // STORE
    // ============================
    public function store(Request $request)
    {
        $user = \Sentinel::check();
        $userId = $user->id;

        $reg     = registeruser($userId);
        $orgId   = $reg->organization;
        $role    = $user->roles()->first()->slug ?? null;

        if (!in_array($role, ['root','sadmin','admin'])) {
            abort(403, 'Not Allowed.');
        }

        // VALIDATION
        $request->validate([
            'name' => 'required',
            'entity_type' => 'required',
            'description' => 'required',
            'method' => 'required',
            'end_point' => 'required',
            'payload_type' => 'required',
        ]);

        // root/sadmin admin अपने org में save करें
        $finalOrg = ($role === 'root' || $role === 'sadmin') ? 
                    ($request->organization_id ?? $orgId) : 
                    $orgId;

        Webhook::create([
            'name'            => $request->name,
            'entity_type'     => $request->entity_type,
            'description'     => $request->description,
            'method'          => $request->method,
            'end_point'       => $request->end_point,
            'query_params'    => json_encode($request->query_params),
            'headers'         => json_encode($request->headers),
            'payload_type'    => $request->payload_type,
            'raw_payload_type'=> $request->raw_payload_type ?? "",
            'payload'         => json_encode($request->payload),
            'organization_id' => $finalOrg,
        ]);

        return redirect()->route('webhooks.index')->with('success', 'Webhook created!');
    }

    // ============================
    // EDIT
    // ============================
    public function edit($id)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        if (!in_array($role, ['root','sadmin','admin'])) {
            abort(403, 'Not Allowed.');
        }

        $webhook = Webhook::findOrFail($id);
        return view('FrontEnd.webhooks.edit', compact('webhook','role'));
    }

    // ============================
    // UPDATE
    // ============================
    public function update(Request $request, $id)
    {
        $user  = \Sentinel::check();
        $reg   = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        $webhook = Webhook::findOrFail($id);

        // admin केवल अपने ही org का webhook edit कर सकता है
        if ($role === 'admin' && $webhook->organization_id != $orgId) {
            abort(403, 'Not Allowed.');
        }

        $request->validate([
            'name' => 'required',
            'entity_type' => 'required',
            'description' => 'required',
            'method' => 'required',
            'end_point' => 'required',
            'payload_type' => 'required',
        ]);

        $webhook->update([
            'name'            => $request->name,
            'entity_type'     => $request->entity_type,
            'description'     => $request->description,
            'method'          => $request->method,
            'end_point'       => $request->end_point,
            'query_params'    => json_encode($request->query_params),
            'headers'         => json_encode($request->headers),
            'payload_type'    => $request->payload_type,
            'raw_payload_type'=> $request->raw_payload_type ?? "",
            'payload'         => json_encode($request->payload),
        ]);

        return redirect()->route('webhooks.index')->with('success', 'Webhook updated!');
    }

    // ============================
    // DELETE
    // ============================
    public function destroy($id)
    {
        $user  = \Sentinel::check();
        $reg   = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        $webhook = Webhook::findOrFail($id);

        // admin only delete own org
        if ($role === 'admin' && $webhook->organization_id != $orgId) {
            abort(403, 'Not Allowed.');
        }

        $webhook->delete();
        return redirect()->route('webhooks.index')->with('success', 'Webhook deleted!');
    }
}
