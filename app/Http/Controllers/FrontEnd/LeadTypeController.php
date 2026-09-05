<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LeadType;
use App\Organization;

class LeadTypeController extends Controller
{
 public function index()
{
    $user = \Sentinel::check();
    $userId = $user->id;
    $role = userRole();

    $reg = registeruser($userId);
    $orgId = $reg->organization;

    if ($role === 'root' || $role === 'sadmin') {
        $types = LeadType::all();
    }
    else {
        $types = LeadType::where('organization_id',$orgId)->get();
    }

    $organization = Organization::find($orgId);

    return view('FrontEnd.types.index', compact('types','organization'));
}

    public function store(Request $request)
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        // Only root, sadmin, admin can add
        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['status'=>'error','message'=>'Not allowed']);
        }

        $request->validate(['name' => 'required']);

        LeadType::create([
            'organization_id' => $orgId,
            'name' => $request->name,
            'status' => 1
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $type = LeadType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);

        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        $type = LeadType::findOrFail($id);

        // Only root + sadmin + admin (own org) can update
        if ($role === 'admin' && $type->organization_id != $orgId) {
            return response()->json(['status'=>'error','message'=>'Not allowed']);
        }

        $type->update(['name' => $request->name]);

        return response()->json(['status'=>'success']);
    }

    public function destroy($id)
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        $type = LeadType::findOrFail($id);

        // delete rules:
        // root → सभी का delete कर सकता
        // sadmin → delete नहीं करेगा (आपने rule बताया था)
        // admin → सिर्फ अपने org का delete
        if ($role === 'sadmin') {
            return response()->json(['status'=>'error','message'=>'Super Admin cannot delete']);
        }

        if ($role === 'admin' && $type->organization_id != $orgId) {
            return response()->json(['status'=>'error','message'=>'Not allowed']);
        }

        $type->delete();

        return response()->json(['status'=>'success']);
    }
}
