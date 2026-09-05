<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;   //  <-- ADD THIS
use Illuminate\Http\Request;
use App\WebForm;
use App\Lead;
use App\WebFormAttribute;
use App\Attribute;
use Illuminate\Support\Str;
use Validator;

class WebFormController extends Controller
{
 public function publicView($form_id)
{
    $form = WebForm::where('form_id', $form_id)->firstOrFail();
	$form->timestamps = false;
$form->increment('view_count');
    // Increase view count WITHOUT touching updated_at
 //   WebForm::where('id', $form->id)->update([    'view_count' => \DB::raw('view_count + 1') ]);

    $attributes = WebFormAttribute::where('web_form_id', $form->id)
                    ->orderBy('sort_order')
                    ->get();

    return view('FrontEnd.public.webform', compact('form', 'attributes'));
}


public function publicSubmit(Request $request, $form_id)
{
    $form = WebForm::where('form_id', $form_id)->firstOrFail();

    $attributes = WebFormAttribute::where('web_form_id', $form->id)->get();

    // Validate Dynamically
    $rules = [];
    foreach ($attributes as $attr) {
        if ($attr->is_required) {
            $rules[$attr->name] = 'required';
        }
    }
    $request->validate($rules);

    // If create_lead enabled, create CRM Lead
 if ($form->create_lead) {

    $leadData = [];

    foreach ($attributes as $attr) {
        // map only allowed fields
        if (in_array($attr->name, [
            'name',
            'mobile',
            'email',
            'city',
            'country',
            'alternate_mobile',
            'faculty',
            'programmes',
            'loan_status'
        ])) {
            $leadData[$attr->name] = $request->input($attr->name);
        }
    }

    // Add CRM auto fields
    $leadData['business_id']        = $form->organization_id;
    $leadData['lead_source_id']     = $form->source_id ?? 0;
    $leadData['lead_registration_date'] = now();
    $leadData['lead_status']        = '0';
    $leadData['lead_score']         = '0';
    $leadData['lead_assign_to']     = '0';
    $leadData['pipeline_id']        = $form->pipeline_id ?? 0;
    $leadData['stage_id']           = $form->stage_id ?? 0;
    $leadData['type_id']            = $form->type_id ?? 0;

    Lead::create($leadData);
}

    // Increase submit count
    $form->increment('submit_count');

    // Success Handler
    if ($form->submit_success_action === 'redirect') {
        return redirect()->away($form->submit_success_content);
    }
return back()->with('message', $form->submit_success_content);
}



  public function index()
    {
         $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $orgId = $reg->organization;
        $webforms = WebForm::where('organization_id', $orgId)->latest()->get();
		    $forms = WebForm::where('organization_id', $orgId)->get();
        return view('FrontEnd.webforms.index', compact('webforms','forms'));
    }

