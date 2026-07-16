<?php

namespace App\Mail;

use App\Models\Withdrawal;
use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Withdrawal $withdrawal,
        public Winner $winner,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Withdrawal Status Update - \${$this->withdrawal->amount}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-status',
            with: [
                'withdrawal' => $this->withdrawal,
                'winner' => $this->winner,
                'appUrl' => config('app.url'),
            ],
        );
    }
}
