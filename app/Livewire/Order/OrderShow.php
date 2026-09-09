<?php

namespace App\Livewire\Order;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderShow extends Component
{
    use WithFileUploads;

    public string $number;

    public ?Order $order = null;

    public bool $verified = false;

    public string $verify_email = '';

    public string $verify_whatsapp = '';

    public $paymentProof;

    public function mount(string $number): void
    {
        $this->number = $number;
        $order = Order::query()
            ->where('number', $number)
            ->with(['items', 'payments'])
            ->firstOrFail();

        if (auth()->check() && $order->user_id === auth()->id()) {
            $this->order = $order;
            $this->verified = true;

            return;
        }

        $this->order = null;
    }

    public function verify(): void
    {
        $this->validate([
            'verify_email' => ['nullable', 'email'],
            'verify_whatsapp' => ['nullable', 'string'],
        ]);

        if ($this->verify_email === '' && $this->verify_whatsapp === '') {
            $this->addError('verify_email', 'Masukkan email atau nomor WhatsApp.');

            return;
        }

        $order = Order::query()
            ->where('number', $this->number)
            ->with(['items', 'payments'])
            ->firstOrFail();

        $emailMatch = $this->verify_email !== ''
            && $order->customer_email !== null
            && strcasecmp($order->customer_email, $this->verify_email) === 0;

        $waMatch = $this->verify_whatsapp !== ''
            && strcasecmp(preg_replace('/\D/', '', $order->customer_whatsapp), preg_replace('/\D/', '', $this->verify_whatsapp)) === 0;

        if (! $emailMatch && ! $waMatch) {
            $this->addError('verify_email', 'Data tidak cocok dengan pesanan ini.');

            return;
        }

        $this->order = $order;
        $this->verified = true;
    }

    public function uploadPaymentProof(): void
    {
        if ($this->order === null) {
            return;
        }

        $key = 'payment-proof:'.($this->order->id).':'.request()->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('paymentProof', 'Terlalu banyak percobaan unggah. Coba lagi nanti.');

            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 3600);

        $this->validate([
            'paymentProof' => ['required', 'image', 'max:5120'],
        ]);

        $payment = $this->order->payments->first();

        if ($payment === null || $payment->status === PaymentStatus::Paid) {
            $this->addError('paymentProof', 'Bukti pembayaran tidak dapat diunggah.');

            return;
        }

        $path = $this->paymentProof->store('payment-proofs', 'local');

        $payment->update([
            'proof_path' => $path,
            'status' => PaymentStatus::AwaitingConfirmation,
        ]);

        app(\App\Services\NotificationService::class)->send($this->order, 'email', 'payment_proof_uploaded', [
            'order_number' => $this->order->number,
        ]);

        $this->order->refresh();
        $this->paymentProof = null;
        session()->flash('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi.');
    }

    public function render()
    {
        return view('livewire.order.order-show')
            ->layout('layouts.shop', ['title' => 'Pesanan '.$this->number]);
    }
}
