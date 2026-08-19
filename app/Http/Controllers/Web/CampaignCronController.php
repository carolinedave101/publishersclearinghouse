<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\Winner;
use App\Services\CampaignService;
use App\Services\EmailValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignCronController extends Controller
{
    protected string $campaignToken;

    public function __construct(
        protected CampaignService $campaignService
    ) {}

    public function handle(Request $request)
    {
        $token = $request->query('token');
        $expected = config('app.setup_token', env('SETUP_TOKEN', 'dev-setup-token'));

        if ($token !== $expected) {
            return response('Invalid token.', 403);
        }

        $this->campaignToken = $token;

        if ($request->query('test')) {
            return $this->handleTest($request);
        }

        if ($request->query('campaign') && $request->query('verify')) {
            return $this->handleVerify($request);
        }

        if ($request->query('campaign')) {
            return $this->handleCronSend($request);
        }

        if ($request->query('cleanup')) {
            return $this->handleCleanup($request);
        }

        return $this->handleStatus($request);
    }

    protected function handleVerify(Request $request)
    {
        $campaignId = (int) $request->query('campaign');
        $campaign = EmailCampaign::find($campaignId);

        if (!$campaign) {
            return response('Campaign not found.', 404);
        }

        $validator = new EmailValidationService();
        $pendingRecipients = $campaign->pendingRecipients()->get();
        $total = $pendingRecipients->count();
        $removed = 0;
        $deletedWinners = [];
        $invalidRecipients = [];

        foreach ($pendingRecipients as $recipient) {
            $result = $validator->isDeliverable($recipient->email);

            if (!$result['valid']) {
                $invalidRecipients[] = [
                    'id' => $recipient->id,
                    'email' => $recipient->email,
                    'winner_id' => $recipient->winner_id,
                    'reason' => $result['reason'],
                ];
                $removed++;
            }
        }

        foreach ($invalidRecipients as $invalid) {
            DB::table('email_campaign_recipients')
                ->where('id', $invalid['id'])
                ->update([
                    'status' => 'failed',
                    'error_message' => 'Pre-send verification: ' . $invalid['reason'],
                ]);
            $campaign->increment('failed_count');
            $campaign->decrement('total_recipients');

            if ($invalid['winner_id']) {
                $winner = Winner::find($invalid['winner_id']);
                if ($winner && !$winner->is_demo) {
                    $deletedWinners[] = [
                        'id' => $winner->id,
                        'email' => $winner->email,
                        'name' => $winner->first_name . ' ' . $winner->last_name,
                    ];
                    $winner->delete();
                }
            }
        }

        $campaign->refresh();
        $remainingPending = $campaign->pendingRecipients()->count();

        $lines = [];
        $lines[] = "Campaign #{$campaign->id}: {$campaign->name}";
        $lines[] = "Verified {$total} pending recipients.";
        $lines[] = "Flagged {$removed} as undeliverable (removed from campaign).";
        $lines[] = "Deleted " . count($deletedWinners) . " winner accounts with invalid emails.";
        $lines[] = "Pending remaining: {$remainingPending}";
        $lines[] = "Campaign totals: {$campaign->sent_count} sent / {$campaign->failed_count} failed / {$campaign->remaining_count} pending";
        $lines[] = "---";

        if (!empty($deletedWinners)) {
            $lines[] = "Deleted winners:";
            foreach ($deletedWinners as $w) {
                $lines[] = "  - #{$w['id']} {$w['name']} <{$w['email']}>";
            }
        } else {
            $lines[] = "No winners were deleted (all invalid recipients were demo winners or already processed).";
        }

        return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
    }

    protected function handleTest(Request $request)
    {
        $config = config('campaign');

        $campaign = $this->campaignService->createFromConfig($config);

        $totalRecipients = $this->campaignService->resolveRecipients($campaign);

        $demoCount = Winner::where('is_demo', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->count();

        $results = $this->campaignService->sendTestEmails($campaign);

        $html = '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">';
        $html .= '<title>Campaign Test Results</title>';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $html .= '<style>
            body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#0B1424;color:#374151;padding:24px;margin:0}
            .card{max-width:720px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.15)}
            .header{background:linear-gradient(135deg,#1B2A4A,#0F1D35);padding:32px;text-align:center}
            .header h1{color:#D4AF37;margin:0;font-size:22px}
            .header p{color:#8899B4;margin:8px 0 0;font-size:14px}
            .body{padding:32px}
            .summary{background:#F8FAFC;border:1px solid #E5E7EB;border-radius:12px;padding:20px;margin-bottom:24px}
            .summary .stat{display:inline-block;margin-right:32px}
            .summary .stat .num{font-size:28px;font-weight:800;color:#1B2A4A}
            .summary .stat .label{font-size:12px;color:#6B7280;text-transform:uppercase;letter-spacing:1px;margin-top:2px}
            .result{padding:12px 16px;border-radius:8px;margin-bottom:8px;font-size:14px;display:flex;align-items:center;gap:8px}
            .result.sent{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0}
            .result.failed{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA}
            .result .icon{font-size:18px;font-weight:bold}
            .result .email{font-weight:600;flex:1}
            .result .variant{color:#6B7280;font-size:12px}
            .next{background:#FEF9E7;border:1px solid #FCD34D;border-radius:12px;padding:20px;margin-top:24px}
            .next h3{margin:0 0 8px;color:#1B2A4A}
            .next code{display:block;background:#1B2A4A;color:#D4AF37;padding:12px 16px;border-radius:8px;margin:8px 0;font-size:13px;word-break:break-all}
            .next p{margin:4px 0;font-size:14px;color:#6B7280}
            .badge{display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600}
            .badge.success{background:#ECFDF5;color:#065F46}
            .badge.danger{background:#FEF2F2;color:#991B1B}
        </style></head><body>';
        $html .= '<div class="card">';
        $html .= '<div class="header"><h1> Campaign Test Results</h1>';
        $html .= '<p>Campaign #' . $campaign->id . ' &mdash; ' . e($campaign->name) . '</p></div>';
        $html .= '<div class="body">';

        $html .= '<div class="summary">';
        $html .= '<div class="stat"><div class="num">' . $results['sent'] . '</div><div class="label">Sent</div></div>';
        $html .= '<div class="stat"><div class="num">' . $results['failed'] . '</div><div class="label">Failed</div></div>';
        $html .= '<div class="stat"><div class="num">' . $totalRecipients . '</div><div class="label">Production Recipients Ready</div></div>';
        $html .= '</div>';

        if (!empty($results['error'])) {
            $html .= '<div class="result failed"><span class="icon">⚠️</span><span class="email">' . e($results['error']) . '</span></div>';
        }

        foreach ($results['results'] as $r) {
            $cls = $r['status'] === 'sent' ? 'sent' : 'failed';
            $icon = $r['status'] === 'sent' ? '✓' : '✗';
            $html .= '<div class="result ' . $cls . '">';
            $html .= '<span class="icon">' . $icon . '</span>';
            $html .= '<span class="email">' . e($r['email']) . '</span>';
            $html .= '<span class="variant">variant ' . $r['variant'] . '</span>';
            if (!empty($r['error'])) {
                $html .= '<span class="badge danger">' . e($r['error']) . '</span>';
            } else {
                $html .= '<span class="badge success">Sent</span>';
            }
            $html .= '</div>';
        }

        $allSucceeded = $results['failed'] === 0;

        $html .= '<div class="next">';
        $html .= '<h3>' . ($allSucceeded ? '✅ All test emails delivered' : '⚠️ Some test emails failed') . '</h3>';

        if ($allSucceeded && $totalRecipients > 0) {
            $html .= '<p>Production campaign is ready with <strong>' . number_format($totalRecipients) . '</strong> recipients.</p>';
            $html .= '<p>Set up this cron job in cPanel (Advanced → Cron Jobs):</p>';
            $html .= '<code>wget -q -O /dev/null "' . url('/cron/send-campaign?token=' . $this->campaignToken . '&campaign=' . $campaign->id) . '"</code>';
            $html .= '<p style="margin-top:12px;">Set it to run <strong>every minute</strong>. It will send 50 emails per hour, 1,000 per day automatically.</p>';
        } elseif (!$allSucceeded) {
            $html .= '<p>Check your mail configuration in .env and try again after fixing the issue.</p>';
        } else {
            $html .= '<p>No production recipients matched the filter criteria. Check the recipient_filter in config/campaign.php.</p>';
        }
        $html .= '</div>';

        $html .= '<p style="text-align:center;margin-top:24px;color:#9CA3AF;font-size:13px;">';
        $html .= 'Campaign status: ' . e($campaign->status) . ' &bull; ';
        $html .= $results['sent'] + $results['failed'] . ' test emails sent';
        $html .= '</p>';

        $html .= '</div></div></body></html>';

        return response($html)->header('Content-Type', 'text/html');
    }

    protected function handleCronSend(Request $request)
    {
        $campaignId = (int) $request->query('campaign');
        $campaign = EmailCampaign::find($campaignId);

        if (!$campaign) {
            return response('Campaign not found.', 404);
        }

        if (!in_array($campaign->status, ['draft', 'sending'])) {
            return response('Campaign is ' . $campaign->status . '.', 200);
        }

        if ($campaign->remaining_count <= 0) {
            $campaign->update(['status' => 'sent', 'completed_at' => now()]);
            return response("Campaign #{$campaignId} complete: {$campaign->sent_count} sent, {$campaign->failed_count} failed.", 200);
        }

        if (!$campaign->canSendMore()) {
            return response("Rate limit reached ({$campaign->last_hour_sent_count}/hr, {$campaign->today_sent_count}/day).", 200);
        }

        $result = $this->campaignService->sendNextPending($campaign);

        if ($result['sent']) {
            $progress = $campaign->progress_percent;
            return response("Sent to {$result['email']}. Campaign #{$campaignId}: {$campaign->sent_count}/{$campaign->total_recipients} ({$progress}%)");
        }

        return response($result['reason'] ?? 'No action taken.');
    }

    protected function handleStatus(Request $request)
    {
        $campaignId = (int) $request->query('id');
        $campaign = $campaignId ? EmailCampaign::find($campaignId) : EmailCampaign::whereIn('status', ['draft', 'sending'])->latest()->first();

        if (!$campaign) {
            return response('No active campaign found.', 404);
        }

        $status = $this->campaignService->getStatus($campaign);

        $lines = [];
        $lines[] = "Campaign #{$status['id']}: {$status['name']}";
        $lines[] = "Status: {$status['status']}";
        $lines[] = "Progress: {$status['sent']} sent / {$status['failed']} failed / {$status['pending']} pending ({$status['progress']}%)";
        $lines[] = "Rate: {$status['hourly_used']}/{$status['rate_per_hour']} per hour, {$status['daily_used']}/{$status['rate_per_day']} per day";
        $lines[] = "Can send more: " . ($status['can_send_more'] ? 'Yes' : 'No');

        if ($status['started_at']) {
            $lines[] = "Started: {$status['started_at']}";
        }
        if ($status['completed_at']) {
            $lines[] = "Completed: {$status['completed_at']}";
        }

        return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
    }

    protected function handleCleanup(Request $request)
    {
        $validator = new EmailValidationService();

        $failedRecipients = DB::table('email_campaign_recipients')
            ->where('status', 'failed')
            ->whereNotNull('winner_id')
            ->whereNotNull('error_message')
            ->get(['id', 'winner_id', 'email', 'error_message']);

        $hardBounceIds = [];
        $softBounceIds = [];
        $deletedWinners = [];
        $softBounceCount = 0;

        foreach ($failedRecipients as $recipient) {
            if ($validator::isHardBounce($recipient->error_message)) {
                $hardBounceIds[] = $recipient->winner_id;
            } else {
                $softBounceIds[] = $recipient->winner_id;
                $softBounceCount++;
            }
        }

        $hardBounceIds = array_unique($hardBounceIds);
        $softBounceIds = array_unique($softBounceIds);

        $hardWinnerIds = Winner::whereIn('id', $hardBounceIds)
            ->whereNotNull('email')
            ->where('is_demo', false)
            ->pluck('id')
            ->toArray();

        foreach ($hardWinnerIds as $winnerId) {
            $winner = Winner::find($winnerId);
            if ($winner) {
                $deletedWinners[] = [
                    'id' => $winner->id,
                    'email' => $winner->email,
                    'name' => $winner->first_name . ' ' . $winner->last_name,
                ];
                $winner->delete();
            }
        }

        $lines = [];
        $lines[] = "Cleanup complete.";
        $lines[] = "Total failed recipients scanned: " . count($failedRecipients);
        $lines[] = "Hard bounces detected: " . count($hardBounceIds);
        $lines[] = "Soft bounces skipped: {$softBounceCount}";
        $lines[] = "Winner accounts deleted: " . count($deletedWinners);

        if (!empty($deletedWinners)) {
            $lines[] = "---";
            $lines[] = "Deleted winners:";
            foreach ($deletedWinners as $w) {
                $lines[] = "  - #{$w['id']} {$w['name']} <{$w['email']}>";
            }
        }

        if (!empty($softBounceIds)) {
            $lines[] = "---";
            $lines[] = "Soft bounces (not deleted, may retry):";
            $softWinners = Winner::whereIn('id', $softBounceIds)->get();
            foreach ($softWinners as $w) {
                $lines[] = "  - #{$w->id} {$w->first_name} {$w->last_name} <{$w->email}>";
            }
        }

        return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
    }
}