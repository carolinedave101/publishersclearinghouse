<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if ($code = $request->query('code')) {
            $winner = Winner::active()->where('unique_code', strtoupper($code))->first();

            if ($winner) {
                session(['winner_id' => $winner->id]);
                return redirect()->route('winner.dashboard');
            }
        }

        return view('pages.home');
    }

    public function recentWinners()
    {
        $winners = Winner::claimed()
            ->active()
            ->latest()
            ->take(10)
            ->get();

        return response()->json($winners->map(fn ($w) => $w->only([
            'id', 'first_name', 'last_name', 'city', 'state', 'prize_amount', 'prize_description', 'claimed_at',
        ])));
    }

    public function stats()
    {
        $totalPrizes = Winner::claimed()->sum('prize_amount');
        $totalWinners = Winner::claimed()->count();
        $recentCount = Winner::claimed()
            ->where('claimed_at', '>=', now()->subDays(30))
            ->count();

        return response()->json([
            'total_prizes' => $totalPrizes,
            'total_winners' => $totalWinners,
            'recent_count' => $recentCount,
        ]);
    }
}
