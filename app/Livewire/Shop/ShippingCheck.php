<?php

namespace App\Livewire\Shop;

use App\Services\ShippingQuoteService;
use Livewire\Component;

class ShippingCheck extends Component
{
    public string $province = '';

    public string $city = '';

    public float $weight_kg = 1;

    public float $subtotal = 0;

    /** @var array<int, array<string, mixed>> */
    public array $quotes = [];

    public function calculate(ShippingQuoteService $shippingQuoteService): void
    {
        $this->validate([
            'province' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'weight_kg' => ['required', 'numeric', 'min:0.1'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $quotes = $shippingQuoteService->quote(
            $this->province,
            $this->city !== '' ? $this->city : null,
            $this->weight_kg,
            $this->subtotal,
        );

        $this->quotes = $quotes->values()->all();

        if ($this->quotes === []) {
            $this->addError('province', 'Tidak ada tarif pengiriman untuk lokasi ini.');
        }
    }

    public function render()
    {
        return view('livewire.shop.shipping-check')
            ->layout('layouts.shop', ['title' => 'Cek Ongkir']);
    }
}
