<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Filters sidebar --}}
        <aside class="lg:w-64 shrink-0">
            <div class="bg-white rounded-xl border border-emerald-100 p-4 sticky top-36">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Filter</h2>
                    <button wire:click="clearFilters" type="button" class="text-xs text-emerald-600 hover:underline">Reset</button>
                </div>

                <div class="space-y-4 text-sm">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Cari</label>
                        <input wire:model.live.debounce.300ms="q" type="search" placeholder="Nama produk..." class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Kategori</label>
                        <select wire:model.live="category" class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Semua</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($materials->isNotEmpty())
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Material</label>
                            <select wire:model.live="material" class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua</option>
                                @foreach($materials as $mat)
                                    <option value="{{ $mat }}">{{ $mat }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($capacities->isNotEmpty())
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Kapasitas (ml)</label>
                            <select wire:model.live="capacity" class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua</option>
                                @foreach($capacities as $cap)
                                    <option value="{{ $cap }}">{{ $cap }} ml</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($lidTypes->isNotEmpty())
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Tutup</label>
                            <select wire:model.live="lid_type" class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua</option>
                                @foreach($lidTypes as $lid)
                                    <option value="{{ $lid }}">{{ $lid }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($unitTypes->isNotEmpty())
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Satuan</label>
                            <select wire:model.live="unit_type" class="w-full rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Semua</option>
                                @foreach($unitTypes as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </aside>

        {{-- Product grid --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <h1 class="text-xl font-bold text-gray-900">{{ $featured ? 'Promo' : 'Belanja' }}</h1>
                <select wire:model.live="sort" class="rounded-lg border-emerald-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="newest">Terbaru</option>
                    <option value="price_asc">Harga: Rendah ke Tinggi</option>
                    <option value="price_desc">Harga: Tinggi ke Rendah</option>
                </select>
            </div>

            <div wire:loading.class="opacity-50" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <p>Tidak ada produk ditemukan.</p>
                        <button wire:click="clearFilters" type="button" class="mt-2 text-emerald-700 underline text-sm">Reset filter</button>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
