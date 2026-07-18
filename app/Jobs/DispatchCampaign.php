<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        public EmailCampaign $campaign,
        public bool $isTestRun = false
    ) {}

    public function handle(): void
    {
        if ($this->campaign->status === 'cancelled') {
            return;
        }

        if ($this->isTestRun) {
            $this->sendTest();
            return;
        }

        if ($this->campaign->status === 'draft') {
            $this->campaign->update(['status' => 'sending', 'started_at' => now()]);
        }

        if ($this->campaign->status !== 'sending') {
            return;
        }

        $batchSize = $this->getBatchSize();

        if ($batchSize <= 0) {
            $this->requeueOrComplete();
            return;
        }

        $pending = $this->campaign->pendingRecipients()
            ->limit($batchSize)
            ->get();

        if ($pending->isEmpty()) {
            $this->requeueOrComplete();
            return;
        }

        $spacingSeconds = $pending->count() > 1
            ? max(10, intval(3600 / $this->campaign->rate_per_hour))
            : 0;

        foreach ($pending as $i => $recipient) {
            $job = new SendCampaignEmail($this->campaign, $recipient);
            if ($spacingSeconds > 0 && $i > 0) {
                $job->delay(now()->addSeconds($spacingSeconds * $i));
            }
            dispatch($job);
        }

        $this->requeueOrComplete();
    }

    protected function getBatchSize(): int
    {
        if ($this->campaign->total_recipients <= 0) {
            return 0;
        }

        $hourlyRemaining = $this->campaign->available_hourly_quota;
        $dailyRemaining = $this->campaign->available_daily_quota;

        if ($hourlyRemaining <= 0 || $dailyRemaining <= 0) {
            return 0;
        }

        return min($hourlyRemaining, $dailyRemaining, $this->campaign->pendingRecipients()->count());
    }

    protected function requeueOrComplete(): void
    {
        $remaining = $this->campaign->pendingRecipients()->count();

        if ($remaining <= 0) {
            $this->campaign->update([
                'status' => 'sent',
                'completed_at' => now(),
            ]);
            Log::info("Campaign [{$this->campaign->id}] completed. Sent: {$this->campaign->sent_count}, Failed: {$this->campaign->failed_count}");
            return;
        }

        $dailyQuotaExhausted = $this->campaign->today_sent_count >= $this->campaign->rate_per_day;
        $hourlyQuotaExhausted = $this->campaign->last_hour_sent_count >= $this->campaign->rate_per_hour;

        if ($dailyQuotaExhausted) {
            $nextRun = now()->addDay()->startOfDay()->addMinutes(1);
        } elseif ($hourlyQuotaExhausted) {
            $nextRun = now()->addHour()->addMinute();
        } else {
            $nextRun = now()->addMinutes(5);
        }

        self::dispatch($this->campaign, false)->delay($nextRun);
    }

    protected function sendTest(): void
    {
        if ($this->campaign->recipients()->count() > 0) {
            Log::info("Test campaign [{$this->campaign->id}]: already dispatched, skipping.");
            return;
        }

        $demoWinners = Winner::where('is_demo', true)->get();

        if ($demoWinners->isEmpty()) {
            Log::warning("Test campaign [{$this->campaign->id}]: No demo winners found.");
            return;
        }

        foreach ($demoWinners as $winner) {
            if (empty($winner->email)) continue;

            $variantIndex = rand(1, $this->campaign->body_variants_count);

            $recipient = $this->campaign->recipients()->create([
                'winner_id' => $winner->id,
                'email' => $winner->email,
                'first_name' => $winner->first_name,
                'body_variant_used' => $variantIndex,
                'status' => 'pending',
            ]);

            dispatch(new SendCampaignEmail($this->campaign, $recipient));
        }

        Log::info("Test campaign [{$this->campaign->id}] dispatched to {$demoWinners->count()} demo winners.");
    }
}
