<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\LeadAssignmentLog;
use App\User;
use Illuminate\Support\Facades\DB;

class LeadAssignmentReportController extends Controller
{
    public function index()
    {
        $orgId = \Sentinel::check()->organization_id ?? 1;

        // Rule type wise count
        $byRuleType = LeadAssignmentLog::select('rule_type', DB::raw('COUNT(*) as total'))
            ->where('organization_id', $orgId)
            ->groupBy('rule_type')
            ->get();

        // User wise lead count
        $byUser = LeadAssignmentLog::select('assigned_to', DB::raw('COUNT(*) as total'))
            ->where('organization_id', $orgId)
            ->groupBy('assigned_to')
            ->get();

        $users = User::whereIn('id', $byUser->pluck('assigned_to'))->get()->keyBy('id');

        return view('FrontEnd.reports.lead_distribution', compact('byRuleType', 'byUser', 'users'));
    }
}
