<?php

namespace App\Services;

use App\WhatsappSetting;   // DB se settings
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $settings;

    public function __construct()
    {
        // ⚠️ Yeh setting database se dynamic aayegi
        $this->settings = WhatsappSetting::where('organization_id', orgId())->first();
    }

    /**
     * Send WhatsApp Message
     */
    public function send($lead, $template)
    {
        if (!$this->settings || !$this->settings->api_key) {
            return [
                'status' => false,
                'message' => 'WhatsApp API settings missing'
            ];
        }

        // ===============================
        // 1. Prepare message dynamically
        // ===============================
        $message = $this->replaceVariables($template['message'], $lead);

        // ===============================
        // 2. API request – DEMO provider
        // ===============================
        $payload = [
            "apikey"   => $this->settings->api_key,
            "sender"   => $this->settings->sender_id,
            "to"       => $lead->mobile,
            "message"  => $message,
            "template" => $template['title'],
        ];

        // ⚠️ Ye API URL bhi DB se aayega WhatsAppSetting table se
        $response = Http::post($this->settings->api_url, $payload);

        return [
            'status' => $response->successful(),
            'req'    => $payload,
            'res'    => $response->json()
        ];
    }

    /**
     * Replace variables {lead_name}, {lead_id}, etc.
     */
    protected function replaceVariables($text, $lead)
    {
        $search = [
            '{customer_name}',
            '{customer_mobile}',
            '{lead_id}',
            '{lead_source}',
            '{assigned_user}',
        ];

        $replace = [
            $lead->name,
            $lead->mobile,
            $lead->id,
            optional($lead->source)->name,
            optional($lead->assignedUser)->first_name,
        ];

        return str_replace($search, $replace, $text);
    }
}
