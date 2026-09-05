<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MarketingEvent;

class MarketingEventController extends Controller
{
 public function index()
{
    $user = \Sentinel::check();
    $userId = $user->id;
    $role = userRole();

    $reg = registeruser($userId);
    $orgId = $reg->organization;

    if ($role === 'root' || $role === 'sadmin') {
        $events = MarketingEvent::orderBy('date','desc')->get();
    }
    elseif ($role === 'admin') {
        $events = MarketingEvent::where('organization_id', $orgId)
                    ->orderBy('date','desc')->get();
    }
    else {
        abort(403,'Unauthorized');
    }

    return view('FrontEnd.marketing_events.index', compact('events'));
}



    public function store(Request $request)
    {
        $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $orgId = $reg->organization;

        $request->validate([
            'name' => 'required',
            'date' => 'required|date'
        ]);

        MarketingEvent::create([
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'organization_id' => $orgId
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        return MarketingEvent::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'date' => 'required|date'
        ]);

        $event = MarketingEvent::findOrFail($id);

        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        MarketingEvent::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
