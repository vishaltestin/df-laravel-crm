<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogWriter
{
    /**
     * Write logs to specific log tables + main automation log
     *
     * @param string $channel sms|whatsapp|ivr
     * @param array  $data
     * @return void
     */
    public static function write(string $channel, array $data)
    {
        try {
            // ---------------------------------------------
            // 1️⃣ Every log MUST have organization_id
            // ---------------------------------------------
            $orgId = $data['organization_id'] ?? null;

            if (!$orgId) {
                Log::error("LogWriter missing organization_id", $data);
                return;
            }

            // ---------------------------------------------
            // 2️⃣ Channel specific log routing
            // ---------------------------------------------
            switch ($channel) {

                // ========== SMS LOGS TABLE ==========
                case 'sms':
                    DB::table('sms_logs')->insert([
                        'organization_id' => $orgId,
                        'mobile'          => $data['mobile'] ?? null,
                        'message'         => $data['message'] ?? null,
                        'provider'        => $data['provider'] ?? null,
                        'api_payload'     => json_encode($data['payload'] ?? []),
                        'api_response'    => json_encode($data['response'] ?? []),
                        'status'          => $data['status'] ?? 'sent',
                        'created_at'      => now(),
                    ]);
                    break;

                // ========== WHATSAPP LOGS TABLE ==========
                case 'whatsapp':
                    DB::table('whatsapp_logs')->insert([
                        'organization_id' => $orgId,
                        'mobile'          => $data['mobile'] ?? null,
                        'template'        => $data['template'] ?? null,
                        'payload_json'    => json_encode($data['payload'] ?? []),
                        'response_json'   => json_encode($data['response'] ?? []),
                        'status'          => $data['status'] ?? 'sent',
                        'created_at'      => now(),
                    ]);
                    break;

                // ========== IVR LOGS TABLE ==========
                case 'ivr':
                    DB::table('ivr_logs')->insert([
                        'organization_id' => $orgId,
                        'mobile'          => $data['mobile'] ?? null,
                        'call_id'         => $data['call_id'] ?? null,
                        'dtmf_input'      => $data['dtmf'] ?? null,
                        'event'           => $data['event'] ?? null,
                        'payload'         => json_encode($data['payload'] ?? []),
                        'response'        => json_encode($data['response'] ?? []),
                        'status'          => $data['status'] ?? 'initiated',
                        'created_at'      => now(),
                    ]);
                    break;
            }

            // ---------------------------------------------
            // 3️⃣ Master Automation Log (Common Table)
            // ---------------------------------------------
            DB::table('automation_logs')->insert([
                'organization_id' => $orgId,
                'type'            => $channel,
                'reference_id'    => $data['call_id'] ?? $data['mobile'] ?? null,
                'payload_json'    => json_encode($data['payload'] ?? []),
                'response_json'   => json_encode($data['response'] ?? []),
                'status'          => $data['status'] ?? 'processed',
                'created_at'      => now(),
            ]);

        } catch (\Exception $e) {

            // Even if DB fails, we still store in Laravel error log
            Log::error("LogWriter Exception", [
                'error' => $e->getMessage(),
                'data'  => $data
            ]);
        }
    }
}
