<?php

namespace App\Http\Controllers\Web;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminUserMessageNotification;
use App\Models\UserMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserMessageController extends Controller
{
    public function index(): View
    {
        $messages = UserMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        UserMessage::where('user_id', auth()->id())
            ->where('direction', 'admin_to_user')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('pages.messages', compact('messages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $message = UserMessage::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'direction' => 'user_to_admin',
            'is_read' => false,
        ]);

        EmailHelper::sendAdmin(new AdminUserMessageNotification($message));

        return redirect()->route('messages')->with('success', 'Message sent. Admin will respond shortly.');
    }
}
