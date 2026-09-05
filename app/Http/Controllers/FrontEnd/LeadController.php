<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
// MODELS
use App\IvrLog;
use App\Services\AutomationRuleEngine;
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
use App\Campaign;
use App\MarketingEvent;

use App\Institute;
use App\Program;
use App\Product;
use App\WhatsAppLog;
use App\ApplicationForm;

use App\Country;
use App\State;
use App\City;

use App\User;
use App\Register;
use App\Organization;
use App\LeadNote;
use App\LeadEvent;
use App\CommunicationLog;
use App\LeadSavedFilter;

use App\Coupon;
use App\CouponUsage;
use App\Notification;    
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



		public function prepareLeadDefaults($orgId, $requestData = [])
		{
			// 1️⃣ Default Lead Status
			$defaultStatus = LeadStatus::where(function ($q) use ($orgId) {
					$q->where('organization_id', $orgId)
					  ->orWhereNull('organization_id');
				})
				->orderBy('sort_order')
				->first();

			// 2️⃣ Default Sub Status
			$defaultSubStatus = null;
			if ($defaultStatus) {
				$defaultSubStatus = LeadSubStatus::where('lead_status_id', $defaultStatus->id)
					->orderBy('sort_order')
					->first();
			}

			// 3️⃣ Default Pipeline (ORG + GLOBAL)
			$pipeline = LeadPipeline::where(function ($q) use ($orgId) {
					$q->where('organization_id', $orgId)
					  ->orWhereNull('organization_id');
				})
				->orderBy('id')
				->first();

			// 4️⃣ First Stage
			$stage = null;
			if ($pipeline) {
				$stage = LeadPipelineStage::where('lead_pipeline_id', $pipeline->id)
					->orderBy('sort_order')
					->first();
			}

			// 5️⃣ Default Lead Type (ORG + GLOBAL)
			$type = LeadType::where(function ($q) use ($orgId) {
					$q->where('organization_id', $orgId)
					  ->orWhereNull('organization_id');
				})
				->orderBy('id')
				->first();

			return [
				'lead_status_id'     => $requestData['lead_status_id']     ?? optional($defaultStatus)->id,
				'lead_sub_status_id' => $requestData['lead_sub_status_id'] ?? optional($defaultSubStatus)->id,

				'lead_source_id'     => $requestData['lead_source_id']     ?? null,
				'lead_campaign_id'   => $requestData['lead_campaign_id']   ?? null,

				'pipeline_id'        => $requestData['pipeline_id']        ?? optional($pipeline)->id,
				'stage_id'           => $requestData['stage_id']           ?? optional($stage)->id,
				'type_id'            => $requestData['type_id']            ?? optional($type)->id,

				// user-selected fields (NO defaults)
				'institute_id'       => $requestData['institute_id']       ?? null,
				'programme_id'       => $requestData['programme_id']       ?? null,
				'faculty_id'         => $requestData['faculty_id']         ?? null,
			];
		}

	public function index(Request $request)
{
    $user  = Sentinel::getUser();
    $reg   = registeruser($user->id);
    $orgId = $reg->organization;
$currentInstitute = \Session::get('current_institute');


    // =====================================================
    // ✅ SAVED FILTER APPLY (NEW)
    // =====================================================
    $savedFilter = null;
    $savedFilterId = $request->saved_filter_id ?? null;

    // agar saved_filter_id nahi hai, to default saved filter pick karo
    if (!$savedFilterId) {
        $savedFilter = LeadSavedFilter::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('is_default', 1)
            ->first();
    } else {
        $savedFilter = LeadSavedFilter::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('id', $savedFilterId)
            ->first();
    }

    // Agar saved filter mila, to request ko override kar do
    // (matlab: listing ka saara data wahi se aayega)
    if ($savedFilter) {
        $savedFiltersJson = $savedFilter->filters_json ?? [];

        // request->merge() se aapka existing filter code automatic work karega
        $request->merge($savedFiltersJson);
    }

    // =====================================================
    // BASE QUERY (SAME)
    // =====================================================
    $query = Lead::query()
    ->from('leads as l')
    ->where('l.organization_id', $orgId)

    // Unique by mobile (oldest lead)
    ->whereIn('l.id', function ($q) use ($orgId, $currentInstitute, $request) {

    $q->select(DB::raw('MIN(id)'))
      ->from('leads')
      ->where('organization_id', $orgId);

    // ✅ correct (inner query me $q use karo)
    if ($currentInstitute && !$request->institute_id) {
        $q->where('institute_id', $currentInstitute);
    }

    $q->groupBy('mobile');
})

    // ===============================
    // JOINS
    // ===============================
    ->leftJoin('lead_sources as ls', 'ls.id', '=', 'l.lead_source_id')
    ->leftJoin('campaigns as lc', 'lc.id', '=', 'l.lead_campaign_id')
    ->leftJoin('marketing_events as me', 'me.id', '=', 'l.lead_marketing_event')

    ->leftJoin('pipelines as p', 'p.id', '=', 'l.pipeline_id')
    ->leftJoin('pipeline_stages as s', 's.id', '=', 'l.stage_id')

    ->leftJoin('lead_types as lt', 'lt.id', '=', 'l.type_id')

    ->leftJoin('lead_statuses as lstat', 'lstat.id', '=', 'l.lead_status_id')
    ->leftJoin('lead_sub_statuses as lsub', 'lsub.id', '=', 'l.lead_sub_status_id')

    ->leftJoin('registers as u', 'u.id', '=', 'l.lead_assign_to')
    ->leftJoin('registers as cb', 'cb.id', '=', 'l.created_by')

    ->leftJoin('organizations as o', 'o.id', '=', 'l.organization_id')

    ->leftJoin('institutes as i', 'i.id', '=', 'l.institute_id')
    ->leftJoin('programs as pr', 'pr.id', '=', 'l.programme_id')
    ->leftJoin('products as f', 'f.id', '=', 'l.faculty_id')

    ->leftJoin('application_forms as af', 'af.id', '=', 'l.application_form_id')

    ->leftJoin('countries as c', 'c.id', '=', 'l.country')
    ->leftJoin('states as st', 'st.id', '=', 'l.state')
    ->leftJoin('cities as ct', 'ct.id', '=', 'l.city');

    // =====================================================
    // FILTERS (SAME)
    // =====================================================
    if ($request->date_from && $request->date_to) {
        $query->whereBetween('l.created_at', [
            $request->date_from . ' 00:00:00',
            $request->date_to   . ' 23:59:59'
        ]);
    }

    foreach (['name', 'mobile', 'email'] as $field) {
        if ($request->$field) {
            $query->where("l.$field", 'LIKE', "%{$request->$field}%");
        }
    }

    if ($request->state) {
        $query->where('l.state', $request->state);
    }

    if ($request->city) {
        $query->where('l.city', $request->city);
    }

    foreach ([
        'lead_status_id',
        'lead_sub_status_id',
        'pipeline_id',
        'stage_id',
        'lead_assign_to',
        'lead_campaign_id',
        'lead_source_id',
        'institute_id',
        'programme_id',
        'faculty_id',
        'type_id'
    ] as $field) {
        if ($request->$field) {
            $query->where("l.$field", $request->$field);
        }
    }

    if ($request->score_from || $request->score_to) {
        $query->whereBetween('l.lead_score', [
            $request->score_from ?? 0,
            $request->score_to   ?? 999999
        ]);
    }

    // =====================================================
    // FINAL SELECT (SAME)
    // =====================================================
    $leadsQuery = $query->select([
    'l.*',

    'ls.name as lead_source_name',
    'lc.name as lead_campaign_name',
    'me.name as lead_marketing_event',

    'p.name  as pipeline_name',
    's.name  as stage_name',

    'lt.name as lead_type_name',

    'lstat.name as lead_status_id',
    'lsub.name  as lead_sub_status_id',

    'u.first_name as assigned_user_name',
    'cb.first_name as created_by',

    'o.name as organization_name',

    'i.name  as institute_name',
    'pr.name as programme_name',
    'f.name  as faculty_name',

    'af.title as application_form_id',

    'c.name  as country',
    'st.name as state_name',
    'ct.name as city_name',
]);
    // =====================================================
    // ✅ SORTING (NEW - Saved Filter Override)
    // =====================================================
    // Default sorting (aapka existing)
    $sortBy = 'l.lead_weight_count';
    $sortDir = 'DESC';

    $sortBy2 = 'l.id';
    $sortDir2 = 'DESC';

    // Agar saved filter me sort_json hai
    if ($savedFilter && !empty($savedFilter->sort_json)) {

        // example: { "by": "created_at", "dir": "desc" }
        $by  = $savedFilter->sort_json['by'] ?? null;
        $dir = strtolower($savedFilter->sort_json['dir'] ?? 'desc');

        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'desc';
        }

        // allowed sorting columns (security)
        $allowed = [
            'id',
            'created_at',
            'lead_weight_count',
            'name',
            'mobile',
            'lead_score'
        ];

        if ($by && in_array($by, $allowed)) {
            $leadsQuery->orderBy("l.$by", strtoupper($dir));
        } else {
            $leadsQuery->orderBy($sortBy, $sortDir);
            $leadsQuery->orderBy($sortBy2, $sortDir2);
        }

    } else {
        // default sorting (same)
        $leadsQuery->orderBy($sortBy, $sortDir);
        $leadsQuery->orderBy($sortBy2, $sortDir2);
    }

    $leads = $leadsQuery
        ->paginate(30)
        ->appends($request->query());

    // =====================================================
    // MASK MOBILE & EMAIL (SAME)
    // =====================================================
    $leads->getCollection()->transform(function ($lead) {

        if (!canViewField('view_mobile')) {
            $lead->mobile = maskMobile($lead->mobile);
        }

        if (!canViewField('view_email')) {
            $lead->email = maskEmail($lead->email);
        }

        return $lead;
    });

    // =====================================================
    // DROPDOWNS (SAME)
    // =====================================================
    $pipelines = LeadPipeline::where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->orWhereNull('organization_id');
        })->get();

    $stages = LeadPipelineStage::all();

    $types = LeadType::where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->orWhereNull('organization_id');
        })->get();

    $institutes = Institute::where('organization_id', $orgId)->get();
    $programs   = Program::all();
    $products   = Product::all();

    $leadStatuses = LeadStatus::where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->orWhereNull('organization_id');
        })->get();

    $leadSubStatuses = LeadSubStatus::all();

    $countries   = Country::all();
    $states      = State::all();
    $cities      = City::all();
    $teamMembers = Register::where('organization', $orgId)->get();
    $campaigns   = Campaign::where('organization_id', $orgId)->get();
    $sources     = LeadSource::where('organization_id', $orgId)->get();
    $applicationForms = ApplicationForm::where('organization_id', $orgId)->get();
    $organizations = Organization::all();
    $events     = MarketingEvent::where('organization_id', $orgId)->get();

    // =====================================================
    // ✅ SAVED FILTERS LIST (NEW) - dropdown ke liye
    // =====================================================
    $savedFilters = LeadSavedFilter::where('organization_id', $orgId)
        ->where('user_id', $user->id)
        ->orderByDesc('is_default')
        ->orderBy('name')
        ->get();
	$columnSetting = \App\LeadColumnSetting::where('organization_id', $orgId)
		->where('user_id', $user->id)
		->first();

	$selectedColumns = $columnSetting->columns_json ?? [
		'name','mobile','email','programme_id','lead_assign_to','created_at'
	];

    return view('FrontEnd.leads_index', compact(
        'leads',
        'pipelines', 'stages', 'types',
        'institutes', 'programs', 'products',
        'leadStatuses', 'leadSubStatuses',
        'teamMembers',
        'campaigns', 'countries', 'states', 'cities',
        'sources','events','applicationForms',
        'organizations',

        // NEW
        'savedFilters',
        'savedFilter','selectedColumns'
    ));
}








