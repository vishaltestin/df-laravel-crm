<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApplicationForm;
use App\ApplicationFormLead;
use App\LeadFormAssignController;
use Sentinel;


class LeadFormAssignController extends Controller
{
    public function assignForm(Request $request)
    {
        $request->validate([
            'lead_id' => 'required',
            'form_id' => 'required'
        ]);

        ApplicationFormLead::create([
            'lead_id' => $request->lead_id,
            'form_id' => $request->form_id,
            'unique_submission_code' => strtoupper(Str::random(15)),
        ]);

        return back()->with('success', 'Form assigned successfully!');
    }
}
