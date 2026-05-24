<?php

namespace Modules\Contact\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Contact\Models\ContactMessage;

class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $message,
        public string $replySubject,
        public string $replyMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->replySubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'contact::emails.reply',
            with: [
                'contactMessage' => $this->message,
                'replyMessage' => $this->replyMessage,
            ],
        );
    }
}

