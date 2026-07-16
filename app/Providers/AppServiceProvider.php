<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Winner;
use App\Services\ActivityLogger;
use App\Services\CodeGenerator;
use App\Services\EmailService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Give Filament a fresh, non-persistent component-cache path per
        // boot so newly added widgets/resources/relation managers are always
        // registered (avoids stale-cache bugs in dev & tests).
        if ($this->app->environment(['local', 'testing', 'development', 'staging'])) {
            $path = storage_path('framework/filament-cache-' . getmypid() . '-' . uniqid());
            $this->app['config']->set('filament.cache_path', $path);
        }

        $this->app->singleton(CodeGenerator::class, function () {
            return new CodeGenerator();
        });

        $this->app->singleton(ActivityLogger::class, function () {
            return new ActivityLogger();
        });

        $this->app->singleton(EmailService::class, function () {
            return new EmailService();
        });
    }

    public function boot(): void
    {
        if ($this->app->environment(['local', 'testing', 'development', 'staging'])) {
            // Disable Filament's component cache in non-prod so discovered
            // widgets/resources/relation managers are always re-registered
            // (avoids stale-cache bugs in dev and PHPUnit test suites).
            $this->app['config']->set('filament.cache_path', false);

            $path = base_path('bootstrap/cache/filament');
            if (is_dir($path)) {
                File::deleteDirectory($path);
            }
        }

        $this->ensureStorageLink();
        $this->applyMailConfig();

        View::composer([
            'pages.winner.*',
            'components.nav',
            'components.footer',
        ], function ($view) {
            $config = Setting::getWinnerFeaturesConfig();

            $winnerId = session('winner_id');
            if ($winnerId) {
                $winner = \App\Models\Winner::find($winnerId);
                if ($winner && $winner->features) {
                    $config = array_merge($config, $winner->features);
                }
            }

            $view->with('winnerConfig', $config);
        });
    }

    private function ensureStorageLink(): void
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        if (File::exists($linkPath)) {
            return;
        }

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                exec("mklink /J \"{$linkPath}\" \"{$targetPath}\"");
            } else {
                if (!File::isDirectory($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
                File::link($targetPath, $linkPath);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function applyMailConfig(): void
    {
        try {
            $mailConfig = Setting::getMailConfig();

            if (empty($mailConfig['mailer'])) {
                return;
            }

            $mailer = $mailConfig['mailer'];

            // Fallback from resend to smtp if Resend SDK not installed
            if ($mailer === 'resend' && !class_exists(\Resend::class)) {
                $mailer = 'smtp';
            }

            Config::set('mail.default', $mailer);

            if ($mailer === 'smtp') {
                Config::set('mail.mailers.smtp.host', $mailConfig['smtp_host'] ?? Config::get('mail.mailers.smtp.host'));
                Config::set('mail.mailers.smtp.port', $mailConfig['smtp_port'] ?? Config::get('mail.mailers.smtp.port'));
                Config::set('mail.mailers.smtp.encryption', $mailConfig['smtp_encryption'] ?? Config::get('mail.mailers.smtp.encryption'));
                Config::set('mail.mailers.smtp.username', $mailConfig['smtp_username'] ?? Config::get('mail.mailers.smtp.username'));
                Config::set('mail.mailers.smtp.password', $mailConfig['smtp_password'] ?? Config::get('mail.mailers.smtp.password'));
            }

            if ($mailer === 'resend' && !empty($mailConfig['resend_api_key'])) {
                Config::set('services.resend.key', $mailConfig['resend_api_key']);
            }

            if (!empty($mailConfig['from_address'])) {
                Config::set('mail.from.address', $mailConfig['from_address']);
            }
            if (!empty($mailConfig['from_name'])) {
                Config::set('mail.from.name', $mailConfig['from_name']);
            }
            if (!empty($mailConfig['admin_email'])) {
                Config::set('mail.admin_address', $mailConfig['admin_email']);
            }
        } catch (\Exception $e) {
            Log::warning('Could not apply mail config from settings: ' . $e->getMessage());
        }
    }
}
