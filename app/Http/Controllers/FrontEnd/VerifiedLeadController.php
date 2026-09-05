<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;              // ✅ FIXED
use App\AutomationRawLead;
 use Sentinel;

class VerifiedLeadController extends Controller
{
    public function index(Request $request)
    {
        // Logged in user
        $user = Sentinel::check();

        // Organization id from register table
        $orgId = registeruser($user->id)->organization;

        // Fetch verified leads
        $data = AutomationRawLead::where('organization_id', $orgId)
            ->orderBy('id','DESC')
            ->get();

        // AJAX request (table loading)
        if ($request->ajax) {
            return response()->json(['data' => $data]);
        }

        // Normal page view
        return view('FrontEnd.automation.verified-leads', compact('data'));
    }
}
