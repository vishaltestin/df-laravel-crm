<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\IvrSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IvrSettingController extends Controller
{
    public function index($orgId = null)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $role  = $user->roles()->first()->slug ?? null;

         
            $setting = IvrSetting::where('organization_id', $orgId)->first();
         

        return view('FrontEnd.ivr.form', compact('setting', 'orgId', 'role'));
    }

    public function update(Request $request)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;
        $role   = $user->roles()->first()->slug ?? null;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $setting = IvrSetting::where('organization_id', $orgId)->first();

        if (!$setting) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Setting not found!'
            ]);
        }

        if (!($role === 'root' || in_array($role, ['admin', 'sadmin']))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to edit IVR settings!'
            ]);
        }

        // ✅ VALIDATION (New fields)
        $request->validate([
            'provider' => 'nullable|string|max:50',

            'base_url' => 'nullable|string|max:255',
            'authcode' => 'nullable|string|max:255',
            'default_deskphone' => 'nullable|string|max:30',

            'caller_id' => 'nullable|string|max:30',

            'status' => 'required|integer|in:0,1',
        ]);

        $oldData = $setting->toArray();

        $setting->update([
            'provider' => $request->provider ?? $setting->provider ?? 'telephony',

            'base_url' => $request->base_url ? rtrim($request->base_url, '/') : null,
            'authcode' => $request->authcode,
            'default_deskphone' => $request->default_deskphone,

            'caller_id' => $request->caller_id,
            'status' => $request->status,
        ]);

        // 🔥 ORG LOG
        logOrganizationActivity(
            $orgId,
            $userId,
            'ivr_setting',
            $setting->id,
            'update_ivr_setting',
            "IVR settings updated",
            ['old' => $oldData, 'new' => $setting->fresh()->toArray()]
        );

        // 🔥 USER LOG
        logUserActivity(
            $orgId,
            $userId,
            'ivr_setting',
            $setting->id,
            'update_ivr_setting',
            "User updated IVR settings",
            ['old' => $oldData, 'new' => $setting->fresh()->toArray()]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'IVR settings updated!'
        ]);
    }

    public function testivr(Request $request)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $request->validate([
            'base_url' => 'required|string|max:255',
            'authcode' => 'required|string|max:255',
        ]);

        $url = rtrim($request->base_url, '/') . '/api/profile_billing_v2';

        try {
            $res = Http::timeout(20)
                ->withHeaders([
                    'authcode' => $request->authcode
                ])
                ->post($url, []);

            if ($res->successful()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Connection OK'
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Connection Failed',
                'error' => $res->body()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
