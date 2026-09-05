<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// MODELS


use App\Lead;
use App\LeadApplication;
use App\LeadCommunicationLog;
use App\LeadVerification;

use App\LeadSource;
use App\LeadStatus;
use App\LeadSubStatus;
use App\LeadPipeline;
use App\LeadPipelineStage;
use App\LeadType;

use App\Institute;
use App\Programme;
use App\Faculty;

use App\User;
use App\Organization;
 

class LeadController extends Controller
{
    /* -----------------------------------------------------
     |  ROLE HELPERS
     ----------------------------------------------------- */
    private function getRole() { return Sentinel::check()->roles()->first()->slug ?? null; }
    private function getOrgId() { $reg = registeruser(Sentinel::check()->id); return $reg->organization; }
    private function isRoot() { return $this->getRole() === 'root'; }
    private function isSadmin() { return $this->getRole() === 'sadmin'; }
    private function isAdmin() { return $this->getRole() === 'admin'; }
    private function isManager() { return $this->getRole() === 'manager'; }
    private function isOperator() { return $this->getRole() === 'operater'; }
private function prepareLeadDefaults($orgId, $requestData = [])
{
    // 1️⃣ Default Lead Status
 $defaultStatus = LeadStatus::where(function ($q) use ($orgId) {
        if (!empty($orgId)) {
            $q->where('organization_id', $orgId);
        } else {
            $q->whereNull('organization_id'); // global default
        }
    })
    ->where('id', 1)
    ->first();

    // 2️⃣ Default Sub Status
   $defaultSubStatus = null;

if ($defaultStatus) {
    $defaultSubStatus = LeadSubStatus::where('lead_status_id', $defaultStatus->id)
        ->where('lead_status_id', 1)
        ->first();
}

    // 3️⃣ Default Pipeline
   $pipeline = LeadPipeline::where('organization_id', $orgId)
    ->first();

    // 4️⃣ First Stage of Pipeline
    $stage = LeadPipelineStage::where('lead_pipeline_id', $pipeline->id ?? null)
         ->first();

    // 5️⃣ Default Lead Type
    $type = LeadType::where('organization_id', $orgId)
         ->first();

    return [
        'lead_status_id'      => $requestData['lead_status_id']      ?? $defaultStatus->id ?? null,
        'lead_sub_status_id'  => $requestData['lead_sub_status_id']  ?? $defaultSubStatus->id ?? null,
        'lead_source_id'      => $requestData['lead_source_id']      ?? null,
        'lead_campaign_id'    => $requestData['lead_campaign_id']    ?? null,
        'institute_applying_for' => $requestData['institute_applying_for'] ?? null,
        'pipeline_id'         => $requestData['pipeline_id']         ?? $pipeline->id ?? null,
        'stage_id'            => $requestData['stage_id']            ?? $stage->id ?? null,
        'type_id'             => $requestData['type_id']             ?? $type->id ?? null,
    ];
}




