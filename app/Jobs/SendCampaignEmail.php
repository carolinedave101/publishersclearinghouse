<?php

namespace App\Jobs;

use App\Helpers\EmailHelper;
use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 30;
    public int $tries = 3;

    public function __construct(
        public EmailCampaign $campaign,
        public EmailCampaignRecipient $recipient
    ) {}

    public function handle(): void
    {
        try {
            $winner = $this->recipient->winner;
            $variantIndex = $this->recipient->body_variant_used ?? 1;
            $body = $this->campaign->getBodyVariant($variantIndex);
            $paraphrased = ParaphraseHelper::paraphrase($body, $variantIndex);

            $personalizedBody = str_replace(
                ['{name}', '{firstName}', '{first_name}'],
                [$this->recipient->first_name, $this->recipient->first_name, $this->recipient->first_name],
                $paraphrased
            );

            if ($winner) {
                $personalizedBody = str_replace(
                    ['{prize_amount}', '{unique_code}'],
                    ['$' . number_format($winner->prize_amount, 0), $winner->unique_code],
                    $personalizedBody
                );
            }

            EmailHelper::send(
                new CampaignMail(
                    $this->campaign->subject,
                    $personalizedBody,
                    $this->recipient->first_name
                ),
                $this->recipient->email,
                $this->recipient->first_name
            );

            $this->recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->campaign->increment('sent_count');

        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            $this->recipient->update([
                'status' => 'failed',
                'error_message' => $errorMsg,
            ]);

            $this->campaign->increment('failed_count');

            Log::error("Campaign email failed [{$this->campaign->id} -> {$this->recipient->email}]: {$errorMsg}");

            if ($this->attempts() < $this->tries) {
                $this->release(60);
            }
        }
    }
}
