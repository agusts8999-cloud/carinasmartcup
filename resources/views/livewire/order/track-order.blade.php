<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-6">Lacak Pesanan</h1>

    <form wire:submit="search" class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6 space-y-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Pesanan</label>
            <input wire:model="number" type="text" placeholder="CSC-..." class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
            @error('number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email atau WhatsApp</label>
            <input wire:model="contact" type="text" class="w-full rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
            @error('contact') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700">Cari Pesanan</button>
    </form>

    @if($order)
        <div class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">{{ $order->number }}</h2>
                <span class="px-3 py-1 rounded-full text-sm bg-emerald-100 text-emerald-800">{{ $order->status->label() }}</span>
            </div>
            <p class="text-sm text-gray-500 mb-4">{{ $order->created_at->format('d M Y H:i') }} · Total Rp {{ number_format((float) $order->total, 0, ',', '.') }}</p>

            <ul class="divide-y divide-gray-100 text-sm mb-4">
                @foreach($order->items as $item)
                    <li class="py-2 flex justify-between">
                        <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                        <span>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            @if($order->shipping_estimate_note)
                <p class="text-sm text-gray-600">{{ $order->shipping_estimate_note }}</p>
            @endif

            <a href="{{ route('order.show', $order->number) }}" class="inline-block mt-4 text-emerald-700 font-medium hover:underline">Lihat detail &amp; bayar →</a>
        </div>
    @endif
</div>
