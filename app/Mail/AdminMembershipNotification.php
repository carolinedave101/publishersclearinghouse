<?php

namespace App\Mail;

use App\Models\MembershipSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminMembershipNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipSubscription $subscription) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Membership Signup - {$this->subscription->subscriber_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-membership-notification',
        );
    }
}
