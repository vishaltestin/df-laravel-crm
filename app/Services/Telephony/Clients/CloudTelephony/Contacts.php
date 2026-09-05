<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Contacts
{
    public static function add($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/savecontact_v2', $data);
    }

    public static function edit($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/editContact_v2', $data);
    }

    public static function delete($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/deleteContact', $data);
    }

    public static function list($organizationId, $data = [])
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/contact_list_v2', $data);
    }
}
