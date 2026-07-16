<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'body_variant_1',
        'body_variant_2',
        'body_variant_3',
        'recipient_filter',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'rate_per_hour',
        'rate_per_day',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'recipient_filter' => 'array',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailCampaignRecipient::class, 'campaign_id');
    }

    public function sentRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'sent');
    }

    public function failedRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'failed');
    }

    public function pendingRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'pending');
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->total_recipients === 0) return 0;
        return round(($this->sent_count + $this->failed_count) / $this->total_recipients * 100, 1);
    }

    public function getRemainingCountAttribute(): int
    {
        return max(0, $this->total_recipients - $this->sent_count - $this->failed_count);
    }

    public function getEstimatedHoursAttribute(): ?float
    {
        $remaining = $this->remaining_count;
        if ($remaining <= 0 || $this->rate_per_hour <= 0) return null;
        return ceil($remaining / $this->rate_per_hour);
    }

    public function getTodaySentCountAttribute(): int
    {
        return $this->recipients()
            ->where('status', 'sent')
            ->whereDate('sent_at', today())
            ->count();
    }

    public function getLastHourSentCountAttribute(): int
    {
        return $this->recipients()
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subHour())
            ->count();
    }

    public function canSendMore(): bool
    {
        return $this->last_hour_sent_count < $this->rate_per_hour
            && $this->today_sent_count < $this->rate_per_day;
    }

    public function getAvailableHourlyQuotaAttribute(): int
    {
        return max(0, $this->rate_per_hour - $this->last_hour_sent_count);
    }

    public function getAvailableDailyQuotaAttribute(): int
    {
        return max(0, $this->rate_per_day - $this->today_sent_count);
    }

    public function getBodyVariantsCountAttribute(): int
    {
        $count = 1;
        if (!empty($this->body_variant_2)) $count++;
        if (!empty($this->body_variant_3)) $count++;
        return $count;
    }

    public function getBodyVariant(int $index): string
    {
        return match ($index) {
            2 => $this->body_variant_2 ?: $this->body_variant_1,
            3 => $this->body_variant_3 ?: $this->body_variant_1,
            default => $this->body_variant_1,
        };
    }
}
