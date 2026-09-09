<?php

namespace App\Livewire\Checkout;

use App\Actions\PlaceOrder;
use App\Actions\RecalculateCart;
use App\Livewire\Concerns\ResolvesCart;
use App\Services\CartService;
use App\Services\ShippingQuoteService;
use Livewire\Component;

class CheckoutPage extends Component
{
    use ResolvesCart;

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_whatsapp = '';

    public string $province = '';

    public string $city = '';

    public string $district = '';

    public string $address_line = '';

    public string $postal_code = '';

    public ?int $shipping_rate_id = null;

    public string $notes = '';

    public bool $shipping_accepted_estimate = false;

    /** @var array<int, array<string, mixed>> */
    public array $shippingQuotes = [];

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name;
            $this->customer_email = $user->email ?? '';
            $this->customer_whatsapp = $user->whatsapp ?? '';
        }
    }

    public function updatedProvince(): void
    {
        $this->loadShippingQuotes();
    }

    public function updatedCity(): void
    {
        $this->loadShippingQuotes();
    }

    public function loadShippingQuotes(): void
    {
        if ($this->province === '') {
            $this->shippingQuotes = [];
            $this->shipping_rate_id = null;

            return;
        }

        $cartService = app(CartService::class);
        $recalculateCart = app(RecalculateCart::class);
        $shippingQuoteService = app(ShippingQuoteService::class);

        $cart = $this->resolveCart($cartService);
        $pricing = $recalculateCart->handle($cart);
        $netSubtotal = $pricing['subtotal'] - $pricing['discount'];

        $quotes = $shippingQuoteService->quote(
            $this->province,
            $this->city !== '' ? $this->city : null,
            (float) $pricing['weight_kg'],
            $netSubtotal,
            (int) $pricing['koli_count'],
        );

        $this->shippingQuotes = $quotes->values()->all();

        if ($this->shipping_rate_id !== null && ! collect($this->shippingQuotes)->contains('id', $this->shipping_rate_id)) {
            $this->shipping_rate_id = null;
        }
    }

    public function placeOrder(PlaceOrder $placeOrder, CartService $cartService): void
    {
        $this->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_whatsapp' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'shipping_rate_id' => ['required', 'integer'],
            'shipping_accepted_estimate' => ['accepted'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'shipping_rate_id.required' => 'Pilih metode pengiriman.',
            'shipping_accepted_estimate.accepted' => 'Anda harus menyetujui estimasi pengiriman.',
        ]);

        $cart = $this->resolveCart($cartService);

        try {
            $order = $placeOrder->handle([
                'cart' => $cart,
                'user_id' => auth()->id(),
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email ?: null,
                'customer_whatsapp' => $this->customer_whatsapp,
                'address' => [
                    'recipient_name' => $this->customer_name,
                    'phone' => $this->customer_whatsapp,
                    'address_line' => $this->address_line,
                    'district' => $this->district,
                    'city' => $this->city,
                    'province' => $this->province,
                    'postal_code' => $this->postal_code,
                ],
                'shipping_rate_id' => $this->shipping_rate_id,
                'notes' => $this->notes ?: null,
                'shipping_accepted_estimate' => $this->shipping_accepted_estimate,
            ]);

            $this->redirect(route('order.show', $order->number), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
        }
    }

    public function render(CartService $cartService, RecalculateCart $recalculateCart)
    {
        $cart = $this->resolveCart($cartService);
        $cart->load(['items.productVariant.product', 'coupon']);
        $pricing = $recalculateCart->handle($cart);

        return view('livewire.checkout.checkout-page', [
            'cart' => $cart,
            'pricing' => $pricing,
        ])->layout('layouts.shop', ['title' => 'Checkout']);
    }
}
