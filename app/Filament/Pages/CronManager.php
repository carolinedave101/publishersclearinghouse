<?php

namespace App\Filament\Pages;

use App\Models\ScheduledTask;
use App\Models\EmailCampaign;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CronManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.cron-manager';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewScheduledTasks() ?? false;
    }

    public array $tasks = [];
    public array $stats = [];
    public string $logPreview = '';
    public ?string $cronUrl = null;
    public ?string $cronToken = null;

    public function mount(): void
    {
        $this->loadStats();
        $this->loadTasks();
        $this->loadLog();
        $this->cronToken = Setting::getValue('cron_trigger_token');
        if (!$this->cronToken) {
            $this->cronToken = Str::random(32);
            Setting::setValue('cron_trigger_token', $this->cronToken);
        }
        $this->cronUrl = url('/cron/trigger?token=' . $this->cronToken);
    }

    public function loadStats(): void
    {
        $queueCount = DB::table('jobs')->count();
        $failedCount = DB::table('failed_jobs')->count();
        $activeCampaigns = EmailCampaign::whereIn('status', ['sending', 'draft'])->count();
        $totalCampaigns = EmailCampaign::count();
        $totalSent = EmailCampaign::sum('sent_count');
        $totalFailed = EmailCampaign::sum('failed_count');

        $this->stats = [
            'queue_count' => $queueCount,
            'failed_count' => $failedCount,
            'active_campaigns' => $activeCampaigns,
            'total_campaigns' => $totalCampaigns,
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
        ];
    }

    public function loadTasks(): void
    {
        $this->tasks = ScheduledTask::orderBy('id')->get()->toArray();
    }

    public function loadLog(): void
    {
        $logPath = storage_path('logs/queue-worker.log');
        if (file_exists($logPath)) {
            $lines = file($logPath);
            $this->logPreview = implode('', array_slice($lines, -50));
        } else {
            $this->logPreview = '(no log file yet)';
        }
    }

    public function toggleTask(int $taskId, bool $enabled): void
    {
        $task = ScheduledTask::find($taskId);
        if ($task) {
            $task->update(['is_enabled' => $enabled]);
            Notification::make()
                ->title(($enabled ? 'Enabled' : 'Disabled') . ': ' . $task->description)
                ->success()
                ->send();
        }
        $this->loadTasks();
    }

    public function updateFrequency(int $taskId, string $frequency, ?string $cronExpression = null): void
    {
        $task = ScheduledTask::find($taskId);
        if ($task) {
            $task->update([
                'frequency' => $frequency,
                'cron_expression' => $frequency === 'custom' ? $cronExpression : null,
            ]);
            Notification::make()
                ->title('Frequency updated for: ' . $task->description)
                ->success()
                ->send();
        }
        $this->loadTasks();
    }

    public function runNow(int $taskId): void
    {
        $task = ScheduledTask::find($taskId);
        if (!$task) return;

        try {
            Artisan::call($task->command);
            $output = Artisan::output();
            $task->update([
                'last_run_at' => now(),
                'last_output' => substr($output, 0, 5000),
            ]);
            Notification::make()
                ->title('Executed: ' . $task->description)
                ->body(trim($output) ?: 'Completed (no output)')
                ->success()
                ->send();
        } catch (\Exception $e) {
            $task->update([
                'last_run_at' => now(),
                'last_output' => 'Error: ' . $e->getMessage(),
            ]);
            Notification::make()
                ->title('Failed: ' . $task->description)
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
        $this->loadTasks();
        $this->loadStats();
        $this->loadLog();
    }

    public function createTask(string $command, string $description, string $frequency, ?string $cronExpression = null): void
    {
        ScheduledTask::create([
            'command' => $command,
            'description' => $description,
            'frequency' => $frequency,
            'cron_expression' => $frequency === 'custom' ? $cronExpression : null,
            'is_enabled' => true,
        ]);
        Notification::make()
            ->title('Task created: ' . $description)
            ->success()
            ->send();
        $this->loadTasks();
    }

    public function deleteTask(int $taskId): void
    {
        $task = ScheduledTask::find($taskId);
        if ($task) {
            $desc = $task->description;
            $task->delete();
            Notification::make()
                ->title('Deleted: ' . $desc)
                ->success()
                ->send();
        }
        $this->loadTasks();
    }

    public function refreshToken(): void
    {
        $token = Str::random(32);
        Setting::setValue('cron_trigger_token', $token);
        $this->cronToken = $token;
        $this->cronUrl = url('/cron/trigger?token=' . $token);
        Notification::make()
            ->title('Cron trigger token regenerated')
            ->success()
            ->send();
    }

    public static function getNavigationLabel(): string
    {
        return 'Cron Manager';
    }

    public function getTitle(): string
    {
        return 'Cron Manager';
    }
}
