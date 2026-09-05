<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
 use App\LeadColumnSetting;
use Illuminate\Http\Request;
use Sentinel;

class LeadColumnSettingController extends Controller
{
    private function getOrgId()
    {
        $reg = registeruser(Sentinel::check()->id);
        return $reg->organization;
    }

    public function get(Request $request)
    {
        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        $row = LeadColumnSetting::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'status' => true,
            'columns' => $row ? ($row->columns_json ?? []) : []
        ]);
    }

    public function save(Request $request)
    {
        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        $request->validate([
            'columns' => 'required|array',
        ]);

        $row = LeadColumnSetting::updateOrCreate(
            [
                'organization_id' => $orgId,
                'user_id' => $user->id
            ],
            [
                'columns_json' => array_values($request->columns)
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Columns saved successfully',
            'data' => $row
        ]);
    }
}
