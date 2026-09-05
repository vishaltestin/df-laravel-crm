<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\WhatsAppLog;
use Sentinel;

class WhatsAppLogController extends Controller
{
    public function index()
    {
        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization;

        $logs = WhatsAppLog::where('organization_id', $orgId)
            ->orderBy('id', 'DESC')
            ->paginate(50);

        return view('FrontEnd.automation.whatsapp', compact('logs'));
    }
}