public function testLeadApi()
{
        return view('FrontEnd.test-lead-api');
}
public function quickStore(Request $req)
{
    $orgId     = $req->organization_id;
    $createdBy = $req->created_by;

    if (!$orgId || !$createdBy) {
        return response()->json([
            'status' => false,
            'message' => 'Organization and Created By required.'
        ]);
    }

    $req->merge([
        'organization_id' => $orgId,
        'created_by'      => $createdBy,
        'source_type'     => 'manual_quick_store',
        'upload_via'      => $req->upload_via ?? 'mobile',
    ]);

    try {

        $apiController = app(\App\Http\Controllers\Api\LeadApiController::class);
        $response = $apiController->store($req);
        $data = $response->getData(true);

        if (empty($data['status'])) {
            return response()->json([
                'status'  => false,
                'message' => $data['message'] ?? 'Lead creation failed.'
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Lead Added Successfully',
            'lead_id' => $data['lead_id']
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}

	public function quickStoreold(Request $req)
{
    $user = Sentinel::getUser();
    if (!$user) {
        abort(401, 'Unauthenticated');
    }

    $orgId = registeruser($user->id)->organization;

    /* ----------------------------------
     | VALIDATION
     ---------------------------------- */
    $rules = [
        'name'   => 'required|string|max:150',
        'mobile' => 'required|string|max:20',
    ];

    if ($req->upload_via === 'email') {
        $rules['email'] = 'required|email';
    }

    $req->validate($rules);

   
	$exists = Lead::where('organization_id', $orgId)
		->where('mobile', $req->mobile)
		->exists();

	if ($exists) {
		return back()->with('error', 'This mobile number already exists in leads.');
	}


 /* ----------------------------------
     | DEFAULTS (status / pipeline / stage / type)
     ---------------------------------- */
    $defaults = $this->prepareLeadDefaults($orgId, $req->all());
    $leadPassword = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    /* ----------------------------------
     | CREATE LEAD (leads table)
     ---------------------------------- */
    $lead = new Lead();

    $lead->organization_id = $orgId;
    $lead->created_by      = $user->id;

    // basic
    $lead->name   = $req->name;
    $lead->mobile = $req->mobile;
    $lead->email  = ($req->upload_via === 'email') ? $req->email : null;

    // location (DIRECT COLUMNS)
    $lead->state   = $req->state ?? null;
    $lead->city    = $req->city ?? null;
    $lead->country = '80';

    // assignment
    $lead->lead_assign_to = $req->lead_assign_to ?? $user->id;

    // normalized master IDs
    $lead->institute_id = $req->institute_id ?? null;
    $lead->programme_id = $req->programme_id ?? null;
    $lead->faculty_id   = $req->faculty_id ?? null;
    $lead->password   = $leadPassword;

    // source / campaign / medium(type)
    $lead->lead_source_id   = $req->source ?? null;
    $lead->lead_campaign_id = $req->campaign ?? null;
    $lead->type_id          = $req->medium ?? null;
	$lead->application_form_id    = $req->application_form_id ?? null;
    // marketing event
    $lead->lead_marketing_event = $req->event_id ?? null;

    // status / pipeline / stage
    $lead->fill($defaults);

    // optional meta (ONLY non-relational data)
    $lead->extra_fields_json = json_encode([
        'state_id'       => $req->state,
        'city_id'        => $req->city,
        'qualification'  => $req->qualification,
        'faculty'        => $req->faculty,
        'upload_via'     => $req->upload_via,
    ]);

    $lead->lead_registration_date = now();

    $lead->save();
	if ($req->application_form_id) {

		\App\ApplicationFormResponse::firstOrCreate(
			[
				'lead_form_id' => $req->application_form_id,
				'lead_id'      => $lead->id,
			],
			[
				'organization_id' => $orgId,
				'assigned_to'     => $lead->lead_assign_to ?? $user->id,
				'current_step'    => 1,
				'fill_percentage' => 0,
				'payment_status'  => 'pending',
				'status'          => 'draft',
				'response_json'   => json_encode([]),
				'created_at'      => now(),
				'updated_at'      => now(),
			]
		);
	}
	//trig
	AutomationRuleEngine::run('lead.created', $lead->id, $lead->organization_id);
		
	
    /* ----------------------------------
     | APPLICATION (lead_applications table)
     ---------------------------------- */
    if ($req->application_form_id) {
        LeadApplication::create([
            'lead_id'             => $lead->id,
            'application_form' => $req->application_form_id,
            'application_score'    => 0,
            'payment_status'      => 'pending',
            'created_at'          => now(),
        ]);
    }

    /* ----------------------------------
     | VERIFICATION (lead_verifications)
     ---------------------------------- */
    LeadVerification::create([
        'lead_id' => $lead->id,
    ]);


	$channel = match ($req->upload_via) {
		'email'    => 'email',
		'whatsapp' => 'whatsapp',
		default    => 'mobile',
	};
$logmessage = "New Lead Created by ".$channel." on ".now()->format('d M Y, h:i A');


	 LeadCommunicationLog::create([
		'lead_id'    => $lead->id,
		'channel'    => $channel,
		'direction'  => 'inbound',
		'purpose'    => $logmessage,	
		'created_at' => now(),
	]);

 


    /* ----------------------------------
     | SOURCE COUNT INCREMENT
     ---------------------------------- */
    if ($lead->lead_source_id) {
    LeadSource::where('id', $lead->lead_source_id)
        ->where('organization_id', $orgId)
        ->where('status', 1)
        ->increment('lead_count');
		}
	   /* ----------------------------------
     | LEAD ACTIVITY LOG  ✅ (FIXED)
     ---------------------------------- */
    logLeadActivity(
        $orgId,
        $lead->id,
        $user->id,
        'lead_created',
        "{$user->first_name} created a new lead",
        [
            'lead_name'   => $lead->name,
            'mobile'      => $lead->mobile,
            'created_via' => $channel,
            'created_at'  => now()->toDateTimeString(),
        ]
    );

    /* ----------------------------------
     | ORG + USER LOGS (AS IS)
     ---------------------------------- */
    logOrganizationActivity(
        $orgId,
        $user->id,
        'lead',
        $lead->id,
        'lead_created',
        'New lead created in organization',
        [
            'lead_name' => $lead->name,
            'mobile'    => $lead->mobile,
            'via'       => $channel,
        ]
    );

    logUserActivity(
        $orgId,
        $user->id,
        'lead',
        $lead->id,
        'lead_created',
        'Lead created by user',
        [
            'lead_name' => $lead->name,
            'mobile'    => $lead->mobile,
            'via'       => $channel,
        ]
    );



	/* ----------------------------------
     | RESPONSE
     ---------------------------------- */
    return redirect()
        ->route('Leads.LeadShow', encrypt($lead->id))
        ->with('success', 'Lead Added Successfully');
}



	/* -----------------------------------------------------
	 |  LOAD FULL LEAD WITH ALL FINAL RELATIONS
	 ----------------------------------------------------- */
	private function loadFullLead($id)
{
    return Lead::with([

        // core relations
        'pipeline',
        'stage',
        'leadStatus',
        'leadSubStatus',
        'assignedUser',
        'createdBy',

        // normalized masters
        'institute',
        'programme',   // ✅ FIXED
        'faculty',     // product / course

        // lead sub tables
        'application',
        'verification',
		'communications' => fn($q) => $q->orderBy('created_at', 'desc'),

        'activities' => fn($q) => $q->orderBy('created_at', 'desc'), // ✅ ADD
        

    ])->findOrFail($id);
}


private function buildTimeline(Lead $lead)
{
    $items = collect();

    /*
     |--------------------------------------------------------------------------
     | Communications (Email / SMS / WhatsApp / Call)
     |--------------------------------------------------------------------------
     */
    foreach ($lead->communications as $c) {

        $icon = match ($c->channel) {
            'email'    => 'ri-mail-line',
            'sms'      => 'ri-message-line',
            'whatsapp' => 'ri-whatsapp-line',
            'call'     => 'ri-phone-line',
            default    => 'ri-information-line',
        };

        $items->push((object)[
            'type'       => 'communication',
            'title'      => strtoupper($c->channel) .
                            ($c->direction ? ' ('.ucfirst($c->direction).')' : ''),
            'details'    => $c->purpose,
            'created_at' => $c->created_at,
            'icon'       => $icon,
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | Application Status
     |--------------------------------------------------------------------------
     */
    if ($lead->application) {
        $items->push((object)[
            'type'       => 'application',
            'title'      => 'Application',
            'details'    => 'Form: ' . ($lead->application->application_form ? 'Submitted' : 'Not Submitted')
                            . ' | Payment: ' . ucfirst($lead->application->payment_status ?? 'N/A'),
            'created_at' => $lead->application->created_at,
            'icon'       => 'ri-file-list-3-line',
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | Verification Status
     |--------------------------------------------------------------------------
     */
    if ($lead->verification && $lead->verification->verified_at) {
        $items->push((object)[
            'type'       => 'verification',
            'title'      => 'Verification',
            'details'    => 'SMS: ' . ($lead->verification->sms_verified ? 'Verified' : 'Pending')
                            . ' | Email: ' . ($lead->verification->email_verified ? 'Verified' : 'Pending'),
            'created_at' => $lead->verification->verified_at,
            'icon'       => 'ri-shield-check-line',
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | Lead Activity Logs (Stage change, notes, events etc.)
     |--------------------------------------------------------------------------
     */
    if (class_exists(\App\LeadActivityLog::class)) {

        $activityLogs = \App\LeadActivityLog::where('lead_id', $lead->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($activityLogs as $log) {

            $icon = match ($log->action) {
                'stage_changed'   => 'ri-shuffle-line',
                'lead_reassigned' => 'ri-user-switch-line',
                'note_added'      => 'ri-sticky-note-line',
                'event_added'     => 'ri-calendar-event-line',
                default           => 'ri-history-line',
            };

            $items->push((object)[
                'type'       => 'activity',
                'title'      => ucwords(str_replace('_',' ',$log->action)),
                'details'    => $log->description,
                'created_at' => $log->created_at,
                'icon'       => $icon,
            ]);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Final Sort
     |--------------------------------------------------------------------------
     */
    return $items
        ->filter(fn($i) => !empty($i->created_at))
        ->sortByDesc(fn($i) => strtotime($i->created_at))
        ->values();
}


    /* -----------------------------------------------------
     |  SHOW LEAD
     ----------------------------------------------------- */
public function show($id)
{
    try {
        $id = Crypt::decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }

    // ---------------------------------------
    // LOAD LEAD WITH RELATIONS
    // ---------------------------------------
    $lead = $this->loadFullLead($id);

    if (!$lead) {
        abort(404);
    }

    // extra relations load (lead दुबारा query नहीं करेंगे)
    $lead->load([
        'application.form',
        'applicationResponse',
        'whatsappChats'
    ]);

    // ---------------------------------------
    // SECURITY CHECK
    // ---------------------------------------
    $user = Sentinel::getUser();
    $userId = $user->id;
    $orgId  = $this->getOrgId();

    if ($this->isAdmin() || $this->isManager()) {
        if ($lead->organization_id != $orgId) {
            abort(403);
        }
    } elseif ($this->isOperator()) {
        if ($lead->lead_assign_to != $userId) {
            abort(403);
        }
    }

    // ---------------------------------------
    // OWNERS
    // ---------------------------------------
    $owners = Register::where('organization', $lead->organization_id)->get();

    // ---------------------------------------
    // PIPELINE STAGES
    // ---------------------------------------
    $stages = LeadPipelineStage::where('lead_pipeline_id', $lead->pipeline_id)
        ->orderBy('sort_order', 'ASC')
        ->get();

    // ---------------------------------------
    // PIPELINES
    // ---------------------------------------
    $pipelines = LeadPipeline::where(function ($q) use ($orgId) {
            $q->where('organization_id', $orgId)
              ->orWhereNull('organization_id');
        })
        ->orderBy('id')
        ->get();

    // ---------------------------------------
    // DROPDOWNS
    // ---------------------------------------
    $campaigns = Campaign::where('organization_id', $orgId)->get();
    $sources   = LeadSource::where('organization_id', $orgId)->get();

    $leadStatuses = LeadStatus::where(function ($q) use ($orgId) {
        $q->where('organization_id', $orgId)
          ->orWhereNull('organization_id');
    })->get();

    $leadSubStatuses = LeadSubStatus::where('lead_status_id', $lead->lead_status_id)->get();

    $institutes = Institute::where('organization_id', $orgId)->get();
    $programs   = Program::all();
    $products   = Product::all();

    $applicationForms = ApplicationForm::where('organization_id', $orgId)->get();

    $events = MarketingEvent::where('organization_id', $orgId)->get();

    // ---------------------------------------
    // MASK MOBILE & EMAIL
    // ---------------------------------------
    if (!canViewField('view_mobile')) {
        $lead->mobile = maskMobile($lead->mobile);
    }

    if (!canViewField('view_email')) {
        $lead->email = maskEmail($lead->email);
    }

    // ---------------------------------------
    // DOCUMENTS
    // ---------------------------------------
    $documents = DB::table('lead_documents')
        ->where('lead_id', $lead->id)
        ->where('organization_id', $orgId)
        ->orderBy('id', 'DESC')
        ->get();

    // ---------------------------------------
    // VIEW
    // ---------------------------------------
    return view('FrontEnd.leads_show', [
        'lead'            => $lead,
        'timeline'        => $this->buildTimeline($lead),

        'pipelines'       => $pipelines,
        'stages'          => $stages,

        'owners'          => $owners,

        'campaigns'       => $campaigns,
        'sources'         => $sources,

        'leadStatuses'    => $leadStatuses,
        'leadSubStatuses' => $leadSubStatuses,

        'institutes'      => $institutes,
        'programs'        => $programs,
        'products'        => $products,

        'applicationForms'=> $applicationForms,
        'events'          => $events,

        'whatsappChats'   => $lead->whatsappChats,
        'documents'       => $documents
    ]);
}


   /* -----------------------------------------------------
     |  ADD NOTE (AJAX)
     ----------------------------------------------------- */
	 public function addNote(Request $req, $id)
	{
		$req->validate(['note' => 'required|max:1024']);

		$user  = Sentinel::getUser();
		$orgId = registeruser($user->id)->organization;

		DB::transaction(function () use ($req, $id, $user, $orgId) {

			LeadNote::create([
				'lead_id'    => $id,
				'note'       => $req->note,
				'created_by' => $user->id
			]);

			logLeadActivity(
				$orgId,
				$id,
				$user->id,
				'note_added',
				"{$user->first_name} added a note",
				[
					'note_preview' => substr($req->note, 0, 200),
					'added_at'     => now()->toDateTimeString(),
				]
			);
		});

		if ($req->wantsJson()) {
			$lead = $this->loadFullLead($id);
			return response()->json([
				'status'   => 'success',
				'message'  => 'Note added',
				'lead'     => $lead,
				'timeline' => $this->buildTimeline($lead),
			]);
		}

		return back()->with('success', 'Note added');
	}

    /* -----------------------------------------------------
     |  CHANGE STAGE + STATUS + OWNER (AJAX)
     ----------------------------------------------------- */
 public function changeStage(Request $req, $id)
{
    $req->validate([
        'stage_id'           => 'required',
        'lead_status_id'     => 'required',
        'lead_sub_status_id' => 'required',
        'owner_id'           => 'nullable',
        'followup_date'      => 'nullable|date',
        'stage_note'         => 'nullable|max:1024'
    ]);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    DB::transaction(function () use ($req, $id, $user, $orgId) {

        $lead = Lead::with(['stage','leadStatus','leadSubStatus'])->findOrFail($id);

        /* -------------------------------
         | OLD STATE
         --------------------------------*/
        $oldOwner = $lead->lead_assign_to;

        $old = [
            'stage'      => $lead->stage->name ?? 'N/A',
            'status'     => $lead->leadStatus->name ?? 'N/A',
            'sub_status' => $lead->leadSubStatus->name ?? 'N/A',
            'owner_id'   => $oldOwner,
        ];

        /* -------------------------------
         | UPDATE LEAD
         --------------------------------*/
        $lead->stage_id           = $req->stage_id;
        $lead->lead_status_id     = $req->lead_status_id;
        $lead->lead_sub_status_id = $req->lead_sub_status_id;

        if ($req->followup_date) {
            $lead->next_followup_at = $req->followup_date;
        }

        // owner change (if any)
        if ($req->owner_id && $req->owner_id != $oldOwner) {
            $lead->lead_assign_to = $req->owner_id;
        }

        $lead->save();
        $lead->load(['stage','leadStatus','leadSubStatus']);

		//trig
		if ($oldStatusId != $lead->lead_status_id || $oldSubId != $lead->lead_sub_status_id) {
			AutomationRuleEngine::run('lead.status.changed', $lead->id, $lead->organization_id);
		}	

		
		
        /* -------------------------------
         | NEW STATE
         --------------------------------*/
        $new = [
            'stage'      => $lead->stage->name ?? 'N/A',
            'status'     => $lead->leadStatus->name ?? 'N/A',
            'sub_status' => $lead->leadSubStatus->name ?? 'N/A',
            'owner_id'   => $lead->lead_assign_to,
        ];

        /* -------------------------------
         | LEAD ACTIVITY LOG ✅
         --------------------------------*/
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'stage_changed',
            "{$user->first_name} updated lead stage/status",
            json_encode([
                'from'        => $old,
                'to'          => $new,
                'followup_at' => $req->followup_date,
                'note'        => $req->stage_note,
                'changed_at'  => now()->toDateTimeString(),
            ])
        );

        /* -------------------------------
         | LEAD ASSIGNMENT LOG (IF OWNER CHANGED) ✅
         --------------------------------*/
        if ($req->owner_id && $req->owner_id != $oldOwner) {
            logLeadAssignment(
                $orgId,
                $lead->id,
                $lead->lead_source_id,
                $lead->lead_assign_to,
                $oldOwner,
                'manual',
                null,
                true,
                "{$user->first_name} reassigned lead during stage change"
            );
        }

        /* -------------------------------
         | OPTIONAL NOTE
         --------------------------------*/
        if ($req->stage_note) {
            LeadNote::create([
                'lead_id'    => $lead->id,
                'note'       => $req->stage_note,
                'created_by' => $user->id,
            ]);
        }
    });

    /* -------------------------------
     | RESPONSE
     --------------------------------*/
    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);
        return response()->json([
            'status'   => 'success',
            'message'  => 'Stage updated successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'Stage updated');
}

    /* -----------------------------------------------------
     |  ADD EVENT (AJAX)
     ----------------------------------------------------- */
  public function addEvent(Request $req, $id)
{
    $req->validate([
        'subject'  => 'required|string|max:255',
        'start_at' => 'required|date',
        'end_at'   => 'nullable|date',
		 'event_type' => 'required|in:phone_call,video_call,meeting,lunch',
    ]);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    DB::transaction(function () use ($req, $id, $user, $orgId) {

        /* -------------------------------
         | CREATE EVENT
         --------------------------------*/
        $event = LeadEvent::create([
            'lead_id'     => $id,
            'subject'     => $req->subject,
            'start_at'    => $req->start_at,
            'end_at'      => $req->end_at,
            'event_type'      => $req->event_type,
            'owner_id'    => $req->owner_id ?? $user->id,
            'description' => $req->description,
        ]);

        /* -------------------------------
         | LEAD ACTIVITY LOG ✅
         --------------------------------*/
        logLeadActivity(
            $orgId,
            $id,
            $user->id,
            'event_added',
            "{$user->first_name} added an event",
            json_encode([
                'event_id' => $event->id,
                'subject'  => $req->subject,
                'start_at' => $req->start_at,
                'end_at'   => $req->end_at,
                'owner_id' => $req->owner_id ?? $user->id,
                'created_at' => now()->toDateTimeString(),
            ])
        );
    });

    /* -------------------------------
     | RESPONSE
     --------------------------------*/
    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Event created successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'Event created successfully');
}

  
    /* -----------------------------------------------------
     |  REASSIGN LEAD
     ----------------------------------------------------- */
 public function reassign(Request $req, $id)
{
    $req->validate([
        'reassign_to' => 'required|integer'
    ]);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    DB::transaction(function () use ($req, $id, $user, $orgId) {

        $lead = Lead::findOrFail($id);

        $oldOwner = $lead->lead_assign_to;
        $newOwner = $req->reassign_to;

        /* ----------------------------------
         | UPDATE LEAD OWNER
         ---------------------------------- */
        $lead->lead_assign_to = $newOwner;
        $lead->save();

		//trig
		AutomationRuleEngine::run('lead.assigned', $lead->id, $lead->organization_id);
		
        /* ----------------------------------
         | NOTIFICATION ✅ (NEW OWNER KO)
         ---------------------------------- */
        Notification::create([
            'organization_id' => $orgId,
            'user_id'         => $newOwner,
            'type'            => 'alert',
            'title'           => 'New Lead Assigned',
            'body'            => 'You got a new lead: ' . ($lead->name ?? ''),
            'url'             => route('Leads.LeadShow', Crypt::encrypt($lead->id)),
            'is_read'         => 0,
            'ref_type'        => 'lead',
            'ref_id'          => $lead->id,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        /* ----------------------------------
         | LEAD ASSIGNMENT LOG ✅
         ---------------------------------- */
        logLeadAssignment(
            $orgId,
            $lead->id,
            $lead->lead_source_id,
            $newOwner,
            $oldOwner,
            'manual',
            null,
            true,
            "Lead reassigned manually by {$user->first_name}"
        );

        /* ----------------------------------
         | LEAD ACTIVITY LOG (TIMELINE) ✅
         ---------------------------------- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'lead_reassigned',
            "{$user->first_name} reassigned the lead",
            json_encode([
                'from_owner_id' => $oldOwner,
                'to_owner_id'   => $newOwner,
                'changed_at'    => now()->toDateTimeString(),
                'changed_by'    => $user->first_name,
            ])
        );

        /* ----------------------------------
         | ORG + USER LOGS
         ---------------------------------- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'lead_reassigned',
            'Lead owner reassigned',
            [
                'from' => $oldOwner,
                'to'   => $newOwner,
            ]
        );

        logUserActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'lead_reassigned',
            'Lead reassigned by user',
            [
                'from' => $oldOwner,
                'to'   => $newOwner,
            ]
        );
    });

    /* ----------------------------------
     | RESPONSE
     ---------------------------------- */
    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Owner updated successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'Lead reassigned successfully');
}

    /* -----------------------------------------------------
     |  WHATSAPP REPLY
     ----------------------------------------------------- */
  public function whatsappReply(Request $req, $id)
{
    $req->validate([
        'reply' => 'required|string|max:2000'
    ]);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    DB::transaction(function () use ($req, $id, $user, $orgId) {

        $lead = Lead::findOrFail($id);

        /* ----------------------------------
         | SAVE WHATSAPP MESSAGE
         ---------------------------------- */
        WhatsAppLog::create([
            'lead_id' => $lead->id,
            'message' => $req->reply,
            'sent_by' => $user->id,
            'created_at' => now(),
        ]);
		/* ----------------------------------
         | SAVE COMMUNICATION (WHATSAPP)
         ---------------------------------- */
		$comm = CommunicationLog::create([
			'lead_id' => $lead->id,
			'channel' => 'whatsapp',
			'to'      => $lead->mobile,
			'sent_by' => $user->id,
			'content' => $req->reply,
		]);
	DB::table('whatsapp_logs')->insert([
		    'communication_log_id' => $comm->id,   // ✅ MAIN FIX
			'organization_id'       => $orgId,
		'lead_id'               => $lead->id,
		'mobile'                => $lead->mobile,
		'template_id'           => null,
		'message'               => $req->reply,
		'media_url'             => null,
		'provider'              => null,
		'provider_message_id'   => null,
		'status'                => 'pending',
		'response_json'         => null,
		'sent_by'               => $user->id,
		'created_at'            => now(),
		'updated_at'            => now(),
	]);

        /* ----------------------------------
         | LEAD ACTIVITY LOG (TIMELINE)
         ---------------------------------- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'whatsapp_reply',
            "{$user->first_name} sent WhatsApp reply",
            json_encode([
                'message_preview' => \Illuminate\Support\Str::limit($req->reply, 200),
                'channel'         => 'whatsapp',
                'sent_by'         => $user->first_name,
                'sent_at'         => now()->toDateTimeString(),
            ])
        );

        /* ----------------------------------
         | OPTIONAL: ORG + USER LOGS (GOOD PRACTICE)
         ---------------------------------- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'whatsapp_reply',
            'WhatsApp reply sent to lead',
            [
                'lead_id' => $lead->id,
            ]
        );

        logUserActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'whatsapp_reply',
            'User sent WhatsApp reply',
            [
                'lead_id' => $lead->id,
            ]
        );
    });

    /* ----------------------------------
     | RESPONSE
     ---------------------------------- */
    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'WhatsApp reply sent successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'WhatsApp reply sent successfully');
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
    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    // Users
    $users = Register::where('organization', $orgId)
        ->orderBy('first_name')
        ->get();

    // Institutes (same org)
    $institutes = Institute::where('organization_id', $orgId)
        ->where('status', 1)
        ->orderBy('name')
        ->get();

return view('FrontEnd.leads.upload', compact('users','institutes'));

}
public function downloadSample(Request $request)
{
	$primaryKey = $request->get('primary_key', 'mobile');

	if (!in_array($primaryKey, ['mobile', 'email'])) {
		$primaryKey = 'mobile';
	}
    // CSV columns (only those we want to allow)
    $columns = [
        'name',
        'mobile',
        'alternate_mobile',
        'email',
        'country',
        'state',
        'city',
        'institute_id',
        'programme_id',
        'faculty_id',
        'application_form_id',
        'lead_source_id',
        'lead_campaign_id',
        'lead_marketing_event',
        'pipeline_id',
        'stage_id',
        'type_id',
        'lead_status_id',
        'lead_sub_status_id',
    ];

    // Ensure primary key is included
    if (!in_array($primaryKey, $columns)) {
        array_unshift($columns, $primaryKey);
    }

    // Sample row
    $sampleRow = [
        'Rahul Sharma',       // name
        '9876543210',         // mobile
        '9999999999',         // alternate_mobile
        'rahul@gmail.com',     // email
        'India',              // country
        'Rajasthan',           // state
        'Jaipur',              // city
        '1',                   // institute_id
        '2',                   // programme_id
        '3',                   // faculty_id
        '1',                   // application_form_id
        '1',                   // lead_source_id
        '0',                   // lead_campaign_id
        '0',                   // lead_marketing_event
        '1',                   // pipeline_id
        '1',                   // stage_id
        '1',                   // type_id
        '1',                   // lead_status_id
        '0',                   // lead_sub_status_id
    ];

    $filename = "leads_bulk_sample_{$primaryKey}.csv";

    return response()->streamDownload(function () use ($columns, $sampleRow) {

        $handle = fopen('php://output', 'w');

        // header
        fputcsv($handle, $columns);

        // row
        fputcsv($handle, $sampleRow);

        fclose($handle);

    }, $filename, [
        'Content-Type' => 'text/csv',
    ]);
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

    'institute_id'  => 'nullable|integer',
    'program_id'    => 'nullable|integer',
    'course_id'     => 'nullable|integer',

    'lead_assign_to'=> 'nullable|integer',
    'assign_same_owner' => 'required|in:yes,no',
]);


    $user = Sentinel::getUser();
    $orgId  = registeruser($user->id)->organization;

    $primaryKey      = $request->primary_key;        // email | mobile
    $uploadAction    = $primaryKey;      // create | update
    $assignSameOwner = $request->assign_same_owner;  // yes | no
    $assignTo        = $request->lead_assign_to ?: $user->id;

    // ------------------------------------------
    // 1) Save File in /public/uploads/lead_uploads/
    // ------------------------------------------
    $uploadDir = public_path('uploads/lead_uploads');

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $originalName = $request->file('csv_file')->getClientOriginalName();
    $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $originalName);

    $request->file('csv_file')->move($uploadDir, $safeName);

    $filePath = 'uploads/lead_uploads/' . $safeName;

    // ------------------------------------------
    // 2) Count Rows + Valid Rows (basic)
    // ------------------------------------------
    $fullPath = public_path($filePath);

    $handle = fopen($fullPath, "r");
    $header = fgetcsv($handle);

		// header normalize: lowercase + trim
		$header = array_map(function($h){
			return strtolower(trim($h));
		}, $header);


    $totalRows = 0;
    $validRows = 0;
    $failedRows = 0;

    while (($row = fgetcsv($handle)) !== false) {

        // skip blank row
        if (count(array_filter($row)) === 0) {
            continue;
        }

        $totalRows++;

        $row = array_map(function($v){
			return trim($v);
		}, $row);

		$data = array_combine($header, $row);


        // primary key check
        if (!empty($data[$primaryKey])) {
            $validRows++;
        } else {
            $failedRows++;
        }
    }

    fclose($handle);

    // ------------------------------------------
    // 3) Save in lead_uploads table
    // ------------------------------------------
    $uploadId = DB::table('lead_uploads')->insertGetId([
    'organization_id' => $orgId,
    'user_id'         => $user->id,

    'file_name'       => $originalName,
    'file_path'       => $filePath,

    'total_rows'      => $totalRows,
    'valid_rows'      => $validRows,
    'duplicate_rows'  => 0,
    'failed_rows'     => $failedRows,

    'status'          => 'uploaded',
    'upload_type'     => $uploadAction,

    'institute'       => $request->institute_id,
    'program'         => $request->program_id,
    'course'          => $request->course_id,

    'assign_user'     => $assignTo,
    'assigni'         => ($assignSameOwner == 'yes' ? 1 : 0),

    'created_at'      => now(),
    'updated_at'      => now(),
]);


    return redirect()
        ->back()
        ->with('success', "CSV Uploaded Successfully | Rows: {$totalRows} | Valid: {$validRows} | Failed: {$failedRows}");
}




public function uploadLogs(Request $request)
{
    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    $logs = DB::table('lead_uploads as lu')
        ->leftJoin('institutes as i', 'i.id', '=', 'lu.institute')
        ->leftJoin('programs as p', 'p.id', '=', 'lu.program')
        ->leftJoin('products as c', 'c.id', '=', 'lu.course')

        ->leftJoin('registers as u', 'u.id', '=', 'lu.user_id')        // uploaded by
        ->leftJoin('registers as au', 'au.id', '=', 'lu.assign_user')  // assigned user

        ->where('lu.organization_id', $orgId)
        ->select([
            'lu.*',

            DB::raw('IFNULL(i.name,"-") as institute_name'),
            DB::raw('IFNULL(p.name,"-") as program_name'),
            DB::raw('IFNULL(c.name,"-") as course_name'),

            DB::raw('IFNULL(CONCAT(u.first_name," ",u.last_name),"-") as uploaded_by_name'),
            DB::raw('IFNULL(CONCAT(au.first_name," ",au.last_name),"-") as assigned_to_name'),
        ])
        ->orderBy('lu.id', 'desc')
        ->paginate(20);

    return view('FrontEnd.leads.upload_logs', compact('logs'));
}















public function updatePipeline(Request $request, $id)
{
    $request->validate([
        'pipeline_id' => 'required'
    ]);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    DB::transaction(function () use ($request, $id, $user, $orgId) {

        $lead = Lead::with('pipeline')->findOrFail($id);

        /* ----------------------------------
         | OLD PIPELINE
         ---------------------------------- */
        $oldPipeline = $lead->pipeline->name ?? 'N/A';

        /* ----------------------------------
         | UPDATE PIPELINE
         ---------------------------------- */
        $lead->pipeline_id = $request->pipeline_id;
        $lead->save();

        $lead->load('pipeline');

        $newPipeline = $lead->pipeline->name ?? 'N/A';

        /* ----------------------------------
         | LEAD ACTIVITY LOG (TIMELINE)
         ---------------------------------- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'pipeline_changed',
            "{$user->first_name} changed lead pipeline",
            json_encode([
                'from'       => $oldPipeline,
                'to'         => $newPipeline,
                'changed_by' => $user->first_name,
                'changed_at' => now()->toDateTimeString(),
            ])
        );

        /* ----------------------------------
         | ORG ACTIVITY LOG
         ---------------------------------- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'pipeline_changed',
            'Lead pipeline updated',
            [
                'from' => $oldPipeline,
                'to'   => $newPipeline,
            ]
        );

        /* ----------------------------------
         | USER ACTIVITY LOG
         ---------------------------------- */
        logUserActivity(
            $orgId,
            $user->id,
            'lead',
            $lead->id,
            'pipeline_changed',
            'User changed lead pipeline',
            [
                'from' => $oldPipeline,
                'to'   => $newPipeline,
            ]
        );
    });

    return response()->json([
        'status' => 'success',
        'lead'   => Lead::with('pipeline')->findOrFail($id)
    ]);
}
	public function updateSource(Request $r, $id)
	{
		$r->validate([
			'source_id' => 'required'
		]);

		$user  = Sentinel::getUser();
		$orgId = registeruser($user->id)->organization;

		DB::transaction(function () use ($r, $id, $user, $orgId) {

			$lead = Lead::with('source')->findOrFail($id);
			$oldSource = $lead->source->name ?? 'N/A';

			if ($lead->lead_source_id == $r->source_id) return;

			$lead->lead_source_id = $r->source_id;
			$lead->save();
			$lead->load('source');

			$newSource = $lead->source->name ?? 'N/A';

			logLeadActivity($orgId, $lead->id, $user->id, 'source_changed',
				"{$user->first_name} updated lead source",
				json_encode([
					'from' => $oldSource,
					'to' => $newSource,
					'changed_by' => $user->first_name,
					'changed_at' => now()
				])
			);

			logOrganizationActivity($orgId, $user->id, 'lead', $lead->id, 'source_changed',
				'Lead source updated', ['from'=>$oldSource,'to'=>$newSource]
			);

			logUserActivity($orgId, $user->id, 'lead', $lead->id, 'source_changed',
				'User changed lead source', ['from'=>$oldSource,'to'=>$newSource]
			);
		});

		return response()->json([
			'status'=>'success',
			'lead'=>Lead::with('source')->findOrFail($id)
		]);
	}

  /* Get all tickets of a lead */
	public function tickets($leadId)
	{
		$tickets = DB::table('lead_queries')
			->where('lead_id', $leadId)
			->orderByDesc('created_at')
			->get(['id','subject','category','status']);

		return response()->json($tickets);
	}


    /* Create new ticket from Lead view */
    public function createTicket(Request $request, $leadId)
	{
		$request->validate([
			'subject'=>'required|string|max:255',
			'message'=>'required|string'
		]);

		$user = Sentinel::getUser();
		$orgId = registeruser($user->id)->organization;

		DB::transaction(function() use ($request,$leadId,$user,$orgId){

			$ticketId = DB::table('lead_queries')->insertGetId([
				'lead_id'=>$leadId,
				'subject'=>$request->subject,
				'category'=>$request->category ?? 'general',
				'priority'=>'medium',
				'status'=>'open',
				'created_by'=>$user->id,
				'created_at'=>now(),
				'updated_at'=>now()
			]);

			DB::table('lead_query_messages')->insert([
				'query_id'=>$ticketId,
				'sender_type'=>'admin',
				'sender_id'=>$user->id,
				'message'=>$request->message,
				'created_at'=>now()
			]);

			logLeadActivity($orgId,$leadId,$user->id,'ticket_created',
				"New ticket created",
				json_encode(['subject'=>$request->subject])
			);

			logOrganizationActivity($orgId,$user->id,'lead',$leadId,'ticket_created',
				'Ticket created for lead',['subject'=>$request->subject]
			);

			logUserActivity($orgId,$user->id,'lead',$leadId,'ticket_created',
				'User created ticket',['subject'=>$request->subject]
			);
		});

		return response()->json(['status'=>'success','message'=>'Ticket created']);
	}

    /* Show ticket + messages */
    public function showTicket($id)
	{
		$ticket = DB::table('lead_queries')->where('id',$id)->first();
		if(!$ticket) return response()->json(['error'=>'Ticket not found'],404);

		$messages = DB::table('lead_query_messages')
			->where('query_id',$id)
			->orderBy('created_at')
			->get(['sender_type','message','created_at']);

		return response()->json([
			'ticket'=>$ticket,
			'messages'=>$messages
		]);
	}

    /* Reply on ticket */
	public function replyTicket(Request $request, $id)
	{
		$request->validate([
			'message'=>'required|string|max:1000'
		]);

		$user = Sentinel::getUser();
		$orgId = registeruser($user->id)->organization;

		DB::transaction(function() use ($request,$id,$user,$orgId){

			$ticket = DB::table('lead_queries')->where('id',$id)->first();

			DB::table('lead_query_messages')->insert([
				'query_id'=>$id,
				'sender_type'=>'admin',
				'sender_id'=>$user->id,
				'message'=>$request->message,
				'created_at'=>now()
			]);

			DB::table('lead_queries')->where('id',$id)->update([
				'status'=>'in_progress',
				'updated_at'=>now()
			]);

			logLeadActivity($orgId,$ticket->lead_id,$user->id,'ticket_replied',
				"Reply added on ticket",
				json_encode(['ticket_id'=>$id])
			);

			logOrganizationActivity($orgId,$user->id,'lead',$ticket->lead_id,'ticket_replied',
				'Reply on lead ticket',['ticket_id'=>$id]
			);

			logUserActivity($orgId,$user->id,'lead',$ticket->lead_id,'ticket_replied',
				'User replied on ticket',['ticket_id'=>$id]
			);
		});

		return response()->json(['status'=>'success','message'=>'Reply sent']);
	}

public function json($id)
{
    $lead = Lead::with(['assignedUser','notes','whatsappChats'])->findOrFail($id);

    return response()->json([
        'id' => $lead->id,
        'name' => $lead->name,
        'email' => $lead->email,
        'mobile' => $lead->mobile,
        'stage_id' => $lead->stage_id,
        'lead_status_id' => $lead->lead_status_id,
        'lead_sub_status_id' => $lead->lead_sub_status_id,
        'lead_assign_to' => $lead->lead_assign_to,
        'assigned_user' => optional($lead->assignedUser)->first_name,
        'organization_id' => $lead->organization_id,
        'notes' => $lead->notes,
        'whatsappChats' => $lead->whatsappChats
    ]);
}
   public function getJson($id)
    {
        $lead = Lead::with(['assignedUser'])
            ->findOrFail($id);

        return response()->json([
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'mobile' => $lead->mobile,
            'stage_id' => $lead->stage_id,
            'lead_status_id' => $lead->lead_status_id,
            'lead_assign_to' => $lead->lead_assign_to,
            'assigned_user' => optional($lead->assignedUser)->first_name,
         'lead_sub_status_id' => $lead->lead_sub_status_id,
            'notes' => $lead->notes()->latest()->take(10)->get()->map(function($n){
                return [
                    'note' => $n->note,
                    'created_at' => $n->created_at->format('d M Y H:i')
                ];
            }),

            'whatsappChats' => $lead->whatsappChats()->latest()->take(10)->get()->map(function($c){
                return [
                    'message' => $c->message
                ];
            })
        ]);
    }

public function getUsersForReassign()
{
    $users = User::select('id','first_name','last_name')
                 ->orderBy('first_name')
                 ->get();

    return response()->json($users);
}



	public function destroy($id)
	{
		if (!Sentinel::check()) {
			return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
		}

		// Only root, admin, manager allowed
		if (!($this->isRoot() || $this->isAdmin() || $this->isManager())) {
			return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
		}

		$lead = Lead::where('id', $id)
					->where('organization_id', $this->getOrgId())
					->firstOrFail();

		$lead->delete();

		return response()->json(['status' => true, 'message' => 'Lead deleted successfully']);
	}


	public function bulkAction(Request $request)
	{
		$user = Sentinel::getUser();
		if (!$user) abort(401);

		$orgId  = $this->getOrgId();
		$action = $request->action;
		$ids    = array_filter(explode(',', $request->ids));

		if (empty($ids)) {
			return response()->json(['message' => 'No leads selected'], 400);
		}

		// SECURITY: ensure leads belong to org
		$leads = Lead::where('organization_id', $orgId)
			->whereIn('id', $ids)
			->get();

		switch ($action) {

			case 'download':
				return $this->bulkDownloadLeads($leads);

			case 'communicate':
				return response()->json(['message' => 'Bulk communication coming soon']);

			case 'facebook':
				return response()->json(['message' => 'Sent to Facebook audience']);

			case 'google':
				return response()->json(['message' => 'Sent to Google Ads audience']);

			case 'reassign':
				return response()->json(['message' => 'Reassign handled']);

			case 'stage':
				return response()->json(['message' => 'Stage updated']);

			case 'update':
				return response()->json(['message' => 'Bulk update done']);

			case 'delete':
				return $this->bulkDeleteLeads($leads);

			case 'pipeline':
				return response()->json(['message' => 'Moved to pipeline']);

			default:
				return response()->json(['message' => 'Invalid action'], 400);
		}
	}



  /*
	private function bulkDownloadLeads($leads)
	{
		if (!canViewField('can_download')) abort(403);

		$canMobile = canViewField('view_mobile');
		$canEmail  = canViewField('view_email');

		return response()->stream(function () use ($leads, $canMobile, $canEmail) {

			$f = fopen('php://output', 'w');
			fputcsv($f, ['Name','Mobile','Email','Created']);

			foreach ($leads as $l) {
				fputcsv($f, [
					$l->name,
					$canMobile ? $l->mobile : maskMobile($l->mobile),
					$canEmail  ? $l->email  : maskEmail($l->email),
					$l->created_at->format('d-m-Y')
				]);
			}

			fclose($f);
		}, 200, [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename=leads.csv'
		]);
	}


	private function bulkCommunicate($leads, $request)
	{
		foreach ($leads as $lead) {
			logLeadActivity(
				$this->getOrgId(),
				$lead->id,
				Sentinel::getUser()->id,
				'bulk_communication',
				'Bulk communication triggered'
			);
		}

		return response()->json(['message' => 'Bulk communication queued']);
	}
	private function bulkReassign($leads, $request)
	{
		if (!$request->assign_to) abort(400);

		foreach ($leads as $lead) {
			$old = $lead->lead_assign_to;
			$lead->update(['lead_assign_to' => $request->assign_to]);

			logLeadAssignment(
				$this->getOrgId(),
				$lead->id,
				$lead->lead_source_id,
				$request->assign_to,
				$old,
				'bulk',
				null,
				true,
				'Bulk reassignment'
			);
		}

		return response()->json(['message' => 'Leads reassigned']);
	}
	private function bulkStageChange($leads, $request)
	{
		foreach ($leads as $lead) {
			$lead->update([
				'stage_id' => $request->stage_id,
				'lead_status_id' => $request->lead_status_id,
				'lead_sub_status_id' => $request->lead_sub_status_id
			]);

			logLeadActivity(
				$this->getOrgId(),
				$lead->id,
				Sentinel::getUser()->id,
				'bulk_stage_change',
				'Stage changed via bulk action'
			);
		}

		return response()->json(['message' => 'Stage updated']);
	}
	private function bulkPipelineMove($leads, $request)
	{
		foreach ($leads as $lead) {
			$lead->update(['pipeline_id' => $request->pipeline_id]);
		}

		return response()->json(['message' => 'Pipeline updated']);
	}
	private function bulkUpdate($leads, $request)
	{
		$field = $request->field;
		$value = $request->value;

		foreach ($leads as $lead) {
			$lead->update([$field => $value]);
		}

		return response()->json(['message' => 'Data updated']);
	}
	private function bulkDeleteLeads($leads)
	{
		if (!($this->isAdmin() || $this->isManager())) abort(403);

		foreach ($leads as $lead) {
			$lead->delete();
		}

		return response()->json(['message' => 'Leads deleted']);
	}
	private function bulkAudiencePush($leads, $platform)
	{
		foreach ($leads as $lead) {
			$hashedMobile = hash('sha256', $lead->mobile);
			$hashedEmail  = hash('sha256', $lead->email);

			// API push later
		}

		return response()->json([
			'message' => "Sent to {$platform} audience"
		]);
	}

 */
 
 
   /* -----------------------------------------------------
     |  COMMUNICATION: EMAIL
     ----------------------------------------------------- */
 public function communicateEmail(Request $req, $id)
{
    $req->validate([
        'subject'    => 'required|string|max:255',
        'email_body' => 'required'
    ]);

    $user = Sentinel::getUser();
    if (!$user) {
        return back()->with('error', 'Session expired. Please login again.');
    }

 
	$reg = registeruser($user->id);
 	if (!$reg) {
		return back()->with('error', 'Register profile not found for this user.');
	}

	$orgId = $reg->organization;
	
	
 	if (!$orgId) {
		return back()->with('error', 'Organization not found for this user.');
	}

    $lead = Lead::findOrFail($id);

    // ✅ Always take email from lead (ignore request)
    $toEmail = $lead->email;

    if (!$toEmail) {
        return back()->with('error', 'Lead email not found.');
    }

    DB::transaction(function () use ($req, $id, $user, $orgId, $toEmail) {
         /* ----------------------------------
         | SAVE COMMUNICATION (TIMELINE)
         ---------------------------------- */
        $comm = CommunicationLog::create([
            'organization_id' => $orgId,   // ✅ if you added this column
            'lead_id'         => $id,
            'channel'         => 'email',
            'to'              => $toEmail,
            'sent_by'         => $user->id,
            'subject'         => $req->subject,
            'content'         => $req->email_body,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        /* ----------------------------------
         | SAVE EMAIL QUEUE LOG
         ---------------------------------- */
			 logEmailActivity(
				$orgId,
				$user->id,
				null,
				$toEmail,
				$req->subject,
				$req->email_body,
				'pending',
				null,
				null,
				$user->id,
				$comm->id
			);

        /* ----------------------------------
         | LEAD ACTIVITY LOG ✅
         ---------------------------------- */
        logLeadActivity(
            $orgId,
            $id,
            $user->id,
            'email_queued', // ✅ better name because actual send cron karega
            "{$user->first_name} queued an email for lead",
            json_encode([
                'to'        => $toEmail,
                'subject'   => $req->subject,
                'preview'   => \Illuminate\Support\Str::limit(strip_tags($req->email_body), 200),
                'queued_at' => now()->toDateTimeString(),
                'channel'   => 'email',
            ])
        );
    });

    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Email queued successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'Email queued successfully');
}


    /* -----------------------------------------------------
     |  COMMUNICATION: SMS
	  ----------------------------------------------------- */
 public function communicateSms(Request $req, $id)
{
    $req->validate([
        'sms_body' => 'required|string'
    ]);

    $user = Sentinel::getUser();
    if (!$user) {
        return back()->with('error', 'Session expired. Please login again.');
    }

    $reg = registeruser($user->id);
    if (!$reg) {
        return back()->with('error', 'Register profile not found for this user.');
    }

    $orgId = $reg->organization;
    if (!$orgId) {
        return back()->with('error', 'Organization not found for this user.');
    }

    $lead = Lead::findOrFail($id);

    // ✅ Always take mobile from lead
    $toMobile = $lead->mobile;

    if (!$toMobile) {
        return back()->with('error', 'Lead mobile not found.');
    }

    DB::transaction(function () use ($req, $id, $user, $orgId, $toMobile) {

        // ✅ COMMUNICATION LOG
       $comm = CommunicationLog::create([
            'organization_id' => $orgId,
            'lead_id'         => $id,
            'channel'         => 'sms',
            'to'              => $toMobile,
            'sent_by'         => $user->id,
            'content'         => $req->sms_body,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // ✅ SMS LOG QUEUE
            logSmsActivity(
				$orgId,
				$id,
				$toMobile,
				$req->sms_body,
				null,
				null,
				'pending',
				null,
				$user->id,
				url()->current(),
				$comm->id
			);

        // ✅ ACTIVITY LOG
        logLeadActivity(
            $orgId,
            $id,
            $user->id,
            'sms_queued',
            "{$user->first_name} queued an SMS for lead",
            json_encode([
                'to'        => $toMobile,
                'preview'   => \Illuminate\Support\Str::limit($req->sms_body, 200),
                'queued_at' => now()->toDateTimeString(),
                'channel'   => 'sms',
            ])
        );
    });

    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'SMS queued successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'SMS queued successfully');
}

    /* -----------------------------------------------------
     |  COMMUNICATION: WHATSAPP
     ----------------------------------------------------- */
public function communicateWhatsapp(Request $req, $id)
{
    $req->validate([
        'wa_body' => 'required|string'
    ]);

    $user = Sentinel::getUser();
    if (!$user) {
        return back()->with('error', 'Session expired. Please login again.');
    }

    $reg = registeruser($user->id);
    if (!$reg) {
        return back()->with('error', 'Register profile not found for this user.');
    }

    $orgId = $reg->organization;
    if (!$orgId) {
        return back()->with('error', 'Organization not found for this user.');
    }

    $lead = Lead::findOrFail($id);

    // ✅ Always take mobile from lead
    $toMobile = $lead->mobile;

    if (!$toMobile) {
        return back()->with('error', 'Lead mobile not found.');
    }

    DB::transaction(function () use ($req, $id, $user, $orgId, $toMobile) {

       $comm = CommunicationLog::create([
            'organization_id' => $orgId,
            'lead_id'         => $id,
            'channel'         => 'whatsapp',
            'to'              => $toMobile,
            'sent_by'         => $user->id,
            'content'         => $req->wa_body,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

			logWhatsappActivity(
				$orgId,
				$id,
				$toMobile,
				$req->wa_body,
				null,
				null,
				null,
				null,
				'pending',
				null,
				$user->id,
				$comm->id
			);

        logLeadActivity(
            $orgId,
            $id,
            $user->id,
            'whatsapp_queued',
            "{$user->first_name} queued a WhatsApp message for lead",
            json_encode([
                'to'        => $toMobile,
                'preview'   => \Illuminate\Support\Str::limit($req->wa_body, 200),
                'queued_at' => now()->toDateTimeString(),
                'channel'   => 'whatsapp',
            ])
        );
    });

    if ($req->wantsJson()) {
        $lead = $this->loadFullLead($id);

        return response()->json([
            'status'   => 'success',
            'message'  => 'WhatsApp queued successfully',
            'lead'     => $lead,
            'timeline' => $this->buildTimeline($lead),
        ]);
    }

    return back()->with('success', 'WhatsApp queued successfully');
}

 
 //bulkkkkkkk
 public function bulkGoogle(Request $request)
{
    $request->validate([
        'ids' => 'required|string',
        'audience_name' => 'required|string|max:100'
    ]);

    $ids = explode(',', $request->ids);

    $leads = Lead::whereIn('id', $ids)->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ], 404);
    }

    // SAFE FILE NAME
    $audience = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->audience_name);
    $fileName = $audience.'_'.date('Ymd_His').'.csv';

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma" => "no-cache",
        "Expires" => "0"
    ];

    $columns = [
        'Name',
        'Mobile',
        'Alt Mobile',
        'Email',
        'City',
        'State',
        'Country',
        'Lead Score',
        'Created At'
    ];

    $callback = function() use ($leads, $columns) {

        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leads as $l) {
            fputcsv($file, [
                $l->name,
                $l->mobile,
                $l->alternate_mobile,
                $l->email,
                $l->city,
                $l->state,
                $l->country,
                $l->lead_score,
                $l->created_at
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function bulkFacebook(Request $request)
{
    $request->validate([
        'ids' => 'required|string',
        'audience_name' => 'required|string|max:100'
    ]);

    $ids = explode(',', $request->ids);

    $leads = Lead::whereIn('id', $ids)->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ], 404);
    }

    // SAFE FILE NAME
    $audience = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->audience_name);
    $fileName = $audience.'_facebook_'.date('Ymd_His').'.csv';

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma" => "no-cache",
        "Expires" => "0"
    ];

    $columns = [
        'Email',
        'Phone',
        'First Name',
        'Country'
    ];

    $callback = function() use ($leads, $columns) {

        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($leads as $l) {
            fputcsv($file, [
                $l->email,
                $l->mobile,
                $l->name,
                $l->country
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function bulkDownload(Request $request)
{
    $request->validate([
        'ids'     => 'required|string',
        'columns' => 'nullable'
    ]);

    $ids = array_filter(explode(',', $request->ids));

    // -----------------------------
    // SELECTED COLUMNS
    // -----------------------------
    $selectedColumns = [];

    if ($request->columns) {
        $selectedColumns = json_decode($request->columns, true) ?? [];
    }

    // fallback (agar user ne kuch select nahi kiya)
    if (empty($selectedColumns)) {
        $selectedColumns = [
            'name',
            'mobile',
            'alternate_mobile',
            'email',
            'city',
            'state',
            'country',
            'programme_id',
            'faculty_id',
            'lead_assign_to',
            'lead_score',
            'created_at'
        ];
    }

    // -----------------------------
    // LABEL MAP
    // -----------------------------
    $labelMap = [
        'name'             => 'Name',
        'mobile'           => 'Mobile',
        'alternate_mobile' => 'Alt Mobile',
        'email'            => 'Email',

        'city'             => 'City',
        'state'            => 'State',
        'country'          => 'Country',

        'programme_id'     => 'Program',
        'faculty_id'       => 'Course',

        'lead_assign_to'   => 'Assign To',
        'organization_id'  => 'Organization',

        'lead_score'       => 'Lead Score',
        'created_at'       => 'Create Date',
    ];

    // CSV headers
    $csvHeaders = [];
    foreach ($selectedColumns as $col) {
        $csvHeaders[] = $labelMap[$col] ?? ucfirst(str_replace('_', ' ', $col));
    }

    // -----------------------------
    // DATA QUERY (WITH JOINS)
    // -----------------------------
    $leads = Lead::select(
            'leads.*',

            'cities.name as city_name',
            'states.name as state_name',
            'countries.name as country_name',

            'pr.name as programme_name',
            'f.name as faculty_name',

            'u.first_name as assigned_user_name',
            'o.name as organization_name'
        )
        ->leftJoin('cities', 'cities.id', '=', 'leads.city')
        ->leftJoin('states', 'states.id', '=', 'leads.state')
        ->leftJoin('countries', 'countries.id', '=', 'leads.country')

        ->leftJoin('programs as pr', 'pr.id', '=', 'leads.programme_id')
        ->leftJoin('products as f', 'f.id', '=', 'leads.faculty_id')

        ->leftJoin('registers as u', 'u.id', '=', 'leads.lead_assign_to')
        ->leftJoin('organizations as o', 'o.id', '=', 'leads.organization_id')

        ->whereIn('leads.id', $ids)
        ->orderBy('leads.id', 'DESC')
        ->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ], 404);
    }

    // -----------------------------
    // MASK LIKE INDEX
    // -----------------------------
    $leads->transform(function ($lead) {

        if (!canViewField('view_mobile')) {
            $lead->mobile = maskMobile($lead->mobile);
            $lead->alternate_mobile = maskMobile($lead->alternate_mobile);
        }

        if (!canViewField('view_email')) {
            $lead->email = maskEmail($lead->email);
        }

        return $lead;
    });

    // -----------------------------
    // CSV STREAM
    // -----------------------------
    $fileName = 'leads_' . date('Ymd_His') . '.csv';

    $headers = [
        "Content-Type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma"              => "no-cache",
        "Expires"             => "0"
    ];

    $callback = function () use ($leads, $selectedColumns, $csvHeaders) {

        $file = fopen('php://output', 'w');

        // Header Row
        fputcsv($file, $csvHeaders);

        foreach ($leads as $l) {

            $row = [];

            foreach ($selectedColumns as $col) {

                switch ($col) {

                    // -----------------------------
                    // SPECIAL DISPLAY FIELDS
                    // -----------------------------
                    case 'city':
                        $row[] = $l->city_name ?? '';
                        break;

                    case 'state':
                        $row[] = $l->state_name ?? '';
                        break;

                    case 'country':
                        $row[] = $l->country_name ?? '';
                        break;

                    case 'programme_id':
                        $row[] = $l->programme_name ?? '';
                        break;

                    case 'faculty_id':
                        $row[] = $l->faculty_name ?? '';
                        break;

                    case 'lead_assign_to':
                        $row[] = $l->assigned_user_name ?? '';
                        break;

                    case 'organization_id':
                        $row[] = $l->organization_name ?? '';
                        break;

                    case 'created_at':
                        $row[] = $l->created_at ? $l->created_at->format('d-m-Y') : '';
                        break;

                    // -----------------------------
                    // DEFAULT DIRECT COLUMN
                    // -----------------------------
                    default:
                        $row[] = $l->{$col} ?? '';
                        break;
                }
            }

            fputcsv($file, $row);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|string'
    ]);

    $ids = explode(',', $request->ids);

    $deleted = Lead::whereIn('id', $ids)->delete();

    if ($deleted === 0) {
        return response()->json([
            'status' => false,
            'message' => 'No leads deleted'
        ]);
    }

    return response()->json([
        'status' => true,
        'message' => $deleted . ' leads deleted successfully'
    ]);
}


public function bulkPipelineUpdate(Request $request)
{
    $request->validate([
        'ids' => 'required|string',
        'pipeline_id' => 'required|integer'
    ]);

    $ids = explode(',', $request->ids);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    /* -------- OLD DATA -------- */
    $leads = Lead::with('pipeline')
        ->whereIn('id', $ids)
        ->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ]);
    }

    /* -------- UPDATE -------- */
    $updated = Lead::whereIn('id', $ids)
        ->update([
            'pipeline_id' => $request->pipeline_id,
            'updated_at'  => now()
        ]);

    /* -------- NEW PIPELINE -------- */
    $newPipeline = Pipeline::find($request->pipeline_id);

    /* -------- LOOP LOGGING -------- */
    foreach ($leads as $lead) {

        $oldPipelineId   = $lead->pipeline_id;
        $oldPipelineName = $lead->pipeline->name ?? 'N/A';

        // agar same pipeline hai to skip
        if ($oldPipelineId == $request->pipeline_id) {
            continue;
        }

        $payload = json_encode([
            'from_pipeline' => [
                'id'   => $oldPipelineId,
                'name' => $oldPipelineName
            ],
            'to_pipeline' => [
                'id'   => $newPipeline->id ?? null,
                'name' => $newPipeline->name ?? 'N/A'
            ],
            'mode'       => 'bulk',
            'changed_at' => now()->toDateTimeString()
        ]);

        /* ---- LEAD ACTIVITY ---- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'pipeline_changed_bulk',
            "{$user->first_name} moved lead to new pipeline",
            $payload
        );

        /* ---- ORG ACTIVITY ---- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'bulk_pipeline_update',
            "Bulk pipeline update on lead #{$lead->id}",
            $payload
        );

        /* ---- USER ACTIVITY ---- */
        logUserActivity(
            $user->id,
            'bulk_pipeline_update',
            "Moved lead #{$lead->id} to pipeline",
            $payload
        );
    }

    return response()->json([
        'status' => true,
        'message' => $updated.' leads moved to pipeline successfully'
    ]);
}


public function bulkDataUpdate(Request $request)
{
    $request->validate([
        'ids' => 'required|string'
    ]);

    $ids = explode(',', $request->ids);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    $leads = Lead::whereIn('id', $ids)->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ]);
    }

    $updatedCount = 0;

    foreach ($leads as $lead) {

        $updateData = [];
        $oldData    = [];
        $newData    = [];

        /* ---------- OLD SNAPSHOT ---------- */
        $oldData = [
            'institute_id' => $lead->institute_id,
            'programme_id' => $lead->programme_id,
            'state'        => $lead->state,
            'city'         => $lead->city
        ];

        /* ---------- MAIN FIELDS ---------- */
        if ($request->institute_id) {
            $updateData['institute_id'] = $request->institute_id;
            $newData['institute_id']    = $request->institute_id;
        }

        if ($request->programme_id) {
            $updateData['programme_id'] = $request->programme_id;
            $newData['programme_id']    = $request->programme_id;
        }

        if ($request->course_id) {
            $updateData['product_id'] = $request->course_id;
            $newData['product_id']    = $request->course_id;
        }

        if ($request->state_id) {
            $updateData['state'] = $request->state_id;
            $newData['state']    = $request->state_id;
        }

        if ($request->city_id) {
            $updateData['city'] = $request->city_id;
            $newData['city']    = $request->city_id;
        }

        /* ---------- EXTRA JSON ---------- */
        $extra = json_decode($lead->extra_fields_json, true) ?? [];

        if ($request->state_id) {
            $extra['state_id'] = $request->state_id;
        }

        if ($request->city_id) {
            $extra['city_id'] = $request->city_id;
        }

        $updateData['extra_fields_json'] = json_encode($extra);
        $updateData['updated_at']        = now();

        /* ---------- UPDATE ---------- */
        if (!empty($updateData)) {

            $lead->update($updateData);
            $updatedCount++;

            $payload = json_encode([
                'from' => $oldData,
                'to'   => $newData,
                'mode' => 'bulk',
                'changed_at' => now()->toDateTimeString()
            ]);

            /* ---- LEAD ACTIVITY ---- */
            logLeadActivity(
                $orgId,
                $lead->id,
                $user->id,
                'bulk_data_update',
                "{$user->first_name} bulk updated lead data",
                $payload
            );

            /* ---- ORG ACTIVITY ---- */
            logOrganizationActivity(
                $orgId,
                $user->id,
                'bulk_data_update',
                "Bulk data update on lead #{$lead->id}",
                $payload
            );

            /* ---- USER ACTIVITY ---- */
            logUserActivity(
                $user->id,
                'bulk_data_update',
                "Updated lead #{$lead->id} in bulk",
                $payload
            );
        }
    }

    return response()->json([
        'status'  => true,
        'message' => $updatedCount . ' leads updated successfully'
    ]);
}


public function bulkReassign(Request $request)
{
    $request->validate([
        'ids' => 'required|string',
        'reassign_to' => 'required|integer'
    ]);

    $ids = explode(',', $request->ids);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    /* -------- OLD DATA -------- */
    $leads = Lead::whereIn('id', $ids)->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ]);
    }

    /* -------- UPDATE -------- */
    $updated = Lead::whereIn('id', $ids)
        ->update([
            'lead_assign_to' => $request->reassign_to,
            'updated_at'     => now()
        ]);

    $newOwnerId = $request->reassign_to;

    /* -------- LOOP LOGGING -------- */
    foreach ($leads as $lead) {

        $oldOwner = $lead->lead_assign_to;

        // agar same owner hai to skip
        if ($oldOwner == $newOwnerId) {
            continue;
        }

        $payload = json_encode([
            'from_owner' => $oldOwner,
            'to_owner'   => $newOwnerId,
            'changed_at' => now()->toDateTimeString(),
            'mode'       => 'bulk'
        ]);

        /* ---- LEAD ACTIVITY ---- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'lead_reassigned_bulk',
            "{$user->first_name} bulk reassigned lead",
            $payload
        );

        /* ---- ORG ACTIVITY ---- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'bulk_reassign',
            "Bulk reassigned lead #{$lead->id}",
            $payload
        );

        /* ---- USER ACTIVITY ---- */
        logUserActivity(
            $user->id,
            'bulk_reassign',
            "Reassigned lead #{$lead->id}",
            $payload
        );

        /* ---- ASSIGNMENT LOG (SPECIAL) ---- */
        logLeadAssignment(
            $orgId,
            $lead->id,
            $lead->lead_source_id,
            $newOwnerId,
            $oldOwner,
            'manual_bulk',
            null,
            true,
            "{$user->first_name} bulk reassigned lead"
        );
    }

    return response()->json([
        'status' => true,
        'message' => $updated.' leads reassigned successfully'
    ]);
}


public function bulkStageUpdate(Request $request)
{
    $request->validate([
        'ids' => 'required|string',
        'lead_status_id' => 'required|integer'
    ]);

    $ids = explode(',', $request->ids);

    $user  = Sentinel::getUser();
    $orgId = registeruser($user->id)->organization;

    /* ---------------- OLD DATA ---------------- */
    $leads = Lead::with(['leadStatus','leadSubStatus'])
        ->whereIn('id', $ids)
        ->get();

    if ($leads->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No leads found'
        ]);
    }

    /* ---------------- UPDATE ---------------- */
    $updateData = [
        'lead_status_id' => $request->lead_status_id,
        'updated_at'     => now()
    ];

    if ($request->lead_sub_status_id) {
        $updateData['lead_sub_status_id'] = $request->lead_sub_status_id;
    }

    $updated = Lead::whereIn('id', $ids)->update($updateData);

    /* ---------------- NEW STATUS NAME ---------------- */
    $newStatus = LeadStatus::find($request->lead_status_id);
    $newSub    = $request->lead_sub_status_id
        ? LeadSubStatus::find($request->lead_sub_status_id)
        : null;

    /* ---------------- LOOP LOGGING ---------------- */
    foreach ($leads as $lead) {

        $old = [
            'status'     => $lead->leadStatus->name ?? 'N/A',
            'sub_status' => $lead->leadSubStatus->name ?? 'N/A',
        ];

        $new = [
            'status'     => $newStatus->name ?? 'N/A',
            'sub_status' => $newSub->name ?? 'N/A',
        ];

        $payload = json_encode([
            'from'       => $old,
            'to'         => $new,
            'changed_at' => now()->toDateTimeString(),
            'mode'       => 'bulk'
        ]);

        /* ---------- LEAD ACTIVITY ---------- */
        logLeadActivity(
            $orgId,
            $lead->id,
            $user->id,
            'stage_changed_bulk',
            "{$user->first_name} bulk updated lead status",
            $payload
        );

        /* ---------- ORG ACTIVITY ---------- */
        logOrganizationActivity(
            $orgId,
            $user->id,
            'bulk_stage_update',
            "Bulk stage update on lead #{$lead->id}",
            $payload
        );

        /* ---------- USER ACTIVITY ---------- */
        logUserActivity(
            $user->id,
            'bulk_stage_update',
            "Updated lead #{$lead->id} status in bulk",
            $payload
        );
    }

    return response()->json([
        'status' => true,
        'message' => $updated.' leads stage updated successfully'
    ]);
}
public function bulkCommunicateEmail(Request $req)
{
    $req->validate([
        'ids'        => 'required|string',
        'subject'    => 'required|string|max:255',
        'email_body' => 'required'
    ]);

    $ids = array_filter(array_map('trim', explode(',', $req->ids)));
    $ids = array_unique($ids);

    $user = Sentinel::getUser();
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Session expired. Please login again.'
        ], 401);
    }

    $reg = registeruser($user->id);
    if (!$reg) {
        return response()->json([
            'status' => 'error',
            'message' => 'Register profile not found for this user.'
        ], 422);
    }

    $orgId = $reg->organization;
    if (!$orgId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Organization not found for this user.'
        ], 422);
    }

    $count = 0;

    DB::transaction(function () use ($ids, $req, $user, $orgId, &$count) {

        foreach ($ids as $id) {

            $lead = Lead::find($id);
            if (!$lead || !$lead->email) {
                continue;
            }

            $toEmail = $lead->email;

            $comm = CommunicationLog::create([
                'organization_id' => $orgId,
                'lead_id'         => $id,
                'channel'         => 'email',
                'to'              => $toEmail,
                'sent_by'         => $user->id,
                'subject'         => $req->subject,
                'content'         => $req->email_body,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

				 logEmailActivity(
					$orgId,
					$user->id,
					null,
					$toEmail,
					$req->subject,
					$req->email_body,
					'pending',
					null,
					null,
					$user->id,
					$comm->id
				);

            logLeadActivity(
                $orgId,
                $id,
                $user->id,
                'email_queued_bulk',
                "{$user->first_name} queued bulk email",
                json_encode([
                    'to'        => $toEmail,
                    'subject'   => $req->subject,
                    'channel'   => 'email',
                    'queued_at' => now()->toDateTimeString(),
                ])
            );

            $count++;
        }
    });

    return response()->json([
        'status'  => 'success',
        'message' => $count . ' emails queued successfully'
    ]);
}
public function bulkCommunicateSms(Request $req)
{
    $req->validate([
        'ids'      => 'required|string',
        'sms_body' => 'required|string'
    ]);

    $ids = array_filter(array_map('trim', explode(',', $req->ids)));
    $ids = array_unique($ids);

    $user = Sentinel::getUser();
    if (!$user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Session expired. Please login again.'
        ], 401);
    }

    $reg = registeruser($user->id);
    if (!$reg) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Register profile not found for this user.'
        ], 422);
    }

    $orgId = $reg->organization;
    if (!$orgId) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Organization not found for this user.'
        ], 422);
    }

    $count = 0;

    DB::transaction(function () use ($ids, $req, $user, $orgId, &$count) {

        foreach ($ids as $id) {

            $lead = Lead::find($id);
            if (!$lead || !$lead->mobile) {
                continue;
            }

            $toMobile = $lead->mobile;

            $comm = CommunicationLog::create([
                'organization_id' => $orgId,
                'lead_id'         => $id,
                'channel'         => 'sms',
                'to'              => $toMobile,
                'sent_by'         => $user->id,
                'content'         => $req->sms_body,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            logSmsActivity(
				$orgId,
				$id,
				$toMobile,
				$req->sms_body,
				null,
				null,
				'pending',
				null,
				$user->id,
				url()->current(),
				$comm->id
			);


            logLeadActivity(
                $orgId,
                $id,
                $user->id,
                'sms_queued_bulk',
                "{$user->first_name} queued bulk SMS",
                json_encode([
                    'channel'   => 'sms',
                    'queued_at' => now()->toDateTimeString()
                ])
            );

            $count++;
        }
    });

    return response()->json([
        'status'  => 'success',
        'message' => $count . ' SMS queued successfully'
    ]);
}
public function bulkCommunicateWhatsapp(Request $req)
{
    $req->validate([
        'ids'     => 'required|string',
        'wa_body' => 'required|string'
    ]);

    $ids = array_filter(array_map('trim', explode(',', $req->ids)));
    $ids = array_unique($ids);

    $user = Sentinel::getUser();
    if (!$user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Session expired. Please login again.'
        ], 401);
    }

    $reg = registeruser($user->id);
    if (!$reg) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Register profile not found for this user.'
        ], 422);
    }

    $orgId = $reg->organization;
    if (!$orgId) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Organization not found for this user.'
        ], 422);
    }

    $count = 0;

    DB::transaction(function () use ($ids, $req, $user, $orgId, &$count) {

        foreach ($ids as $id) {

            $lead = Lead::find($id);
            if (!$lead || !$lead->mobile) {
                continue;
            }

            $toMobile = $lead->mobile;

            $comm = CommunicationLog::create([
                'organization_id' => $orgId,
                'lead_id'         => $id,
                'channel'         => 'whatsapp',
                'to'              => $toMobile,
                'sent_by'         => $user->id,
                'content'         => $req->wa_body,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

			logWhatsappActivity(
				$orgId,
				$id,
				$toMobile,
				$req->wa_body,
				null,
				null,
				null,
				null,
				'pending',
				null,
				$user->id,
				$comm->id
			);

            logLeadActivity(
                $orgId,
                $id,
                $user->id,
                'whatsapp_queued_bulk',
                "{$user->first_name} queued bulk WhatsApp",
                json_encode([
                    'channel'   => 'whatsapp',
                    'queued_at' => now()->toDateTimeString()
                ])
            );

            $count++;
        }
    });

    return response()->json([
        'status'  => 'success',
        'message' => $count . ' WhatsApp messages queued successfully'
    ]);
}

	public function callNow(Request $request, $lead_id)
{
    $user = Sentinel::getUser();
    $userId = $user->id;

    $reg   = registeruser($userId);
    $orgId = $reg->organization;

    $lead = Lead::where('organization_id', $orgId)->where('id', $lead_id)->first();

    if (!$lead) {
        return response()->json([
            'status' => 'error',
            'message' => 'Lead not found'
        ], 404);
    }

    // ✅ Student mobile (confirmed)
    $studentMobile = $lead->mobile;

    // ✅ Agent mobile (try multiple)
    $agentMobile =
        $reg->mobile;
       

    if (!$agentMobile) {
        return response()->json([
            'status' => 'error',
            'message' => 'Agent mobile not found. Please update user profile mobile.'
        ], 400);
    }

    // ✅ IVR setting check
    $ivrSetting = \App\IvrSetting::where('organization_id', $orgId)->where('status', 1)->first();

    if (!$ivrSetting) {
        return response()->json([
            'status' => 'error',
            'message' => 'IVR setting not found or inactive.'
        ], 400);
    }

    if (!$ivrSetting->base_url || !$ivrSetting->authcode) {
        return response()->json([
            'status' => 'error',
            'message' => 'IVR base_url or authcode missing in settings.'
        ], 400);
    }

    if (!$ivrSetting->default_deskphone) {
        return response()->json([
            'status' => 'error',
            'message' => 'Default deskphone missing in IVR settings.'
        ], 400);
    }

    // ✅ Call Provider API
    $res = \App\Services\Telephony\Clients\CloudTelephony\ClickToCall::normal(
        $orgId,
        $agentMobile,
        $studentMobile
    );

    // call_id निकालना
    $callId = null;
    if (isset($res['data']['sid_id'])) {
        $callId = $res['data']['sid_id'];
    } elseif (isset($res['data']['call_id'])) {
        $callId = $res['data']['call_id'];
    }

    $logStatus = $res['ok'] ? 'initiated' : 'failed';

    // Save ivr_logs
    $ivrLog = \App\IvrLog::create([
        'organization_id' => $orgId,
        'lead_id' => $lead->id,
        'mobile' => $studentMobile,
        'dtmf' => null,
        'call_id' => $callId,
        'status' => $logStatus,
        'provider' => 'cloudtelephony',
        'response_json' => json_encode($res),
    ]);

    // ORG LOG
    logOrganizationActivity(
        $orgId,
        $userId,
        'ivr_log',
        $ivrLog->id,
        'click2call',
        "Click2Call initiated",
        $ivrLog->toArray()
    );

    // USER LOG
    logUserActivity(
        $orgId,
        $userId,
        'ivr_log',
        $ivrLog->id,
        'click2call',
        "User initiated Click2Call",
        $ivrLog->toArray()
    );

    if (!$res['ok']) {
        return response()->json([
            'status' => 'error',
            'message' => $res['message'] ?? 'Call failed',
            'log_id' => $ivrLog->id
        ], 400);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Call initiated successfully.',
        'call_id' => $callId,
        'log_id' => $ivrLog->id
    ]);
}

