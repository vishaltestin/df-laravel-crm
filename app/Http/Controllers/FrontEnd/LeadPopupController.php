<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Lead;

class LeadPopupController extends Controller
{
    // 🔹 Change Lead Stage
    public function stage(Lead $lead)
    {
        return view('FrontEnd.leads.popups.change-stage', compact('lead'));
    }

    // 🔹 Reassign Lead
    public function reassign(Lead $lead)
    {
        return view('FrontEnd.leads.popups.reassign', compact('lead'));
    }

    // 🔹 Add Note
    public function note(Lead $lead)
    {
        return view('FrontEnd.leads.popups.add-note', compact('lead'));
    }

    // 🔹 Communicate
    public function communicate(Lead $lead)
    {
        return view('FrontEnd.leads.popups.communicate', compact('lead'));
    }

    // 🔹 Add Event
    public function event(Lead $lead)
    {
        return view('FrontEnd.leads.popups.add-event', compact('lead'));
    }

    // 🔹 Quick View
    public function view(Lead $lead)
    {
        return view('FrontEnd.leads.popups.quick-view', compact('lead'));
    }

    // 🔹 Delete Lead
    public function delete(Lead $lead)
    {
        return view('FrontEnd.leads.popups.delete', compact('lead'));
    }
}
