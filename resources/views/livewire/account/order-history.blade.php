<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-6">Riwayat Pesanan</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl border border-emerald-100">
            <p class="text-gray-500 mb-4">Belum ada pesanan.</p>
            <a href="{{ route('catalog') }}" class="text-emerald-700 underline">Mulai belanja</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div wire:key="order-{{ $order->id }}" class="bg-white rounded-xl border border-emerald-100 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <a href="{{ route('order.show', $order->number) }}" class="font-bold text-gray-900 hover:text-emerald-700">{{ $order->number }}</a>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} item</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ $order->status->label() }}</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('order.show', $order->number) }}" class="text-sm text-emerald-700 hover:underline">Detail</a>
                        <button wire:click="reorder({{ $order->id }})" type="button" class="text-sm text-emerald-700 hover:underline">Pesan Ulang</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
