<?php

namespace App\Console\Commands;

use App\Models\EmailCampaign;
use App\Services\CampaignService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SendCampaignCommand extends Command
{
    protected $signature = 'campaign:send
        {--config= : Path to JSON config file for creating a new campaign}
        {--campaign= : ID of an existing campaign to run or resume}
        {--test : Send test emails to demo winners only}
        {--run : Run production sending (continuous)}
        {--force : Force re-send (clear existing recipients)}';

    protected $description = 'Create and send email campaigns';

    public function handle(CampaignService $service): int
    {
        if ($this->option('config')) {
            return $this->createFromConfig($service);
        }

        if ($this->option('campaign')) {
            $campaign = EmailCampaign::find((int) $this->option('campaign'));
            if (!$campaign) {
                $this->error("Campaign #{$this->option('campaign')} not found.");
                return 1;
            }
            return $this->processExisting($service, $campaign);
        }

        $this->info('Usage:');
        $this->info('  php artisan campaign:send --config=campaign.json          Create campaign from JSON');
        $this->info('  php artisan campaign:send --campaign=5 --test              Send test to demo winners');
        $this->info('  php artisan campaign:send --campaign=5 --run               Send to all recipients');
        $this->info('  php artisan campaign:send --campaign=5 --run --force       Re-send from scratch');

        return 0;
    }

    protected function createFromConfig(CampaignService $service): int
    {
        $path = $this->option('config');
        if (!File::exists($path)) {
            $this->error("Config file not found: {$path}");
            return 1;
        }

        $config = json_decode(File::get($path), true);
        if (!$config) {
            $this->error('Invalid JSON in config file.');
            return 1;
        }

        $campaign = $service->createFromConfig($config);
        $this->info("Campaign #{$campaign->id} \"{$campaign->name}\" created.");

        $total = $service->resolveRecipients($campaign);
        $this->info("Resolved {$total} recipients.");

        if ($this->option('test')) {
            $this->info('Sending test emails to demo winners...');
            $results = $service->sendTestEmails($campaign);
            foreach ($results['results'] as $r) {
                $icon = $r['status'] === 'sent' ? '✓' : '✗';
                $this->line("  {$icon} {$r['email']} ({$r['status']})");
            }
            $this->info("Test results: {$results['sent']} sent, {$results['failed']} failed.");
        }

        if ($this->option('run')) {
            $this->info("Starting production send ({$campaign->rate_per_hour}/hr)...");
            $this->runContinuous($service, $campaign);
        }

        return 0;
    }

    protected function processExisting(CampaignService $service, EmailCampaign $campaign): int
    {
        if ($this->option('force')) {
            $campaign->recipients()->delete();
            $campaign->update([
                'status' => 'draft',
                'sent_count' => 0,
                'failed_count' => 0,
                'total_recipients' => 0,
                'started_at' => null,
                'completed_at' => null,
            ]);
            $total = $service->resolveRecipients($campaign);
            $this->info("Re-resolved {$total} recipients.");
        }

        if ($this->option('test')) {
            $results = $service->sendTestEmails($campaign);
            foreach ($results['results'] as $r) {
                $icon = $r['status'] === 'sent' ? '✓' : '✗';
                $this->line("  {$icon} {$r['email']}");
            }
            $this->info("Test: {$results['sent']} sent, {$results['failed']} failed.");
            return 0;
        }

        if ($this->option('run')) {
            return $this->runContinuous($service, $campaign);
        }

        $status = $service->getStatus($campaign);
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $status['id']],
                ['Name', $status['name']],
                ['Status', $status['status']],
                ['Total', $status['total']],
                ['Sent', $status['sent']],
                ['Failed', $status['failed']],
                ['Pending', $status['pending']],
                ['Progress', $status['progress'] . '%'],
                ['Rate', "{$status['rate_per_hour']}/hr, {$status['rate_per_day']}/day"],
                ['Hourly Used', $status['hourly_used']],
                ['Daily Used', $status['daily_used']],
                ['Can Send More', $status['can_send_more'] ? 'Yes' : 'No'],
            ]
        );

        return 0;
    }

    protected function runContinuous(CampaignService $service, EmailCampaign $campaign): int
    {
        if ($campaign->status === 'draft') {
            $campaign->update(['status' => 'sending', 'started_at' => now()]);
        }

        $this->info("Sending {$campaign->rate_per_hour} emails/hour (1 every " . ceil(3600 / $campaign->rate_per_hour) . "s)");

        while ($campaign->remaining_count > 0) {
            if (!$campaign->canSendMore()) {
                $waitSeconds = $campaign->last_hour_sent_count >= $campaign->rate_per_hour
                    ? 3600
                    : 86400;
                $this->warn("Rate limit reached. Waiting " . ceil($waitSeconds / 60) . " minutes...");
                sleep(min($waitSeconds, 3600));
                $campaign->refresh();
                continue;
            }

            $result = $service->sendNextPending($campaign);

            if ($result['sent']) {
                $this->info("[{$campaign->sent_count}/{$campaign->total_recipients}] ✓ {$result['email']}");
            } else {
                if (isset($result['error'])) {
                    $this->error("✗ {$result['email']}: {$result['error']}");
                }
            }

            $delay = ceil(3600 / $campaign->rate_per_hour);
            sleep(min($delay, 3600));

            $campaign->refresh();
        }

        $campaign->update(['status' => 'sent', 'completed_at' => now()]);
        $this->info("Campaign #{$campaign->id} complete: {$campaign->sent_count} sent, {$campaign->failed_count} failed.");

        return 0;
    }
}
