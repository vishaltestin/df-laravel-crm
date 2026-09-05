<?php

namespace App\Services;

use App\SmsLog;
use App\SmsSetting;
use Illuminate\Support\Facades\Http;

class SmsService
{
    /**
     * Send SMS (Unified function)
     */
    public static function send($mobile, $message, $leadId = null, $orgId = null, $userId = null)
    {
        try {
            // Load SMS provider settings
            $settings = SmsSetting::where('organization_id', $orgId)->first();

            if (!$settings) {
                return self::logFailure([
                    'mobile' => $mobile,
                    'message' => $message,
                    'lead_id' => $leadId,
                    'organization_id' => $orgId,
                    'sent_by' => $userId,
                    'error' => 'SMS Provider settings not found!'
                ]);
            }

            // Prepare payload based on provider (demo)
            $payload = [
                'apikey'    => $settings->api_key,
                'sender'    => $settings->sender_id,
                'to'        => $mobile,
                'message'   => $message,
                'route'     => $settings->route,
                'template'  => $settings->template_id
            ];

            // Demo API → replace with actual provider later
            $response = Http::post('https://sms-provider-demo.com/send', $payload);

            // Log success
            return self::logSuccess([
                'mobile'        => $mobile,
                'message'       => $message,
                'lead_id'       => $leadId,
                'organization_id' => $orgId,
                'sent_by'       => $userId,
                'provider'      => $settings->provider,
                'provider_message_id' => $response->json('message_id'),
                'response_json' => $response->body(),
            ]);
        } catch (\Exception $e) {

            return self::logFailure([
                'mobile' => $mobile,
                'message' => $message,
                'lead_id' => $leadId,
                'organization_id' => $orgId,
                'sent_by' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log success
     */
    private static function logSuccess($data)
    {
        SmsLog::create([
            'organization_id' => $data['organization_id'],
            'lead_id'         => $data['lead_id'],
            'mobile'          => $data['mobile'],
            'message'         => $data['message'],
            'provider'        => $data['provider'],
            'provider_message_id' => $data['provider_message_id'],
            'status'          => 'success',
            'response_json'   => $data['response_json'],
            'sent_by'         => $data['sent_by']
        ]);

        return true;
    }

    /**
     * Log failure
     */
    private static function logFailure($data)
    {
        SmsLog::create([
            'organization_id' => $data['organization_id'],
            'lead_id'         => $data['lead_id'],
            'mobile'          => $data['mobile'],
            'message'         => $data['message'],
            'provider'        => $data['provider'] ?? 'unknown',
            'provider_message_id' => null,
            'status'          => 'failed',
            'response_json'   => $data['error'],
            'sent_by'         => $data['sent_by']
        ]);

        return false;
    }
}
