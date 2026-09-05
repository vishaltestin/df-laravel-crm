<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Members
{
    public static function add($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/addmember_v2', $data);
    }

    public static function update($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/updatemember_v2', $data);
    }

    public static function delete($organizationId, $data)
    {
        // doc says BODY param
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/deletemember_v2', $data);
    }

    public static function list($organizationId, $data = [])
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/getmemberlist_V2', $data);
    }

    public static function analysis($organizationId, $data = [])
    {
        // doc: POST .../api/member_analysis_v2?authcode=<authcode>
        // but easiest is header-based (same auth)
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/member_analysis_v2', $data);
    }
}
