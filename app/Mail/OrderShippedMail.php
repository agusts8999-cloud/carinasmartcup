<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public Shipment $shipment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan '.$this->order->number.' telah dikirim',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('emails.order-shipped', [
                'order' => $this->order,
                'shipment' => $this->shipment,
            ])->render(),
        );
    }
}
