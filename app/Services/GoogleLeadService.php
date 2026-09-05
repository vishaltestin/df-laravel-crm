<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleLeadService
{
    public static function fetchLeads($accessToken, $customerId)
    {
        $url = "https://googleads.googleapis.com/v15/customers/{$customerId}/googleAds:search";

        $query = "
            SELECT
              lead_form_submission_data.lead_form_submission_fields
            FROM
              lead_form_submission_data
        ";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'developer-token' => env('GOOGLE_ADS_DEV_TOKEN')
        ])->post($url, [
            'query' => $query
        ]);

        return $response->json();
    }
}
