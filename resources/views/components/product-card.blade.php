@php
    $minPrice = $product->variants->min('price_retail');
    $imageUrl = $product->primaryImageUrl();
@endphp
<a href="{{ route('product.show', $product->slug) }}" class="group block bg-white rounded-xl border border-emerald-100 overflow-hidden hover:shadow-md transition">
    <div class="aspect-square bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
        @else
            <span class="text-4xl">🥤</span>
        @endif
    </div>
    <div class="p-3">
        @if($product->category)
            <p class="text-xs text-emerald-600 font-medium mb-1">{{ $product->category->name }}</p>
        @endif
        <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-emerald-700">{{ $product->name }}</h3>
        @if($product->capacity_ml)
            <p class="text-xs text-gray-500 mt-1">{{ $product->capacity_ml }} ml · {{ $product->material }}</p>
        @endif
        @if($minPrice)
            <p class="mt-2 font-bold text-emerald-700">Rp {{ number_format((float) $minPrice, 0, ',', '.') }}</p>
        @endif
    </div>
</a>
