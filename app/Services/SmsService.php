<?php

namespace App\Services;

use App\SmsSetting;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public function send(int $orgId, string $mobile, string $message): array
    {
        $setting = SmsSetting::where('organization_id', $orgId)->first();

        if (!$setting || (int)($setting->status ?? 0) !== 1) {
            return [
                'success' => false,
                'provider' => null,
                'http_status' => null,
                'request' => [],
                'response_raw' => null,
                'error' => 'SMS setting disabled or missing for this organization'
            ];
        }

        if (
            empty($setting->api_url) ||
            empty($setting->http_method) ||
            empty($setting->mobile_param) ||
            empty($setting->message_param)
        ) {
            return [
                'success' => false,
                'provider' => $setting->provider ?? null,
                'http_status' => null,
                'request' => [],
                'response_raw' => null,
                'error' => 'SMS settings incomplete (api_url/http_method/mobile_param/message_param missing)'
            ];
        }

        // ===============================
        // Base Payload
        // ===============================
        $payload = [];

        // mobile + message dynamic param
        $payload[$setting->mobile_param]  = $mobile;
        $payload[$setting->message_param] = $message;

        // API Key (some providers accept apikey, some api_key)
        if (!empty($setting->api_key)) {
            $payload['apikey'] = $setting->api_key;
        }

        // route optional
        if (!empty($setting->route)) {
            $payload['route'] = $setting->route;
        }

        // sender_id optional
        if (!empty($setting->sender_id)) {
            $payload['sender'] = $setting->sender_id;
        }

        // ===============================
        // extra_params (query string)
        // example: action=send-sms&from=ADTUIN
        // ===============================
        if (!empty($setting->extra_params)) {
            parse_str($setting->extra_params, $extraArr);

            if (is_array($extraArr)) {
                foreach ($extraArr as $k => $v) {
                    $payload[$k] = $v;
                }
            }
        }

        $method = strtoupper(trim($setting->http_method));

        try {

            if ($method === 'GET') {
                $res = Http::timeout(15)->get($setting->api_url, $payload);
            } else {
                // default POST
                $res = Http::asForm()->timeout(15)->post($setting->api_url, $payload);
            }

            $raw = $res->body();

            return [
                'success' => $res->successful(),
                'provider' => $setting->provider ?? 'sms',
                'http_status' => $res->status(),
                'request' => [
                    'url' => $setting->api_url,
                    'method' => $method,
                    'payload' => $payload
                ],
                'response_raw' => $raw,
                'error' => $res->successful() ? null : $raw,
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'provider' => $setting->provider ?? 'sms',
                'http_status' => null,
                'request' => [
                    'url' => $setting->api_url,
                    'method' => $method,
                    'payload' => $payload
                ],
                'response_raw' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
