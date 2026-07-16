<?php

namespace App\Mail;

use App\Models\MembershipSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MembershipConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipSubscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to PCH Membership!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membership-confirmation',
        );
    }
}
