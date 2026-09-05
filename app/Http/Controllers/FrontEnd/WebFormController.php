<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\WebForm;
use App\Lead;
use App\WebFormAttribute;
use App\LeadSource;
use App\Campaign;
use App\Product;
use App\LeadPipeline;
use App\LeadType;
use App\Institute;
use App\Program;
use Illuminate\Support\Str;
use Validator;
use Sentinel;
 use Illuminate\Support\Facades\Http;
use App\ApplicationFormResponse;
use App\ApplicationForm;
class WebFormController extends Controller
{
    // -------------------------
    // Role helpers
    // -------------------------
    private function getUser()
    {
        return Sentinel::check();
    }

    private function getRole()
    {
        $u = $this->getUser();
        return $u ? $u->roles()->first()->slug : null;
    }

    private function getOrgId()
    {
        $u = $this->getUser();
        $reg = $u ? registeruser($u->id) : null;
        return $reg->organization ?? null;
    }

    private function isRoot()     { return $this->getRole() === 'root'; }
    private function isSadmin()   { return $this->getRole() === 'sadmin'; }
    private function isAdmin()    { return $this->getRole() === 'admin'; }
    private function isManager()  { return $this->getRole() === 'manager'; }
    private function isOperator() { return $this->getRole() === 'operater'; }

    /*-------------------------------------------
        PUBLIC FORM VIEW
    -------------------------------------------*/
    public function publicView($form_id)
    {
        $form = WebForm::where('form_id', $form_id)->firstOrFail();

        // increment views (no timestamps update)
        $form->timestamps = false;
        $form->increment('view_count');

        $allowedInstitutes = collect(json_decode($form->attr_programme_check, true) ?? [])
                        ->map(fn($x) => (int) $x)
                        ->toArray();

        $attributes = WebFormAttribute::where('web_form_id', $form->id)
                        ->orderBy('sort_order')
                        ->get();

        return view('FrontEnd.public.webform', compact('form', 'attributes', 'allowedInstitutes'));
    }