    public function index(Request $request)
{
    $user = \Sentinel::check();
    $userId = $user->id;

    $reg = registeruser($userId);
    $orgId = $reg->organization;

    // -----------------------------------------------------
    //  BASE QUERY
    // -----------------------------------------------------
    $query = Lead::query()
        ->from('leads as l')
        ->where('l.organization_id', $orgId)

        // Unique by mobile (first entry)
        ->whereIn('l.id', function ($q) use ($orgId) {
            $q->select(DB::raw('MIN(id)'))
              ->from('leads')
              ->where('organization_id', $orgId)
              ->groupBy('mobile');
        })

        ->leftJoin('lead_sources as ls', 'ls.id', '=', 'l.lead_source_id')
        ->leftJoin('campaigns as lc', 'lc.id', '=', 'l.lead_campaign_id')
        ->leftJoin('pipelines as p', 'p.id', '=', 'l.pipeline_id')
        ->leftJoin('pipeline_stages as s', 's.id', '=', 'l.stage_id')
        ->leftJoin('lead_types as lt', 'lt.id', '=', 'l.type_id')
        ->leftJoin('registers as u', 'u.id', '=', 'l.lead_assign_to')
        ->leftJoin('organizations as o', 'o.id', '=', 'l.organization_id');

    // -----------------------------------------------------
    // 🔥 APPLY ALL FILTERS (DYNAMIC)
    // -----------------------------------------------------

    // DATE RANGE
    if ($request->date_from && $request->date_to) {
        $query->whereBetween('l.created_at', [
            $request->date_from . ' 00:00:00',
            $request->date_to . ' 23:59:59'
        ]);
    }

    // BASIC TEXT FIELDS
    foreach (['name', 'mobile', 'email'] as $field) {
        if ($request->$field) {
            $query->where("l.$field", 'LIKE', "%".$request->$field."%");
        }
    }

    // --------------------------------------------
    // 🔥 JSON FIELDS → STATE & CITY
    // --------------------------------------------
  // --------------------------------------------
// 🔥 JSON FILTER (STATE & CITY) – CORRECT
// --------------------------------------------
	if ($request->state) {
		$query->whereRaw(
			"JSON_UNQUOTE(JSON_EXTRACT(l.extra_fields_json, '$.state_id')) = ?",
			[$request->state]
		);
	}

	if ($request->city) {
		$query->whereRaw(
			"JSON_UNQUOTE(JSON_EXTRACT(l.extra_fields_json, '$.city_id')) = ?",
			[$request->city]
		);
	}

    // EXACT MATCH FIELDS (IDs)
    foreach ([
        'lead_status_id',
        'lead_sub_status_id',
        'pipeline_id',
        'stage_id',
        'lead_assign_to',
        'lead_campaign_id',
        'lead_source_id',
        'organization_id',
        'programmes'
    ] as $field) {
        if ($request->$field) {
            $query->where("l.$field", $request->$field);
        }
    }

    // SCORE RANGE
    if ($request->score_from || $request->score_to) {
        $query->whereBetween('l.lead_score', [
            $request->score_from ?? 0,
            $request->score_to ?? 999999
        ]);
    }

    // -----------------------------------------------------
    //  FINAL SELECT + ORDERING + PAGINATION
    // -----------------------------------------------------
    $leads = $query
        ->select([
            'l.*',
            'ls.name as lead_source_name',
            'lc.name as lead_campaign_name',
            'p.name  as pipeline_name',
            's.name  as stage_name',
            'lt.name as lead_type_name',
            'u.first_name as assigned_user_name',
            'o.name as organization_name',
        ])
        ->orderBy('l.lead_weight_count', 'DESC')
        ->orderBy('l.id', 'DESC')
        ->paginate(30)
        ->appends($request->query());

    // -----------------------------------------------------
    //  SEND DYNAMIC DROPDOWNS TO VIEW
    // -----------------------------------------------------
    $countries   = Country::all();
    $states      = State::all();
    $cities      = City::all();
    $pipelines   = LeadPipeline::all();
    $stages      = LeadPipelineStage::all();
    $type      = LeadType::all();

$institutes = Institute::all();     // Qualification
$faculties  = Program::all();     // Faculty
$products   = Product::all();       // Programmes dropdown

    $leadStatuses = LeadStatus::all();
    $leadSubStatuses = LeadSubStatus::all();
    $teamMembers = Register::all();
    $campaigns = Campaign::all();
    $sources = LeadSource::all();
    $organizations = Organization::all();

    return view('FrontEnd.leads_index', compact(
        'leads',
        'countries', 'states', 'cities',
        'pipelines', 'stages',
        'institutes','faculties','products',
        'leadStatuses', 'leadSubStatuses',
        'teamMembers',
        'campaigns',
        'sources',
        'organizations','type'
    ));
}



public function quickStore(Request $req)
{
    $user = Sentinel::check();
    if (!$user) {
        abort(401, 'Unauthenticated');
    }

    $orgId = registeruser($user->id)->organization;
	$defaults = $this->prepareLeadDefaults($orgId, $req->all()); // tp collect data of lead_status_id,lead_sub_status_id,lead_source_id,lead_campaign_id,institute_applying_for, pipline_id,stage_id,type_id
    // -------------------------
    // BASIC VALIDATION
    // -------------------------
    $rules = [
        'name'   => 'required',
        'mobile' => 'required',
        'state'  => 'required',
        'city'   => 'required',
    ];

    if ($req->upload_via === 'email') {
        $rules['email'] = 'required|email';
    }

    $req->validate($rules);

    // -------------------------
    // CREATE LEAD
    // -------------------------
    $lead = new Lead();
    $lead->organization_id = $orgId;
    $lead->created_by      = $user->id;

    $lead->name   = $req->name;
    $lead->mobile = $req->mobile;
    $lead->email  = $req->upload_via === 'email' ? $req->email : null;

    $lead->lead_assign_to = $req->lead_assign_to ?? $user->id;
	
	$lead->fill($defaults);
	
    // 🔹 Correct column names
    $lead->programmes       = $req->programme ?? null;
    $lead->lead_campaign_id = $req->campaign ?? null;
    $lead->lead_source_id   = $req->source ?? null;
    $lead->type_id     = $req->medium ?? null;

    // -------------------------
    // EXTRA FIELDS JSON
    // -------------------------
    $lead->extra_fields_json = json_encode([
        'state_id'       => $req->state,
        'city_id'        => $req->city,
        'qualification'  => $req->qualification,
        'faculty'        => $req->faculty,
        'upload_via'     => $req->upload_via,
    ]);

    $lead->save();

    // -------------------------
    // REDIRECT
    // -------------------------
    if ($req->action === 'save_new') {
        return back()
            ->with('success', 'Lead Added!')
            ->with('openCanvas', true);
    }

    return redirect()
        ->route('Leads.LeadShow', $lead->id)
        ->with('success', 'Lead Added Successfully');
}


/* -----------------------------------------------------
     |  LOAD FULL LEAD WITH ALL RELATIONS
     ----------------------------------------------------- */
    private function loadFullLead($id)
    {
        return Lead::with([
            'notes' => fn($q) => $q->orderBy('created_at','desc'),
            'activityLogs' => fn($q) => $q->orderBy('created_at','desc'),
            'communications' => fn($q) => $q->orderBy('created_at','desc'),
            'WhatsAppLogs' => fn($q) => $q->orderBy('created_at','desc'),
            'callLogs' => fn($q) => $q->orderBy('created_at','desc'),
            'events' => fn($q) => $q->orderBy('start_at','desc'),
            'pipeline',
            'stage',
            'programme',
            'assignedUser',
            'leadStatus',
            'leadSubStatus'
        ])->find($id);
    }

