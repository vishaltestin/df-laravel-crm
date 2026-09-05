<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Country;
use App\Institute;
use App\ApplicationForm;
use App\ApplicationFormResponse;
use Sentinel;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Lead;

class ApplicationFormController extends Controller
{
    // =========================
    // LIST FORMS
    // =========================
   public function index()
{
    $forms = ApplicationForm::withCount('responses')
        ->latest()
        ->get();

    return view('FrontEnd.application.index', compact('forms'));
}


    // =========================
    // CREATE PAGE
    // =========================
    public function create()
    {
        $countries  = Country::all();
        $institutes = Institute::all();

        return view('FrontEnd.application.create', compact('countries', 'institutes'));
    }

    // =========================
    // STORE FORM
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'required|string',
            'form_name'      => 'required',
            'allowed_countries'  => 'required|array',
            'allowed_institutes' => 'required|array',
        ]);

        ApplicationForm::create([
            'unique_code'        => Str::random(10),
            'title'              => $request->title,
            'description'        => $request->description,
            'form_name'      => $request->form_name,
             'allowed_countries'  => $request->allowed_countries,
            'allowed_institutes' => $request->allowed_institutes,
            'fees'               => $request->fees,
            'tax'                => $request->tax,
            'total_amount'       => $request->total_amount,
            'gateway'            => $request->gateway,
            'status'             => 1,
            'created_by'         => auth()->id(),
        ]);

        return redirect()
            ->route('application.forms')
            ->with('success', 'Form Created Successfully');
    }

    // =========================
    // FORM SUBMISSIONS LIST
    // =========================


		public function submissions($id)
		{
			try {
				$id = decrypt($id);
			} catch (DecryptException $e) {
				abort(404, 'Invalid Form ID');
			}

			$form = ApplicationForm::findOrFail($id);

			$responses = ApplicationFormResponse::select(
					'application_form_responses.*',

					// leads table
					'leads.name as lead_name',
					'leads.mobile as lead_mobile',
					'leads.email as lead_email',

					// lead_applications table
					'lead_applications.application_score',
					'lead_applications.payment_status as app_payment_status',
					'lead_applications.payment_method as app_payment_method'
				)
				->leftJoin('leads', 'leads.id', '=', 'application_form_responses.lead_id')

				->leftJoin('lead_applications', function ($join) {
					$join->on('lead_applications.lead_id', '=', 'application_form_responses.lead_id');
					$join->on('lead_applications.application_form', '=', 'application_form_responses.lead_form_id');
				})

				->where('application_form_responses.lead_form_id', $id)
				->orderBy('application_form_responses.id', 'desc')
				->get();

			return view('FrontEnd.application.submissions', compact('form', 'responses'));
		}

    // =========================
    // SINGLE SUBMISSION VIEW
    // =========================
    public function viewSubmission($id)
{
    $response = ApplicationFormResponse::findOrFail($id);

    $data = json_decode($response->response_json, true);

    if (!is_array($data)) {
        $data = [];
    }

    return view('FrontEnd.application.submission_view', compact('response', 'data'));
}

    // =========================
    // EDIT PAGE
    // =========================
    public function edit($id)
    {
		
		 try {
        $id = decrypt($id);
    } catch (DecryptException $e) {
        abort(404, 'Invalid Form ID');
    }
	
	
        $form = ApplicationForm::findOrFail($id);

        $countries  = Country::all();
        $institutes = Institute::all();

        return view('FrontEnd.application.edit', compact(
            'form', 'countries', 'institutes'
        ));
    }

    // =========================
    // UPDATE FORM
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'              => 'required|string',
            'form_name'      => 'required',
            'allowed_countries'  => 'required|array',
            'allowed_institutes' => 'required|array',
        ]);

        $form = ApplicationForm::findOrFail($id);

        $form->title              = $request->title;
        $form->description        = $request->description;
        $form->form_name      = $request->form_name;
         $form->allowed_countries  = $request->allowed_countries;
        $form->allowed_institutes = $request->allowed_institutes;
        $form->fees               = $request->fees;
        $form->tax                = $request->tax;
        $form->total_amount       = $request->total_amount;
        $form->gateway            = $request->gateway;

        $form->save();

        return redirect()
            ->route('application.forms')
            ->with('success', 'Form Updated Successfully');
    }
	
	
	
	private function renderFormHtml(ApplicationForm $form)
{
    $schema = json_decode($form->json_schema, true);

    $replace = [

        '{{COUNTRY_SELECT}}' => view(
            'FrontEnd.form_parts.country',
            ['countries' => $schema['allowed_countries']]
        )->render(),

        '{{STATE_SELECT}}' => '<select id="state" name="state_id" class="form-control"></select>',

        '{{CITY_SELECT}}' => '<select id="city" name="city_id" class="form-control"></select>',

        '{{INSTITUTE_SELECT}}' => view(
            'FrontEnd.form_parts.institute',
            ['institutes' => $schema['allowed_institutes']]
        )->render(),

        '{{PROGRAM_SELECT}}' => '<select id="program" name="program_id" class="form-control"></select>',

        '{{COURSE_SELECT}}' => '<select id="course" name="course_id" class="form-control"></select>',

        '{{FEES_FIELD}}' => '<input type="number" id="fees" value="'.$schema['fees'].'" readonly class="form-control">',

        '{{TAX_FIELD}}' => '<input type="number" id="tax" value="'.$schema['tax'].'" readonly class="form-control">',

        '{{TOTAL_FIELD}}' => '<input type="number" id="total" value="'.$schema['total_amount'].'" readonly class="form-control">',

        '{{PAYMENT_GATEWAY}}' => view(
            'FrontEnd.form_parts.gateway',
            ['gateway' => $schema['gateway']]
        )->render(),

        '{{PAY_NOW_BUTTON}}' => '<button type="submit" class="btn btn-success">Pay & Submit</button>',
    ];

    return str_replace(
        array_keys($replace),
        array_values($replace),
        $form->html_template
    );
}




}
