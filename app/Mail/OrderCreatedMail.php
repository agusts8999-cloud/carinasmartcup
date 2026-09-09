<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan '.$this->order->number.' menunggu pembayaran — CarinaSmartCup',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('emails.order-created', ['order' => $this->order])->render(),
        );
    }
}
