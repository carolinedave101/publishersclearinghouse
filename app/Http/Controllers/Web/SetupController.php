<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SetupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $expected = env('SETUP_TOKEN', 'pch-setup-default');

        if ($token !== $expected) {
            return response()->json(['error' => 'Invalid setup token.'], 403);
        }

        $results = [];
        $success = true;

        $results[] = $this->step('Generating app key', function () {
            if (config('app.key') && config('app.key') !== 'base64:') {
                return 'App key already set.';
            }
            Artisan::call('key:generate', ['--force' => true]);
            return trim(Artisan::output()) ?: 'App key generated.';
        });

        $results[] = $this->step('Checking database connection', function () {
            DB::connection()->getPdo();
            return 'Connected to ' . DB::connection()->getDatabaseName();
        });

        $results[] = $this->step('Running migrations', function () {
            Artisan::call('migrate', ['--force' => true]);
            return trim(Artisan::output());
        });

        if (app()->environment('local', 'production')) {
            $results[] = $this->step('Creating storage symlink', function () {
                $public = public_path('storage');
                if (File::exists($public) && File::isLink($public)) {
                    return 'Symlink already exists.';
                }
                if (File::exists($public) && File::isDirectory($public)) {
                    return 'Public storage directory already exists.';
                }
                try {
                    File::link(storage_path('app/public'), $public);
                    return 'Storage symlink created.';
                } catch (\Exception $e) {
                    if (File::isDirectory($public)) {
                        return 'Public storage directory exists (symlink not needed).';
                    }
                    return 'Could not create symlink: ' . $e->getMessage() . ' (not critical)';
                }
            });
        }

        $results[] = $this->step('Applying mail configuration', function () {
            try {
                $mailConfig = \App\Models\Setting::getMailConfig();
                if (!empty($mailConfig['mailer'])) {
                    return 'Mail config loaded (' . $mailConfig['mailer'] . ')';
                }
                $mailConfig = [
                    'mailer' => env('MAIL_MAILER', 'smtp'),
                    'smtp_host' => env('MAIL_HOST', 'smtp.stackmail.com'),
                    'smtp_port' => env('MAIL_PORT', 587),
                    'smtp_encryption' => env('MAIL_ENCRYPTION', 'tls'),
                    'smtp_username' => env('MAIL_USERNAME', ''),
                    'smtp_password' => env('MAIL_PASSWORD', ''),
                    'from_address' => env('MAIL_FROM_ADDRESS', 'winnersteam@publishersclearing.info'),
                    'from_name' => env('MAIL_FROM_NAME', 'Publishers Clearing House'),
                    'admin_email' => env('PCH_ADMIN_EMAIL', 'admin@pch.com'),
                ];
                \App\Models\Setting::setMailConfig($mailConfig);
                return 'Mail configuration saved from environment.';
            } catch (\Exception $e) {
                return 'Mail config skipped: ' . $e->getMessage();
            }
        });

        $results[] = $this->step('Caching config', function () {
            try {
                Artisan::call('config:cache', ['--force' => true]);
                return trim(Artisan::output()) ?: 'Config cached.';
            } catch (\Exception $e) {
                return 'Config cache skipped: ' . $e->getMessage();
            }
        });

        $results[] = $this->step('Caching routes', function () {
            try {
                Artisan::call('route:cache', ['--force' => true]);
                return trim(Artisan::output()) ?: 'Routes cached.';
            } catch (\Exception $e) {
                return 'Route cache skipped: ' . $e->getMessage();
            }
        });

        $results[] = $this->step('Caching views', function () {
            try {
                Artisan::call('view:cache');
                return trim(Artisan::output()) ?: 'Views cached.';
            } catch (\Exception $e) {
                return 'View cache skipped: ' . $e->getMessage();
            }
        });

        $hasErrors = false;
        foreach ($results as $r) {
            if ($r['status'] === 'error') {
                $hasErrors = true;
                break;
            }
        }

        return response()->json([
            'success' => !$hasErrors,
            'results' => $results,
            'message' => $hasErrors ? 'Setup completed with errors. Check results.' : 'Setup completed successfully!',
        ]);
    }

    private function step(string $label, callable $callback): array
    {
        try {
            $output = $callback();
            return ['step' => $label, 'status' => 'ok', 'output' => $output];
        } catch (\Throwable $e) {
            return ['step' => $label, 'status' => 'error', 'output' => $e->getMessage()];
        }
    }
}
