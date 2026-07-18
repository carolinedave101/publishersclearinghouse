<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'command',
        'description',
        'frequency',
        'cron_expression',
        'is_enabled',
        'last_run_at',
        'last_output',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'last_run_at' => 'datetime',
        ];
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'everyMinute' => 'Every Minute',
            'everyFiveMinutes' => 'Every 5 Minutes',
            'everyTenMinutes' => 'Every 10 Minutes',
            'everyFifteenMinutes' => 'Every 15 Minutes',
            'everyThirtyMinutes' => 'Every 30 Minutes',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Custom: ' . $this->cron_expression,
            default => $this->frequency,
        };
    }
}
