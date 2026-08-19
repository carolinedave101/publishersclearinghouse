<?php

namespace App\Services;

use App\Helpers\EmailHelper;
use App\Jobs\ParaphraseHelper;
use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\Winner;
use App\Services\EmailValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    public function createFromConfig(array $config): EmailCampaign
    {
        $campaign = EmailCampaign::create([
            'name' => $config['name'] ?? 'Untitled Campaign',
            'subject' => $config['subject'] ?? '',
            'body_variant_1' => $config['body_variant_1'] ?? '',
            'body_variant_2' => $config['body_variant_2'] ?? '',
            'body_variant_3' => $config['body_variant_3'] ?? '',
            'recipient_filter' => $config['recipient_filter'] ?? [],
            'total_recipients' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'status' => 'draft',
            'rate_per_hour' => $config['rate_per_hour'] ?? 50,
            'rate_per_day' => $config['rate_per_day'] ?? 1000,
        ]);

        return $campaign;
    }

    public function resolveRecipients(EmailCampaign $campaign): int
    {
        if ($campaign->recipients()->count() > 0) {
            return $campaign->recipients()->count();
        }

        $filter = $campaign->recipient_filter ?? [];
        $query = Winner::query();

        if (!empty($filter['statuses'])) {
            $query->whereIn('status', $filter['statuses']);
        }

        if (!empty($filter['is_demo'])) {
            if ($filter['is_demo'] === 'exclude') {
                $query->where('is_demo', false);
            } elseif ($filter['is_demo'] === 'only') {
                $query->where('is_demo', true);
            }
        }

        if (!empty($filter['claim_status'])) {
            if ($filter['claim_status'] === 'claimed') {
                $query->where('is_claimed', true);
            } elseif ($filter['claim_status'] === 'unclaimed') {
                $query->where('is_claimed', false);
            }
        }

        if (!empty($filter['prize_min'])) {
            $query->where('prize_amount', '>=', (float) $filter['prize_min']);
        }

        if (!empty($filter['prize_max'])) {
            $query->where('prize_amount', '<=', (float) $filter['prize_max']);
        }

        if (!empty($filter['states'])) {
            $query->whereIn('state', $filter['states']);
        }

        if (!empty($filter['created_from'])) {
            $query->whereDate('created_at', '>=', $filter['created_from']);
        }

        if (!empty($filter['created_until'])) {
            $query->whereDate('created_at', '<=', $filter['created_until']);
        }

        $query->whereNotNull('email')->where('email', '!=', '');

        $variantsCount = $campaign->body_variants_count;
        $now = now();
        $total = 0;

        $query->chunk(500, function ($winners) use ($campaign, $variantsCount, $now, &$total) {
            $recipients = [];

            foreach ($winners as $winner) {
                $variantIndex = $variantsCount > 1 ? rand(1, $variantsCount) : 1;
                $recipients[] = [
                    'campaign_id' => $campaign->id,
                    'winner_id' => $winner->id,
                    'email' => $winner->email,
                    'first_name' => $winner->first_name,
                    'body_variant_used' => $variantIndex,
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($recipients)) {
                DB::table('email_campaign_recipients')->insert($recipients);
            }

            $total += count($recipients);
        });

        $campaign->update(['total_recipients' => $total]);

        return $total;
    }

    public function sendTestEmails(EmailCampaign $campaign): array
    {
        $demoWinners = Winner::where('is_demo', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($demoWinners->isEmpty()) {
            return ['sent' => 0, 'failed' => 0, 'results' => [], 'error' => 'No demo winners found with email addresses.'];
        }

        $variantsCount = $campaign->body_variants_count;
        $results = [];
        $sent = 0;
        $failed = 0;

        foreach ($demoWinners as $winner) {
            $variantIndex = $variantsCount > 1 ? rand(1, $variantsCount) : 1;

            $recipient = $campaign->recipients()->create([
                'winner_id' => $winner->id,
                'email' => $winner->email,
                'first_name' => $winner->first_name,
                'body_variant_used' => $variantIndex,
                'status' => 'pending',
            ]);

            try {
                $this->sendSingle($campaign, $recipient);
                $sent++;
                $results[] = ['email' => $winner->email, 'status' => 'sent', 'variant' => $variantIndex];
            } catch (\Throwable $e) {
                $failed++;
                $results[] = ['email' => $winner->email, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return ['sent' => $sent, 'failed' => $failed, 'results' => $results];
    }

    public function sendSingle(EmailCampaign $campaign, EmailCampaignRecipient $recipient): void
    {
        $winner = $recipient->winner;
        $variantIndex = $recipient->body_variant_used ?? 1;
        $body = $campaign->getBodyVariant($variantIndex);
        $paraphrased = ParaphraseHelper::paraphrase($body, $variantIndex);

        $personalizedBody = str_replace(
            ['{name}', '{firstName}', '{first_name}'],
            [$recipient->first_name, $recipient->first_name, $recipient->first_name],
            $paraphrased
        );

        if ($winner) {
            $personalizedBody = str_replace(
                ['{prize_amount}', '{unique_code}'],
                ['$' . number_format($winner->prize_amount, 0), $winner->unique_code],
                $personalizedBody
            );
        }

        $validation = (new EmailValidationService())->isDeliverable($recipient->email);
        if (!$validation['valid']) {
            throw new \RuntimeException("Pre-send validation failed: {$validation['reason']} ({$recipient->email})");
        }

        EmailHelper::send(
            new CampaignMail(
                $campaign->subject,
                $personalizedBody,
                $recipient->first_name
            ),
            $recipient->email,
            $recipient->first_name
        );

        $recipient->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $campaign->increment('sent_count');
    }

    public function sendNextPending(EmailCampaign $campaign): array
    {
        if (!$campaign->canSendMore()) {
            return ['sent' => false, 'reason' => 'Rate limit reached'];
        }

        if ($campaign->status === 'draft') {
            $campaign->update(['status' => 'sending', 'started_at' => now()]);
        }

        if (!in_array($campaign->status, ['sending', 'draft'])) {
            return ['sent' => false, 'reason' => "Campaign status is '{$campaign->status}'"];
        }

        $recipient = $campaign->pendingRecipients()->first();

        if (!$recipient) {
            if ($campaign->sent_count + $campaign->failed_count >= $campaign->total_recipients) {
                $campaign->update(['status' => 'sent', 'completed_at' => now()]);
            }
            return ['sent' => false, 'reason' => 'No pending recipients'];
        }

        try {
            $this->sendSingle($campaign, $recipient);
            return ['sent' => true, 'email' => $recipient->email, 'recipient_id' => $recipient->id];
        } catch (\Throwable $e) {
            $recipient->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            $campaign->increment('failed_count');
            Log::error("Campaign send failed [{$campaign->id} -> {$recipient->email}]: {$e->getMessage()}");
            return ['sent' => false, 'error' => $e->getMessage(), 'email' => $recipient->email];
        }
    }

    public function getStatus(EmailCampaign $campaign): array
    {
        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'status' => $campaign->status,
            'total' => $campaign->total_recipients,
            'sent' => $campaign->sent_count,
            'failed' => $campaign->failed_count,
            'pending' => $campaign->remaining_count,
            'progress' => $campaign->progress_percent,
            'rate_per_hour' => $campaign->rate_per_hour,
            'rate_per_day' => $campaign->rate_per_day,
            'hourly_used' => $campaign->last_hour_sent_count,
            'daily_used' => $campaign->today_sent_count,
            'can_send_more' => $campaign->canSendMore(),
            'started_at' => $campaign->started_at,
            'completed_at' => $campaign->completed_at,
            'created_at' => $campaign->created_at,
        ];
    }
}
