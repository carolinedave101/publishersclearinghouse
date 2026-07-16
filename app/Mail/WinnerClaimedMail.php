<?php

namespace App\Mail;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WinnerClaimedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Winner $winner) {}

    public function envelope(): Envelope
    {
        $prize = '$' . number_format($this->winner->prize_amount, 0);
        return new Envelope(
            subject: "Congratulations {$this->winner->first_name}! You've Won {$prize}!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.winner-notification',
            with: [
                'winner' => $this->winner,
                'formattedPrize' => '$' . number_format($this->winner->prize_amount, 0),
                'appUrl' => config('app.url'),
            ],
        );
    }
}
