<?php

namespace App\Filament\Widgets;

use App\Models\Winner;
use Filament\Widgets\DoughnutChartWidget;

class WinnersByStatus extends DoughnutChartWidget
{
    protected static ?string $heading = 'Winners by Status';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $statuses = ['new', 'under_review', 'documents_needed', 'processing', 'approved', 'delivered'];
        $labels = ['New', 'Under Review', 'Documents Needed', 'Processing', 'Approved', 'Delivered'];
        $colors = ['#9CA3AF', '#F59E0B', '#3B82F6', '#1B2A4A', '#22C55E', '#D4AF37'];

        $counts = [];
        foreach ($statuses as $s) {
            $counts[] = Winner::where('status', $s)->count();
        }

        return [
            'datasets' => [
                [
                    'data' => $counts,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