    public function create()
    {
        return view('FrontEnd.webforms.create');
    }
 public function store(Request $request)
{
    $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $orgId = $reg->organization;

    $allowedTypes = [
        'text','textarea','email','number','mobile',
        'date','time','datetime-local','select','radio','checkbox'
    ];

    //--------------------------------------------------------
    // VALIDATION
    //--------------------------------------------------------
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'submit_button_label' => 'required|string|max:255',
        'submit_success_action' => 'required|in:message,redirect',
        'submit_success_content' => 'required|string',

        // UPDATED HERE ↓↓↓↓↓
        'form_attributes' => 'required|array|min:3',
        'form_attributes.*.name' => 'required|string|max:255',
        'form_attributes.*.type' => 'required|string|in:' . implode(',', $allowedTypes),
        'form_attributes.*.placeholder' => 'nullable|string|max:255',
        'form_attributes.*.is_required' => 'numeric|in:0,1',
        'form_attributes.*.is_hidden' => 'numeric|in:0,1',
        'form_attributes.*.sort_order' => 'nullable|integer|min:0',
        'form_attributes.*.options' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'=>'error',
            'errors'=>$validator->errors()
        ], 422);
    }

    //--------------------------------------------------------
    // FIXED ATTRIBUTE ARRAY
    //--------------------------------------------------------
    $attributes = collect($request->form_attributes)
        ->map(fn($a)=>[
            'name' => trim($a['name']),
            'type' => $a['type'],
            'placeholder' => $a['placeholder'] ?? null,
            'is_required' => $a['is_required'],
            'is_hidden' => $a['is_hidden'],
            'sort_order' => $a['sort_order'],
            'options' => $a['options'] ?? null
        ]);

    //--------------------------------------------------------
    // REQUIRED DEFAULT CHECK
    //--------------------------------------------------------
    $requiredDefaults = ['name','email','mobile'];

    foreach ($requiredDefaults as $df) {
        if (!$attributes->contains(fn($item) => strtolower(trim($item['name'])) === $df)) {
            return response()->json([
                'status' => 'error',
                'message' => ucfirst($df) . ' field must exist.'
            ], 422);
        }
    }

    //--------------------------------------------------------
    // CREATE MAIN WEBFORM
    //--------------------------------------------------------
    $webform = WebForm::create([
        'form_id' => 'wf_' . Str::random(8),
        'title' => $request->title,
        'description' => $request->description,
        'submit_button_label' => $request->submit_button_label,
        'submit_success_action' => $request->submit_success_action,
        'submit_success_content' => $request->submit_success_content,
        'create_lead' => $request->create_lead,
        'background_color' => $request->background_color,
        'form_background_color' => $request->form_background_color,
        'form_title_color' => $request->form_title_color,
        'form_submit_button_color' => $request->form_submit_button_color,
        'attribute_label_color' => $request->attribute_label_color,
        'organization_id' => $orgId
    ]);

    //--------------------------------------------------------
    // SYSTEM DEFAULT ATTRIBUTE IDS
    //--------------------------------------------------------
    $defaultMap = [
        'name'   => 1,
        'email'  => 2,
        'mobile' => 3
    ];

    //--------------------------------------------------------
    // SAVE EACH ATTRIBUTE
    //--------------------------------------------------------
    foreach ($attributes as $index => $attr) {

        $lower = strtolower($attr['name']);

        if (isset($defaultMap[$lower])) {
            $attributeId = $defaultMap[$lower];

        } else {
            // CUSTOM FIELD → ADD TO attributes TABLE
            $attributeId = DB::table('attributes')->insertGetId([
                'name' => $attr['name'],
                'code' => Str::slug($attr['name']),
                'type' => $attr['type'],
                'lookup_type' => in_array($attr['type'], ['select','radio','checkbox']) ? 'options' : null,
                'entity_type' => 'leads',
                'sort_order' => $index,
                'validation' => null,
                'is_required' => $attr['is_required'],
                'is_unique' => 0,
                'quick_add' => 0,
                'is_user_defined' => 1,
                'organization_id' => $orgId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // SAVE IN web_form_attributes
        WebFormAttribute::create([
            'web_form_id' => $webform->id,
            'attribute_id' => $attributeId,
            'name' => $attr['name'],
            'type' => $attr['type'],
            'placeholder' => $attr['placeholder'],
            'is_required' => $attr['is_required'],
            'is_hidden' => $attr['is_hidden'],
            'sort_order' => $attr['sort_order'] ?? $index,
            'options' => $attr['options'],
        ]);
    }

    return response()->json([
        'status'=>'success',
        'message'=>'Webform Created Successfully'
    ]);
}


  public function edit($id)
    {
        $webform = WebForm::with('attributes')->findOrFail($id);
        return view('FrontEnd.webforms.edit', compact('webform'));
    }

   public function update(Request $request, $id)
{
    $webform = WebForm::findOrFail($id);
     $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $orgId = $reg->organization;

    $allowedTypes = [
        'text','textarea','email','number','mobile',
        'date','time','datetime-local','select','radio','checkbox'
    ];

    // Validate request basic fields
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'submit_button_label' => 'required|string|max:255',
        'submit_success_action' => 'required|in:message,redirect',
        'submit_success_content' => 'required|string',
        'form_attributes' => 'required|array|min:3',
        'form_attributes.*.name' => 'required|string|max:255',
        'form_attributes.*.type' => 'required|string|in:' . implode(',', $allowedTypes),
        'form_attributes.*.placeholder' => 'nullable|string|max:255',
        'form_attributes.*.is_required' => 'numeric|in:0,1',
        'form_attributes.*.is_hidden' => 'numeric|in:0,1',
        'form_attributes.*.sort_order' => 'nullable|integer|min:0',
        'form_attributes.*.options' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['status'=>'error','errors'=>$validator->errors()], 422);
    }

    $attrs = collect($request->form_attributes)
        ->map(fn($a)=>[
            'name' => trim($a['name']),
            'type' => $a['type'],
            'placeholder' => $a['placeholder'] ?? null,
            'is_required' => (int)($a['is_required'] ?? 0),
            'is_hidden' => (int)($a['is_hidden'] ?? 0),
            'sort_order' => isset($a['sort_order']) ? (int)$a['sort_order'] : 0,
            'options' => $a['options'] ?? null,
            'attribute_id' => $a['attribute_id'] ?? null
        ]);

    // Ensure default fields exist
    $requiredDefaults = ['name','email','mobile'];
    $normalized = $attrs->map(fn($x) => strtolower(trim($x['name'] ?? '')));
    foreach ($requiredDefaults as $df) {
        if (!$normalized->contains($df)) {
            return response()->json(['status'=>'error','message'=> ucfirst($df).' field must exist.'], 422);
        }
    }

    // DB Transaction: update webform + attributes
    DB::beginTransaction();
    try {
        // Update webform basic fields
        $webform->update([
            'title' => $request->title,
            'description' => $request->description,
            'submit_button_label' => $request->submit_button_label,
            'submit_success_action' => $request->submit_success_action,
            'submit_success_content' => $request->submit_success_content,
            'create_lead' => $request->create_lead ? 1 : 0,
            'background_color' => $request->background_color,
            'form_background_color' => $request->form_background_color,
            'form_title_color' => $request->form_title_color,
            'form_submit_button_color' => $request->form_submit_button_color,
            'attribute_label_color' => $request->attribute_label_color,
            'organization_id' => $orgId
        ]);

        // Remove existing web_form_attributes for this webform
        WebFormAttribute::where('web_form_id', $webform->id)->delete();

        // default map for system attributes (ensure these IDs exist in attributes table)
        $defaultMap = [
            'name' => 1,
            'email' => 2,
            'mobile' => 3
        ];

        // Insert each attribute (create master attributes if needed)
        foreach ($attrs->values() as $index => $attr) {

            $lower = strtolower($attr['name']);

            if (isset($defaultMap[$lower])) {
                $attributeId = $defaultMap[$lower];
            } else {
                // if attribute_id passed (editing existing custom), try to reuse it
                if ($attr['attribute_id']) {
                    $exists = DB::table('attributes')->where('id', $attr['attribute_id'])->first();
                    if ($exists) {
                        // update master attribute properties if needed
                        DB::table('attributes')->where('id', $attr['attribute_id'])->update([
                            'name' => $attr['name'],
                            'code' => Str::slug($attr['name']),
                            'type' => $attr['type'],
                            'lookup_type' => in_array($attr['type'], ['select','radio','checkbox']) ? 'options' : null,
                            'entity_type' => 'leads',
                            'sort_order' => $index,
                            'validation' => null,
                            'is_required' => $attr['is_required'],
                            'is_user_defined' => 1,
                            'organization_id' => $orgId,
                            'updated_at' => now()
                        ]);
                        $attributeId = $attr['attribute_id'];
                    } else {
                        // insert new master attribute
                        $attributeId = DB::table('attributes')->insertGetId([
                            'name' => $attr['name'],
                            'code' => Str::slug($attr['name']),
                            'type' => $attr['type'],
                            'lookup_type' => in_array($attr['type'], ['select','radio','checkbox']) ? 'options' : null,
                            'entity_type' => 'leads',
                            'sort_order' => $index,
                            'validation' => null,
                            'is_required' => $attr['is_required'],
                            'is_unique' => 0,
                            'quick_add' => 0,
                            'is_user_defined' => 1,
                            'organization_id' => $orgId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                } else {
                    // insert new master attribute
                    $attributeId = DB::table('attributes')->insertGetId([
                        'name' => $attr['name'],
                        'code' => Str::slug($attr['name']),
                        'type' => $attr['type'],
                        'lookup_type' => in_array($attr['type'], ['select','radio','checkbox']) ? 'options' : null,
                        'entity_type' => 'leads',
                        'sort_order' => $index,
                        'validation' => null,
                        'is_required' => $attr['is_required'],
                        'is_unique' => 0,
                        'quick_add' => 0,
                        'is_user_defined' => 1,
                        'organization_id' => $orgId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // insert into web_form_attributes (note: web_form_attributes DOES NOT have `type` column in your schema)
            WebFormAttribute::create([
                'web_form_id' => $webform->id,
                'attribute_id' => $attributeId,
                'name' => $attr['name'],
                'placeholder' => $attr['placeholder'],
                'is_required' => $attr['is_required'],
                'is_hidden' => $attr['is_hidden'],
                'sort_order' => $attr['sort_order'] ?? $index
            ]);
        }

        DB::commit();
        return response()->json(['status'=>'success','message'=>'Webform updated']);
    } catch (\Exception $e) {
        DB::rollback();
        // return error for AJAX
        return response()->json(['status'=>'error','message'=>$e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        $webform = WebForm::findOrFail($id);
        WebFormAttribute::where('web_form_id', $webform->id)->delete();
        $webform->delete();
        return response()->json(['status'=>'success','message'=>'Deleted']);
    }
}
