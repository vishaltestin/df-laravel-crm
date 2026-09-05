<?php
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Institute;
use App\Program;
use App\Product;

class InstituteAPIController extends Controller
{
    public function institutes(Request $req)
    {
        $orgId = $req->query('organization_id') ?: ($req->user()?->organization_id ?? null);
        $q = Institute::when($orgId, function($q2) use($orgId){ $q2->where('organization_id',$orgId); });
        return response()->json($q->get());
    }

    public function programs($id)
    {
        return response()->json(Program::where('institute_id', $id)->get());
    }

    public function courses($id)
    {
        return response()->json(Product::where('program_id', $id)->get());
    }
}
