<div class="enterprise-panel dash-fade-up dash-delay-2">
    <div class="enterprise-panel__header">
        <div>
            <h3 class="enterprise-panel__title">Antrian Perhatian</h3>
            <p class="enterprise-panel__desc">Item yang perlu ditindak sekarang</p>
        </div>
    </div>

    <div class="enterprise-panel__section">
        <div class="enterprise-panel__section-head">
            <span>Pembayaran menunggu</span>
            <a href="{{ $ordersUrl }}" class="enterprise-panel__link">Lihat pesanan</a>
        </div>
        @forelse($payments as $payment)
            <div class="enterprise-row">
                <div>
                    <p class="enterprise-row__title">{{ $payment->order?->number ?? '—' }}</p>
                    <p class="enterprise-row__meta">{{ $payment->status->value }} · Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <p class="enterprise-empty">Tidak ada pembayaran tertunda.</p>
        @endforelse
    </div>

    <div class="enterprise-panel__section">
        <div class="enterprise-panel__section-head">
            <span>Stok rendah</span>
            <a href="{{ $stockUrl }}" class="enterprise-panel__link">Kelola stok</a>
        </div>
        @forelse($stocks as $stock)
            @php
                $available = (int) $stock->qty_on_hand - (int) $stock->qty_reserved;
                $label = $stock->productVariant?->product?->name
                    ?? $stock->productVariant?->sku
                    ?? 'SKU #'.$stock->product_variant_id;
            @endphp
            <div class="enterprise-row">
                <div>
                    <p class="enterprise-row__title">{{ $label }}</p>
                    <p class="enterprise-row__meta">Tersedia {{ $available }} unit</p>
                </div>
            </div>
        @empty
            <p class="enterprise-empty">Stok aman — tidak ada alert.</p>
        @endforelse
    </div>
</div>
