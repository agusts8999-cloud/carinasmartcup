<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if(!$verified)
        <div class="max-w-md mx-auto bg-white rounded-xl border border-emerald-100 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Verifikasi Pesanan</h1>
            <p class="text-sm text-gray-500 mb-6">Masukkan email atau WhatsApp yang digunakan saat checkout untuk melihat pesanan <strong>{{ $number }}</strong>.</p>
            <form wire:submit="verify" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input wire:model="verify_email" type="email" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                    <input wire:model="verify_whatsapp" type="tel" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                @error('verify_email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700">Lihat Pesanan</button>
            </form>
        </div>
    @elseif($order)
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Pesanan {{ $order->number }}</h1>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">{{ $order->status->label() }}</span>
        </div>

        <div class="space-y-4">
            <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Item Pesanan</h2>
                <ul class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <li class="py-3 flex justify-between gap-4 text-sm">
                            <div>
                                <p class="font-medium">{{ $item->product_name }}</p>
                                <p class="text-gray-500">{{ $item->variant_name }} · {{ $item->quantity }} × Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-medium">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-4 pt-4 border-t border-gray-100 space-y-1 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</dd></div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700"><dt>Diskon volume</dt><dd>- Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</dd></div>
                    @endif
                    @if($order->coupon_discount > 0)
                        <div class="flex justify-between text-emerald-700"><dt>Kupon ({{ $order->coupon_code }})</dt><dd>- Rp {{ number_format((float) $order->coupon_discount, 0, ',', '.') }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt class="text-gray-500">Ongkir ({{ $order->shipping_rate_name }})</dt><dd>Rp {{ number_format((float) $order->shipping_amount, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between font-bold text-lg pt-2"><dt>Total</dt><dd class="text-emerald-700">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</dd></div>
                </dl>
            </section>

            @php $payment = $order->payments->first(); @endphp
            @if($payment && !in_array($payment->status->value, ['paid', 'rejected', 'expired']))
                <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Instruksi Pembayaran</h2>
                    <div class="bg-emerald-50 rounded-lg p-4 text-sm space-y-2 mb-4">
                        <p>Transfer ke rekening berikut:</p>
                        <p class="font-bold text-emerald-900">{{ config('carina.bank.name') }} — {{ config('carina.bank.account_number') }}</p>
                        <p>a.n. {{ config('carina.bank.account_name') }}</p>
                        <p class="font-bold">Jumlah: Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                        @if($order->payment_due_at)
                            <p class="text-red-600">Batas waktu: {{ $order->payment_due_at->format('d M Y H:i') }}</p>
                        @endif
                    </div>

                    @if(in_array($payment->status->value, ['pending', 'awaiting_confirmation']))
                        <form wire:submit="uploadPaymentProof" class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label>
                            <input wire:model="paymentProof" type="file" accept="image/*" class="text-sm">
                            @error('paymentProof') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            <div wire:loading wire:target="paymentProof" class="text-sm text-gray-500">Mengunggah...</div>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Kirim Bukti</button>
                        </form>
                    @endif

                    @if($payment->status->value === 'awaiting_confirmation')
                        <p class="mt-3 text-sm text-amber-700 bg-amber-50 p-2 rounded-lg">Bukti pembayaran sudah diterima. Menunggu konfirmasi admin.</p>
                    @endif
                </section>
            @endif

            <section class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6 text-sm">
                <h2 class="font-semibold text-gray-900 mb-2">Alamat Pengiriman</h2>
                @php $addr = $order->shipping_address_snapshot ?? []; @endphp
                <p>{{ $order->customer_name }}</p>
                <p>{{ $addr['address_line'] ?? '' }}</p>
                <p>{{ ($addr['district'] ?? '').', '.($addr['city'] ?? '').', '.($addr['province'] ?? '') }} {{ $addr['postal_code'] ?? '' }}</p>
                <p class="mt-2 text-gray-500">WA: {{ $order->customer_whatsapp }}</p>
            </section>
        </div>
    @endif
</div>
