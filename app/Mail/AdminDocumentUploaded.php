<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Winner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDocumentUploaded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Document $document, public Winner $winner) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Document Uploaded - {$this->winner->first_name} {$this->winner->last_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-document-uploaded',
        );
    }
}
