<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWithdrawalRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Withdrawal $withdrawal) {}

    public function envelope(): Envelope
    {
        $winnerName = $this->withdrawal->winner?->first_name ?? 'Unknown';
        return new Envelope(
            subject: "Withdrawal Request - \${$this->withdrawal->amount} from {$winnerName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-withdrawal-request',
        );
    }
}
