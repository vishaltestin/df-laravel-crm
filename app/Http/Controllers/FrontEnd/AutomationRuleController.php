<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use App\AutomationRule;

class AutomationRuleController extends Controller
{
    /**
     * Automation rules list (org wise)
     */
    public function index()
    {
        $user = Sentinel::check();
        if (!$user) abort(403);

        $reg   = registeruser($user->id);
        $orgId = $reg->organization;

        $rules = AutomationRule::where('organization_id', $orgId)
            ->latest()
            ->get();

        return view('FrontEnd.settings.automation.index', compact('rules'));
    }

    /**
     * Create rule page
     */
    public function create()
    {
        $user = Sentinel::check();
        if (!$user) abort(403);

        return view('FrontEnd.settings.automation.create');
    }

    /**
     * Store rule
     */
   public function store(Request $request)
{
    $user = Sentinel::check();
    if (!$user) abort(403);

    $reg   = registeruser($user->id);
    $orgId = $reg->organization;

    $request->validate([
        'name'          => 'required|string|max:255',
        'trigger_event' => 'required|string',
        'action_type'   => 'required|string',
    ]);

    AutomationRule::create([
        'organization_id' => $orgId,
        'name'            => $request->name,
        'trigger_event'   => $request->trigger_event,
        'conditions'      => $request->conditions ?? [],

        // 🔥 FIX HERE
        'actions' => [
            'type' => $request->action_type,
            'data' => $request->actions ?? []
        ],

        'created_by' => $user->id,
        'is_active'  => 1,
    ]);

    return redirect()
        ->route('automation.index')
        ->with('success', 'Automation rule created successfully');
}

    /**
     * Edit rule
     */
    public function edit($id)
    {
        $user = Sentinel::check();
        if (!$user) abort(403);

        $reg   = registeruser($user->id);
        $orgId = $reg->organization;

        $rule = AutomationRule::where('organization_id', $orgId)
            ->where('id', $id)
            ->firstOrFail();

        return view('FrontEnd.settings.automation.edit', compact('rule'));
    }

    /**
     * Update rule
     */
  public function update(Request $request, $id)
{
    $user = Sentinel::check();
    if (!$user) abort(403);

    $reg   = registeruser($user->id);
    $orgId = $reg->organization;

    $rule = AutomationRule::where('organization_id', $orgId)
        ->where('id', $id)
        ->firstOrFail();

    $request->validate([
        'name'          => 'required|string|max:255',
        'trigger_event' => 'required|string',
        'action_type'   => 'required|string',
    ]);

    $rule->update([
        'name'          => $request->name,
        'trigger_event' => $request->trigger_event,
        'conditions'    => $request->conditions ?? [],

        // 🔥 FIX HERE
        'actions' => [
            'type' => $request->action_type,
            'data' => $request->actions ?? []
        ],

        'is_active' => $request->has('is_active'),
    ]);

    return redirect()
        ->route('automation.index')
        ->with('success', 'Automation rule updated successfully');
}

    /**
     * Toggle active / inactive
     */
    public function toggle($id)
    {
        $user = Sentinel::check();
        if (!$user) abort(403);

        $reg   = registeruser($user->id);
        $orgId = $reg->organization;

        $rule = AutomationRule::where('organization_id', $orgId)
            ->where('id', $id)
            ->firstOrFail();

        $rule->update([
            'is_active' => !$rule->is_active
        ]);

        return back();
    }
}
