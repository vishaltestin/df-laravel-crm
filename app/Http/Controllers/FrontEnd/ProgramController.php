<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Program;
use App\Institute;
use Sentinel;

class ProgramController extends Controller
{
    // ============================================
    // COMMON USER + ROLE + ORGANIZATION CONTEXT
    // ============================================
    private function ctx()
    {
        $u = \Sentinel::check();
        $reg = registeruser($u->id);

        return (object)[
            'user'  => $u,
            'role'  => $u->roles()->first()->slug ?? null,
            'orgId' => $reg->organization
        ];
    }

    private function allow(...$roles)
    {
        $ctx = $this->ctx();
        if (!in_array($ctx->role, $roles)) {
            abort(403, 'Unauthorized Action');
        }
    }

    // ===================================================
    // INDEX PAGE — role-based institute/program listing
    // ===================================================
    public function index()
    {
        $ctx = $this->ctx();

        if ($ctx->role === 'root' || $ctx->role === 'sadmin') {
            // full access
            $institutes = Institute::orderBy('name')->get();
            $programs   = Program::with('institute')->orderBy('id','DESC')->get();
        } 
        else {
            // admin, manager, operater → only their organization
            $institutes = Institute::where('organization_id', $ctx->orgId)
                                   ->orderBy('name')
                                   ->get();

            $programs = Program::with('institute')
                ->whereHas('institute', function($q) use ($ctx) {
                    $q->where('organization_id', $ctx->orgId);
                })
                ->orderBy('id','DESC')
                ->get();
        }

        return view('FrontEnd.products.program_manager', compact('institutes','programs'));
    }

    // ===================================================
    // SHOW INSTITUTE DETAILS (AJAX)
    // ===================================================
    public function showInstitute($id)
    {
        $ctx = $this->ctx();
        $inst = Institute::findOrFail($id);

        if (!in_array($ctx->role, ['root','sadmin']) && $inst->organization_id != $ctx->orgId) {
            abort(403, 'Unauthorized');
        }

        return response()->json($inst);
    }

    public function showProgram($id)
    {
        $ctx = $this->ctx();
        $prog = Program::findOrFail($id);

        if (!in_array($ctx->role, ['root','sadmin']) &&
            $prog->institute->organization_id != $ctx->orgId) 
        {
            abort(403, 'Unauthorized');
        }

        return response()->json($prog);
    }

    // ===================================================
    // STORE PROGRAM (root , sadmin , admin only)
    // ===================================================
    public function store(Request $request)
    {
        $ctx = $this->ctx();
        $this->allow('root','sadmin','admin');

        $request->validate([
            'institute_id' => 'required',
            'name' => 'required'
        ]);

        $institute = Institute::findOrFail($request->institute_id);

        // admin cannot create in another org
        if ($ctx->role === 'admin' && $institute->organization_id != $ctx->orgId) {
            abort(403, 'Unauthorized');
        }

        $program = Program::create([
            'institute_id' => $request->institute_id,
            'name'         => $request->name,
            'description'  => $request->description,
            'status'       => 1
        ]);

        return response()->json(['status'=>'success','data'=>$program]);
    }

    // ===================================================
    // UPDATE PROGRAM (root, sadmin, admin, manager)
    // ===================================================
    public function update(Request $request, $id)
    {
        $ctx = $this->ctx();

        if (!in_array($ctx->role, ['root','sadmin','admin','manager'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'institute_id' => 'required',
            'name' => 'required'
        ]);

        $program = Program::findOrFail($id);

        // manager/admin cannot modify programs of another org
        if (!in_array($ctx->role, ['root','sadmin']) &&
            $program->institute->organization_id != $ctx->orgId)
        {
            abort(403, 'Unauthorized');
        }

        $program->update($request->all());

        return response()->json(['status'=>'success']);
    }

    // ===================================================
    // DELETE PROGRAM (root + admin only)
    // ===================================================
    public function delete($id)
    {
        $ctx = $this->ctx();
        $program = Program::findOrFail($id);

        // sadmin cannot delete
        if ($ctx->role === 'sadmin') {
            abort(403, 'Delete Not Allowed For SADMIN');
        }

        if ($ctx->role === 'admin' && $program->institute->organization_id != $ctx->orgId) {
            abort(403, 'Unauthorized');
        }

        if (!in_array($ctx->role, ['root','admin'])) {
            abort(403, 'Unauthorized');
        }

        $program->delete();

        return response()->json(['status'=>'success']);
    }

    // ===================================================
    // FETCH PROGRAMS FOR INSTITUTE (Role Protected)
    // ===================================================
    public function programsByInstitute($id)
    {
        $ctx = $this->ctx();
        $inst = Institute::findOrFail($id);

        if (!in_array($ctx->role, ['root','sadmin']) &&
            $inst->organization_id != $ctx->orgId)
        {
            abort(403, 'Unauthorized');
        }

        return response()->json(
            Program::where('institute_id',$id)
                    ->orderBy('name')
                    ->get()
        );
    }

}
