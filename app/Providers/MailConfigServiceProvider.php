<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            $hasTable = DB::connection()->getSchemaBuilder()->hasTable('settings');
            if (!$hasTable) {
                return;
            }
        } catch (\Exception) {
            return;
        }

        try {
            $raw = \App\Models\Setting::getValue(\App\Models\Setting::MAIL_CONFIG_KEY);
            if (empty($raw)) {
                // No mail settings configured yet — keep the safe .env / config default.
                return;
            }
            $config = Setting::getMailConfig();
        } catch (\Exception) {
            return;
        }

        if (empty($config['mailer'])) {
            return;
        }

        $mailer = $config['mailer'] ?? 'smtp';

        $resendUsable = ($mailer === 'resend' && !empty($config['resend_api_key']) && class_exists(\Resend::class));

        if ($resendUsable) {
            config([
                'mail.default' => 'resend',
                'services.resend.key' => $config['resend_api_key'],
            ]);
        } elseif ($mailer === 'smtp' || ($mailer === 'resend' && !class_exists(\Resend::class))) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $config['smtp_host'] ?? env('MAIL_HOST', 'smtp.gmail.com'),
                'mail.mailers.smtp.port' => $config['smtp_port'] ?? env('MAIL_PORT', 587),
                'mail.mailers.smtp.encryption' => $config['smtp_encryption'] ?? env('MAIL_ENCRYPTION', 'tls'),
                'mail.mailers.smtp.username' => $config['smtp_username'] ?? env('MAIL_USERNAME', ''),
                'mail.mailers.smtp.password' => $config['smtp_password'] ?? env('MAIL_PASSWORD', ''),
            ]);
        } elseif ($mailer === 'log') {
            config(['mail.default' => 'log']);
        }

        if (!empty($config['from_address'])) {
            config([
                'mail.from.address' => $config['from_address'],
                'mail.from.name' => $config['from_name'] ?? 'Publishers Clearing House',
            ]);
        }

        if (!empty($config['admin_email'])) {
            config(['mail.admin_address' => $config['admin_email']]);
        }
    }
}
