<?php

namespace App\Filament\Widgets;

use App\Models\ActivityLog;
use App\Models\MembershipSubscription;
use App\Models\ShopOrder;
use App\Models\SpinResult;
use App\Models\Winner;
use Filament\Widgets\StatsOverviewWidget;

class StatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalWinners = Winner::count();
        $claimed = Winner::where('is_claimed', true)->count();
        $totalPrize = Winner::sum('prize_amount');
        $activeOrders = ShopOrder::where('status', 'pending')->orWhere('status', 'processing')->count();
        $activeMembers = MembershipSubscription::where('status', 'active')->count();
        $todaySpins = SpinResult::whereDate('created_at', today())->count();

        return [
            StatsOverviewWidget\Stat::make('Total Winners', number_format($totalWinners))
                ->description('Imported prize winners')
                ->icon('heroicon-o-trophy')
                ->color('gold'),
            StatsOverviewWidget\Stat::make('Prizes Claimed', number_format($claimed))
                ->description($totalWinners ? round($claimed / $totalWinners * 100) . '% claimed' : '0%')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            StatsOverviewWidget\Stat::make('Total Prize Value', '$' . number_format($totalPrize, 0))
                ->description('Across all winners')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),
            StatsOverviewWidget\Stat::make('Open Orders', number_format($activeOrders))
                ->description('Pending / processing')
                ->icon('heroicon-o-shopping-bag')
                ->color('info'),
            StatsOverviewWidget\Stat::make('Active Memberships', number_format($activeMembers))
                ->icon('heroicon-o-star')
                ->color('primary'),
            StatsOverviewWidget\Stat::make("Today's Spins", number_format($todaySpins))
                ->description('Spin & Win activity')
                ->icon('heroicon-o-arrow-path')
                ->color('danger'),
        ];
    }
}
