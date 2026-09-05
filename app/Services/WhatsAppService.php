<?php

namespace App\Services;

use App\WhatsappSetting;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send(int $orgId, string $mobile, string $name, string $message): array
    {
        $setting = WhatsappSetting::where('organization_id', $orgId)->first();

        if (!$setting || (int)($setting->status ?? 0) !== 1) {
            return [
                'success' => false,
                'provider' => null,
                'http_status' => null,
                'request' => [],
                'response_raw' => null,
                'error' => 'WhatsApp setting disabled or missing for this organization'
            ];
        }

        if (empty($setting->provider_url) || empty($setting->username) || empty($setting->password)) {
            return [
                'success' => false,
                'provider' => $setting->provider ?? null,
                'http_status' => null,
                'request' => [],
                'response_raw' => null,
                'error' => 'WhatsApp settings incomplete (provider_url/username/password missing)'
            ];
        }

        // Provider ka fixed payload
        $payload = [
            'user'     => $setting->username,
            'password' => $setting->password,
            'mobile'   => $mobile,
            'name'     => $name,
            'message'  => $message,
        ];

        try {
            $res = Http::asForm()
                ->timeout(20)
                ->post($setting->provider_url, $payload);

            $raw = $res->body();

            return [
                'success' => $res->successful(),
                'provider' => $setting->provider ?? 'whatsapp',
                'http_status' => $res->status(),
                'request' => [
                    'url' => $setting->provider_url,
                    'method' => 'POST',
                    'payload' => $payload
                ],
                'response_raw' => $raw,
                'error' => $res->successful() ? null : $raw,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'provider' => $setting->provider ?? 'whatsapp',
                'http_status' => null,
                'request' => [
                    'url' => $setting->provider_url,
                    'method' => 'POST',
                    'payload' => $payload
                ],
                'response_raw' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
