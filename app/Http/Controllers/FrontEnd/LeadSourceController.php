<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LeadSource;
use App\Organization;
use Sentinel;

class LeadSourceController extends Controller
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
        $reg = registeruser($this->user()->id);
        return $reg->organization;
    }

    // ---------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------
    public function index()
    {
        $role  = $this->role();
        $orgId = $this->orgId();

        if ($role === 'root' || $role === 'sadmin') {
            $sources = LeadSource::with('organization')->get();
        } else {
            $sources = LeadSource::where('organization_id', $orgId)->get();
        }

        $organization = Organization::find($orgId);

        return view('FrontEnd.sources.index', compact('sources','organization','role'));
    }

    // ---------------------------------------------------------
    // STORE
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $role  = $this->role();
        $orgId = $this->orgId();
        $uid   = $this->user()->id;

        // manager + operator cannot create
        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['message'=>'You do not have permission to create.'],403);
        }

        $request->validate(['name'=>'required']);

        $source = LeadSource::create([
            'organization_id'     => $orgId,
            'name'                => $request->name,
            'status'              => 1,
            'lead_count'          => 0,
            'billing_setup_amount'=> 0
        ]);

        logOrganizationActivity($orgId,$uid,'lead_source',$source->id,'create_lead_source',"Lead Source created",$source->toArray());
        logUserActivity($orgId,$uid,'lead_source',$source->id,'create_lead_source',"User created a lead source",$source->toArray());

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    // EDIT LOAD
    // ---------------------------------------------------------
    public function edit($id)
    {
        $role  = $this->role();
        $orgId = $this->orgId();

        $source = LeadSource::findOrFail($id);

        if ($role !== 'root' && $role !== 'sadmin' && $source->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        return response()->json($source);
    }

    // ---------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------
    public function update(Request $request, $id)
    {
        $role  = $this->role();
        $orgId = $this->orgId();
        $uid   = $this->user()->id;

        // manager + operator cannot update
        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['message'=>'You do not have permission to update'],403);
        }

        $request->validate(['name'=>'required']);

        $source = LeadSource::findOrFail($id);

        if ($role !== 'root' && $source->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $old = $source->toArray();

        $source->update(['name'=>$request->name]);

        logOrganizationActivity($orgId,$uid,'lead_source',$id,'update_lead_source',"Lead Source Updated",['old'=>$old,'new'=>$request->all()]);
        logUserActivity($orgId,$uid,'lead_source',$id,'update_lead_source',"User updated lead source",['old'=>$old,'new'=>$request->all()]);

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    // BILLING UPDATE
    // ---------------------------------------------------------
    public function billingUpdate(Request $request, $id)
    {
        $role  = $this->role();
        $orgId = $this->orgId();
        $uid   = $this->user()->id;

        // Only admin / sadmin / root allowed
        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['message'=>'Permission denied'],403);
        }

        $request->validate(['billing_setup_amount'=>'required|numeric']);

        $source = LeadSource::findOrFail($id);

        if ($role !== 'root' && $source->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $old = $source->toArray();

        $source->update([
            'billing_setup_amount'=>$request->billing_setup_amount,
            'status'=>$request->status
        ]);

        logOrganizationActivity(
            $orgId,$uid,'lead_source',$id,'update_billing',
            "Billing updated",
            ['old'=>$old,'new'=>$request->all()]
        );

        logUserActivity(
            $orgId,$uid,'lead_source',$id,'update_billing',
            "User updated billing",
            ['old'=>$old,'new'=>$request->all()]
        );

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    // DELETE
    // ---------------------------------------------------------
    public function destroy($id)
    {
        $role  = $this->role();
        $orgId = $this->orgId();
        $uid   = $this->user()->id;

        // sadmin cannot delete
        if (!in_array($role, ['root','admin'])) {
            return response()->json(['message'=>'Delete not allowed'],403);
        }

        $source = LeadSource::findOrFail($id);

        if ($role !== 'root' && $source->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized'],403);
        }

        $old = $source->toArray();

        $source->delete();

        logOrganizationActivity($orgId,$uid,'lead_source',$id,'delete_lead_source',"Deleted",$old);
        logUserActivity($orgId,$uid,'lead_source',$id,'delete_lead_source',"User deleted lead source",$old);

        return response()->json(['status'=>'success']);
    }
}

