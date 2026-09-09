<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if($variant)
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => strip_tags((string) $product->description),
        'sku' => $variant->sku,
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => 'IDR',
            'price' => (float) $variant->price_retail,
            'availability' => $available > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url' => route('product.show', $product->slug),
        ],
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('home') }}" class="hover:text-emerald-700">Beranda</a>
        <span class="mx-1">/</span>
        <a href="{{ route('catalog') }}" class="hover:text-emerald-700">Belanja</a>
        <span class="mx-1">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Image --}}
        <div class="bg-white rounded-2xl border border-emerald-100 overflow-hidden">
            @php $imageUrl = $product->primaryImageUrl(); @endphp
            <div class="aspect-square bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-8xl">🥤</span>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div>
            @if($product->category)
                <p class="text-sm text-emerald-600 font-medium">{{ $product->category->name }}</p>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>

            @if($variant)
                <p class="text-2xl font-bold text-emerald-700 mt-4">Rp {{ number_format((float) $variant->price_retail, 0, ',', '.') }}</p>
            @endif

            @if($product->description)
                <div class="mt-4 text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none">{!! nl2br(e($product->description)) !!}</div>
            @endif

            @if($product->variants->count() > 1)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Varian</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->variants as $v)
                            <button
                                wire:click="$set('selectedVariantId', {{ $v->id }})"
                                type="button"
                                class="px-4 py-2 rounded-lg border text-sm font-medium transition {{ $selectedVariantId === $v->id ? 'border-emerald-600 bg-emerald-50 text-emerald-800' : 'border-gray-200 hover:border-emerald-300' }}"
                            >
                                {{ $v->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($variant)
                <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div><dt class="text-gray-500">SKU</dt><dd class="font-medium">{{ $variant->sku }}</dd></div>
                    <div><dt class="text-gray-500">Stok</dt><dd class="font-medium {{ $available > 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $available > 0 ? $available.' tersedia' : 'Habis' }}</dd></div>
                    @if($variant->pack_size)
                        <div><dt class="text-gray-500">Isi Pack</dt><dd class="font-medium">{{ $variant->pack_size }} pcs</dd></div>
                    @endif
                    <div><dt class="text-gray-500">MOQ</dt><dd class="font-medium">{{ $variant->min_order_qty }} (kelipatan {{ $variant->order_multiple }})</dd></div>
                </dl>
            @endif

            <div class="mt-6">
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                <input wire:model="quantity" id="qty" type="number" min="1" class="w-32 rounded-lg border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    wire:click="addToCart"
                    wire:loading.attr="disabled"
                    type="button"
                    class="flex-1 sm:flex-none inline-flex justify-center items-center px-8 py-3 bg-emerald-600 text-white font-semibold rounded-full hover:bg-emerald-700 disabled:opacity-50 transition"
                    @if(!$variant || $available <= 0) disabled @endif
                >
                    <span wire:loading.remove wire:target="addToCart">Tambah ke Keranjang</span>
                    <span wire:loading wire:target="addToCart">Menambahkan...</span>
                </button>
                <a href="https://wa.me/{{ config('carina.whatsapp_support') }}?text={{ urlencode('Halo, saya ingin tanya tentang '.$product->name) }}" target="_blank" rel="noopener" class="inline-flex items-center px-6 py-3 border border-green-500 text-green-700 font-semibold rounded-full hover:bg-green-50 transition">
                    Tanya via WA
                </a>
            </div>
        </div>
    </div>
</div>
