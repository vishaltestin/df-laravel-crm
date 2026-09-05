<?php

namespace App\Console\Commands;

use App\EmailSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Lead;

class SendPendingEmail extends Command
{
    protected $signature = 'crm:send-pending-email {--limit=10}';
    protected $description = 'Send pending Email from email_logs using org SMTP settings';

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $logs = DB::table('email_logs')
            ->where('status', 'pending')
            ->where('attempts', '<', 3)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($logs->count() === 0) {
            $this->info("No pending email logs.");
            return 0;
        }

        foreach ($logs as $log) {

            DB::beginTransaction();

            try {

                $row = DB::table('email_logs')
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

                $smtp = EmailSetting::where('organization_id', $row->organization_id)
                    ->where('status', 1)
                    ->first();

                if (!$smtp) {
                    $this->markFailed($row->id, $row->communication_log_id, "Email SMTP settings missing");
                    DB::commit();
                    continue;
                }

                // Render subject + body
                $finalSubject = renderCommunicationTemplate($row->subject, $lead, $row->organization_id);
                $finalBody    = renderCommunicationTemplate($row->body, $lead, $row->organization_id);

                // attempt update
                DB::table('email_logs')->where('id', $row->id)->update([
                    'attempts'        => DB::raw('attempts+1'),
                    'last_attempt_at' => now(),
                    'updated_at'      => now(),
                ]);

                // Apply SMTP runtime config (per org)
                Config::set('mail.default', $smtp->mail_mailer ?? 'smtp');
                Config::set('mail.mailers.smtp.host', $smtp->mail_host);
                Config::set('mail.mailers.smtp.port', (int) $smtp->mail_port);
                Config::set('mail.mailers.smtp.username', $smtp->mail_username);
                Config::set('mail.mailers.smtp.password', $smtp->mail_password);
                Config::set('mail.mailers.smtp.encryption', $smtp->mail_encryption);
                Config::set('mail.from.address', $smtp->mail_from_address);
                Config::set('mail.from.name', $smtp->mail_from_name);

                // SEND
                Mail::html($finalBody, function ($message) use ($row, $finalSubject, $smtp) {
                    $message->to($row->to_email);
                    $message->subject($finalSubject);
                    $message->from($smtp->mail_from_address, $smtp->mail_from_name);
                });

                // SUCCESS
                DB::table('email_logs')->where('id', $row->id)->update([
                    'subject'        => $finalSubject,
                    'body'           => $finalBody,
                    'status'         => 'sent',
                    'sent_at'        => now(),
                    'response_message' => 'sent',
                    'provider'       => $smtp->mail_host,
                    'updated_at'     => now(),
                ]);

                if ($row->communication_log_id) {
                    DB::table('communication_logs')->where('id', $row->communication_log_id)->update([
                        'status'     => 'sent',
                        'sent_at'    => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->markFailed($log->id, $log->communication_log_id, $e->getMessage());
            }
        }

        $this->info("Email cron done. Processed: ".$logs->count());
        return 0;
    }

    private function markFailed($id, $commId, $msg)
    {
        DB::table('email_logs')->where('id', $id)->update([
            'status'           => 'failed',
            'response_message' => $msg,
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
