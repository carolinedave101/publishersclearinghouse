<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Winner;
use App\Notifications\WinnerClaimedNotification;
use App\Services\ActivityLogger;
use App\Services\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class WinnerClaimController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected EmailService $emailService,
    ) {}

    public function claim(Request $request): JsonResponse
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
                'message' => 'Winner not found.',
            ], 404);
        }

        $winner->update([
            'is_claimed' => true,
            'claimed_at' => now(),
        ]);

        $this->activityLogger->log(
            action: 'claim',
            collection: 'winners',
            documentId: (string) $winner->id,
            userId: null,
            description: "Winner {$winner->first_name} {$winner->last_name} claimed their prize of \${$winner->prize_amount}.",
        );

        $users = User::all();
        Notification::send($users, new WinnerClaimedNotification($winner));

        $this->emailService->sendWinnerNotification($winner);

        return response()->json([
            'success' => true,
            'message' => 'Prize claimed successfully!',
            'winner' => $winner->only(['id', 'first_name', 'last_name', 'is_claimed', 'prize_amount']),
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $winner = $request->get('winner');

        if (!$winner) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $winner->load(['messages', 'documents']);

        return response()->json([
            'success' => true,
            'winner' => $winner->only(['id', 'first_name', 'last_name', 'email', 'prize_amount', 'prize_description', 'is_claimed', 'status', 'available_balance']),
            'messages' => $winner->messages,
            'documents' => $winner->documents,
        ]);
    }
}
