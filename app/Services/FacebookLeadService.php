<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\LeadIntegrationCredential;

class FacebookLeadService
{
    public static function fetchLead($organizationId, $leadgenId)
    {
        // Page access token DB se
        $cred = LeadIntegrationCredential::where([
            'organization_id' => $organizationId,
            'provider'        => 'facebook',
            'key'             => 'page_access_token'
        ])->first();

        if (!$cred) return null;

        try {
            $accessToken = Crypt::decryptString($cred->value);
        } catch (\Exception $e) {
            $accessToken = $cred->value;
        }

        $url = "https://graph.facebook.com/v19.0/{$leadgenId}";

        $response = Http::get($url, [
            'access_token' => $accessToken
        ]);

        if (!$response->ok()) return null;

        $data = $response->json();

        $lead = [
            'name'   => '',
            'email'  => '',
            'mobile' => ''
        ];

        foreach ($data['field_data'] ?? [] as $field) {
            if ($field['name'] === 'full_name') {
                $lead['name'] = $field['values'][0] ?? '';
            }
            if (in_array($field['name'], ['phone_number','mobile'])) {
                $lead['mobile'] = $field['values'][0] ?? '';
            }
            if ($field['name'] === 'email') {
                $lead['email'] = $field['values'][0] ?? '';
            }
        }

        return $lead;
    }
}
