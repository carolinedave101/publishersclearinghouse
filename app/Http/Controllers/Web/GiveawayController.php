<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\GiveawayEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GiveawayController extends Controller
{
    public function index(): View
    {
        $giveaways = Giveaway::orderBy('sort_order')->get()->map(function ($g) {
            return [
                'id' => $g->id,
                'title' => $g->title,
                'description' => $g->description,
                'prize' => $g->prize,
                'prize_value' => $g->prize_value,
                'prizeValue' => $g->prize_value ? '$' . number_format($g->prize_value) : $g->prize,
                'image' => $g->image ?? '🎁',
                'endsAt' => $g->ends_at?->toIso8601String(),
                'entries' => $g->entry_count,
                'maxEntries' => $g->max_entries,
                'status' => $g->status,
                'color' => $g->color,
            ];
        });

        return view('pages.giveaways', compact('giveaways'));
    }

    public function enter(Request $request, Giveaway $giveaway): JsonResponse
    {
        if ($giveaway->max_entries && $giveaway->entry_count >= $giveaway->max_entries) {
            return response()->json([
                'success' => false,
                'message' => 'This giveaway has reached the maximum number of entries.',
            ], 422);
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ]);

        $existing = GiveawayEntry::where('giveaway_id', $giveaway->id)
            ->where('email', $data['email'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This email has already entered this giveaway.',
            ], 422);
        }

        GiveawayEntry::create([
            'giveaway_id' => $giveaway->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have successfully entered the giveaway! Good luck!',
            'entries' => $giveaway->fresh()->entry_count,
            'max_entries' => $giveaway->max_entries,
        ]);
    }
}
