<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use App\Organization;
use App\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function index($orgId = null)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        if ($role === 'root' || in_array($role, ['admin', 'sadmin'])) {
            $setting = GeneralSetting::firstOrCreate(['organization_id' => $orgId]);
        } else {
            $setting = GeneralSetting::where('organization_id', $orgId)->first();
        }
		if ($setting) {
			session([
				'general_settings' => [
					'app_name' => $setting->app_name,
					'currency' => $setting->currency,
					'timezone' => $setting->timezone,
					'payment_credentials' => $setting->payment_credentials,
				]
			]);
		}
        return view('FrontEnd.general.form', compact('setting', 'orgId', 'role'));
    }


public function update(Request $request)
{
    $user   = \Sentinel::check();
    $userId = $user->id;
    $role   = $user->roles()->first()->slug ?? null;

    $reg   = registeruser($userId);
    $orgId = $reg->organization;

    $setting = GeneralSetting::where('organization_id', $orgId)->first();

    if (!$setting) {
        return response()->json([
            'status'  => 'error',
            'message' => 'General settings not found for your organization!'
        ]);
    }

    if (!($role === 'root' || in_array($role, ['admin', 'sadmin']))) {
        return response()->json([
            'status'  => 'error',
            'message' => 'You are not allowed to update General settings!'
        ]);
    }

    // 🔥 OLD DATA (FOR LOGS)
    $oldData = $setting->toArray();

    // -----------------------------
    // VALIDATION
    // -----------------------------
    $validated = $request->validate([
        'app_name'  => 'required|string|max:255',
        'currency'  => 'nullable|string|max:10',
        'timezone'  => 'nullable',

        // payment
        'payment.gateway' => 'nullable|string',
        'payment.mode'    => 'nullable|in:test,live',
        'payment.key_id'  => 'nullable|string',
        'payment.secret_key' => 'nullable|string',
        'payment.merchant_id' => 'nullable|string',
        'payment.webhook_secret' => 'nullable|string',
    ]);

    // -----------------------------
    // PAYMENT GATEWAY SAVE (SIMPLE)
    // -----------------------------
    $payment = $request->input('payment');

    $paymentCredentials = null;

    if (!empty($payment['gateway'])) {
        $paymentCredentials = [
            $payment['gateway'] => [
                'mode' => $payment['mode'] ?? 'test',
                'credentials' => [
                    'key_id'         => $payment['key_id'] ?? '',
                    'secret_key'     => $payment['secret_key'] ?? '',
                    'merchant_id'    => $payment['merchant_id'] ?? '',
                    'webhook_secret' => $payment['webhook_secret'] ?? '',
                ]
            ]
        ];
    }

    // -----------------------------
    // SAVE (ONE TIME)
    // -----------------------------
    $setting->update([
        'app_name' => $validated['app_name'],
        'currency' => $validated['currency'] ?? null,
        'timezone' => $validated['timezone'] ?? null,
        'payment_credentials' => $paymentCredentials
    ]);

    // -----------------------------
    // LOGS
    // -----------------------------
    $newData = [
        'app_name' => $validated['app_name'],
        'currency' => $validated['currency'] ?? null,
        'timezone' => $validated['timezone'] ?? null,
        'payment_credentials' => $paymentCredentials
    ];

    logOrganizationActivity(
        $orgId,
        $userId,
        'general_setting',
        $setting->id,
        'update_general_setting',
        'General settings updated',
        ['old' => $oldData, 'new' => $newData]
    );

    logUserActivity(
        $orgId,
        $userId,
        'general_setting',
        $setting->id,
        'update_general_setting',
        'User updated general settings',
        ['old' => $oldData, 'new' => $newData]
    );
// -----------------------------
// UPDATE SESSION AFTER SAVE
// -----------------------------
session([
    'general_settings' => [
        'app_name' => $setting->app_name,
        'currency' => $setting->currency,
        'timezone' => $setting->timezone,
        'payment_credentials' => $setting->payment_credentials,
    ]
]);

    return response()->json([
        'status'  => 'success',
        'message' => 'General settings updated successfully!'
    ]);
}

    public function destroy($id)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;
        $role   = $user->roles()->first()->slug ?? null;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $record = GeneralSetting::findOrFail($id);

        if ($role === 'root' || (in_array($role, ['admin','sadmin']) && $record->organization_id == $orgId)) {

            $oldData = $record->toArray();
            $record->delete();

            // 🔥 ORG LOG
            logOrganizationActivity(
                $orgId,
                $userId,
                'general_setting',
                $id,
                'delete_general_setting',
                "General settings deleted",
                $oldData
            );

            // 🔥 USER LOG
            logUserActivity(
                $orgId,
                $userId,
                'general_setting',
                $id,
                'delete_general_setting',
                "User deleted general settings",
                $oldData
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Deleted!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'You are not allowed to delete!'
        ]);
    }
}