    /* -----------------------------------------------------
     |  UNIFIED TIMELINE BUILDER
     ----------------------------------------------------- */
    private function buildTimeline(Lead $lead)
    {
        $items = collect();

        foreach ($lead->notes as $n) {
            $items->push((object)[
                'type'=>'note',
                'title'=>'Note',
                'details'=>$n->note,
                'created_at'=>$n->created_at,
                'icon'=>'ri-sticky-note-line'
            ]);
        }

        foreach ($lead->activityLogs as $l) {
            $items->push((object)[
                'type'=>'activity',
                'title'=>$l->title ?? $l->action,
                'details'=>$l->details,
                'created_at'=>$l->created_at,
                'icon'=>'ri-history-line'
            ]);
        }

        foreach ($lead->communications as $c) {
            $items->push((object)[
                'type'=>'comm',
                'title'=>strtoupper($c->channel),
                'details'=>$c->content ?? $c->message,
                'created_at'=>$c->created_at,
                'icon'=>$c->channel == 'email' ? 'ri-mail-line' : ($c->channel=='sms' ? 'ri-message-line' : 'ri-whatsapp-line')
            ]);
        }

        foreach ($lead->WhatsAppLogs as $w) {
            $items->push((object)[
                'type'=>'whatsapp',
                'title'=>'WhatsApp',
                'details'=>$w->message,
                'created_at'=>$w->created_at,
                'icon'=>'ri-whatsapp-line'
            ]);
        }

        foreach ($lead->events as $e) {
            $items->push((object)[
                'type'=>'event',
                'title'=>$e->subject,
                'details'=>$e->description,
                'created_at'=>$e->start_at ?? $e->created_at,
                'icon'=>'ri-calendar-event-line'
            ]);
        }

        foreach ($lead->callLogs as $c) {
            $items->push((object)[
                'type'=>'call',
                'title'=>'Call',
                'details'=>$c->notes ?? $c->summary,
                'created_at'=>$c->created_at,
                'icon'=>'ri-phone-line'
            ]);
        }

        return $items->sortByDesc(fn($i)=>strtotime($i->created_at))->values();
    }

