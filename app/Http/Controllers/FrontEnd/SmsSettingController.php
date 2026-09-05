<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\SmsSetting;
use Illuminate\Http\Request;

class SmsSettingController extends Controller
{
    public function index($orgId = null)
    {
        // === आपका exact login/role/org extraction जैसा रखा ✔ ===
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        // ✅ root/admin/sadmin only row create/manage
        if ($role === 'root' || in_array($role, ['admin', 'sadmin'])) {
            $setting = SmsSetting::firstOrCreate([
                'organization_id' => $orgId
            ]);
        } else {
            // ❌ normal user only view, no create allowed
            $setting = SmsSetting::where('organization_id', $orgId)->first();
        }

        return view('FrontEnd.sms.form', compact('setting', 'orgId', 'role'));
    }

    public function update(Request $request)
    {
        // === same extraction ✔ ===
        $user   = \Sentinel::check();
        $userId = $user->id;
        $role   = $user->roles()->first()->slug ?? null;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $setting = SmsSetting::where('organization_id', $orgId)->first();

        if (!$setting) {
            return response()->json([
                'status'  => 'error',
                'message' => 'SMS settings not found!'
            ]);
        }

        // ❌ normal user update nahi kar sakta
        if (!($role === 'root' || in_array($role, ['admin', 'sadmin']))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to update SMS settings!'
            ]);
        }

        // ✅ validation optional but safe
        $request->validate([
            'sms_provider' => 'nullable|string',
            'sms_api_key'  => 'nullable|string',
            'sms_sender'   => 'nullable|string|max:20',
        ]);

        // ✅ update
        $setting->update([
    'provider'      => $request->provider,
    'api_key'       => $request->api_key,
    'sender_id'     => $request->sender_id,
    'api_url'       => $request->api_url,
    'http_method'   => $request->http_method,
    'mobile_param'  => $request->mobile_param,
    'message_param' => $request->message_param,
    'extra_params'  => $request->extra_params
]);


        return response()->json([
            'status'  => 'success',
            'message' => 'SMS settings updated!'
        ]);
    }
public function testSms(Request $request)
{
    $request->validate([
        'mobile'  => 'required',
        'message' => 'required'
    ]);

    $user  = \Sentinel::check();
    $reg   = registeruser($user->id);
    $orgId = $reg->organization;

    $sms = SmsSetting::where('organization_id', $orgId)->first();
    if(!$sms) {
        return response()->json(['status'=>'error','message'=>'Settings not found']);
    }

    // Build params
    parse_str($sms->extra_params, $extra);

    $params = array_merge($extra, [
        $sms->mobile_param  => $request->mobile,
        $sms->message_param => $request->message,
        'api_key'           => $sms->api_key,
        'from'              => $sms->sender_id
    ]);

    $fullUrl = null;
    $response = null;

    if ($sms->http_method === 'GET') {

        $fullUrl = $sms->api_url . '?' . http_build_query($params);
        $response = @file_get_contents($fullUrl);

    } else {

        $fullUrl = $sms->api_url; // POST URL only
        $response = \Http::asForm()->post($sms->api_url, $params)->body();
    }

    // Try extracting provider message id (customize based on provider)
    $providerMsgId = null;
    $responseJson = null;

    if (!empty($response)) {
        $responseJson = $response;

        // Attempt JSON decode
        $decoded = json_decode($response, true);
        if (is_array($decoded)) {
            // Example key (edit based on provider)
            $providerMsgId = $decoded['message_id'] ?? null;
        }
    }

    // Save log
    logSmsActivity(
        $organization_id     = $orgId,
        $lead_id             = null,                     // test sms so null
        $mobile              = $request->mobile,
        $message             = $request->message,
        $provider            = $sms->provider,
        $provider_message_id = $providerMsgId,
        $status              = 'sent',
        $response_json       = $responseJson,
        $sent_by             = $user->id,
        $full_url            = $fullUrl
    );

    return response()->json([
        'status'   => 'success',
        'response' => $response
    ]);
}


    public function destroy($id)
    {
        $user   = \Sentinel::check();
        $role   = $user->roles()->first()->slug ?? null;
        $uid    = $user->id;

        $reg   = registeruser($uid);
        $orgId = $reg->organization;

        $row = SmsSetting::findOrFail($id);

        // ✅ root ya same org ke admin/sadmin only delete allowed
        if ($role === 'root' || (in_array($role, ['admin','sadmin']) && $row->organization_id == $orgId)) {
            $row->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Deleted!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'You are not allowed to delete this record!'
        ]);
    }
}
