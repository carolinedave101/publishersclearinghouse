<?php

namespace App\Mail;

use App\Models\UserMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminUserMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public UserMessage $message) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New User Message - {$this->message->subject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-user-message',
        );
    }
}
