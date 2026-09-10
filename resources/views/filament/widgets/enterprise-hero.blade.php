<div class="enterprise-hero dash-fade-up">
    <div class="enterprise-hero__main">
        <p class="enterprise-hero__eyebrow">Command Center</p>
        <h2 class="enterprise-hero__title">{{ $greeting }}, {{ $userName }}</h2>
        <p class="enterprise-hero__meta">{{ $todayLabel }} · pantau operasi toko secara real-time</p>

        <div class="enterprise-hero__chips">
            <span class="enterprise-chip enterprise-chip--primary">
                {{ number_format($ordersToday) }} pesanan hari ini
            </span>
            <span class="enterprise-chip enterprise-chip--warning">
                {{ number_format($pendingPayments) }} pembayaran menunggu
            </span>
        </div>
    </div>

    <div class="enterprise-hero__actions">
        <a href="{{ $ordersUrl }}" class="enterprise-quick-link">Pesanan</a>
        <a href="{{ $stockUrl }}" class="enterprise-quick-link">Stok</a>
        <a href="{{ $settingsUrl }}" class="enterprise-quick-link enterprise-quick-link--ghost">Pengaturan</a>
    </div>
</div>
