<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\SmsLog;
use Sentinel;

class SmsLogController extends Controller
{
    public function index()
    {
        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization;

        $logs = SmsLog::where('organization_id', $orgId)
            ->orderBy('id','DESC')
            ->paginate(50);

        return view('FrontEnd.automation.sms', compact('logs'));
    }
}
