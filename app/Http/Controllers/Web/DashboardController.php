<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GiveawayEntry;
use App\Models\MembershipSubscription;
use App\Models\ShopOrder;
use App\Models\SpinResult;
use App\Models\UserMessage;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $recentSpins = SpinResult::where('user_id', $user->id)
            ->with(['spinAndWin', 'segment'])
            ->latest()
            ->take(10)
            ->get();

        $spinStats = [
            'total' => SpinResult::where('user_id', $user->id)->count(),
            'wins' => SpinResult::where('user_id', $user->id)->where('prize_type', '!=', 'nothing')->count(),
            'jackpots' => SpinResult::where('user_id', $user->id)->whereHas('segment', fn ($q) => $q->where('is_jackpot', true))->count(),
            'recent' => $recentSpins,
        ];

        $entries = GiveawayEntry::where('email', $user->email)
            ->with('giveaway')
            ->latest()
            ->take(5)
            ->get();

        $subscription = MembershipSubscription::where('subscriber_email', $user->email)
            ->with('tier')
            ->latest()
            ->first();

        $orders = ShopOrder::where('customer_email', $user->email)
            ->latest()
            ->take(5)
            ->get();

        $orderStats = [
            'total' => ShopOrder::where('customer_email', $user->email)->count(),
            'spent' => ShopOrder::where('customer_email', $user->email)->sum('total'),
        ];

        $winner = Winner::where('email', $user->email)->latest()->first();

        $unreadMessages = UserMessage::where('user_id', $user->id)
            ->where('direction', 'admin_to_user')
            ->where('is_read', false)
            ->count();

        $recentMessages = UserMessage::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        $totalWon = SpinResult::where('user_id', $user->id)
            ->where('prize_type', 'cash')
            ->sum('prize_value');

        return view('pages.dashboard', compact(
            'spinStats', 'entries', 'subscription',
            'recentSpins', 'unreadMessages', 'recentMessages',
            'orders', 'orderStats', 'winner', 'totalWon'
        ));
    }

    public function orders(): View
    {
        $user = auth()->user();
        $orders = ShopOrder::where('customer_email', $user->email)->latest()->get();
        return view('pages.orders', compact('orders'));
    }
}
