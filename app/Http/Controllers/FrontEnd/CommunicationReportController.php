<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\CommunicationLog;
use Illuminate\Http\Request;

class CommunicationReportController extends Controller
{
    public function index(Request $request)
    {
        $user = \Sentinel::check();
        if(!$user){
            return redirect('login')->with('error','Session expired');
        }

        $reg   = registeruser($user->id);
        $orgId = $reg->organization ?? null;

        $role = $user->roles()->first()->slug ?? null;

        // Filters
        $from    = $request->from;
        $to      = $request->to;
        $channel = $request->channel;
        $status  = $request->status;

        $q = CommunicationLog::with(['smsLog','emailLog','whatsappLog'])
            ->orderByDesc('id');

        // org restriction
        if($role !== 'root'){
            $q->where('organization_id', $orgId);
        }else{
            if($request->filled('organization_id')){
                $q->where('organization_id', $request->organization_id);
            }
        }

        // date filter
        if($from){
            $q->whereDate('created_at', '>=', $from);
        }
        if($to){
            $q->whereDate('created_at', '<=', $to);
        }

        // channel
        if($channel && $channel !== 'all'){
            $q->where('channel', $channel);
        }

        // status
        if($status && $status !== 'all'){
            $q->where('status', $status);
        }

        $logs = $q->paginate(50)->withQueryString();

        return view('FrontEnd.reports.communication_report', compact(
            'logs','from','to','channel','status','role'
        ));
    }
}
