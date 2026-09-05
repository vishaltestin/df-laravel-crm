<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\IvrSetting;

class IvrPayloadMapper
{
    /**
     * Get provider settings dynamically from DB
     */
    private static function settings()
    {
        return IvrSetting::where('organization_id', orgId())->first();
    }

    /**
     * Build payload for initiating IVR call based on provider
     */
    public static function buildCallPayload($lead)
    {
        $s = self::settings();

        if (!$s || !$s->provider) {
            return ['error' => 'IVR provider not configured'];
        }

        // ---------------------------
        // PROVIDER : EXOTEL
        // ---------------------------
        if ($s->provider === 'exotel') {
            return [
                "From"        => $s->from_number,
                "To"          => $lead->mobile,
                "CallerId"    => $s->caller_id,
                "Url"         => $s->flow_url,               // DB value
                "CustomField" => $lead->id,                   // Lead tracking
                "callback_url" => route('automation.ivr-callback')
            ];
        }

        // ---------------------------
        // PROVIDER : TWILIO
        // ---------------------------
        if ($s->provider === 'twilio') {
            return [
                "to"        => $lead->mobile,
                "from"      => $s->from_number,
                "twiml_url" => $s->flow_url,               // TwiML URL from DB
                "lead_id"   => $lead->id,
                "callback_url" => route('automation.ivr-callback')
            ];
        }

        // ---------------------------
        // PROVIDER : ANY CUSTOM IVR
        // ---------------------------
        return [
            "mobile"   => $lead->mobile,
            "caller"   => $s->from_number,
            "flow"     => $s->flow_id,
            "lead_id"  => $lead->id,
            "callback" => route('automation.ivr-callback')
        ];
    }

    /**
     * Map callback from ANY provider to a standard format
     */
    public static function mapCallback(Request $request)
    {
        $provider = self::settings()->provider;

        // ---------------------------
        // EXOTEL CALLBACK
        // ---------------------------
        if ($provider === 'exotel') {
            return [
                'mobile'  => $request->input('To'),
                'dtmf'    => $request->input('Digits'),
                'call_id' => $request->input('CallSid'),
                'status'  => $request->input('CallStatus')
            ];
        }

        // ---------------------------
        // TWILIO CALLBACK
        // ---------------------------
        if ($provider === 'twilio') {
            return [
                'mobile'  => $request->input('To'),
                'dtmf'    => $request->input('Digits'),
                'call_id' => $request->input('CallSid'),
                'status'  => $request->input('CallStatus')
            ];
        }

        // ---------------------------
        // CUSTOM PROVIDER CALLBACK
        // ---------------------------
        return [
            'mobile'  => $request->input('mobile'),
            'dtmf'    => $request->input('dtmf'),
            'call_id' => $request->input('call_id'),
            'status'  => $request->input('status'),
        ];
    }
}
