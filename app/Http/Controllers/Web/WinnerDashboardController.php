<?php

namespace App\Http\Controllers\Web;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminDepositNotification;
use App\Mail\AdminDocumentUploaded;
use App\Mail\AdminWinnerClaimedNotification;
use App\Mail\AdminWithdrawalRequestNotification;
use App\Mail\WinnerClaimedMail;
use App\Mail\WinnerNotification;
use App\Models\Deposit;
use App\Models\Document;
use App\Models\Message;
use App\Models\PaymentMethod;
use App\Models\ShopOrder;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Winner;
use App\Notifications\WinnerClaimedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WinnerDashboardController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {}

    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('winner_id')) {
            return redirect()->route('winner.dashboard');
        }

        return view('auth.login');
    }

    public function lookup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $winner = Winner::active()
            ->where('unique_code', strtoupper($data['code']))
            ->first();

        if (!$winner) {
            return redirect()->back()->with('error', 'Invalid winner code. Please check and try again.');
        }

        session(['winner_id' => $winner->id]);

        return redirect()->route('winner.dashboard');
    }

    public function claim(Request $request): RedirectResponse
    {
        /** @var Winner|null $winner */
        $winner = Winner::find(session('winner_id'));

        if (!$winner) {
            return redirect()->route('home')->withErrors(['error' => 'Winner not found. Please look up your code again.']);
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

        $users = \App\Models\User::all();
        Notification::send($users, new WinnerClaimedNotification($winner));

        EmailHelper::send(new WinnerClaimedMail($winner), $winner->email, $winner->first_name);
        EmailHelper::sendAdmin(new AdminWinnerClaimedNotification($winner));

        return redirect()->route('winner.dashboard');
    }

    public function dashboard(): View
    {
        /** @var Winner $winner */
        $winner = Winner::with(['messages', 'documents'])->find(session('winner_id'));

        return view('pages.winner.dashboard', compact('winner'));
    }

    public function logout(): RedirectResponse
    {
        session()->forget('winner_id');

        return redirect()->route('home');
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $message = Message::create([
            'winner_id' => session('winner_id'),
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

        $winner = Winner::find(session('winner_id'));

        if ($winner && $winner->email) {
            \App\Helpers\EmailHelper::send(
                new \App\Mail\WinnerNotification(
                    $winner,
                    'We Received Your Message',
                    "Hi {$winner->first_name}, thanks for reaching out! Our team has received your message titled \"{$data['subject']}\" and will respond shortly."
                ),
                $winner->email,
                $winner->first_name
            );
        }

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function markRead(Message $message): RedirectResponse
    {
        if ((int) $message->winner_id !== (int) session('winner_id')) {
            abort(403, 'This message does not belong to you.');
        }

        $message->update(['read' => true]);

        return redirect()->back();
    }

    public function uploadDocument(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'document_type' => ['required', 'string'],
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx'],
        ]);

        $winnerId = session('winner_id');
        $winner = Winner::find($winnerId);
        $file = $request->file('file');
        $path = $file->store('documents/' . $winnerId, 'public');

        $document = Document::create([
            'winner_id' => $winnerId,
            'document_type' => $data['document_type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        if ($winner) {
            EmailHelper::sendAdmin(new AdminDocumentUploaded($document, $winner));

            if ($winner->email) {
                \App\Helpers\EmailHelper::send(
                    new \App\Mail\WinnerNotification(
                        $winner,
                        'Document Received',
                        "Hi {$winner->first_name}, we have received your uploaded document for \"{$data['document_type']}\". Our team will review it and update your account shortly."
                    ),
                    $winner->email,
                    $winner->first_name
                );
            }
        }

        return redirect()->back()->with('success', 'Document uploaded successfully. Admin will review it shortly.');
    }

    public function showWithdrawals(): View
    {
        $winner = Winner::with(['withdrawals.paymentMethod', 'documents'])->find(session('winner_id'));
        $paymentMethods = PaymentMethod::active()->forWithdrawals()->orderBy('sort_order')->get();

        return view('pages.winner.withdrawals', compact('winner', 'paymentMethods'));
    }

    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $winner = Winner::find(session('winner_id'));

        if (!$winner) {
            return redirect()->route('home')->withErrors(['error' => 'Winner not found.']);
        }

        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:1', "max:{$winner->available_balance}"],
            'account_details' => ['required', 'json'],
        ]);

        $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
        $amount = (float) $data['amount'];
        $fee = 0;
        $netAmount = $amount - $fee;

        $withdrawal = Withdrawal::create([
            'winner_id' => $winner->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $netAmount,
            'account_details' => json_decode($data['account_details'], true),
            'status' => 'pending',
        ]);

        $this->activityLogger->log(
            action: 'withdrawal_requested',
            collection: 'withdrawals',
            documentId: (string) $withdrawal->id,
            userId: null,
            description: "Winner {$winner->first_name} {$winner->last_name} requested withdrawal of \${$amount} via {$paymentMethod->name}.",
        );

        EmailHelper::sendAdmin(new AdminWithdrawalRequestNotification($withdrawal));

        if ($winner->email) {
            EmailHelper::send(
                new WinnerNotification(
                    $winner,
                    'Withdrawal Request Received',
                    "Hi {$winner->first_name}, we have received your withdrawal request for \${$amount} via {$paymentMethod->name}. Our team will process it shortly."
                ),
                $winner->email,
                $winner->first_name
            );
        }

        return redirect()->route('winner.withdrawals')->with('success', 'Withdrawal request submitted successfully.');
    }

    public function showDeposits(): View
    {
        $winner = Winner::with(['deposits.paymentMethod'])->find(session('winner_id'));
        $paymentMethods = PaymentMethod::active()->forDeposits()->orderBy('sort_order')->get();

        return view('pages.winner.deposits', compact('winner', 'paymentMethods'));
    }

    public function submitDeposit(Request $request): RedirectResponse
    {
        $winner = Winner::find(session('winner_id'));

        if (!$winner) {
            return redirect()->route('home')->withErrors(['error' => 'Winner not found.']);
        }

        $data = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'proof_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
        $file = $request->file('proof_file');
        $path = $file->store('deposits/' . $winner->id, 'public');

        $deposit = Deposit::create([
            'winner_id' => $winner->id,
            'payment_method_id' => $paymentMethod->id,
            'amount' => $data['amount'],
            'fee' => 0,
            'net_amount' => $data['amount'],
            'proof_file' => $path,
            'proof_file_name' => $file->getClientOriginalName(),
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        $this->activityLogger->log(
            action: 'deposit_submitted',
            collection: 'deposits',
            documentId: (string) $deposit->id,
            userId: null,
            description: "Winner {$winner->first_name} {$winner->last_name} submitted a deposit of \${$data['amount']} via {$paymentMethod->name}.",
        );

        EmailHelper::sendAdmin(new AdminDepositNotification($deposit));

        if ($winner->email) {
            EmailHelper::send(
                new WinnerNotification(
                    $winner,
                    'Deposit Submitted',
                    "Hi {$winner->first_name}, your deposit of \${$data['amount']} via {$paymentMethod->name} has been submitted for review. We'll notify you once it's approved."
                ),
                $winner->email,
                $winner->first_name
            );
        }

        return redirect()->route('winner.deposits')->with('success', 'Deposit submitted successfully. Awaiting admin approval.');
    }

    public function showTransactions(): View
    {
        $winner = Winner::with('transactions')->find(session('winner_id'));

        return view('pages.winner.transactions', compact('winner'));
    }

    public function showOrders(): View
    {
        $winner = Winner::find(session('winner_id'));

        $orders = collect();
        if ($winner && $winner->email) {
            $orders = ShopOrder::where('customer_email', $winner->email)
                ->latest()
                ->get();
        }

        return view('pages.winner.orders', compact('winner', 'orders'));
    }
}
