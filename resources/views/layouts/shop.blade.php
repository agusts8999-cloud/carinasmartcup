<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CarinaSmartCup' }} — CarinaSmartCup</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-stone-50 text-gray-800">
    <header class="sticky top-0 z-40 bg-white border-b border-emerald-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-sm">C</span>
                    <span class="font-bold text-emerald-800 text-lg hidden sm:inline">CarinaSmartCup</span>
                </a>

                <form action="{{ route('catalog') }}" method="GET" class="flex-1 max-w-md">
                    <label for="search" class="sr-only">Cari produk</label>
                    <div class="relative">
                        <input
                            id="search"
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari gelas, cup, kemasan..."
                            class="w-full rounded-full border border-emerald-200 bg-stone-50 py-2 pl-4 pr-10 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-emerald-600 hover:text-emerald-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>
                </form>

                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <a href="{{ route('cart') }}" class="relative p-2 text-emerald-700 hover:text-emerald-900" aria-label="Keranjang">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span
                            x-data="{ count: {{ $cartItemCount ?? 0 }} }"
                            x-on:cart-updated.window="count = $event.detail.count"
                            x-show="count > 0"
                            x-text="count"
                            class="absolute -top-0.5 -right-0.5 min-w-[1.25rem] h-5 px-1 flex items-center justify-center rounded-full bg-teal-500 text-white text-xs font-semibold"
                        ></span>
                    </a>

                    @auth
                        <a href="{{ route('account.orders') }}" class="hidden sm:inline text-sm font-medium text-emerald-700 hover:text-emerald-900">Akun</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-medium text-emerald-700 hover:text-emerald-900">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>

        <nav class="border-t border-emerald-50 bg-emerald-50/50 overflow-x-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <ul class="flex gap-1 py-2 text-sm whitespace-nowrap">
                    @foreach([
                        ['Beranda', route('home')],
                        ['Belanja', route('catalog')],
                        ['Promo', route('catalog', ['featured' => 1])],
                        ['Cara Belanja', route('cms.show', 'cara-belanja')],
                        ['Cek Ongkir', route('shipping.check')],
                        ['Lacak Pesanan', route('order.track')],
                        ['Bantuan', route('faq')],
                    ] as [$label, $url])
                        <li>
                            <a href="{{ $url }}" class="inline-block px-3 py-1.5 rounded-full text-emerald-800 hover:bg-emerald-100 font-medium">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </header>

    <main class="min-h-[calc(100vh-12rem)]">
        {{ $slot }}
    </main>

    <footer class="mt-12 bg-emerald-900 text-emerald-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                <div class="sm:col-span-2">
                    <p class="font-bold text-xl text-white mb-2">CarinaSmartCup</p>
                    <p class="text-emerald-200 text-sm leading-relaxed">Solusi kemasan cup berkualitas untuk bisnis F&amp;B Anda. Praktis, higienis, dan siap kirim ke seluruh Indonesia.</p>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Informasi</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('cms.show', 'tentang-kami') }}" class="text-emerald-200 hover:text-white">Tentang Kami</a></li>
                        <li><a href="{{ route('cms.show', 'cara-belanja') }}" class="text-emerald-200 hover:text-white">Cara Belanja</a></li>
                        <li><a href="{{ route('shipping.check') }}" class="text-emerald-200 hover:text-white">Cek Ongkir</a></li>
                        <li><a href="{{ route('order.track') }}" class="text-emerald-200 hover:text-white">Lacak Pesanan</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Bantuan</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('faq') }}" class="text-emerald-200 hover:text-white">FAQ</a></li>
                        <li><a href="{{ route('cms.show', 'kebijakan-privasi') }}" class="text-emerald-200 hover:text-white">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('cms.show', 'syarat-ketentuan') }}" class="text-emerald-200 hover:text-white">Syarat &amp; Ketentuan</a></li>
                        <li><a href="{{ route('cms.show', 'kebijakan-retur') }}" class="text-emerald-200 hover:text-white">Kebijakan Retur</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Hubungi Kami</p>
                    <a href="https://wa.me/{{ config('carina.whatsapp_support') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-emerald-200 hover:text-white">
                        WhatsApp Support
                    </a>
                    <p class="mt-3 text-xs text-emerald-300 leading-relaxed">Senin–Sabtu, jam operasional gudang. Respon cepat via WhatsApp.</p>
                </div>
            </div>
            <p class="mt-8 pt-6 border-t border-emerald-800 text-center text-xs text-emerald-300">&copy; {{ date('Y') }} CarinaSmartCup. Semua hak dilindungi.</p>
        </div>
    </footer>

    <a
        href="https://wa.me/{{ config('carina.whatsapp_support') }}"
        target="_blank"
        rel="noopener"
        class="fixed bottom-5 right-5 z-50 flex items-center justify-center w-14 h-14 rounded-full bg-green-500 text-white shadow-lg hover:bg-green-600 transition"
        aria-label="Chat WhatsApp"
    >
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
</body>
</html>
