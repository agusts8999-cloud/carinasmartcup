<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $number = '';

    public string $contact = '';

    public ?Order $order = null;

    public function search(): void
    {
        $this->validate([
            'number' => ['required', 'string'],
            'contact' => ['required', 'string'],
        ]);

        $order = Order::query()
            ->where('number', trim($this->number))
            ->with(['items', 'payments', 'shipments'])
            ->first();

        if ($order === null) {
            $this->addError('number', 'Pesanan tidak ditemukan.');
            $this->order = null;

            return;
        }

        $contact = trim($this->contact);
        $emailMatch = $order->customer_email !== null && strcasecmp($order->customer_email, $contact) === 0;
        $waMatch = strcasecmp(
            preg_replace('/\D/', '', $order->customer_whatsapp),
            preg_replace('/\D/', '', $contact)
        ) === 0;

        if (! $emailMatch && ! $waMatch) {
            $this->addError('contact', 'Email atau WhatsApp tidak cocok.');
            $this->order = null;

            return;
        }

        $this->order = $order;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.order.track-order')
            ->layout('layouts.shop', ['title' => 'Lacak Pesanan']);
    }
}
