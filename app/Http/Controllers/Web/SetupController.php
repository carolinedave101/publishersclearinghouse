<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $expected = config('app.setup_token', env('SETUP_TOKEN', 'dev-setup-token'));

        if ($token !== $expected) {
            return response()->json(['error' => 'Invalid setup token.'], 403);
        }

        $results = [];

        $results[] = $this->step('Generating app key', function () {
            $key = config('app.key');
            if ($key && $key !== 'base64:' && strlen($key) >= 16) {
                return 'App key already set.';
            }
            Artisan::call('key:generate', ['--force' => true]);
            $output = trim(Artisan::output());
            if (str_contains($output, 'APP_KEY')) {
                return 'App key generated: ' . $output;
            }
            $key = config('app.key');
            if ($key && $key !== 'base64:' && strlen($key) >= 16) {
                return 'App key set (' . substr($key, 0, 10) . '...).';
            }
            throw new \RuntimeException(
                'Failed to generate APP_KEY. Edit pch/.env and set APP_KEY=' .
                'base64:' . base64_encode(random_bytes(32))
            );
        });

        $results[] = $this->step('Checking database connection', function () {
            DB::connection()->getPdo();
            return 'Connected to ' . DB::connection()->getDatabaseName();
        });

        $results[] = $this->step('Running migrations', function () {
            Artisan::call('migrate', ['--force' => true]);
            return trim(Artisan::output());
        });

        $results[] = $this->step('Creating storage symlink', function () {
            $public = public_path('storage');
            $target = storage_path('app/public');
            if (File::exists($public) && is_link($public)) {
                return 'Symlink already exists.';
            }
            if (File::exists($public) && File::isDirectory($public)) {
                return 'Public storage directory already exists.';
            }
            if (!File::isDirectory($target)) {
                File::makeDirectory($target, 0755, true);
            }
            try {
                File::link($target, $public);
                return 'Storage symlink created.';
            } catch (\Exception $e) {
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("mklink /J \"{$public}\" \"{$target}\"");
                    if (File::exists($public)) {
                        return 'Storage symlink created (junction).';
                    }
                }
                File::copyDirectory($target, $public);
                return 'Storage directory copied (symlink not available).';
            }
        });

        $results[] = $this->step('Checking storage writability', function () {
            $paths = [
                storage_path('logs'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views'),
            ];
            $unwritable = [];
            foreach ($paths as $path) {
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0755, true);
                }
                if (!is_writable($path)) {
                    $unwritable[] = str_replace(base_path(), '', $path);
                }
            }
            if (!empty($unwritable)) {
                throw new \RuntimeException(
                    'Storage paths not writable: ' . implode(', ', $unwritable)
                    . '. Set permissions to 755 or 777.'
                );
            }
            return 'All storage paths writable.';
        });

        $results[] = $this->step('Syncing admin credentials', function () {
            $email = env('PCH_ADMIN_EMAIL', 'admin@pch.com');
            $password = env('PCH_ADMIN_PASSWORD', 'password');
            $name = env('PCH_ADMIN_NAME', 'Super Admin');

            $user = User::where('is_super_admin', true)->first();
            if (!$user) {
                $user = User::where('email', $email)->first();
            }

            if ($user) {
                $changed = [];
                if ($user->email !== $email) {
                    $user->email = $email;
                    $changed[] = 'email';
                }
                if (!Hash::check($password, $user->password)) {
                    $user->password = Hash::make($password);
                    $changed[] = 'password';
                }
                if ($user->name !== $name) {
                    $user->name = $name;
                    $changed[] = 'name';
                }
                $user->is_super_admin = true;
                $user->is_admin = true;
                $user->role = User::ROLE_ADMIN;
                $user->save();

                if (empty($changed)) {
                    return 'Admin credentials unchanged (' . $email . ').';
                }
                return 'Admin ' . implode(', ', $changed) . ' updated (' . $email . ').';
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'is_super_admin' => true,
                'role' => User::ROLE_ADMIN,
            ]);

            return 'Admin user created (' . $email . ').';
        });

        $results[] = $this->step('Applying mail configuration', function () {
            try {
                $mailConfig = \App\Models\Setting::getMailConfig();
                if (!empty($mailConfig['mailer'])) {
                    return 'Mail config loaded (' . $mailConfig['mailer'] . ')';
                }
                $mailConfig = [
                    'mailer' => env('MAIL_MAILER', 'smtp'),
                    'smtp_host' => env('MAIL_HOST', 'smtp.stackmail.com'),
                    'smtp_port' => env('MAIL_PORT', 465),
                    'smtp_encryption' => env('MAIL_ENCRYPTION', 'ssl'),
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
            'message' => $hasErrors
                ? 'Setup completed with errors. Check each step above.'
                : 'Setup completed successfully! The portal is ready.',
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
