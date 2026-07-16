<?php

namespace App\Mail;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDepositNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Deposit $deposit) {}

    public function envelope(): Envelope
    {
        $winnerName = $this->deposit->winner?->first_name ?? 'Unknown';
        return new Envelope(
            subject: "New Deposit - \${$this->deposit->amount} from {$winnerName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-deposit',
        );
    }
}
