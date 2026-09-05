<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Security
{
    public static function block($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/block_number', $data);
    }

    public static function unblock($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/unblock_number', $data);
    }
}
