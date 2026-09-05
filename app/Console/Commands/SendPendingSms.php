<?php

namespace App\Console\Commands;

use App\Lead;
use App\SmsSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendPendingSms extends Command
{
    protected $signature = 'crm:send-pending-sms {--limit=20}';
    protected $description = 'Send pending SMS from sms_logs (org wise settings)';

    public function handle()
    {
        $limit = (int) $this->option('limit');

        // pending + retry condition
        $logs = DB::table('sms_logs')
            ->where('status', 'pending')
            ->where('attempts', '<', 3)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($logs->count() === 0) {
            $this->info("No pending SMS logs.");
            return 0;
        }

        foreach ($logs as $log) {

            DB::beginTransaction();

            try {

                // lock row to avoid double send
                $row = DB::table('sms_logs')
                    ->where('id', $log->id)
                    ->lockForUpdate()
                    ->first();

                if (!$row || $row->status !== 'pending') {
                    DB::rollBack();
                    continue;
                }

                $lead = Lead::find($row->lead_id);
                if (!$lead) {
                    $this->markSmsFailed($row->id, $row->communication_log_id, "Lead not found");
                    DB::commit();
                    continue;
                }

                // org settings
                $settings = SmsSetting::where('organization_id', $row->organization_id)->first();
                if (!$settings || !$settings->api_url) {
                    $this->markSmsFailed($row->id, $row->communication_log_id, "SMS settings missing");
                    DB::commit();
                    continue;
                }

                // render template
                $finalMessage = renderCommunicationTemplate($row->message, $lead, $row->organization_id);

                // build request params
                $params = [];

                // extra params (stored like: action=send-sms&from=ADTUIN)
                if (!empty($settings->extra_params)) {
                    parse_str($settings->extra_params, $extra);
                    $params = array_merge($params, $extra);
                }

                // dynamic params
                $params[$settings->mobile_param ?? 'to'] = $row->mobile;
                $params[$settings->message_param ?? 'sms'] = $finalMessage;

                // attempt update
                DB::table('sms_logs')->where('id', $row->id)->update([
                    'attempts'        => DB::raw('attempts+1'),
                    'last_attempt_at' => now(),
                    'updated_at'      => now(),
                ]);

                // SEND
                $http = Http::timeout(20);

                if (strtoupper($settings->http_method ?? 'GET') === 'POST') {
                    $response = $http->asForm()->post($settings->api_url, $params);
                } else {
                    $response = $http->get($settings->api_url, $params);
                }

                $rawBody = $response->body();

                // SUCCESS/FAIL decide
                // NOTE: provider response unknown -> basic check
                $isSuccess = $response->successful();

                if ($isSuccess) {
                    DB::table('sms_logs')->where('id', $row->id)->update([
                        'message'          => $finalMessage,
                        'status'           => 'sent',
                        'sent_at'          => now(),
                        'response_json'    => $rawBody,
                        'provider'         => $settings->provider,
                        'updated_at'       => now(),
                    ]);

                    if ($row->communication_log_id) {
                        DB::table('communication_logs')->where('id', $row->communication_log_id)->update([
                            'status'     => 'sent',
                            'sent_at'    => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    DB::table('sms_logs')->where('id', $row->id)->update([
                        'message'          => $finalMessage,
                        'status'           => 'failed',
                        'response_json'    => $rawBody,
                        'provider'         => $settings->provider,
                        'updated_at'       => now(),
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

                // mark failed but safe
                $this->markSmsFailed($log->id, $log->communication_log_id, $e->getMessage());
            }
        }

        $this->info("SMS cron done. Processed: ".$logs->count());
        return 0;
    }

    private function markSmsFailed($smsLogId, $commId, $msg)
    {
        DB::table('sms_logs')->where('id', $smsLogId)->update([
            'status'           => 'failed',
            'response_json'    => $msg,
            'updated_at'       => now(),
            'last_attempt_at'  => now(),
            'attempts'         => DB::raw('attempts+1'),
        ]);

        if ($commId) {
            DB::table('communication_logs')->where('id', $commId)->update([
                'status'     => 'failed',
                'updated_at' => now(),
            ]);
        }
    }
}
