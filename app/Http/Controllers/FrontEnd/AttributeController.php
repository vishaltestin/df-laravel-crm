<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attribute;

class AttributeController extends Controller
{
    // ------------------------
    // INDEX (LISTING)
    // ------------------------
    public function index(Request $request)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        $reg = registeruser($user->id);
        $orgId = $reg->organization;

        $lookup = $request->lookup_type;

        // ROOT & SADMIN → can see all organizations
        if ($role === 'root' || $role === 'sadmin') {
            $attributes = Attribute::where('lookup_type', $lookup)
                ->orderBy('organization_id')
                ->orderBy('sort_order')
                ->get();
        }
        // ADMIN → only own organization
        else if ($role === 'admin') {
            $attributes = Attribute::where('organization_id', $orgId)
                ->where('lookup_type', $lookup)
                ->orderBy('sort_order')
                ->get();
        }
        // MANAGER / OPERATOR / STUDENT → Not allowed
        else {
            abort(403, "Unauthorized Access");
        }

        return view('FrontEnd.attributes.index', compact('attributes', 'lookup'));
    }


    // ------------------------
    // CREATE
    // ------------------------
    public function store(Request $request)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        // Only root + sadmin + admin allowed
        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['status'=>'error', 'message'=>"Not allowed"]);
        }

        $reg = registeruser($user->id);
        $orgId = $reg->organization;

        $request->validate([
            'name'        => 'required',
            'type'        => 'required',
            'lookup_type' => 'required',
            'entity_type' => 'required',
        ]);

        Attribute::create([
            'code'            => strtolower(str_replace(' ', '_', $request->name)),
            'name'            => $request->name,
            'type'            => $request->type,
            'lookup_type'     => $request->lookup_type,
            'entity_type'     => $request->entity_type,
            'sort_order'      => $request->sort_order ?? 0,
            'validation'      => $request->validation ?? null,
            'is_required'     => $request->is_required ?? 0,
            'is_unique'       => $request->is_unique ?? 0,
            'quick_add'       => $request->quick_add ?? 0,
            'is_user_defined' => 1,
            'organization_id' => $orgId,
        ]);

        return response()->json(['status' => 'success']);
    }


    // ------------------------
    // EDIT LOAD
    // ------------------------
    public function edit($id)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        $attr = Attribute::findOrFail($id);

        // root → allowed
        // sadmin → allowed
        // admin → only if same org
        if ($role === 'admin') {
            $reg = registeruser($user->id);
            if ($attr->organization_id != $reg->organization) {
                abort(403, "Unauthorized");
            }
        }

        // manager / operator cannot edit
        if (!in_array($role, ['root','sadmin','admin'])) {
            abort(403, "Unauthorized");
        }

        return response()->json($attr);
    }


    // ------------------------
    // UPDATE
    // ------------------------
    public function update(Request $request, $id)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        $attr = Attribute::findOrFail($id);

        // admin must match organization
        if ($role === 'admin') {
            $reg = registeruser($user->id);
            if ($attr->organization_id != $reg->organization) {
                abort(403, "Unauthorized");
            }
        }

        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['status'=>'error', 'message'=>'Not allowed']);
        }

        $request->validate([
            'name'        => 'required',
            'type'        => 'required',
            'entity_type' => 'required',
        ]);

        $attr->update([
            'name'        => $request->name,
            'type'        => $request->type,
            'entity_type' => $request->entity_type,
            'lookup_type' => $request->lookup_type,
            'sort_order'  => $request->sort_order ?? 0,
            'validation'  => $request->validation ?? null,
            'is_required' => $request->is_required ?? 0,
            'is_unique'   => $request->is_unique ?? 0,
        ]);

        return response()->json(['status' => 'success']);
    }


    // ------------------------
    // DELETE
    // ------------------------
    public function destroy($id)
    {
        $user = \Sentinel::check();
        $role = $user->roles()->first()->slug ?? null;

        $attr = Attribute::findOrFail($id);

        // Only ROOT can delete from any org
        if ($role === 'root') {
            $attr->delete();
            return response()->json(['status'=>'success']);
        }

        // SADMIN cannot delete (rule from CRM)
        if ($role === 'sadmin') {
            return response()->json(['status'=>'error','message'=>'Delete not allowed for SADMIN']);
        }

        // ADMIN can delete only if belongs to same org
        if ($role === 'admin') {
            $reg = registeruser($user->id);
            if ($attr->organization_id != $reg->organization) {
                return response()->json(['status'=>'error','message'=>'Unauthorized']);
            }
            $attr->delete();
            return response()->json(['status'=>'success']);
        }

        // Others denied
        return response()->json(['status'=>'error','message'=>'Unauthorized']);
    }
}
