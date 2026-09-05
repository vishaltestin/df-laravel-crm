<?php
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Designation;
use App\Organization;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Crypt;

class DesignationController extends Controller
{
    // ---------------------------------------------------------
    // HELPERS
    // ---------------------------------------------------------
    protected function user()
    {
        return Sentinel::check();
    }

    protected function role()
    {
        return $this->user()->roles()->first()->slug ?? null;
    }

    protected function userId()
    {
        return $this->user()->id;
    }

    protected function orgId()
    {
        return registeruser($this->userId())->organization;
    }

    // ---------------------------------------------------------
    // INDEX
    // ---------------------------------------------------------
    public function index()
    {
        $role  = $this->role();
        $orgId = $this->orgId();

        if ($role === 'root') {
            $designations = Designation::with('organization')
                ->orderBy('id','DESC')
                ->get();

            $organizations = Organization::orderBy('name')->get();
        } 
        else {
            $designations = Designation::with('organization')
                ->where('organization_id',$orgId)
                ->orderBy('id','DESC')
                ->get();

            $organizations = Organization::where('id',$orgId)->get();
        }

        return view('FrontEnd.designation.index', compact(
            'designations','organizations','role','orgId'
        ));
    }

    // ---------------------------------------------------------
    // STORE
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $role  = $this->role();
        $uid   = $this->userId();
        $orgId = $this->orgId();

        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['message' => 'Permission denied'],403);
        }

        $request->validate([
            'name' => 'required',
            'organization_id' => 'required|exists:organizations,id',
            'status' => 'required'
        ]);

        // Non-root user can create only in own org
        if ($role !== 'root' && $request->organization_id != $orgId) {
            return response()->json(['message' => 'You cannot add designation to another organization'],403);
        }

        $designation = Designation::create($request->all());

        logOrganizationActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $designation->id,
            'create_designation',
            "Designation '{$designation->name}' created",
            $designation->toArray()
        );

        logUserActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $designation->id,
            'create_designation',
            "User created designation '{$designation->name}'",
            $designation->toArray()
        );

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    // EDIT
    // ---------------------------------------------------------
   public function edit($id)
{
    $role  = $this->role();
    $orgId = $this->orgId();

    $designation = Designation::with('organization')->findOrFail($id);

    if ($role !== 'root' && $designation->organization_id != $orgId) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized record'
        ], 403);
    }

    return response()->json([
        'id' => $designation->id,
        'name' => $designation->name,
        'status' => $designation->status,
        'organization' => [
            'id'   => $designation->organization_id,
            'name' => $designation->organization?->name
        ]
    ]);
}

 public function show($id)
{
    try {
        $id = Crypt::decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }

	$role  = $this->role();
    $orgId = $this->orgId();

    $designation = Designation::with('organization')->findOrFail($id);

    if ($role !== 'root' && $designation->organization_id != $orgId) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized record'
        ], 403);
    }

    return response()->json([
        'id'                => $designation->id,
        'name'              => $designation->name,
        'organization_id'   => $designation->organization_id,
        'organization_name' => $designation->organization?->name, // ✅ yahi chahiye tha
        'status'            => $designation->status,
        'created_at'        => $designation->created_at,
    ]);
}


    // ---------------------------------------------------------
    // UPDATE
    // ---------------------------------------------------------
    public function update(Request $request, $id)
    {
        $role  = $this->role();
        $uid   = $this->userId();
        $orgId = $this->orgId();

        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['message'=>'Permission denied'],403);
        }

        $request->validate([
            'name'=>'required',
            'organization_id'=>'required',
            'status'=>'required'
        ]);

        $designation = Designation::findOrFail($id);

        if ($role !== 'root' && $designation->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized organization record'],403);
        }

        $old = $designation->toArray();
        $designation->update($request->all());

        logOrganizationActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $designation->id,
            'update_designation',
            "Designation '{$designation->name}' updated",
            ['old'=>$old,'new'=>$designation->toArray()]
        );

        logUserActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $designation->id,
            'update_designation',
            "User updated designation '{$designation->name}'",
            ['old'=>$old,'new'=>$designation->toArray()]
        );

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    // DELETE
    // ---------------------------------------------------------
    public function destroy($id)
    {
        $role  = $this->role();
        $uid   = $this->userId();
        $orgId = $this->orgId();

        if (!in_array($role, ['root','admin'])) {
            return response()->json(['message'=>'Delete not allowed'],403);
        }

        $designation = Designation::findOrFail($id);

        if ($role !== 'root' && $designation->organization_id != $orgId) {
            return response()->json(['message'=>'Unauthorized delete'],403);
        }

        $old = $designation->toArray();

        $designation->delete();

        logOrganizationActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $id,
            'delete_designation',
            "Designation deleted",
            $old
        );

        logUserActivity(
            $designation->organization_id,
            $uid,
            'designation',
            $id,
            'delete_designation',
            "User deleted designation",
            $old
        );

        return response()->json(['status'=>'success']);
    }
}
