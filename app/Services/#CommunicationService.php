<?php

namespace App\Services;

use App\Services\WhatsAppService;
use App\Services\SmsService;
use App\Services\IvrService;
use App\Helpers\LogWriterHelper;

class CommunicationService
{
    protected $whatsapp;
    protected $sms;
    protected $ivr;
    protected $logger;

    public function __construct(
        WhatsAppService $whatsapp,
        SmsService $sms,
        IvrService $ivr,
        LogWriterHelper $logger
    ) {
        $this->whatsapp = $whatsapp;
        $this->sms = $sms;
        $this->ivr = $ivr;
        $this->logger = $logger;
    }

    /**
     * Send WhatsApp
     */
    public function sendWhatsapp($lead, $template)
    {
        $response = $this->whatsapp->send($lead, $template);

        // log
        $this->logger->logCommunication([
            'lead_id' => $lead->id,
            'type' => 'whatsapp',
            'payload' => json_encode($template),
            'response' => json_encode($response),
        ]);

        return $response;
    }

    /**
     * Send SMS
     */
    public function sendSms($lead, $template)
    {
        $response = $this->sms->send($lead, $template);

        // log
        $this->logger->logCommunication([
            'lead_id' => $lead->id,
            'type' => 'sms',
            'payload' => json_encode($template),
            'response' => json_encode($response),
        ]);

        return $response;
    }

    /**
     * Initiate IVR Call
     */
    public function startIvrCall($lead, $template)
    {
        $response = $this->ivr->call($lead, $template);

        $this->logger->logCommunication([
            'lead_id' => $lead->id,
            'type' => 'ivr',
            'payload' => json_encode($template),
            'response' => json_encode($response),
        ]);

        return $response;
    }
}
