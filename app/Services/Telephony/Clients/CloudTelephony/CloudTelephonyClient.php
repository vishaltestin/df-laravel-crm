<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

use App\IvrSetting;
use Illuminate\Support\Facades\Http;

class CloudTelephonyClient
{
    public static function getSetting($organizationId)
    {
        return IvrSetting::where('organization_id', $organizationId)
            ->where('status', 1)
            ->first();
    }

    public static function baseUrl($organizationId)
    {
        $s = self::getSetting($organizationId);
        return $s ? rtrim($s->base_url, '/') : null;
    }

    public static function authcode($organizationId)
    {
        $s = self::getSetting($organizationId);
        return $s ? $s->authcode : null;
    }

    public static function defaultDeskphone($organizationId)
    {
        $s = self::getSetting($organizationId);
        return $s ? $s->default_deskphone : null;
    }

    public static function ensureConfigured($organizationId)
    {
        $s = self::getSetting($organizationId);

        if (!$s) {
            return [false, 'IVR setting not found or inactive.'];
        }

        if (!$s->base_url || !$s->authcode) {
            return [false, 'Base URL / Authcode missing in IVR settings.'];
        }

        return [true, null];
    }

    public static function postHeaderAuth($organizationId, $endpoint, $payload = [])
    {
        [$ok, $msg] = self::ensureConfigured($organizationId);
        if (!$ok) {
            return ['ok' => false, 'message' => $msg];
        }

        $url = self::baseUrl($organizationId) . $endpoint;

        try {
            $res = Http::timeout(25)
                ->withHeaders([
                    'authcode' => self::authcode($organizationId)
                ])
                ->post($url, $payload);

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

    public static function getQueryAuth($organizationId, $endpoint, $query = [])
    {
        [$ok, $msg] = self::ensureConfigured($organizationId);
        if (!$ok) {
            return ['ok' => false, 'message' => $msg];
        }

        $query['authcode'] = self::authcode($organizationId);

        $url = self::baseUrl($organizationId) . $endpoint;

        try {
            $res = Http::timeout(25)->get($url, $query);

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

    public static function postWithFilesHeaderAuth($organizationId, $endpoint, $fields = [], $files = [])
    {
        [$ok, $msg] = self::ensureConfigured($organizationId);
        if (!$ok) {
            return ['ok' => false, 'message' => $msg];
        }

        $url = self::baseUrl($organizationId) . $endpoint;

        try {
            $http = Http::timeout(60)->withHeaders([
                'authcode' => self::authcode($organizationId)
            ]);

            // attach files
            foreach ($files as $fileKey => $fileInfo) {
                // $fileInfo = ['path' => '...', 'name' => '...']
                $http = $http->attach(
                    $fileKey,
                    file_get_contents($fileInfo['path']),
                    $fileInfo['name'] ?? basename($fileInfo['path'])
                );
            }

            $res = $http->post($url, $fields);

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
}