public function applyCoupon(Request $req, $leadId)
{
    $user = Sentinel::getUser();
    if (!$user) abort(401);

    $orgId = registeruser($user->id)->organization;

    $req->validate([
        'coupon_code'  => 'required|string|max:50',
        'total_amount' => 'required|numeric|min:0',
    ]);

    $coupon = DB::table('coupons')
        ->where('organization_id', $orgId)
        ->where('code', $req->coupon_code)
        ->where('status', 1)
        ->first();

    if (!$coupon) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid coupon code'
        ]);
    }

    $total = (float)$req->total_amount;

    // min_amount check
    if (!empty($coupon->min_amount) && $total < (float)$coupon->min_amount) {
        return response()->json([
            'status' => false,
            'message' => 'Minimum amount required: ₹' . $coupon->min_amount
        ]);
    }

    $discount = 0;

    // type: percent OR flat
    if ($coupon->type === 'percent') {
        $discount = ($total * (float)$coupon->value) / 100;
    } else {
        $discount = (float)$coupon->value;
    }

    // max_discount cap
    if (!empty($coupon->max_discount) && $discount > (float)$coupon->max_discount) {
        $discount = (float)$coupon->max_discount;
    }

    if ($discount > $total) $discount = $total;

    $final = $total - $discount;

    return response()->json([
        'status' => true,
        'message' => 'Coupon applied successfully',
        'coupon_id' => $coupon->id,
        'discount_amount' => round($discount, 2),
        'final_amount' => round($final, 2),
    ]);
}



