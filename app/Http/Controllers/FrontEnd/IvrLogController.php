<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\IvrLog;
use Sentinel;

class IvrLogController extends Controller
{
   public function index()
{
    $user = Sentinel::check();
    $orgId = registeruser($user->id)->organization;

    $logs = IvrLog::where('organization_id', $orgId)
            ->orderBy('id', 'DESC')
            ->paginate(50);

    return view('FrontEnd.automation.ivr', compact('logs'));
}

}
