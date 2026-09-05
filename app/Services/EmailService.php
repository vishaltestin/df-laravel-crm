<?php

namespace App\Services;

use App\EmailSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EmailService
{
    /**
     * Send Email using organization SMTP settings
     */
    public function send(int $orgId, string $to, string $subject, string $htmlBody): array
    {
        $setting = EmailSetting::where('organization_id', $orgId)->first();

        if (!$setting || (int)($setting->status ?? 0) !== 1) {
            return [
                'success' => false,
                'provider' => null,
                'error' => 'Email setting disabled or missing for this organization'
            ];
        }

        // Required fields
        if (
            empty($setting->mail_host) ||
            empty($setting->mail_port) ||
            empty($setting->mail_username) ||
            empty($setting->mail_password)
        ) {
            return [
                'success' => false,
                'provider' => 'smtp',
                'error' => 'Email SMTP settings incomplete'
            ];
        }

        try {

            // ================================
            // 1) Set runtime mail config
            // ================================
            Config::set('mail.default', $setting->mail_mailer ?? 'smtp');

            Config::set('mail.mailers.smtp.host', $setting->mail_host);
            Config::set('mail.mailers.smtp.port', (int)$setting->mail_port);
            Config::set('mail.mailers.smtp.username', $setting->mail_username);
            Config::set('mail.mailers.smtp.password', $setting->mail_password);
            Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption ?: null);

            // SSL validation (mail_validatessl)
            // 0 = validate ssl, 1 = disable validate (as per your DB)
            if ((int)($setting->mail_validatessl ?? 0) === 1) {
                Config::set('mail.mailers.smtp.verify_peer', false);
                Config::set('mail.mailers.smtp.verify_peer_name', false);
                Config::set('mail.mailers.smtp.allow_self_signed', true);
            }

            // From details
            Config::set('mail.from.address', $setting->mail_from_address ?: $setting->mail_username);
            Config::set('mail.from.name', $setting->mail_from_name ?: 'LMS');

            // ================================
            // 2) Send email
            // ================================
            Mail::html($htmlBody, function ($message) use ($to, $subject, $setting) {
                $message->to($to)->subject($subject);

                if (!empty($setting->mail_from_address)) {
                    $message->from(
                        $setting->mail_from_address,
                        $setting->mail_from_name ?: 'LMS'
                    );
                }
            });

            return [
                'success' => true,
                'provider' => 'smtp',
                'error' => null
            ];

        } catch (\Throwable $e) {

            return [
                'success' => false,
                'provider' => 'smtp',
                'error' => $e->getMessage()
            ];
        }
    }
}
