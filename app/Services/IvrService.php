<?php

namespace App\Services;

use App\IvrSetting;     // DB se IVR config aayegi
use App\Lead;    
use Illuminate\Support\Facades\Http;

class IvrService
{
    protected $settings;

    public function __construct()
    {
        // ⚠ Database se IVR settings load
        $this->settings = IvrSetting::where('organization_id', orgId())->first();
    }

    /**
     * Trigger outbound IVR call
     */
    public function callLead(Lead $lead)
    {
        if (!$this->isConfigured()) {
            return [
                'status' => false,
                'message' => 'IVR settings are not configured.'
            ];
        }

        // =============================
        // 1. CALL PAYLOAD BUILD
        // =============================
        $payload = [
            "api_key"    => $this->settings->api_key,    // DB se
            "caller_id"  => $this->settings->caller_id,  // DB se
            "flow_id"    => $this->settings->flow_id,    // DB se
            "phone"      => $lead->mobile,
            "lead_id"    => $lead->id,
            "variables"  => [
                "name"  => $lead->name,
                "city"  => $lead->city ?? '',
            ],
            "callback_url" => route('ivr.callback') // callback hum banayenge
        ];

        // =============================
        // 2. MAKE API CALL (Dummy)
        // =============================
        $response = Http::post($this->settings->api_url, $payload);

        // =============================
        // 3. RETURN RESULT + Log usage
        // =============================
        return [
            'status' => $response->successful(),
            'request' => $payload,
            'response' => $response->json()
        ];
    }

    /**
     * Validate settings exist
     */
    private function isConfigured()
    {
        return $this->settings 
            && $this->settings->api_url
            && $this->settings->api_key
            && $this->settings->caller_id;
    }

    /**
     * Handle IVR Callback
     * (AutomationRawLeadController me use hoga)
     */
    public function handleCallback($payload)
    {
        // Callback example payload (Dummy)
        /**
         * [
         *   "lead_id" => 22,
         *   "status"  => "answered | failed | no_response",
         *   "digits"  => "1/2/3"
         * ]
         */

        $lead = Lead::find($payload['lead_id'] ?? null);

        if (!$lead) {
            return ['status'=>false, 'message'=>'Lead not found'];
        }

        // -------------------------------
        // IVR LOG WRITE (hum next file me LogWriter denge)
        // -------------------------------

        ivr_log_write([
            'lead_id' => $lead->id,
            'status'  => $payload['status'],
            'response_digits' => $payload['digits'] ?? null
        ]);

        // -------------------------------
        // IVR KE RESPONSE SE AUTOMATION  
        // -------------------------------
        if (($payload['digits'] ?? null) === "1") {
            // Lead confirmed
            $lead->update(['ivr_verified' => 1]);
        } 
        elseif (($payload['digits'] ?? null) === "2") {
            // Lead rejected
            $lead->update(['ivr_verified' => 0]);
        }

        return ['status'=>true];
    }
}
