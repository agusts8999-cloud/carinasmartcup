<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-6">Keranjang Belanja</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($cart->items->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-emerald-100">
            <p class="text-gray-500 mb-4">Keranjang Anda masih kosong.</p>
            <a href="{{ route('catalog') }}" class="inline-flex px-6 py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700">Mulai Belanja</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    @php $variant = $item->productVariant; $product = $variant->product; @endphp
                    <div wire:key="item-{{ $item->id }}" class="bg-white rounded-xl border border-emerald-100 p-4 flex gap-4">
                        <div class="w-20 h-20 shrink-0 bg-emerald-50 rounded-lg flex items-center justify-center text-2xl">🥤</div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('product.show', $product->slug) }}" class="font-semibold text-gray-900 hover:text-emerald-700">{{ $product->name }}</a>
                            <p class="text-sm text-gray-500">{{ $variant->name }} · {{ $variant->sku }}</p>
                            <p class="text-emerald-700 font-bold mt-1">Rp {{ number_format((float) ($pricing['unit_prices'][$variant->id] ?? $variant->price_retail), 0, ',', '.') }}</p>

                            <div class="mt-3 flex items-center gap-3">
                                <input
                                    type="number"
                                    value="{{ $item->quantity }}"
                                    wire:change="updateQuantity({{ $variant->id }}, $event.target.value)"
                                    min="{{ $variant->min_order_qty }}"
                                    step="{{ $variant->order_multiple }}"
                                    class="w-24 rounded-lg border-emerald-200 text-sm"
                                >
                                <button wire:click="removeItem({{ $variant->id }})" type="button" class="text-sm text-red-600 hover:underline">Hapus</button>
                            </div>
                            @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-emerald-100 p-4">
                    <h2 class="font-semibold text-gray-900 mb-4">Ringkasan</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd>Rp {{ number_format($pricing['subtotal'], 0, ',', '.') }}</dd></div>
                        @if($pricing['discount'] > 0)
                            <div class="flex justify-between text-emerald-700"><dt>Diskon volume</dt><dd>- Rp {{ number_format($pricing['discount'], 0, ',', '.') }}</dd></div>
                        @endif
                        <div class="flex justify-between"><dt class="text-gray-500">Berat</dt><dd>{{ number_format($pricing['weight_kg'], 2, ',', '.') }} kg ({{ $pricing['koli_count'] }} koli)</dd></div>
                    </dl>

                    @if($pricing['next_tier_remaining'] !== null)
                        <p class="mt-3 text-xs text-emerald-600 bg-emerald-50 p-2 rounded-lg">
                            Tambah Rp {{ number_format($pricing['next_tier_remaining'], 0, ',', '.') }} lagi untuk diskon tier berikutnya!
                        </p>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span class="text-emerald-700">Rp {{ number_format($pricing['subtotal'] - $pricing['discount'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-emerald-100 p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Kupon</label>
                    <div class="flex gap-2">
                        <input wire:model="couponCode" type="text" placeholder="Masukkan kode" class="flex-1 rounded-lg border-emerald-200 text-sm">
                        <button wire:click="applyCoupon" type="button" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Pakai</button>
                    </div>
                    @error('coupon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    @if($cart->coupon)
                        <button wire:click="removeCoupon" type="button" class="mt-2 text-xs text-red-600 hover:underline">Hapus kupon ({{ $cart->coupon->code }})</button>
                    @endif
                </div>

                <button wire:click="shareCart" type="button" class="w-full py-2.5 border border-emerald-300 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50">
                    Bagikan Keranjang
                </button>
                @if($shareLink)
                    <div class="p-3 bg-gray-50 rounded-lg text-xs break-all">
                        <p class="text-gray-500 mb-1">Link bagikan:</p>
                        <input type="text" readonly value="{{ $shareLink }}" class="w-full bg-transparent text-emerald-700" onclick="this.select(); navigator.clipboard.writeText(this.value)">
                    </div>
                @endif

                <a href="{{ route('checkout') }}" class="block w-full text-center py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700 transition">
                    Lanjut Checkout
                </a>
            </div>
        </div>
    @endif
</div>
