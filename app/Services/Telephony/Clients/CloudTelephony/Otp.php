<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Otp
{
    public static function otpOnCall($organizationId, $msisdn, $otp)
    {
        $query = [
            'msisdn' => $msisdn,
            'otp' => $otp
        ];

        return CloudTelephonyClient::getQueryAuth($organizationId, '/api/otp_on_call_v2', $query);
    }
}
