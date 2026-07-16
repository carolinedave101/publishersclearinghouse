<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SpinAndWin;
use App\Models\SpinResult;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpinAndWinController extends Controller
{
    public function index(): View
    {
        $game = SpinAndWin::active()->with('activeSegments')->orderBy('sort_order')->first();

        if (!$game) {
            return view('pages.spin-and-win', [
                'game' => null,
                'segments' => [],
            ]);
        }

        $remainingSpins = 0;
        $isWinner = false;

        if ($winnerId = session('winner_id')) {
            $winner = Winner::find($winnerId);
            if ($winner) {
                $isWinner = true;
                $todaySpins = SpinResult::where('spin_and_win_id', $game->id)
                    ->where('winner_email', $winner->email ?? 'winner-' . $winner->id)
                    ->whereDate('created_at', today())
                    ->count();
                $remainingSpins = max(0, $game->max_spins_per_day - $todaySpins);
            }
        }

        return view('pages.spin-and-win', [
            'game' => $game,
            'segments' => $game->activeSegments,
            'remainingSpins' => $remainingSpins,
            'isWinner' => $isWinner,
        ]);
    }

    public function spin(Request $request): JsonResponse
    {
        $winnerId = session('winner_id');
        if (!$winnerId) {
            return response()->json(['error' => 'Please login with your winner code to play.'], 401);
        }

        $winner = Winner::find($winnerId);
        if (!$winner) {
            return response()->json(['error' => 'Winner not found.'], 404);
        }

        $game = SpinAndWin::active()->with('activeSegments')->orderBy('sort_order')->first();

        if (!$game || $game->activeSegments->isEmpty()) {
            return response()->json(['error' => 'Game not available.'], 404);
        }

        $ip = $request->ip();
        $winnerIdentifier = $winner->email ?? 'winner-' . $winner->id;

        if ($game->max_spins_per_day > 0) {
            $todaySpins = SpinResult::where('spin_and_win_id', $game->id)
                ->where('winner_email', $winnerIdentifier)
                ->whereDate('created_at', today())
                ->where('prize_type', '!=', 'free_spin')
                ->count();

            $todayFreeSpins = SpinResult::where('spin_and_win_id', $game->id)
                ->where('winner_email', $winnerIdentifier)
                ->whereDate('created_at', today())
                ->where('prize_type', 'free_spin')
                ->count();

            $effectiveMax = $game->max_spins_per_day + $todayFreeSpins;

            if ($todaySpins >= $effectiveMax) {
                return response()->json([
                    'error' => 'You have reached the maximum spins for today. Come back tomorrow!',
                ], 429);
            }
        }

        if ($game->cooldown_minutes > 0) {
            $lastSpin = SpinResult::where('spin_and_win_id', $game->id)
                ->where('winner_email', $winnerIdentifier)
                ->latest()
                ->first();

            if ($lastSpin && $lastSpin->created_at->diffInMinutes(now()) < $game->cooldown_minutes) {
                $remaining = $game->cooldown_minutes - $lastSpin->created_at->diffInMinutes(now());
                return response()->json([
                    'error' => "Please wait {$remaining} more minute(s) before spinning again.",
                ], 429);
            }
        }

        $todaySpins = 0;

        if ($game->max_spins_per_day > 0) {
            $todaySpins = SpinResult::where('spin_and_win_id', $game->id)
                ->where('winner_email', $winnerIdentifier)
                ->whereDate('created_at', today())
                ->where('prize_type', '!=', 'free_spin')
                ->count();
        }

        $segments = $game->activeSegments;
        $selected = $this->weightedRandom($segments);

        $result = SpinResult::create([
            'spin_and_win_id' => $game->id,
            'spin_wheel_segment_id' => $selected->id,
            'winner_name' => $winner->first_name . ' ' . $winner->last_name,
            'winner_email' => $winnerIdentifier,
            'prize_label' => $selected->label,
            'prize_type' => $selected->prize_type,
            'prize_value' => $selected->prize_value,
            'is_claimed' => $selected->prize_type === 'nothing',
            'ip_address' => $ip,
        ]);

        if ($winner->email && $selected->prize_type !== 'nothing') {
            try {
                \App\Helpers\EmailHelper::send(
                    new \App\Mail\WinnerNotification(
                        $winner,
                        'You Won on Spin & Win!',
                        "Congratulations {$winner->first_name}! You just spun the wheel and won: {$selected->label}. Log in to your dashboard to claim your prize."
                    ),
                    $winner->email,
                    $winner->first_name
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Spin win email failed: ' . $e->getMessage());
            }
        }

        $segmentsArray = $segments->pluck('id')->toArray();
        $targetIndex = array_search($selected->id, $segmentsArray);
        $totalSegments = count($segmentsArray);
        $segmentAngle = 360 / $totalSegments;
        $segmentCenter = ($targetIndex * $segmentAngle) + ($segmentAngle / 2);
        $pointerAngle = 270;
        $alignAngle = ($pointerAngle - $segmentCenter + 360) % 360;
        $fullRotations = 360 * 5;
        $finalAngle = $fullRotations + $alignAngle;

        $remainingSpins = $game->max_spins_per_day - $todaySpins + ($selected->prize_type === 'free_spin' ? 1 : 0);

        $message = $game->success_message
            ? str_replace('{prize}', $selected->label, $game->success_message)
            : null;

        return response()->json([
            'success' => true,
            'segment' => [
                'id' => $selected->id,
                'label' => $selected->label,
                'color' => $selected->color,
                'prize_type' => $selected->prize_type,
                'prize_value' => $selected->prize_value,
                'is_jackpot' => $selected->is_jackpot,
                'prize_description' => $selected->prize_description,
            ],
            'result_id' => $result->id,
            'final_angle' => $finalAngle,
            'target_index' => $targetIndex,
            'message' => $message,
            'remaining_spins' => max(0, $remainingSpins),
        ]);
    }

    private function weightedRandom($items)
    {
        $totalWeight = $items->sum('weight');
        $random = mt_rand(1, $totalWeight);
        $current = 0;

        foreach ($items as $item) {
            $current += $item->weight;
            if ($random <= $current) {
                return $item;
            }
        }

        return $items->last();
    }
}