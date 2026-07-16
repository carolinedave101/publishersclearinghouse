<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\Widget;

class RecentActivity extends Widget
{
    protected static string $heading = 'Recent Activity';

    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.recent-activity';

    public function getViewData(): array
    {
        return [
            'activities' => ActivityLog::query()
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn (ActivityLog $log): object => (object) [
                    'action' => $log->action,
                    'collection' => $log->collection,
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                    'admin' => $log->user?->name,
                ]),
        ];
    }
}
