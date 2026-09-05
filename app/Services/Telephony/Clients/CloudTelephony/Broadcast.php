<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class Broadcast
{
    public static function createCampaign($organizationId, $fields, $promptFilePath, $dataFilePath)
    {
        $files = [
            'prompt_file' => [
                'path' => $promptFilePath,
                'name' => basename($promptFilePath)
            ],
            'data_file' => [
                'path' => $dataFilePath,
                'name' => basename($dataFilePath)
            ]
        ];

        return CloudTelephonyClient::postWithFilesHeaderAuth(
            $organizationId,
            '/api/create_obd_campaign_v2',
            $fields,
            $files
        );
    }

    public static function logs($organizationId, $data)
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/obdlogs_v2', $data);
    }
}
