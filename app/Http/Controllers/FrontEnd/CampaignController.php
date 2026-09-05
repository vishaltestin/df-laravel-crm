<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Campaign;
use App\MarketingEvent;
use App\EmailTemplate;

class CampaignController extends Controller
{
  public function index()
{
    $user = \Sentinel::check();
    $userId = $user->id;
    $role = userRole();

    $reg = registeruser($userId);
    $orgId = $reg->organization;

    if ($role === 'root' || $role === 'sadmin') {
        $campaigns = Campaign::latest()->get();
        $events = MarketingEvent::all();
        $templates = EmailTemplate::all();
    }
    elseif ($role === 'admin') {
        $campaigns = Campaign::where('organization_id',$orgId)->latest()->get();
        $events = MarketingEvent::where('organization_id',$orgId)->get();
        $templates = EmailTemplate::where('organization_id',$orgId)->get();
    }
    else {
        abort(403,"Unauthorized");
    }

    return view('FrontEnd.campaigns.index', compact('campaigns','events','templates'));
}


    public function store(Request $request)
    {
         $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $orgId = $reg->organization;

        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'status' => 'required',
            'type' => 'required',
            'mail_to' => 'required',
            'spooling' => 'required',
            'marketing_template_id' => 'required|integer',
            'marketing_event_id' => 'required|integer',
        ]);

        Campaign::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'status' => $request->status,
            'type' => $request->type,
            'mail_to' => $request->mail_to,
            'spooling' => $request->spooling,
            'marketing_template_id' => $request->marketing_template_id,
            'marketing_event_id' => $request->marketing_event_id,
            'organization_id' => $orgId,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        return Campaign::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'status' => 'required',
            'type' => 'required',
            'mail_to' => 'required',
            'spooling' => 'required',
            'marketing_template_id' => 'required|integer',
            'marketing_event_id' => 'required|integer',
        ]);

        $campaign = Campaign::findOrFail($id);

        $campaign->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'status' => $request->status,
            'type' => $request->type,
            'mail_to' => $request->mail_to,
            'spooling' => $request->spooling,
            'marketing_template_id' => $request->marketing_template_id,
            'marketing_event_id' => $request->marketing_event_id,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        Campaign::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
