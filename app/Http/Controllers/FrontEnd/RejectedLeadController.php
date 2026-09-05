<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\AutomationRawLead;
use Sentinel;

class RejectedLeadController extends Controller
{
   public function index()
{
    $user = Sentinel::check();
    $orgId = registeruser($user->id)->organization;

    $rows = AutomationRawLead::where('organization_id', $orgId)
        ->whereIn('status', ['rejected', 'ivr_failed'])
        ->orderBy('id', 'DESC')
        ->get();

    return view('FrontEnd.automation.rejected-leads', compact('rows'));
}

}
