<?php

namespace App\Mail;

use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WinnerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Winner $winner,
        public string $subjectLine = '',
        public string $messageBody = '',
    ) {}

    public function envelope(): Envelope
    {
        $prize = '$' . number_format($this->winner->prize_amount, 0);
        $subject = $this->subjectLine
            ?: "Congratulations {$this->winner->first_name}! You've Won {$prize}!";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.winner-activity',
            with: [
                'winner' => $this->winner,
                'formattedPrize' => '$' . number_format($this->winner->prize_amount, 0),
                'appUrl' => config('app.url'),
                'messageBody' => $this->messageBody,
            ],
        );
    }
}
