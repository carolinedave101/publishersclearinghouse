<?php

namespace App\Mail;

use App\Models\Deposit;
use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Deposit $deposit,
        public Winner $winner,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Deposit Confirmation - \${$this->deposit->amount}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deposit-confirmation',
            with: [
                'deposit' => $this->deposit,
                'winner' => $this->winner,
                'appUrl' => config('app.url'),
            ],
        );
    }
}
