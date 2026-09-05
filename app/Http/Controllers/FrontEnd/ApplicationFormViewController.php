<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\ApplicationForm;
use App\ApplicationFormResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ApplicationFormViewController extends Controller
{
  private function updateApplicationStage($leadId)
{
    $lead = \App\Lead::find($leadId);

    if(!$lead){
        return;
    }

    $stage = DB::table('pipeline_stages')
        ->where('lead_pipeline_id',$lead->pipeline_id)
        ->where('code','application_started')
        ->first();

    if(!$stage){
        return;
    }

    if($lead->stage_id < $stage->id){
        $lead->stage_id = $stage->id;
        $lead->save();
    }
}



// PUBLIC FORM VIEW
public function showForm($code, $lead_id)
{
    // 1. Form
	try {
        $lead_id = decrypt($lead_id);   // ✅ decrypt here
    } catch (DecryptException $e) {
        abort(404, 'Invalid Lead ID');
    }


   $form = ApplicationForm::where('unique_code', $code)
        ->where('status', '1')
        ->firstOrFail();

    // 2. Lead
    $lead = \App\Lead::where('id', $lead_id)->first();

    // 3. Draft / existing response (lead based)
    $response = null;
    if ($lead) {
        $response = ApplicationFormResponse::where('lead_form_id', $form->id)
            ->where('lead_id', $lead_id)
            ->first();
    }

    // 4. Form file
    $formName = preg_replace('/[^a-zA-Z0-9_]/', '', $form->form_name);
    $formPath = resource_path("views/FrontEnd/application/{$formName}.blade.php");

    if (!file_exists($formPath)) {
        abort(404, 'Form file not found');
    }

	  if(!$lead){
		return view('FrontEnd.application.show_form', [
			'form' => $form,
			'lead' => null,
			'leadId' => $lead_id,
			'response' => null,
			'leadKey' => $code,
			'formPath' => $formPath,
		]);
	}
	else
	{
		
    return view('FrontEnd.application.show_form', [
        'form'      => $form,
        'lead'      => $lead,          // ✅ MAIN FIX
        'leadKey'   => $code,
        'formPath'  => $formPath,
        'leadId'    => $lead_id,
        'response'  => $response
    ]);
	}
}


    // FORM SUBMIT (AJAX)
 public function submitForm(Request $request)
{
    $request->validate([
        'form_id' => 'required',
        'lead_id' => 'required',
        'form_data' => 'required'
    ]);

    ApplicationFormResponse::create([
        'lead_form_id' => $request->form_id,
        'lead_id'      => $request->lead_id,
        'response_json'=> json_encode($request->form_data),
        'status'       => 'submitted'
    ]);

    $this->updateApplicationStage($request->lead_id);

    return response()->json([
        'status' => true,
        'message' => 'Form Submitted Successfully'
    ]);
}




public function partialSave(Request $request)
{

    if (!$request->form_id || !$request->lead_id) {
        return response()->json([
            'status' => false,
            'message' => 'Form ID or Lead ID missing'
        ]);
    }

    $formData = $request->except([
        '_token',
        'form_id',
        'lead_id',
        'current_step',
        'progress'
    ]);

    /*
    ---------------------------
    | DOCUMENT UPLOAD
    ---------------------------
    */

    $uploadPath = public_path('uploads/applications');

    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    if ($request->hasFile('photo')) {

        $file = $request->file('photo');

        $name = time().'_'.$file->getClientOriginalName();

        $file->move($uploadPath, $name);

        $formData['photo'] = 'uploads/applications/'.$name;
    }

    if ($request->hasFile('signature')) {

        $file = $request->file('signature');

        $name = time().'_'.$file->getClientOriginalName();

        $file->move($uploadPath, $name);

        $formData['signature'] = 'uploads/applications/'.$name;
    }

    ApplicationFormResponse::updateOrCreate(
        [
            'lead_form_id' => $request->form_id,
            'lead_id'      => $request->lead_id
        ],
        [
            'response_json' => json_encode($formData),
            'current_step'  => $request->current_step ?? 1,
            'fill_percentage' => $request->progress ?? 0,
            'status' => 'draft'
        ]
    );

    $this->updateApplicationStage($request->lead_id);

    return response()->json([
        'status'   => true,
        'message'  => 'Draft saved successfully',
        'progress' => $request->progress
    ]);
}

public function finalSubmit(Request $request)
{

    $leadId = $request->lead_id;

    $data = $request->except('_token');

    ApplicationFormResponse::where('lead_id', $leadId)->update([
        'response_json' => json_encode($data),
        'status'        => 'submitted',
        'fill_percentage' => 100
    ]);

    $this->updateApplicationStage($leadId);

    $paymentUrl = route('payment.init', ['lead_id' => $leadId]);

    return response()->json([
        'status' => true,
        'payment_url' => $paymentUrl
    ]);
}

}