    /* -----------------------------------------------------
     |  SHOW LEAD
     ----------------------------------------------------- */
    public function show($id)
    {
        $lead = $this->loadFullLead($id);
        if (!$lead) abort(404);

        // security
        $userId = Sentinel::check()->id;
        $orgId = $this->getOrgId();
        if ($this->isAdmin() || $this->isManager()) {
            if ($lead->organization_id != $orgId) abort(403);
        } elseif ($this->isOperator()) {
            if ($lead->lead_assign_to != $userId) abort(403);
        }

        // owners list
        $owners = $lead->lead_assign_to
            ? Register::where('id',$lead->lead_assign_to)->get()
            : Register::where('organization',$lead->organization_id)->get();

        $stages = LeadPipelineStage::where('lead_pipeline_id',$lead->pipeline_id)
                    ->orderBy('sort_order','ASC')->get();
	   // ✅ PIPELINES FOR DROPDOWN
		$pipelines = LeadPipeline::where(function ($q) use ($orgId) {
				if ($orgId) {
                $q->where('organization_id', $orgId);
            }
        })
        ->orderBy('id')
        ->get();
			return view('FrontEnd.leads_show', [
			'lead'            => $lead,
			'timeline'        => $this->buildTimeline($lead),
			'stages'          => $stages,
			'owners'          => $owners,
			'campaigns'       => Campaign::all(),
			'sources'         => LeadSource::all(),
			'leadStatuses'    => LeadStatus::all(),
			'leadSubStatuses' => LeadSubStatus::all(),
			'programmes'      => Product::all(),
			'pipelines'       => $pipelines,   // ✅ FIX
		]);

    }

