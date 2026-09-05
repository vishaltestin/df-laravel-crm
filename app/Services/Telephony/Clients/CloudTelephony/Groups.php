<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Groups
{
    public static function create($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/createcallgroup', $data);
    }

    public static function update($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/updategroup_v2', $data);
    }

    public static function delete($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/deletegroup', $data);
    }

    public static function list($organizationId, $data = [])
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/getgrouplist_v2', $data);
    }

    public static function getById($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/getgroupbyid_v2', $data);
    }

    public static function schedule($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/getgroupschedule', $data);
    }
}
