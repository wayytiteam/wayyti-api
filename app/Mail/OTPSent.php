<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPSent extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $header;
    public $subject;
    public $message;
    public $greetings;
    public $action;
    /**
     * Create a new message instance.
     */
    public function __construct($code, $header, $subject, $message, $greetings = null, $action)
    {
        $this->code = $code;
        $this->header = $header;
        $this->subject = $subject;
        $this->message = $message;
        $this->greetings = $greetings;
        $this->action = $action;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.request_verification_code',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
