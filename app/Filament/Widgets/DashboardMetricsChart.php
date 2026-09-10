<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\InventoryStock;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class DashboardMetricsChart extends ChartWidget
{
    protected ?string $heading = 'Ringkasan Metrik';

    protected ?string $description = 'Visualisasi 4 kartu statistik di bawah. Pendapatan ditampilkan dalam juta Rupiah.';

    protected static ?int $sort = 0;

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $ordersToday = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $pendingPayments = Payment::query()
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
            ->count();

        $revenueThisMonth = (float) Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->whereNotNull('paid_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        $revenueInMillions = round($revenueThisMonth / 1_000_000, 2);

        $lowStockCount = InventoryStock::query()
            ->whereRaw('(qty_on_hand - qty_reserved) < 10')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Nilai',
                    'data' => [
                        $ordersToday,
                        $pendingPayments,
                        $revenueInMillions,
                        $lowStockCount,
                    ],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.75)',
                        'rgba(245, 158, 11, 0.75)',
                        'rgba(34, 197, 94, 0.75)',
                        'rgba(239, 68, 68, 0.75)',
                    ],
                    'borderColor' => [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => [
                'Pesanan Hari Ini',
                'Pembayaran Tertunda',
                'Pendapatan (Juta Rp)',
                'Stok Rendah',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
