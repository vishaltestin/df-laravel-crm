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
use Illuminate\Support\Str;
use Validator;

class WebFormController extends Controller
{
    /*-------------------------------------------
        PUBLIC FORM VIEW
    -------------------------------------------*/
    public function publicView($form_id)
    {
        $form = WebForm::where('form_id', $form_id)->firstOrFail();

        // view count increase (without updating updated_at)
        $form->timestamps = false;
        $form->increment('view_count');

        $attributes = WebFormAttribute::where('web_form_id', $form->id)
                        ->orderBy('sort_order')
                        ->get();

        return view('FrontEnd.public.webform', compact('form', 'attributes'));
    }

    /*-------------------------------------------
        PUBLIC FORM SUBMIT
    -------------------------------------------*/
public function publicSubmit(Request $request, $form_id)
{
    $form = WebForm::where('form_id', $form_id)->firstOrFail();
    $attributes = WebFormAttribute::where('web_form_id', $form->id)->get();

    /*-------------------------------------------------------
        DYNAMIC VALIDATION
    -------------------------------------------------------*/
    $rules = [];
    foreach ($attributes as $attr) {
        if ($attr->is_required) {
            $rules[$attr->name] = 'required';
        }
    }
    $request->validate($rules);

    $lead = null; // for activity logs later

    /*-------------------------------------------------------
        IF LEAD CREATE ENABLED
    -------------------------------------------------------*/
    if ($form->create_lead) {

        $leadData      = [];
        $extraFields   = [];

        foreach ($attributes as $attr) {

            $value = $request->input($attr->name);

            // CORE FIELDS (go to dedicated DB columns)
            if (in_array($attr->name, ['name','mobile','email'])) {
                $leadData[$attr->name] = $value;
            }
            else {
                // EVERYTHING ELSE → EXTRA JSON
                $extraFields[$attr->name] = $value;
            }
        }

        // CRM Auto Fields
        $leadData['organization_id']        = $form->organization_id;
        $leadData['lead_source_id']         = $form->lead_source_id ?? 0;
        $leadData['lead_registration_date'] = now();
        $leadData['lead_status']            = '0';
        $leadData['lead_score']             = '0';
        $leadData['lead_assign_to']         = '0';
        $leadData['pipeline_id']            = $form->pipeline_id ?? 0;
        $leadData['stage_id']               = $form->stage_id ?? 0;
        $leadData['type_id']                = $form->type_id ?? 0;

        // NEW FIELD: extra fields JSON
        $leadData['extra_fields_json']      = json_encode($extraFields);

        // Create lead
        $lead = Lead::create($leadData);

        $meta_data = ["form_id" => $form->form_id];
        $meta_data1 = json_encode($meta_data);

        /*-------------------------------------------------------
            LEAD ACTIVITY LOG
        -------------------------------------------------------*/
        logLeadActivity(
            $form->organization_id,
            $lead->id,
            0,
            "lead_created_from_webform",
            "New Lead Created Successfully",
            $meta_data1
        );
    }

    /*-------------------------------------------------------
        Increase submit count
    -------------------------------------------------------*/
    $form->increment('submit_count');

    /*-------------------------------------------------------
        ORGANIZATION ACTIVITY LOG
    -------------------------------------------------------*/
    logOrganizationActivity(
        $form->organization_id,
        '0',
        'organization',
        $lead ? $lead->id : 0,
        'webform_submitted',
        "WebForm '" . ($lead ? $lead->id : '') . "' created",
        ["form_id" => $form->form_id]
    );

    /*-------------------------------------------------------
        SUBMIT SUCCESS ACTION
    -------------------------------------------------------*/
    if ($form->submit_success_action === 'redirect') {

        // If request came from iframe
        if ($request->has('iframe')) {
            return response("
                <script>
                    window.top.location.href = '{$form->submit_success_content}';
                </script>
            ");
        }

        // Normal redirect
        return redirect()->away($form->submit_success_content);
    }

    return back()->with('message', $form->submit_success_content);
}

   

    /*-------------------------------------------
        INDEX (LIST)
    -------------------------------------------*/
    public function index()
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;

        $webforms = WebForm::where('organization_id', $orgId)->latest()->get();
        $forms = WebForm::where('organization_id', $orgId)->get();

        return view('FrontEnd.webforms.index', compact('webforms','forms'));
    }

