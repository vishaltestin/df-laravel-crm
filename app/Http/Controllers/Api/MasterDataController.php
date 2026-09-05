<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Sentinel;

/* MODELS */
use App\Institute;
use App\Program;
use App\Product;
use App\Country;
use App\State;
use App\City;

use App\EmailTemplate;
use App\MessageTemplate;
use App\WhatsappTemplate;
use App\Team;
use App\Register;
use App\Lead;
use App\LeadStatus;
use App\LeadSubStatus;


class MasterDataController extends Controller
{
   
    
    public function countries()
    {
        return Country::select('id','name','code')->orderBy('name')->get();
    }

  
    // 🔹 States
    public function states(Request $request)
    {
        $countryId = $request->get('country_id', 80); // India default

        return State::select('id', 'name')
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get();
    }

    // 🔹 Cities
    public function cities(Request $request)
    {
        $stateId = $request->get('state_id');

        if (!$stateId) return [];

        return City::select('id', 'name')
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get();
    }

   /* =========================================
       COMMON ORG CONTEXT
    ========================================= */
private function orgContext()
{
    $user = Sentinel::check(); // ✅ ab true

    $reg = registeruser($user->id);

    return [
        'uid'  => $user->id,
        'role' => $user->roles()->first()->slug ?? null,
        'org'  => $reg->organization
    ];
}

public function templateById(Request $request)
{
    $ctx  = $this->orgContext();
    $id   = $request->id;
    $type = $request->type;

    if($type == 'email'){
        return \App\EmailTemplate::where('organization_id',$ctx['org'])->findOrFail($id);
    }

    if($type == 'sms'){
        return \App\MessageTemplate::where('organization_id',$ctx['org'])->findOrFail($id);
    }

    if($type == 'whatsapp'){
        return \App\WhatsappTemplate::where('organization_id',$ctx['org'])->findOrFail($id);
    }

    return response()->json(['error'=>'Invalid type'],404);
}

    /* =========================================
       MASTER DATA (already existing)
    ========================================= */
// 🔹 Programs by Institute
    public function programs(Request $request)
    {
        $instituteId = $request->get('institute_id');

        if (!$instituteId) return [];

        return Program::select('id', 'name')
            ->where('institute_id', $instituteId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
    }

    public function institutes()
    {
        return Institute::select('id','name')
            ->where('status',1)
            ->orderBy('name')
            ->get();
    }

   
    public function courses(Request $request)
{
    // single id OR multiple ids (comma separated)
    $programId = $request->program_id;
    $programmeIds = $request->programme_ids;

    if (!$programId && !$programmeIds) {
        return [];
    }

    $query = Product::select('id', 'name');

    // Case 1: multiple ids
    if ($programmeIds) {
        $ids = array_filter(explode(',', $programmeIds));
        $query->whereIn('program_id', $ids);
    }
    // Case 2: single id
    else {
        $query->where('program_id', $programId);
    }

    return $query->orderBy('name')->get();
}

public function coursesonly(Request $request)
{
    $programId     = $request->program_id;
    $programmeIds  = $request->programme_ids;
    $instituteId   = $request->institute_id;

    $query = Product::with('programme:id,institute_id')
                    ->select('id','name','program_id');

    if ($programmeIds) {
        $ids = array_filter(explode(',', $programmeIds));
        $query->whereIn('program_id', $ids);
    } elseif ($programId) {
        $query->where('program_id', $programId);
    }

    if ($instituteId) {
        $query->whereHas('programme', function($q) use ($instituteId){
            $q->where('institute_id', $instituteId);
        });
    }

    return $query->orderBy('name')->get();
}  
    /* =========================================
       ORGANIZATION BASED (NEW APIs)
    ========================================= */

    // 📧 Email Templates
    public function emailTemplates()
    {
        $ctx = $this->orgContext();

        return EmailTemplate::select('id','title as name')
            ->where('organization_id',$ctx['org'])
            ->orderBy('title')
            ->get();
    }

    // 💬 WhatsApp Templates
    public function whatsappTemplates()
    {
        $ctx = $this->orgContext();

        return WhatsappTemplate::select('id','title as name')
            ->where('organization_id',$ctx['org'])
            ->orderBy('title')
            ->get();
    }

    // 📱 SMS Templates
    public function smsTemplates()
    {
        $ctx = $this->orgContext();

        return MessageTemplate::select('id','title as name')
            ->where('organization_id',$ctx['org'])
            ->orderBy('title')
            ->get();
    }

    // 👥 Teams (role aware)
    public function teams()
    {
        $ctx = $this->orgContext();

        $query = Team::select('id','name')
            ->where('organization_id',$ctx['org']);

        if (!in_array($ctx['role'], ['root','admin','sadmin'])) {
            $query->where(function ($q) use ($ctx) {
                $q->where('head',$ctx['uid'])
                  ->orWhereRaw("FIND_IN_SET(?, team_member)", [$ctx['uid']]);
            });
        }

        return $query->orderBy('name')->get();
    }

    // 👤 Users (optional team_id filter)
    public function users(Request $request)
    {
        $ctx = $this->orgContext();
         $query = Register::where('organization',$ctx['org']);

        if ($request->team_id) {
            $query->where('team',$request->team_id);
        }

        return $query->orderBy('first_name')->get()->map(function ($u) {
            return [
                'id'   => $u->id,
                'name' => trim($u->first_name.' '.$u->last_name)
            ];
        });
    }

		public function leadStatuses()
		{
			return LeadStatus::select('id','name')
				->orderBy('name')
				->get();
		}
public function leadStatusesapi(Request $request)
{
    $orgId = $request->org_id;

    return LeadStatus::select('id','name')
        ->where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->orWhereNull('organization_id');
        })
        ->orderByRaw("
            CASE 
                WHEN organization_id = {$orgId} THEN 0 
                ELSE 1 
            END
        ")
        ->orderBy('name')
        ->get();
}

public function leadStatusSubStatusAjax(Request $request)
{
    // Case 1: Load substatuses by status id
    if ($request->lead_status_id) {
        return LeadSubStatus::where('lead_status_id', $request->lead_status_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    // Case 2: Load current lead status & substatus
    if ($request->lead_id) {
        $lead = Lead::find($request->lead_id);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        $subStatuses = LeadSubStatus::where('lead_status_id', $lead->lead_status_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'lead_status_id'     => $lead->lead_status_id,
            'lead_sub_status_id' => $lead->lead_sub_status_id,
            'sub_statuses'       => $subStatuses
        ]);
    }

    return response()->json([]);
}

}
