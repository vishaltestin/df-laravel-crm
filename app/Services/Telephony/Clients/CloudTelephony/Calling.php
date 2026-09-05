<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Calling
{
    public static function callLogs($organizationId, $data = [])
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/call_list_v2', $data);
    }

    public static function routingDetails($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/getroutingdetail_V2', $data);
    }

    public static function liveCalls($organizationId)
    {
        return CloudTelephonyClient::getQueryAuth($organizationId, '/api/live_call_v2', []);
    }
}
