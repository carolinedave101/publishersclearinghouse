<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\Winner|null $winner */
        $winner = $request->get('winner');

        if (!$winner) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $messages = $winner->messages()->latest()->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\Winner|null $winner */
        $winner = $request->get('winner');

        if (!$winner) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $message = Message::create([
            'winner_id' => $winner->id,
            'subject' => $data['subject'],
            'content' => $data['content'],
            'sent_by_admin' => false,
            'sent_by' => 'winner',
            'read' => false,
        ]);

        $this->activityLogger->log(
            action: 'message_sent',
            collection: 'messages',
            documentId: (string) $message->id,
            userId: null,
            description: "Winner sent a message: {$data['subject']}",
        );

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message,
        ], 201);
    }

    public function markRead(Request $request, Message $message): JsonResponse
    {
        /** @var \App\Models\Winner|null $winner */
        $winner = $request->get('winner');

        if (!$winner || (int) $message->winner_id !== (int) $winner->id) {
            return response()->json([
                'success' => false,
                'message' => 'This message does not belong to you.',
            ], 403);
        }

        $message->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read.',
        ]);
    }
}
