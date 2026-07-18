<?php

namespace App\Console;

use App\Models\ScheduledTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        try {
            $tasks = ScheduledTask::where('is_enabled', true)->get();
        } catch (\Exception) {
            $tasks = collect();
        }

        if ($tasks->isEmpty()) {
            $schedule->command('queue:work database --stop-when-empty --max-time=55')
                ->everyMinute()
                ->withoutOverlapping()
                ->sendOutputTo(storage_path('logs/queue-worker.log'));
            return;
        }

        foreach ($tasks as $task) {
            $event = $schedule->command($task->command)
                ->withoutOverlapping()
                ->sendOutputTo(storage_path('logs/queue-worker.log'))
                ->before(function () use ($task) {
                    $task->update(['last_run_at' => now()]);
                });

            if ($task->frequency === 'custom' && $task->cron_expression) {
                $event->cron($task->cron_expression);
            } else {
                $method = $task->frequency;
                if (method_exists($event, $method)) {
                    $event->{$method}();
                } else {
                    $event->everyMinute();
                }
            }
        }
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
