<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WinnerLookupController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $data = $request->validate([
            'unique_code' => ['required', 'string'],
        ]);

        $winner = Winner::active()
            ->where('unique_code', $data['unique_code'])
            ->first();

        if (!$winner) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid winner code.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'winner' => $winner->only(['id', 'first_name', 'last_name', 'prize_amount', 'prize_description', 'is_claimed']),
        ]);
    }
}