public function savePayment(Request $req, $leadId)
{
    $user = Sentinel::getUser();
    if (!$user) abort(401);

    $orgId = registeruser($user->id)->organization;

    $req->validate([
        'payment_method' => 'required|in:online,offline,cash,dd,free',
        'final_amount'   => 'required|numeric|min:0',
        'payment_amount' => 'required|numeric|min:0', // base amount
        'coupon_code'    => 'nullable|string|max:50',
        'coupon_id'      => 'nullable|integer',
    ]);

    $lead = Lead::where('id', $leadId)
        ->where('organization_id', $orgId)
        ->firstOrFail();

    $app = LeadApplication::where('lead_id', $lead->id)->first();

    if (!$app) {
        $app = LeadApplication::create([
            'lead_id' => $lead->id,
            'payment_status' => 'pending',
        ]);
    }

    $orderAmount = (float)$req->payment_amount;
    $finalAmount = (float)$req->final_amount;
    $discountAmount = max(0, $orderAmount - $finalAmount);

    DB::beginTransaction();

    try {

        // 1) Update lead_applications
        $app->payment_status = 'paid';
        $app->payment_method = $req->payment_method;
       // $app->payment_amount = $finalAmount;
      //  $app->coupon_code    = $req->coupon_code;
		$app->application_score = ($app->application_score ?? 0) + 25;
        $app->created_at        = now();
        $app->save();


            // ----------------------------------
            // UPDATE LEAD STAGE -> PAYMENT APPROVED
            // ----------------------------------

            $paymentStage = DB::table('pipeline_stages')
                ->where('lead_pipeline_id', $lead->pipeline_id)
                ->where('code', 'payment approved')
                ->first();

            if ($paymentStage) {
                DB::table('leads')
                    ->where('id', $lead->id)
                    ->update([
                        'stage_id' => $paymentStage->id,
                        'updated_at' => now()
                    ]);
            }



        // 2) Coupon usage log + used_count increment
        if (!empty($req->coupon_code) && $discountAmount > 0) {

            $coupon = DB::table('coupons')
                ->where('organization_id', $orgId)
                ->where('code', $req->coupon_code)
                ->where('status', 1)
                ->first();

            if ($coupon) {

                // Insert coupon_usages
                DB::table('coupon_usages')->insert([
                    'organization_id' => $orgId,
                    'coupon_id'       => $coupon->id,
                    'lead_id'         => $lead->id,
                    'application_id'  => $app->id,
                    'used_by_user_id' => $user->id,
                    'used_at'         => now(),
                    'discount_amount' => $discountAmount,
                    'order_amount'    => $orderAmount,
                    'meta_json'       => json_encode([
                        'payment_method' => $req->payment_method,
                        'coupon_code'    => $req->coupon_code,
                        'final_amount'   => $finalAmount,
                    ]),
                    'created_at'      => now(),
                ]);

                // Increment used_count
                DB::table('coupons')
                    ->where('id', $coupon->id)
                    ->increment('used_count');
            }
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Payment saved successfully'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function uploadDocument(Request $req, $leadId)
{
    $user = Sentinel::getUser();
    if (!$user) abort(401);

    $orgId = registeruser($user->id)->organization;

    $req->validate([
        'document' => 'required|file|max:5120', // 5MB
        'doc_type' => 'nullable|string|max:50',
        'doc_name' => 'nullable|string|max:150',
    ]);

    $lead = Lead::where('id', $leadId)
        ->where('organization_id', $orgId)
        ->firstOrFail();

    $file = $req->file('document');

    $path = $file->store("lead_documents/org_{$orgId}/lead_{$lead->id}", 'public');

    DB::table('lead_documents')->insert([
        'organization_id' => $orgId,
        'lead_id'         => $lead->id,
        'doc_type'        => $req->doc_type,
        'doc_name'        => $req->doc_name ?? $file->getClientOriginalName(),
        'file_path'       => $path,
        'file_ext'        => $file->getClientOriginalExtension(),
        'file_size'       => $file->getSize(),
        'uploaded_by'     => $user->id,
        'status'          => 1,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    return back()->with('success', 'Document uploaded successfully.');
}


public function viewDocument($docId)
{
    $user = Sentinel::getUser();
    if (!$user) abort(401);

    $orgId = registeruser($user->id)->organization;

    $doc = \DB::table('lead_documents')
        ->where('id', $docId)
        ->where('organization_id', $orgId)
        ->first();

    if (!$doc) abort(404);

    return response()->file(storage_path('app/public/' . $doc->file_path));
}
public function deleteDocument($docId)
{
    $user = Sentinel::getUser();
    if (!$user) abort(401);

    $orgId = registeruser($user->id)->organization;

    $doc = \DB::table('lead_documents')
        ->where('id', $docId)
        ->where('organization_id', $orgId)
        ->first();

    if (!$doc) abort(404);

    // delete file
    \Storage::disk('public')->delete($doc->file_path);

    // delete db row
    \DB::table('lead_documents')->where('id', $docId)->delete();

    return back()->with('success', 'Document deleted successfully.');
}

}
