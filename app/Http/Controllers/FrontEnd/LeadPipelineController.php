<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LeadPipeline;
use App\LeadPipelineStage;
use Sentinel;

class LeadPipelineController extends Controller
{
    // ============================================================
    // INDEX (Role Based Listing)
    // ============================================================
    public function index()
    {
        $user  = Sentinel::check();
        $reg   = registeruser($user->id);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        // ROOT → सभी organizations की pipelines
        if ($role === 'root') {
            $pipelines = LeadPipeline::with('stages')->get();
        }
        // SADMIN → सभी organizations देख सकता है, delete नहीं
        elseif ($role === 'sadmin') {
            $pipelines = LeadPipeline::with('stages')->get();
        }
        // ADMIN → सिर्फ अपनी organization की pipelines
        elseif ($role === 'admin') {
            $pipelines = LeadPipeline::where('organization_id', $orgId)->get();
        }
        // MANAGER → सिर्फ view access
        elseif ($role === 'manager') {
            $pipelines = LeadPipeline::where('organization_id', $orgId)->get();
        }
        else {
            abort(403, "Unauthorized");
        }

        return view('FrontEnd.pipelines.index', compact('pipelines', 'role'));
    }

    // ============================================================
    // STORE PIPELINE (ROOT + SADMIN + ADMIN ONLY)
    // ============================================================
    public function store(Request $req)
    {
        $user  = Sentinel::check();
        $role  = $user->roles()->first()->slug;
        $orgId = registeruser($user->id)->organization;

        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $req->validate([
            'name' => 'required|string|max:255|unique:pipelines,name'
        ]);

        $pipeline = LeadPipeline::create([
            'organization_id' => $orgId,
            'name'            => $req->name,
            'code'            => strtolower($req->name),
            'rotten_days'     => $req->rotten_days ?? 15,
            'is_default'      => 0,
        ]);

        // Default stages
        LeadPipelineStage::create([
            'lead_pipeline_id' => $pipeline->id,
            'name'             => "New Lead",
            'code'             => "new_lead",
            'sort_order'       => 0,
            'is_final'         => 0,
        ]);

        LeadPipelineStage::create([
            'lead_pipeline_id' => $pipeline->id,
            'name'             => "Closed",
            'code'             => "closed",
            'sort_order'       => 9999,
            'is_final'         => 1,
        ]);

        return response()->json(['id' => $pipeline->id]);
    }

    // ============================================================
    // EDIT RETURN JSON
    // ============================================================
    public function editData($id)
    {
        $pipeline = LeadPipeline::findOrFail($id);

        $stages = LeadPipelineStage::where('lead_pipeline_id', $id)
            ->where('is_final', 0)
            ->where('sort_order', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $final = LeadPipelineStage::where('lead_pipeline_id', $id)
            ->where('is_final', 1)
            ->first();

        return response()->json([
            'stages'       => $stages,
            'final_stage'  => $final->name ?? 'Closed'
        ]);
    }

    // ============================================================
    // EDIT PAGE
    // ============================================================
    public function edit($id)
    {
        $user   = Sentinel::check();
        $role   = $user->roles()->first()->slug;
        $pipeline = LeadPipeline::findOrFail($id);

        // Manager is not allowed
        if ($role == 'manager') {
            abort(403, "Unauthorized");
        }

        $stages = LeadPipelineStage::where('lead_pipeline_id', $id)
            ->orderBy('sort_order')->get();

        $finalStage = LeadPipelineStage::where('lead_pipeline_id', $id)
            ->where('is_final', 1)->first();

        return view('FrontEnd.pipelines.edit', compact('pipeline', 'stages', 'finalStage','role'));
    }

    // ============================================================
    // UPDATE PIPELINE (ROOT + SADMIN + ADMIN)
    // ============================================================
    public function update(Request $req, $id)
    {
        $user  = Sentinel::check();
        $role  = $user->roles()->first()->slug;

        if (!in_array($role, ['root','sadmin','admin'])) {
            return response()->json(['error'=>'Permission denied'], 403);
        }

        $order = 1;

        foreach ($req->stages as $stage) {

            if (!empty($stage['id'])) {

                $s = LeadPipelineStage::find($stage['id']);

                // Skip "New Lead"
                if ($s && $s->sort_order == 0) {
                    $order++;
                    continue;
                }

                $s->update([
                    'name'       => $stage['name'],
                    'sort_order' => $order++,
                ]);
            }

            // New stage
            if (empty($stage['id']) && !empty($stage['name'])) {
                LeadPipelineStage::create([
                    'lead_pipeline_id' => $id,
                    'name'             => $stage['name'],
                    'code'             => strtolower($stage['name']),
                    'sort_order'       => $order++,
                    'is_final'         => 0
                ]);
            }
        }

        // Update final stage
        LeadPipelineStage::where('lead_pipeline_id', $id)
            ->where('is_final', 1)
            ->update(['name' => $req->final_stage]);

        return response()->json(['status' => 'success']);
    }

    // ============================================================
    // DELETE PIPELINE (ROOT + ADMIN ONLY)
    // ============================================================
    public function destroy($id)
    {
        $user = Sentinel::check();
        $role = $user->roles()->first()->slug;

        // sadmin delete नहीं कर सकता
        if (!in_array($role, ['root','admin'])) {
            return response()->json(['error'=>'Delete access denied'], 403);
        }

        LeadPipelineStage::where('lead_pipeline_id', $id)->delete();
        LeadPipeline::findOrFail($id)->delete();

        return response()->json(['deleted' => true]);
    }
}
