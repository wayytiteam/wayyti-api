<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriceDownUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $old_price;
    public $new_price;
    public $product_name;
    public $percentage;
    public $store_url;
    public $seller;
    /**
     * Create a new message instance.
     */
    public function __construct($old_price, $new_price, $product_name, $percentage, $store_url, $seller)
    {
        $this->old_price = $old_price;
        $this->new_price = $new_price;
        $this->product_name = $product_name;
        $this->percentage = $percentage;
        $this->store_url = $store_url;
        $this->seller = $seller;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Price Drop Alert – One of Your Tracked Items Just Got Cheaper',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.price_down_update',
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
