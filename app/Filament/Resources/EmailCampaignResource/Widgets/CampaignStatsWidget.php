<?php

namespace App\Filament\Resources\EmailCampaignResource\Widgets;

use App\Models\EmailCampaign;
use Filament\Widgets\Widget;

class CampaignStatsWidget extends Widget
{
    protected static string $view = 'filament.resources.email-campaign.campaign-stats-widget';

    public ?EmailCampaign $campaign = null;

    public function __construct()
    {
        parent::__construct();
    }

    protected function getViewData(): array
    {
        if (!$this->campaign) {
            return [
                'stats' => [],
                'hourlyData' => [],
                'maxHourly' => 1,
                'totalForPie' => 1,
                'segments' => [],
                'dailyPct' => 0,
            ];
        }

        $c = $this->campaign;
        $stats = [
            'total' => $c->total_recipients,
            'sent' => $c->sent_count,
            'failed' => $c->failed_count,
            'pending' => $c->remaining_count,
            'progress' => $c->progress_percent,
            'hourly_used' => $c->last_hour_sent_count,
            'hourly_max' => $c->rate_per_hour,
            'daily_used' => $c->today_sent_count,
            'daily_max' => $c->rate_per_day,
            'estimated_hours' => $c->estimated_hours,
            'status' => $c->status,
            'started_at' => $c->started_at,
            'completed_at' => $c->completed_at,
            'variants' => $c->body_variants_count,
        ];

        $hourlyRaw = $c->recipients()
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subHours(24))
            ->selectRaw("HOUR(sent_at) as hour, COUNT(*) as count")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $hourlyData = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyData[] = $hourlyRaw[$h] ?? 0;
        }

        $maxHourly = max(1, max($hourlyData));
        $totalForPie = max(1, $stats['sent'] + $stats['failed'] + $stats['pending']);
        $dailyPct = $stats['daily_max'] > 0 ? min(100, round($stats['daily_used'] / $stats['daily_max'] * 100)) : 0;

        $segments = [
            ['label' => 'Sent', 'count' => $stats['sent'], 'color' => '#10B981'],
            ['label' => 'Failed', 'count' => $stats['failed'], 'color' => '#DC2626'],
            ['label' => 'Pending', 'count' => $stats['pending'], 'color' => '#9CA3AF'],
        ];

        return compact('stats', 'hourlyData', 'maxHourly', 'totalForPie', 'segments', 'dailyPct');
    }
}
