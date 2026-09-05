<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LeadPipelineStage;

class LeadPipelineStageController extends Controller
{
    public function store(Request $req, $pipelineId)
    {
        $stage = LeadPipelineStage::create([
            'lead_pipeline_id' => $pipelineId,
            'name'             => $req->name,
            'sort_order'       => $req->sort_order ?? 1,
            'is_final'         => 0
        ]);

        return response()->json($stage);
    }

    public function update(Request $req, $stageId)
    {
        LeadPipelineStage::where('id', $stageId)
            ->update([
                'name'       => $req->name,
                'sort_order' => $req->sort_order ?? 0
            ]);

        return response()->json(['updated' => true]);
    }

    public function destroy($stageId)
    {
        LeadPipelineStage::where('id', $stageId)->delete();
        return response()->json(['deleted' => true]);
    }
}
