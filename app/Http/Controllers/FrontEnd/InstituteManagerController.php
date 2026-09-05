<?php
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Institute;
use App\Program;
use App\Product;
use Sentinel;

class InstituteManagerController extends Controller
{
    // Single canvas page
    public function index()
    {
        // get organization id from your registeruser helper (same as your app)
     $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
        $userId = $reg->id;


   

        $institutes = Institute::where('organization_id', $orgId)->orderBy('id','desc')->get();
        // courses from products
        $products = Product::where('organization_id', $orgId)->orderBy('id','desc')->get();

        return view('FrontEnd.products.institute_manager', compact('institutes','products','orgId'));
    }

    // ---------------- Institute CRUD ----------------
    public function storeInstitute(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'organization_id' => 'required|integer'
        ]);

        $inst = Institute::create([
            'organization_id' => $req->organization_id,
            'name' => $req->name,
            'description' => $req->description
        ]);

        return response()->json(['status'=>'success','data'=>$inst]);
    }

    public function updateInstitute(Request $req, $id)
    {
        $req->validate(['name'=>'required|string|max:255']);

        $inst = Institute::findOrFail($id);
        $inst->update([
            'name' => $req->name,
            'description' => $req->description
        ]);

        return response()->json(['status'=>'success','data'=>$inst]);
    }

    public function deleteInstitute($id)
    {
        $inst = Institute::findOrFail($id);
        $inst->delete();
        return response()->json(['status'=>'success']);
    }

    // ---------------- Program CRUD ----------------
    public function storeProgram(Request $req)
    {
        $req->validate([
            'institute_id' => 'required|integer',
            'name' => 'required|string|max:255'
        ]);

        $program = Program::create([
            'institute_id' => $req->institute_id,
            'name' => $req->name,
            'description' => $req->description
        ]);

        return response()->json(['status'=>'success','data'=>$program]);
    }

    public function updateProgram(Request $req, $id)
    {
        $req->validate(['name'=>'required|string|max:255']);

        $program = Program::findOrFail($id);
        $program->update([
            'name' => $req->name,
            'description' => $req->description
        ]);

        return response()->json(['status'=>'success','data'=>$program]);
    }

    public function deleteProgram($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();
        return response()->json(['status'=>'success']);
    }

    // ---------------- Course assign ----------------
    public function assignCourseToProgram(Request $req)
    {
        $req->validate([
            'course_id' => 'required|integer',
            'program_id' => 'nullable|integer'
        ]);

        $course = Product::findOrFail($req->course_id);
        $course->program_id = $req->program_id ?: null;
        $course->save();

        return response()->json(['status'=>'success','data'=>$course]);
    }

    // Helper to get programs by institute (used by AJAX)
 
public function programsByInstitute($institute_id)
{
    // ----------------------------------
    // Organization (MANDATORY)
    // ----------------------------------
    

    

    // ----------------------------------
    // CASE 1: ALL institutes programs
    // ----------------------------------
    if ($institute_id === 'all') {

        $programs = \App\Program::select('id', 'name', 'institute_id')
             ->where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $programs
        ]);
    }

    // ----------------------------------
    // CASE 2: Invalid institute id
    // ----------------------------------
    if (!is_numeric($institute_id)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Valid institute_id is required'
        ], 400);
    }

    // ----------------------------------
    // CASE 3: Single institute programs
    // ----------------------------------
    $programs = \App\Program::select('id', 'name')
         ->where('institute_id', $institute_id)
        ->where('status', 1)
        ->orderBy('name')
        ->get();

    return response()->json([
        'status' => 'success',
        'data'   => $programs
    ]);
}

public function coursesByProgram(Request $request)
{
    $programmeId = $request->get('programme_ids');

    if (!$programmeId) {
        return response()->json([
            'status' => 'error',
            'message' => 'programme_ids missing'
        ], 400);
    }

    $courses = \App\Product::select('id', 'name', 'programme_ids')
        ->where('programme_ids', $programmeId)
        ->get();

    return response()->json([
        'status' => 'success',
        'count'  => $courses->count(),
        'data'   => $courses
    ]);
}



	public function showInstitute($id){
    $inst = Institute::findOrFail($id);
    return response()->json($inst);
}

public function showProgram($id){
    $prog = Program::findOrFail($id);
    return response()->json($prog);
}

public function allPrograms(){
    $user = \Sentinel::check();
    $orgId = $user ? registeruser($user->id)->organization : 0;
    $programs = Program::whereHas('institute', function($q) use($orgId){
        $q->where('organization_id', $orgId);
    })->get();
    return response()->json($programs);
}
public function courseJson($id)
{
    $course = Product::find($id);
     if(!$course){
        return response()->json(['status' => false, 'message' => 'Course not found'], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $course
    ]);
}



}
