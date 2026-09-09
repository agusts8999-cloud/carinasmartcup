<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-xl font-bold text-gray-900 mb-2">Bantuan & FAQ</h1>
    <p class="text-sm text-gray-500 mb-8">Temukan jawaban untuk pertanyaan umum seputar pemesanan CarinaSmartCup.</p>

    @if($faqs->isEmpty())
        <div class="bg-white rounded-xl border border-emerald-100 p-8 text-center text-gray-500">
            <p>Belum ada FAQ tersedia.</p>
            <a href="https://wa.me/{{ website()->whatsapp() }}" target="_blank" rel="noopener" class="inline-block mt-4 text-emerald-700 font-medium">Hubungi WhatsApp →</a>
        </div>
    @else
        <div class="space-y-3" x-data="{ open: null }">
            @foreach($faqs as $index => $faq)
                <div wire:key="faq-{{ $faq->id }}" class="bg-white rounded-xl border border-emerald-100 overflow-hidden">
                    <button
                        type="button"
                        @click="open = open === {{ $index }} ? null : {{ $index }}"
                        class="w-full flex items-center justify-between p-4 text-left font-medium text-gray-900 hover:bg-emerald-50 transition"
                    >
                        {{ $faq->title }}
                        <svg class="w-5 h-5 shrink-0 transition" :class="open === {{ $index }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $index }}" class="px-4 pb-4 text-sm text-gray-600 prose prose-sm max-w-none">
                        {!! $faq->body !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-8 bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center">
        <p class="text-emerald-800 font-medium mb-2">Masih butuh bantuan?</p>
        <a href="https://wa.me/{{ website()->whatsapp() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 text-white font-semibold rounded-full hover:bg-green-600">Chat WhatsApp</a>
    </div>
</div>
