<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

use Illuminate\Support\Facades\Http;

class Auth
{
    public static function signin($baseUrl, $email, $pwd)
    {
        $url = rtrim($baseUrl, '/') . '/api_v2/signin';

        try {
            $res = Http::timeout(25)
                ->withHeaders([
                    'email' => $email,
                    'pwd' => $pwd
                ])
                ->post($url, []);

            return [
                'ok' => $res->successful(),
                'status_code' => $res->status(),
                'data' => $res->json(),
                'raw' => $res->body()
            ];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public static function profileBilling($organizationId, $data = [])
    {
        return CloudTelephonyClient::postHeaderAuth($organizationId, '/api/profile_billing_v2', $data);
    }
}
