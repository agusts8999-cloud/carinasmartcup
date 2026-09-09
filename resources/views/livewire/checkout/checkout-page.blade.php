<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-6">Checkout</h1>

    @if($cart->items->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Keranjang kosong.</p>
            <a href="{{ route('catalog') }}" class="text-emerald-700 underline">Kembali belanja</a>
        </div>
    @else
        <form wire:submit="placeOrder" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Data Pemesan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input wire:model="customer_name" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('customer_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input wire:model="customer_email" type="email" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('customer_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp *</label>
                            <input wire:model="customer_whatsapp" type="tel" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('customer_whatsapp') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Alamat Pengiriman</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi *</label>
                            <input wire:model.blur="province" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('province') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota/Kab *</label>
                            <input wire:model.blur="city" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('city') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                            <input wire:model="district" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('district') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input wire:model="postal_code" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                            <textarea wire:model="address_line" rows="3" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            @error('address_line') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Pengiriman</h2>
                    @if($shippingQuotes === [])
                        <p class="text-sm text-gray-500">Isi provinsi untuk melihat opsi pengiriman.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($shippingQuotes as $quote)
                                <label wire:key="ship-{{ $quote['id'] }}" class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer {{ $shipping_rate_id === $quote['id'] ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' }}">
                                    <input wire:model.live="shipping_rate_id" type="radio" value="{{ $quote['id'] }}" class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $quote['name'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $quote['zone_name'] }} · Estimasi {{ $quote['eta_days_min'] }}–{{ $quote['eta_days_max'] }} hari</p>
                                    </div>
                                    <span class="font-bold text-emerald-700">Rp {{ number_format($quote['price'], 0, ',', '.') }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('shipping_rate_id') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                </section>

                <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pesanan</label>
                    <textarea wire:model="notes" rows="2" placeholder="Opsional" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </section>

                <label class="flex items-start gap-3 text-sm">
                    <input wire:model="shipping_accepted_estimate" type="checkbox" class="mt-0.5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Saya menyetujui estimasi waktu pengiriman yang ditampilkan.</span>
                </label>
                @error('shipping_accepted_estimate') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('stock') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('cart') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="bg-white rounded-xl border border-emerald-100 p-4 sticky top-36">
                    <h2 class="font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                    <ul class="space-y-2 text-sm mb-4 max-h-48 overflow-y-auto">
                        @foreach($cart->items as $item)
                            <li class="flex justify-between gap-2">
                                <span class="truncate">{{ $item->productVariant->product->name }} × {{ $item->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <dl class="space-y-2 text-sm border-t border-gray-100 pt-4">
                        <div class="flex justify-between"><dt>Subtotal</dt><dd>Rp {{ number_format($pricing['subtotal'], 0, ',', '.') }}</dd></div>
                        @if($pricing['discount'] > 0)
                            <div class="flex justify-between text-emerald-700"><dt>Diskon</dt><dd>- Rp {{ number_format($pricing['discount'], 0, ',', '.') }}</dd></div>
                        @endif
                    </dl>
                    <button type="submit" wire:loading.attr="disabled" class="w-full mt-6 py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="placeOrder">Buat Pesanan</span>
                        <span wire:loading wire:target="placeOrder">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
