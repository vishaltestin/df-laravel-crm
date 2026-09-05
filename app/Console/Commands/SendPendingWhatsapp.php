<?php

namespace App\Console\Commands;

use App\Lead;
use App\WhatsappSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendPendingWhatsapp extends Command
{
    protected $signature = 'crm:send-pending-whatsapp {--limit=20}';
    protected $description = 'Send pending WhatsApp from whatsapp_logs';

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $logs = DB::table('whatsapp_logs')
            ->where('status', 'pending')
            ->where('attempts', '<', 3)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($logs->count() === 0) {
            $this->info("No pending WhatsApp logs.");
            return 0;
        }

        foreach ($logs as $log) {

            DB::beginTransaction();

            try {

                $row = DB::table('whatsapp_logs')
                    ->where('id', $log->id)
                    ->lockForUpdate()
                    ->first();

                if (!$row || $row->status !== 'pending') {
                    DB::rollBack();
                    continue;
                }

                $lead = Lead::find($row->lead_id);
                if (!$lead) {
                    $this->markFailed($row->id, $row->communication_log_id, "Lead not found");
                    DB::commit();
                    continue;
                }

                $settings = WhatsappSetting::where('organization_id', $row->organization_id)->first();
                if (!$settings || !$settings->provider_url) {
                    $this->markFailed($row->id, $row->communication_log_id, "WhatsApp settings missing");
                    DB::commit();
                    continue;
                }

                $finalMessage = renderCommunicationTemplate($row->message, $lead, $row->organization_id);

                // attempt update
                DB::table('whatsapp_logs')->where('id', $row->id)->update([
                    'attempts'        => DB::raw('attempts+1'),
                    'last_attempt_at' => now(),
                    'updated_at'      => now(),
                ]);

                // Provider API (DigitalFueled fixed)
                $payload = [
                    'user'     => $settings->username ?? 'df001#',
                    'password' => $settings->password ?? 'df#123',
                    'mobile'   => $row->mobile,
                    'name'     => $lead->name ?? '',
                    'message'  => $finalMessage,
                ];

                $response = Http::timeout(25)->asForm()->post($settings->provider_url, $payload);

                $raw = $response->body();
                $isSuccess = $response->successful();

                if ($isSuccess) {

                    DB::table('whatsapp_logs')->where('id', $row->id)->update([
                        'message'       => $finalMessage,
                        'status'        => 'sent',
                        'sent_at'       => now(),
                        'response_json' => $raw,
                        'provider'      => $settings->provider,
                        'updated_at'    => now(),
                    ]);

                    if ($row->communication_log_id) {
                        DB::table('communication_logs')->where('id', $row->communication_log_id)->update([
                            'status'     => 'sent',
                            'sent_at'    => now(),
                            'updated_at' => now(),
                        ]);
                    }

                } else {

                    DB::table('whatsapp_logs')->where('id', $row->id)->update([
                        'message'       => $finalMessage,
                        'status'        => 'failed',
                        'response_json' => $raw,
                        'provider'      => $settings->provider,
                        'updated_at'    => now(),
                    ]);

                    if ($row->communication_log_id) {
                        DB::table('communication_logs')->where('id', $row->communication_log_id)->update([
                            'status'     => 'failed',
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->markFailed($log->id, $log->communication_log_id, $e->getMessage());
            }
        }

        $this->info("WhatsApp cron done. Processed: ".$logs->count());
        return 0;
    }

    private function markFailed($id, $commId, $msg)
    {
        DB::table('whatsapp_logs')->where('id', $id)->update([
            'status'          => 'failed',
            'response_json'   => $msg,
            'updated_at'      => now(),
            'last_attempt_at' => now(),
            'attempts'        => DB::raw('attempts+1'),
        ]);

        if ($commId) {
            DB::table('communication_logs')->where('id', $commId)->update([
                'status'     => 'failed',
                'updated_at' => now(),
            ]);
        }
    }
}
