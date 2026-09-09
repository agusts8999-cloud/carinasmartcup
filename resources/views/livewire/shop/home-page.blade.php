<div>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
            <div class="max-w-2xl">
                <p class="text-emerald-200 text-sm font-medium mb-2">Kemasan Cup Premium</p>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight mb-4">Solusi Cup Praktis untuk Bisnis F&amp;B Anda</h1>
                <p class="text-emerald-100 text-base sm:text-lg mb-8">Gelas cup berkualitas, harga grosir, pengiriman ke seluruh Indonesia. Pesan sekarang, produksi cepat!</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('catalog') }}" class="inline-flex items-center px-6 py-3 bg-white text-emerald-800 font-semibold rounded-full hover:bg-emerald-50 transition">Belanja Sekarang</a>
                    <a href="{{ route('cms.show', 'cara-belanja') }}" class="inline-flex items-center px-6 py-3 border-2 border-white/60 text-white font-semibold rounded-full hover:bg-white/10 transition">Cara Belanja</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    @if($categories->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Kategori Produk</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('catalog', ['category' => $category->slug]) }}" class="group block bg-white rounded-xl border border-emerald-100 overflow-hidden hover:border-emerald-300 hover:shadow-sm transition">
                        @php $categoryImage = $category->imageUrl(); @endphp
                        <div class="aspect-square bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center overflow-hidden">
                            @if($categoryImage)
                                <img src="{{ $categoryImage }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            @else
                                <span class="text-4xl">📦</span>
                            @endif
                        </div>
                        <div class="p-3 text-center">
                            <span class="font-medium text-gray-900 text-sm group-hover:text-emerald-700">{{ $category->name }}</span>
                            <span class="block text-xs text-gray-500 mt-1">{{ $category->products_count }} produk</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured Products --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Produk Unggulan</h2>
            <a href="{{ route('catalog') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-900">Lihat Semua →</a>
        </div>

        @if($featuredProducts->isEmpty())
            <p class="text-gray-500 text-center py-8">Belum ada produk unggulan. <a href="{{ route('catalog') }}" class="text-emerald-700 underline">Jelajahi katalog</a></p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- CTA --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center">
            <h2 class="text-xl font-bold text-emerald-900 mb-2">Butuh Bantuan Memilih?</h2>
            <p class="text-emerald-700 mb-4">Tim kami siap membantu Anda menemukan kemasan cup yang tepat.</p>
            <a href="https://wa.me/{{ website()->whatsapp() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white font-semibold rounded-full hover:bg-green-600 transition">
                Chat WhatsApp
            </a>
        </div>
    </section>
</div>