    /*-------------------------------------------
        CREATE
    -------------------------------------------*/
    public function create()
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;

        $leadSources = LeadSource::where('organization_id', $orgId)->get();
        $campaigns   = Campaign::where('organization_id', $orgId)->get();
        $pipelines   = LeadPipeline::where('organization_id', $orgId)->get();
        $leadTypes   = LeadType::where('organization_id', $orgId)->get();
        $products    = Product::where('organization_id', $orgId)->get();

        return view('FrontEnd.webforms.create', compact(
            'leadSources','campaigns','pipelines','leadTypes','products','orgId'
        ));
    }

    /*-------------------------------------------
        STORE WEBFORM
    -------------------------------------------*/
    public function store(Request $request)
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;
         $actorId = $user->id;
        $allowedTypes = [
            'text','textarea','email','number','mobile','date',
            'time','datetime-local','select','radio','checkbox','state','city'
        ];

        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'submit_button_label' => 'required|string|max:255',
            'submit_success_action' => 'required|in:message,redirect',
            'submit_success_content' => 'required|string',
            'form_attributes' => 'required|array|min:3',
            'form_attributes.*.name' => 'required|string|max:255',
            'form_attributes.*.type' => 'required|string|in:' . implode(',', $allowedTypes),
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','errors'=>$validator->errors()], 422);
        }

        // Map attributes
        $attributes = collect($request->form_attributes)
        ->map(fn($a,$i)=>[
            'name' => trim($a['name']),
            'type' => $a['type'],
            'placeholder' => $a['placeholder'] ?? null,
            'is_required' => $a['required'] ?? 0,
            'is_hidden' => $a['is_hidden'] ?? 0,
            'sort_order' => $i,
            'options' => $a['options'] ?? null
        ]);

        // Must fields
        foreach (['name','email','mobile'] as $mf) {
            if (!$attributes->contains(fn($x)=> strtolower($x['name'])===$mf)) {
                return response()->json(['status'=>'error','message'=> ucfirst($mf)." field must exist"],421);
            }
        }

        // Unique form id
        $formUID = 'wf_'.Str::random(8);

        // Create WebForm
        $webform = WebForm::create([
            'form_id' => $formUID,
            'title' => $request->title,
            'description' => $request->description,
            'submit_button_label' => $request->submit_button_label,
            'submit_success_action' => $request->submit_success_action,
            'submit_success_content' => $request->submit_success_content,
            'create_lead' => $request->create_lead ?? 1,
            'organization_id' => $orgId,
            'lead_source_id' => $request->lead_source_id ?? 0,
            'pipeline_id' => $request->pipeline_id ?? 0,
            'type_id' => $request->type_id ?? 0,
            'campaign_id' => $request->lead_campaign_id ?? 0,
            'view_count' => 0,
            'submit_count' => 0,
            'created_by' => $user->id,
        ]);

        // Insert attributes
        foreach ($attributes as $index => $attr) {
            WebFormAttribute::create([
                'form_id'      => $formUID,
                'web_form_id'  => $webform->id,
                'name'         => $attr['name'],
                'placeholder'  => $attr['placeholder'],
                'is_required'  => $attr['is_required'],
                'is_hidden'    => $attr['is_hidden'],
                'sort_order'   => $index,
                'type'   => $attr['type'],
                'options'      => $attr['options'],
            ]);
        }
        $validated['created_by'] = $actorId;

        // 🔥 ORG LOG — organization created
        logOrganizationActivity(
            $orgId,
            $actorId,
            'organization',
            $orgId,
            'Web Form Created',
            "WebForm '{$formUID}' created",
            $validated
        );

        // 🔥 USER LOG — created org
        logUserActivity(
            $orgId,
            $actorId,
            'WebForm',
           $webform->id,
            'create_WebForm',
            "User created a new WebForm '{$request->title}'",
            $validated
        );



 

        return response()->json(['status'=>'success','message'=>'Webform Created Successfully']);
    }

    /*-------------------------------------------
        EDIT VIEW
    -------------------------------------------*/
    public function edit($id)
    {
        $user = \Sentinel::check();
        $reg  = registeruser($user->id);
        $orgId = $reg->organization;

        $webform = WebForm::with('attributes')->findOrFail($id);

        $leadSources = LeadSource::where('organization_id', $orgId)->get();
        $campaigns   = Campaign::where('organization_id', $orgId)->get();
        $pipelines   = LeadPipeline::where('organization_id', $orgId)->get();
        $leadTypes   = LeadType::where('organization_id', $orgId)->get();
        $products    = Product::where('organization_id', $orgId)->get();

        return view('FrontEnd.webforms.edit', compact(
            'webform','orgId','leadSources','campaigns','pipelines','leadTypes','products'
        ));
    }

    /*-------------------------------------------
        UPDATE WEBFORM
    -------------------------------------------*/
    public function update(Request $request, $id)
    {
        $webform = WebForm::findOrFail($id);

        DB::beginTransaction();
        try {
            $webform->update([
                'title' => $request->title,
                'description' => $request->description,
                'submit_button_label' => $request->submit_button_label,
                'submit_success_action' => $request->submit_success_action,
                'submit_success_content' => $request->submit_success_content,
            ]);

            // Delete old
            WebFormAttribute::where('web_form_id', $webform->id)->delete();

            // Add new
            foreach ($request->form_attributes as $index => $a) {
                WebFormAttribute::create([
                    'web_form_id' => $webform->id,
                    'name' => $a['name'],
                    'placeholder' => $a['placeholder'],
                    'is_required' => $a['is_required'],
                    'is_hidden' => $a['is_hidden'],
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            /*-------------------------------------------
                ORG LOG
            -------------------------------------------*/
            addOrgLog(
                $webform->organization_id,
                "webform_updated",
                ["form_id" => $webform->form_id]
            );

            /*-------------------------------------------
                USER LOG
            -------------------------------------------*/
            $user = \Sentinel::check();
            addUserLog(
                $webform->organization_id,
                $user->id,
                null,
                "webform_updated",
                ["form_id" => $webform->form_id]
            );

            return response()->json(['status'=>'success','message'=>'Webform updated']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status'=>'error','message'=>$e->getMessage()], 500);
        }
    }

    /*-------------------------------------------
        DELETE WEBFORM
    -------------------------------------------*/
    public function destroy($id)
    {
        $webform = WebForm::findOrFail($id);

        WebFormAttribute::where('web_form_id', $webform->id)->delete();
        $webform->delete();

        /*-------------------------------------------
            ORG LOG
        -------------------------------------------*/
        addOrgLog(
            $webform->organization_id,
            "webform_deleted",
            ["form_id" => $webform->form_id]
        );

        /*-------------------------------------------
            USER LOG
        -------------------------------------------*/
        $user = \Sentinel::check();
        addUserLog(
            $webform->organization_id,
            $user->id,
            null,
            "webform_deleted",
            ["form_id" => $webform->form_id]
        );

        return response()->json(['status'=>'success','message'=>'Deleted']);
    }


public function embedCode($id)
{
    $form = WebForm::findOrFail($id);

    $publicUrl = route('webform.public', $form->form_id);

    // FINAL EMBED CODE (iframe + auto-height adjust script)
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
