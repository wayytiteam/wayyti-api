<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordOtpSent extends Mailable
{
    use Queueable, SerializesModels;
    public $code;
    public $header;
    public $message;
    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            <x-mail::message>
                <strong style="font-size: 20px;">{{ $greetings }}</strong>

                {{ $header }}

                <strong style="font-size: 30px; color: #0077CE;">{{ $code }}</strong>

                This code is valid for the next 24 hours. <br><br>
                {{ $message }}

                Thank you,<br>
                The Waytti Team
            </x-mail::message>

            subject: 'Password Otp Sent',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