    /*-------------------------------------------
        PUBLIC FORM SUBMIT
    -------------------------------------------*/
public function publicSubmit(Request $request, $form_id)
{
    /*
    -----------------------------
    | 1. Load form & attributes
    -----------------------------
    */
    $form = WebForm::where('form_id', $form_id)->firstOrFail();
    $attributes = WebFormAttribute::where('web_form_id', $form->id)->get();

    $applicationFormId = $form->application_form_id;
    if (!$applicationFormId) {
        throw new \Exception('Application Form not found for this organization.');
    }

    /*
    -----------------------------
    | 2. Dynamic validation
    -----------------------------
    */
    $rules = [];

    foreach ($attributes as $attr) {

        // Skip captcha from validation
        if ($attr->type === 'captcha') {
            continue;
        }

        if ($attr->is_required) {

            // Special handling for terms checkbox
            if ($attr->type === 'terms') {
                $rules[$attr->name] = 'accepted';
            } else {
                $rules[$attr->name] = 'required';
            }
        }
    }

    $request->validate($rules);

    DB::beginTransaction();

    try {

        /*
        -----------------------------
        | 3. Prepare response JSON
        -----------------------------
        */
        $responseData = [];

        foreach ($attributes as $attr) {

            if ($attr->type === 'captcha') {
                continue;
            }

            $responseData[$attr->name] = $request->input($attr->name);
        }

        /*
        -----------------------------
        | 4. Fill percentage
        -----------------------------
        */
        $fillableAttributes = $attributes->where('type', '!=', 'captcha');

        $totalFields = $fillableAttributes->count();

        $filledFields = collect($responseData)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->count();

        $fillPercentage = $totalFields > 0
            ? round(($filledFields / $totalFields) * 100)
            : 0;

        $leadId = null;

        /*
        -----------------------------
        | 5. Create Lead (if enabled)
        -----------------------------
        */
        if ($form->create_lead) {

            $payload = new \Illuminate\Http\Request([
                'organization_id'      => $form->organization_id,
                'created_by'           => 0,
                'name'                 => $request->input('name'),
                'mobile'               => $request->input('mobile'),
                'email'                => $request->input('email'),
                'upload_via'           => 'webform',
                'application_form_id'  => $applicationFormId,
                'lead_marketing_event' => 1,
                'institute_id'         => $request->input('institute'),
                'programme_id'         => $request->input('programme'),
                'faculty_id'         => $request->input('course'),
                'state'                => $request->input('state'),
                'city'                 => $request->input('city'),
                'country'              => $request->input('country', 80),
                'source'               => $form->lead_source_id,
                'campaign'             => $form->lead_campaign_id,
                'medium'               => $form->type_id,
                'source_type'          => 'webform_internal',
            ]);

            $apiController = app(\App\Http\Controllers\Api\LeadApiController::class);
            $response = $apiController->store($payload);

            $data = $response->getData(true);

            if (empty($data['status'])) {
                throw new \Exception($data['message'] ?? 'Lead creation failed.');
            }

            $leadId = $data['lead_id'] ?? null;
        }

        /*
        -----------------------------
        | 6. Save application form response
        -----------------------------
        */
        ApplicationFormResponse::create([
            'lead_form_id'    => $applicationFormId,
            'lead_id'         => $leadId,
            'response_json'   => json_encode($responseData),
            'fill_percentage' => $fillPercentage,
            'email_verified'  => 0,
            'otp_verified'    => 0,
        ]);

        /*
        -----------------------------
        | 7. Increment + Log
        -----------------------------
        */
        $form->increment('submit_count');

        logOrganizationActivity(
            $form->organization_id,
            0,
            'organization',
            $leadId ?? 0,
            'webform_submitted',
            'WebForm submitted successfully',
            [
                'form_id' => $form->form_id
            ]
        );

        DB::commit();

        /*
        -----------------------------
        | 8. Success action
        -----------------------------
        */
        if ($form->submit_success_action === 'redirect') {

            if ($request->has('iframe')) {
                return response("
                    <script>
                        window.top.location.href = '{$form->submit_success_content}';
                    </script>
                ");
            }

            return redirect()->away($form->submit_success_content);
        }

        if ($request->ajax()) {
            return response()->json([
                'status'  => true,
                'message' => $form->submit_success_content,
                'redirect'=> $form->submit_success_action === 'redirect'
                                ? $form->submit_success_content
                                : null
            ]);
        }

        return back()->with('message', $form->submit_success_content);

    } catch (\Throwable $e) {

        DB::rollBack();

        if ($request->ajax()) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }

        return back()->withErrors([
            'error' => $e->getMessage()
        ]);
    }
}






//YE HATA KE UPER API SE KIYA HAIN API SE
public function publicSubmit1(Request $request, $form_id)
{
    /* -----------------------------
     | 1. Captcha validation
     ----------------------------- */
    $request->validate([
        'captcha_input' => 'required'
    ]);
	$leadPassword = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    if (strtoupper($request->captcha_input) !== session('captcha_code')) {
        return back()->withErrors([
            'captcha_input' => 'Captcha not matched! Try again.'
        ]);
    }

    /* -----------------------------
     | 2. Load form & attributes
     ----------------------------- */
    $form = WebForm::where('form_id', $form_id)->firstOrFail();
    $attributes = WebFormAttribute::where('web_form_id', $form->id)->get();

    /* -----------------------------
     | 3. Dynamic validation rules
     ----------------------------- */
    $rules = [];
    foreach ($attributes as $attr) {
        if ($attr->is_required) {
            $rules[$attr->name] = 'required';
        }
    }
    $request->validate($rules);

    DB::beginTransaction();

    try {

        /* -----------------------------
         | 4. Prepare response JSON
         ----------------------------- */
        $responseData = [];
        foreach ($attributes as $attr) {
            $responseData[$attr->name] = $request->input($attr->name);
        }

        /* -----------------------------
         | 5. Fill percentage
         ----------------------------- */
        $totalFields  = count($attributes);
        $filledFields = collect($responseData)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->count();

        $fillPercentage = $totalFields > 0
            ? round(($filledFields / $totalFields) * 100)
            : 0;

        $lead = null;

        /* -----------------------------
         | 6. Lead creation (FINAL STRUCTURE)
         ----------------------------- */
        if ($form->create_lead) {

            $leadData    = [];
            $extraFields = [];

            foreach ($attributes as $attr) {
                $value = $request->input($attr->name);

                if (in_array($attr->name, ['name', 'mobile', 'email'])) {
                    $leadData[$attr->name] = $value;
                } else {
                    $extraFields[$attr->name] = $value;
                }
            }

            $leadData['organization_id'] = $form->organization_id;
            $leadData['created_by']      = 0; // public webform
            $leadData['lead_assign_to']  = null;

            $leadData['institute_id'] = $form->institute_id ?? null;
            $leadData['programme_id'] = $form->programme_id ?? null;
            $leadData['faculty_id']   = null;
			$leadData['password'] = $leadPassword;
            $leadData['lead_source_id']   = $form->lead_source_id ?? null;
            $leadData['lead_campaign_id'] = $form->campaign_id ?? null;
            $leadData['type_id']          = $form->type_id ?? null;

            $leadData['extra_fields_json']      = json_encode($extraFields);
            $leadData['lead_registration_date'] = now();

            $lead = Lead::create($leadData);

            /* -----------------------------
             | 6.1 Lead Activity Log
             ----------------------------- */
            logLeadActivity(
                $form->organization_id,
                $lead->id,
                0,
                'lead_created_from_webform',
                'Lead created via WebForm',
                [
                    'form_id'         => $form->form_id,
                    'fill_percentage' => $fillPercentage
                ]
            );

            /* -----------------------------
             | 6.2 Lead Application
             ----------------------------- */
            LeadApplication::create([
                'lead_id'           => $lead->id,
                'application_form'  => $form->form_id,
                'application_score' => 0,
                'payment_status'    => 'pending'
            ]);

            /* -----------------------------
             | 6.3 Lead Verification
             ----------------------------- */
            LeadVerification::create([
                'lead_id' => $lead->id
            ]);

            /* -----------------------------
             | 6.4 Communication Log
             ----------------------------- */
            LeadCommunicationLog::create([
                'lead_id'   => $lead->id,
                'channel'   => 'webform',
                'direction' => 'inbound',
                'purpose'   => 'WebForm Submission'
            ]);
        }

        /* -----------------------------
         | 7. Save application form response
         ----------------------------- */
        ApplicationFormResponse::create([
            'lead_form_id'    => $form->id,
            'lead_id'         => $lead ? $lead->id : null,
            'response_json'   => json_encode($responseData),
            'fill_percentage' => $fillPercentage,
            'email_verified'  => 0,
            'otp_verified'    => 0,
        ]);

        /* -----------------------------
         | 8. Submit count + org log
         ----------------------------- */
        $form->increment('submit_count');

        logOrganizationActivity(
            $form->organization_id,
            0,
            'organization',
            $lead ? $lead->id : 0,
            'webform_submitted',
            'WebForm submitted successfully',
            [
                'form_id' => $form->form_id
            ]
        );

        DB::commit();

        /* -----------------------------
         | 9. Success action
         ----------------------------- */
        if ($form->submit_success_action === 'redirect') {

            if ($request->has('iframe')) {
                return response("
                    <script>
                        window.top.location.href = '{$form->submit_success_content}';
                    </script>
                ");
            }

            return redirect()->away($form->submit_success_content);
        }

        return back()->with('message', $form->submit_success_content);

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->withErrors([
            'error' => $e->getMessage()
        ]);
    }
}
    /*-------------------------------------------
        INDEX (Admin)
    -------------------------------------------*/
    public function index()
    {
        $user = $this->getUser();
        if (!$user) abort(403);

        $orgId = $this->getOrgId();

        // root / sadmin => all
        if ($this->isRoot() || $this->isSadmin()) {
            $webforms = WebForm::latest()->get();
            $forms = WebForm::all();
        }
        // admin / manager / operator => only their org
        elseif ($this->isAdmin() || $this->isManager() || $this->isOperator()) {
            $webforms = WebForm::where('organization_id', $orgId)->latest()->get();
            $forms = WebForm::where('organization_id', $orgId)->get();
        } else {
            abort(403);
        }

        return view('FrontEnd.webforms.index', compact('webforms','forms'));
    }
public function create()
{
    $user = $this->getUser();
    if (!$user) abort(403);

    if (!($this->isRoot() || $this->isSadmin() || $this->isAdmin())) {
        abort(403, "You are not allowed to create webforms.");
    }

    $orgId = $this->getOrgId();

    $applicationForms = ApplicationForm::where('organization_id', $orgId)->get();
    $leadSources = LeadSource::where('organization_id', $orgId)->get();
    $campaigns   = Campaign::where('organization_id', $orgId)->get();
    $pipelines   = LeadPipeline::where('organization_id', $orgId)->get();
    $leadTypes   = LeadType::where('organization_id', $orgId)->get();
    $products    = Product::where('organization_id', $orgId)->get();
    $institutes  = Institute::where('organization_id', $orgId)->get();

    return view('FrontEnd.webforms.create', compact(
        'applicationForms','leadSources','campaigns','pipelines','leadTypes','products','institutes','orgId'
    ));
}
    /*-------------------------------------------
        CREATE (Form)
    -------------------------------------------*/
 public function store(Request $request)
{
    $user = $this->getUser();
    if (!$user) abort(403);

    if (!($this->isRoot() || $this->isSadmin() || $this->isAdmin())) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Not allowed'
        ], 403);
    }

    $reg      = registeruser($user->id);
    $orgId    = $reg->organization;
    $actorId  = $user->id;

    $allowedTypes = [
        'text','textarea',
        'email','email_otp',
        'number','mobile','mobile_otp',
        'date','time','datetime-local',
        'select','radio','checkbox',
        'country','state','city',
        'institute','programme','course',
        'captcha',
        'terms'
    ];

    $validator = Validator::make($request->all(), [
        'title'                     => 'required|string|max:255',
        'submit_button_label'       => 'required|string|max:255',
        'submit_success_action'     => 'required|in:message,redirect',
        'submit_success_content'    => 'required|string',
        'form_attributes'           => 'required|array|min:3',
        'form_attributes.*.name'    => 'required|string|max:255',
        'form_attributes.*.type'    => 'required|string|in:' . implode(',', $allowedTypes),
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $attributes = collect($request->form_attributes)->map(function ($a, $i) {

        $type = $a['type'];
        $name = strtolower(trim($a['name']));

        if ($type === 'terms') {
            $name = 'terms_and_conditions';
        }

        return [
            'name'          => $name,
            'type'          => $type,
            'placeholder'   => $a['placeholder'] ?? null,
            'is_required'   => !empty($a['required']) ? 1 : 0,
            'is_hidden'     => !empty($a['is_hidden']) ? 1 : 0,
            'sort_order'    => $i,
            'options'       => $a['options'] ?? null,
            'default_value' => $a['default_value'] ?? null,
        ];
    });

    foreach (['name','email','mobile'] as $mf) {
        if (!$attributes->contains(fn($x)=> $x['name'] == $mf)) {
            return response()->json([
                'status'  => 'error',
                'message' => ucfirst($mf) . " field must exist"
            ], 421);
        }
    }

    if ($attributes->where('type','terms')->count() > 1) {
        return response()->json([
            'status' => 'error',
            'message' => 'Only one Terms & Conditions field allowed'
        ], 422);
    }

    DB::beginTransaction();

    try {

        $formUID = 'wf_' . Str::random(8);

        $webform = WebForm::create([
            'form_id'               => $formUID,
            'title'                 => $request->title,
            'description'           => $request->description,
            'submit_button_label'   => $request->submit_button_label,
            'submit_success_action' => $request->submit_success_action,
            'submit_success_content'=> $request->submit_success_content,
            'create_lead'           => $request->create_lead ?? 1,
            'organization_id'       => $orgId,
            'lead_source_id'        => $request->lead_source_id ?? 0,
            'pipeline_id'           => $request->pipeline_id ?? 0,
            'type_id'               => $request->type_id ?? 0,
            'lead_campaign_id'      => $request->lead_campaign_id ?? 0,
            'background_color'      => $request->background_color ?? 0,
            'form_background_color' => $request->form_background_color ?? 0,
            'form_title_color'      => $request->form_title_color ?? 0,
            'form_submit_button_color' => $request->form_submit_button_color ?? 0,
            'attribute_label_color' => $request->attribute_label_color ?? 0,
            'application_form_id'   => $request->application_form_id,
            'view_count'            => 0,
            'submit_count'          => 0,
            'created_by'            => $actorId,
            'extra_fields_json'     => json_encode($attributes)
        ]);

        foreach ($attributes as $index => $attr) {
            WebFormAttribute::create([
                'form_id'       => $formUID,
                'web_form_id'   => $webform->id,
                'name'          => $attr['name'],
                'placeholder'   => $attr['placeholder'],
                'is_required'   => $attr['is_required'],
                'is_hidden'     => $attr['is_hidden'],
                'sort_order'    => $index,
                'type'          => $attr['type'],
                'options'       => $attr['options'],
                'default_value' => $attr['default_value'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CLEAR CAPTCHA SESSION
        |--------------------------------------------------------------------------
        */
        session()->forget('captcha_code');

        /*
        |--------------------------------------------------------------------------
        | LOGGING
        |--------------------------------------------------------------------------
        */
        $logData = [
            'form_id'   => $formUID,
            'title'     => $request->title,
            'created_by'=> $actorId
        ];

        logOrganizationActivity(
            $orgId,
            $actorId,
            'organization',
            $orgId,
            'Web Form Created',
            "WebForm '{$formUID}' created",
            $logData
        );

        logUserActivity(
            $orgId,
            $actorId,
            'WebForm',
            $webform->id,
            'create_WebForm',
            "User created a new WebForm '{$request->title}'",
            $logData
        );

        DB::commit();

        return response()->json([
            'status'  => 'success',
            'message' => 'Webform Created Successfully'
        ]);

    } catch (\Exception $e) {

        DB::rollback();

        return response()->json([
            'status'=>'error',
            'message'=>$e->getMessage()
        ], 500);
    }
}
    /*-------------------------------------------
        NEW API: GET PROGRAMMES BY INSTITUTE IDs
    -------------------------------------------*/
 /*   public function programmes(Request $r)
    {
        $ids = explode(',', $r->institute_ids);

        return Program::whereIn('institute_id', $ids)
                ->where('status',1)
                ->select('id','name')
                ->get();
    }

    /*-------------------------------------------
        NEW API: GET COURSES BY PROGRAMME ID
    -------------------------------------------*/
  /*  public function courses(Request $r)
    {
        $pids = explode(',', $r->programme_ids);

        return Product::whereIn('program_id', $pids)
                ->where('status',1)
                ->select('id','name')
                ->get();
    }
*/
    /*-------------------------------------------
        EDIT (Form)
    -------------------------------------------*/
    public function edit($id)
    {
        $user = $this->getUser();
        if (!$user) abort(403);

        $webform = WebForm::with('attributes')->findOrFail($id);
        $orgId = $this->getOrgId();

        // permission: root/sadmin allowed, admin/manager allowed only for their org
        if ($this->isRoot() || $this->isSadmin()) {
            // ok
        } elseif ($this->isAdmin() || $this->isManager()) {
            if ($webform->organization_id != $orgId) abort(403);
        } else {
            abort(403);
        }
 
        $institutes = Institute::where('organization_id', $orgId)->where('status', 1)->get();
        $leadSources = LeadSource::where('organization_id', $orgId)->get();
        $campaigns   = Campaign::where('organization_id', $orgId)->get();
        $pipelines   = LeadPipeline::where('organization_id', $orgId)->get();
        $leadTypes   = LeadType::where('organization_id', $orgId)->get();
        $products    = Product::where('organization_id', $orgId)->get();
        $applicationForms    = ApplicationForm::where('organization_id', $orgId)->get();

        return view('FrontEnd.webforms.edit', compact(
          'applicationForms',  'webform','orgId','leadSources','campaigns','pipelines','leadTypes','products','institutes'
        ));
    }

    /*-------------------------------------------
        UPDATE
    -------------------------------------------*/
   public function update(Request $request, $id)
{
    $webform = WebForm::findOrFail($id);
    $user = $this->getUser();
    if (!$user) abort(403);

    $orgId = $this->getOrgId();
    $actorId = $user->id;

    // Permission check
    if ($this->isRoot() || $this->isSadmin()) {
        // ok
    } elseif ($this->isAdmin() || $this->isManager()) {
        if ($webform->organization_id != $orgId) abort(403);
    } else {
        return response()->json(['status'=>'error','message'=>'Not allowed'],403);
    }

    /*
    |--------------------------------------------------------------------------
    | Allowed Field Types
    |--------------------------------------------------------------------------
    */
    $allowedTypes = [
        'text','textarea',
        'email','email_otp',
        'number','mobile','mobile_otp',
        'date','time','datetime-local',
        'select','radio','checkbox',
        'country','state','city',
        'institute','programme','course',
        'captcha',
        'terms'
    ];

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    $validator = Validator::make($request->all(), [
        'title'                     => 'required|string|max:255',
        'submit_button_label'       => 'required|string|max:255',
        'submit_success_action'     => 'required|in:message,redirect',
        'submit_success_content'    => 'required|string',
        'form_attributes'           => 'required|array|min:3',
        'form_attributes.*.name'    => 'required|string|max:255',
        'form_attributes.*.type'    => 'required|string|in:' . implode(',', $allowedTypes),
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'=>'error',
            'errors'=>$validator->errors()
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Map Attributes (Safe Mode)
    |--------------------------------------------------------------------------
    */
    $attributes = collect($request->form_attributes)->map(function ($a, $i) {

        $type = $a['type'];
        $name = strtolower(trim($a['name']));

        // 🔒 Force system name for terms
        if ($type === 'terms') {
            $name = 'terms_and_conditions';
        }

        return [
            'name'          => $name,
            'type'          => $type,
            'placeholder'   => $a['placeholder'] ?? null,
            'is_required'   => !empty($a['required']) ? 1 : 0,
            'is_hidden'     => !empty($a['is_hidden']) ? 1 : 0,
            'sort_order'    => $i,
            'options'       => $a['options'] ?? null,
            'default_value' => $a['default_value'] ?? null,
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Mandatory Fields Check
    |--------------------------------------------------------------------------
    */
    foreach (['name','email','mobile'] as $mf) {
        if (!$attributes->contains(fn($x)=> strtolower($x['name']) == $mf)) {
            return response()->json([
                'status'=>'error',
                'message'=> ucfirst($mf) . " field must exist"
            ], 421);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Only One Terms Field Allowed
    |--------------------------------------------------------------------------
    */
    if ($attributes->where('type','terms')->count() > 1) {
        return response()->json([
            'status' => 'error',
            'message' => 'Only one Terms & Conditions field allowed'
        ], 422);
    }

    /*
    |--------------------------------------------------------------------------
    | Programme Check Handling
    |--------------------------------------------------------------------------
    */
    $selectedInstitutes = $request->attr_programme_check ?? [];
    if (!is_array($selectedInstitutes)) {
        $selectedInstitutes = [$selectedInstitutes];
    }

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Update Webform
        |--------------------------------------------------------------------------
        */
        $webform->update([
            'title'                     => $request->title,
            'description'               => $request->description,
            'submit_button_label'       => $request->submit_button_label,
            'submit_success_action'     => $request->submit_success_action,
            'submit_success_content'    => $request->submit_success_content,
            'create_lead'               => $request->create_lead ?? 1,
            'lead_source_id'            => $request->lead_source_id ?? 0,
            'pipeline_id'               => $request->pipeline_id ?? 0,
            'type_id'                   => $request->type_id ?? 0,
            'lead_campaign_id'          => $request->lead_campaign_id ?? 0,
            'background_color'          => $request->background_color,
            'form_background_color'     => $request->form_background_color,
            'form_title_color'          => $request->form_title_color,
            'form_submit_button_color'  => $request->form_submit_button_color,
            'attribute_label_color'     => $request->attribute_label_color,
            'application_form_id'       => $request->application_form_id,
            'attr_programme_check'      => json_encode($selectedInstitutes),
            'programme_id'              => $request->programme_id ?? 0,
            'course_id'                 => $request->course_id ?? 0,
            'extra_fields_json'         => json_encode($attributes),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Reset Attributes
        |--------------------------------------------------------------------------
        */
        WebFormAttribute::where('web_form_id', $webform->id)->delete();

        foreach ($attributes as $index => $attr) {
            WebFormAttribute::create([
                'form_id'       => $webform->form_id,
                'web_form_id'   => $webform->id,
                'name'          => $attr['name'],
                'placeholder'   => $attr['placeholder'],
                'is_required'   => $attr['is_required'],
                'is_hidden'     => $attr['is_hidden'],
                'sort_order'    => $index,
                'type'          => $attr['type'],
                'options'       => $attr['options'],
                'default_value' => $attr['default_value'],
            ]);
        }

        DB::commit();

        /*
        |--------------------------------------------------------------------------
        | Logging
        |--------------------------------------------------------------------------
        */
        logOrganizationActivity(
            $orgId,
            $actorId,
            'organization',
            $orgId,
            'Web Form Updated',
            "WebForm '{$webform->form_id}' updated",
            ['updated_by' => $actorId]
        );

        logUserActivity(
            $orgId,
            $actorId,
            'WebForm',
            $webform->id,
            'update_WebForm',
            "User updated WebForm '{$webform->title}'",
            ['updated_by' => $actorId]
        );

        return response()->json([
            'status'=>'success',
            'message'=>'Webform Updated Successfully'
        ]);

    } catch (\Exception $e) {

        DB::rollback();

        return response()->json([
            'status'=>'error',
            'message'=>$e->getMessage()
        ], 500);
    }
}

    /*-------------------------------------------
        DELETE
    -------------------------------------------*/
    public function destroy($id)
    {
        $user = $this->getUser();
        if (!$user) abort(403);

        $webform = WebForm::findOrFail($id);
        $orgId = $this->getOrgId();

        // Only root OR admin (on their org) can delete. SADMIN cannot delete
        if ($this->isRoot()) {
            // allowed
        } elseif ($this->isAdmin()) {
            if ($webform->organization_id != $orgId) abort(403);
        } else {
            abort(403, "You are not allowed to delete this webform.");
        }

        WebFormAttribute::where('web_form_id', $webform->id)->delete();
        $webform->delete();

        // logs - keeping your previous style but using consistent log names
        logOrganizationActivity(
            $webform->organization_id,
            $user->id,
            'organization',
            $webform->id,
            'webform_deleted',
            ["form_id" => $webform->form_id]
        );

        logUserActivity(
            $webform->organization_id,
            $user->id,
            'webform',
            $webform->id,
            'webform_deleted',
            ["form_id" => $webform->form_id]
        );

        return response()->json(['status'=>'success','message'=>'Deleted']);
    }

    /*-------------------------------------------
        EMBED CODE
    -------------------------------------------*/
    public function embedCode($id)
    {
        $user = $this->getUser();
        if (!$user) abort(403);

        $form = WebForm::findOrFail($id);

        // permission: root/sadmin can embed any. admin/manager/operator only for their org
        if ($this->isRoot() || $this->isSadmin()) {
            // ok
        } elseif ($this->isAdmin() || $this->isManager() || $this->isOperator()) {
            if ($form->organization_id != $this->getOrgId()) abort(403);
        } else {
            abort(403);
        }

        $publicUrl = route('webform.public', $form->form_id);

        $embedCode = <<<HTML
<!-- Webform Widget -->
<iframe 
    src="{$publicUrl}" 
    width="100%" 
    style="border:0; min-height:500px;" 
    id="webform_iframe_{$form->id}">
</iframe>

<script>
window.addEventListener("message", function(e){
    if (e.data.height) {
        document.getElementById("webform_iframe_{$form->id}").style.height = e.data.height + "px";
    }
});
</script>
HTML;

        return response()->json([
            'status' => 'success',
            'embed_code' => $embedCode
        ]);
    }
}