    /* -----------------------------------------------------
     |  ADD NOTE (AJAX)
     ----------------------------------------------------- */
    public function addNote(Request $req, $id)
    {
        $req->validate(['note'=>'required|max:1024']);

        DB::transaction(function() use($req,$id){
            LeadNote::create([
                'lead_id'=>$id,
                'note'=>$req->note,
                'created_by'=>Sentinel::check()->id
            ]);

            LeadActivityLog::create([
                'lead_id'=>$id,
                'action'=>'note_added',
                'title'=>'Note added',
                'details'=>substr($req->note,0,240),
                'actor_id'=>Sentinel::check()->id
            ]);
        });

        // AJAX RESPONSE
        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Note added',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Note added');
    }

    /* -----------------------------------------------------
     |  CHANGE STAGE + STATUS + OWNER (AJAX)
     ----------------------------------------------------- */
    public function changeStage(Request $req, $id)
    {
        $req->validate([
            'stage_id'=>'required',
            'lead_status_id'=>'required',
            'lead_sub_status_id'=>'required',
            'owner_id'=>'nullable',
            'followup_date'=>'nullable|date',
            'stage_note'=>'nullable|max:1024'
        ]);

        DB::transaction(function() use($req,$id){
            $lead = Lead::findOrFail($id);
            $old = $lead->stage_id;

            $lead->stage_id = $req->stage_id;
            $lead->lead_status_id = $req->lead_status_id;
            $lead->lead_sub_status_id = $req->lead_sub_status_id;
            if($req->owner_id) $lead->lead_assign_to = $req->owner_id;
            if($req->followup_date) $lead->next_followup_at = $req->followup_date;
            $lead->save();

            LeadActivityLog::create([
                'lead_id'=>$id,
                'action'=>'stage_changed',
                'title'=>'Stage/Status changed',
                'details'=>"Stage {$old} → {$req->stage_id}, Status {$req->lead_status_id}",
                'actor_id'=>Sentinel::check()->id
            ]);

            if($req->stage_note){
                LeadNote::create([
                    'lead_id'=>$id,
                    'note'=>$req->stage_note,
                    'created_by'=>Sentinel::check()->id
                ]);
            }
        });

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Stage updated',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Stage updated');
    }

    /* -----------------------------------------------------
     |  ADD EVENT (AJAX)
     ----------------------------------------------------- */
    public function addEvent(Request $req, $id)
    {
        $req->validate([
            'subject'=>'required',
            'start_at'=>'required|date',
            'end_at'=>'nullable|date',
        ]);

        LeadEvent::create([
            'lead_id'=>$id,
            'subject'=>$req->subject,
            'start_at'=>$req->start_at,
            'end_at'=>$req->end_at,
            'owner_id'=>$req->owner_id ?? Sentinel::check()->id,
            'description'=>$req->description
        ]);

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'event_added',
            'title'=>'Event added',
            'details'=>$req->subject,
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Event created',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Event created');
    }

    /* -----------------------------------------------------
     |  COMMUNICATION: EMAIL
     ----------------------------------------------------- */
    public function communicateEmail(Request $req, $id)
    {
        $req->validate([
            'send_to'=>'required',
            'subject'=>'required',
            'email_body'=>'required'
        ]);

        CommunicationLog::create([
            'lead_id'=>$id,
            'channel'=>'email',
            'to'=>$req->send_to,
            'sent_by'=>Sentinel::check()->id,
            'subject'=>$req->subject,
            'content'=>$req->email_body
        ]);

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'email_sent',
            'title'=>'Email sent',
            'details'=>substr($req->email_body,0,250),
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Email Sent',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Email sent');
    }

    /* -----------------------------------------------------
     |  COMMUNICATION: SMS
     ----------------------------------------------------- */
    public function communicateSms(Request $req, $id)
    {
        $req->validate([
            'sms_to'=>'required',
            'sms_body'=>'required'
        ]);

        CommunicationLog::create([
            'lead_id'=>$id,
            'channel'=>'sms',
            'to'=>$req->sms_to,
            'sent_by'=>Sentinel::check()->id,
            'content'=>$req->sms_body
        ]);

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'sms_sent',
            'title'=>'SMS sent',
            'details'=>substr($req->sms_body,0,250),
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'SMS Sent',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','SMS sent');
    }

    /* -----------------------------------------------------
     |  COMMUNICATION: WHATSAPP
     ----------------------------------------------------- */
    public function communicateWhatsapp(Request $req, $id)
    {
        $req->validate([
            'wa_to'=>'required',
            'wa_body'=>'required'
        ]);

        CommunicationLog::create([
            'lead_id'=>$id,
            'channel'=>'whatsapp',
            'to'=>$req->wa_to,
            'sent_by'=>Sentinel::check()->id,
            'content'=>$req->wa_body
        ]);

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'whatsapp_sent',
            'title'=>'WhatsApp sent',
            'details'=>substr($req->wa_body,0,250),
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'WhatsApp Sent',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','WhatsApp sent');
    }

    /* -----------------------------------------------------
     |  REASSIGN LEAD
     ----------------------------------------------------- */
    public function reassign(Request $req, $id)
    {
        $req->validate(['reassign_to'=>'required']);

        $lead = Lead::findOrFail($id);
        $old = $lead->lead_assign_to;
        $lead->lead_assign_to = $req->reassign_to;
        $lead->save();

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'reassigned',
            'title'=>'Owner changed',
            'details'=>"From {$old} To {$req->reassign_to}",
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Owner updated',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Lead reassigned');
    }

    /* -----------------------------------------------------
     |  WHATSAPP REPLY
     ----------------------------------------------------- */
    public function whatsappReply(Request $req, $id)
    {
        $req->validate(['reply'=>'required']);

        WhatsAppLog::create([
            'lead_id'=>$id,
            'message'=>$req->reply,
            'sent_by'=>Sentinel::check()->id
        ]);

        LeadActivityLog::create([
            'lead_id'=>$id,
            'action'=>'whatsapp_reply',
            'title'=>'WhatsApp reply',
            'details'=>substr($req->reply,0,200),
            'actor_id'=>Sentinel::check()->id
        ]);

        if ($req->wantsJson()) {
            $lead = $this->loadFullLead($id);
            return response()->json([
                'status'=>'success',
                'message'=>'Reply saved',
                'lead'=>$lead,
                'timeline'=>$this->buildTimeline($lead)
            ]);
        }

        return back()->with('success','Reply saved');
    }

	public function getEvents(Request $request)
	{
		$lead_id = $request->lead_id;

		if(!$lead_id){
			return response()->json([]);
		}

		$events = LeadEvent::where('lead_id', $lead_id)->get();

		return $events->map(function ($e) {
			return [
				'id'    => $e->id,
				'title' => $e->subject,     // ✔ Correct Field
				'start' => $e->start_at,    // ✔ Correct Field
				'end'   => $e->end_at,      // ✔ Optional but good
			];
		});
	}


   /**
     * Upload form
     */
    public function uploadForm(Request $request)
    {
        return view('FrontEnd.leads.upload');
    }

    /**
     * Handle CSV upload
     */
    public function uploadStore(Request $request)
    {
        $request->validate([
            'csv_file'      => 'required|mimes:csv,txt',
            'primary_key'   => 'required|in:email,mobile',
            'upload_action' => 'required|in:create,update',
        ]);

        $user   = Sentinel::check();
        $orgId  = registeruser($user->id)->organization;

        $primaryKey      = $request->primary_key; // email | mobile
        $uploadAction    = $request->upload_action; // create | update
        $assignSameOwner = $request->assign_same_owner === 'yes';
        $assignTo        = $request->lead_assign_to ?: $user->id;

        $file   = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($file);

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        DB::beginTransaction();

        try {

            while (($row = fgetcsv($file)) !== false) {

                if (count(array_filter($row)) === 0) {
                    continue; // blank row skip
                }

                $data = array_combine($header, $row);

                if (empty($data[$primaryKey])) {
                    $skipped++;
                    continue;
                }

                // Existing lead
                $lead = Lead::where('organization_id', $orgId)
                    ->where($primaryKey, $data[$primaryKey])
                    ->first();

                // ----------------------------
                // CREATE NEW
                // ----------------------------
                if (!$lead && $uploadAction === 'create') {

                    Lead::create([
                        'organization_id' => $orgId,
                        'name'            => $data['name']   ?? null,
                        'email'           => $data['email']  ?? null,
                        'mobile'          => $data['mobile'] ?? null,
                        'lead_source_id'  => $data['lead_source_id'] ?? null,
                        'lead_assign_to'  => $assignTo,
                        'created_by'      => $user->id,
                        'institute_applying_for' => $request->institute_applying_for,
                        'faculty'         => $request->faculty,
                        'created_at'      => now(),
                    ]);

                    $inserted++;
                    continue;
                }

                // ----------------------------
                // UPDATE EXISTING
                // ----------------------------
                if ($lead && $uploadAction === 'update') {

                    $updateData = [
                        'name'   => $data['name']   ?? $lead->name,
                        'email'  => $data['email']  ?? $lead->email,
                        'mobile' => $data['mobile'] ?? $lead->mobile,
                        'faculty'=> $request->faculty ?: $lead->faculty,
                    ];

                    if ($assignSameOwner) {
                        $updateData['lead_assign_to'] = $assignTo;
                    }

                    $lead->update($updateData);
                    $updated++;
                    continue;
                }

                $skipped++;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('Leads.ListView')
            ->with(
                'success',
                "Bulk Upload Complete | Added: {$inserted}, Updated: {$updated}, Skipped: {$skipped}"
            );
    }



	public function updatePipeline(Request $request, $id)
	{
		$lead = Lead::findOrFail($id);

		$lead->pipeline_id = $request->pipeline_id;
		$lead->save();

		return response()->json([
			'status' => 'success',
			'lead' => $lead->load('pipeline')
		]);
	}

	public function updateSource(Request $r, $id)
	{
		$lead = Lead::findOrFail($id);
		$lead->source_id = $r->source_id;
		$lead->save();

		return response()->json([
			'status'=>'success',
			'lead'=>$lead->load('source')
		]);
	}


}
