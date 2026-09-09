<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\InventoryStock;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ordersToday = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $pendingPayments = Payment::query()
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
            ->count();

        $revenueThisMonth = Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->whereNotNull('paid_at')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        $lowStockCount = InventoryStock::query()
            ->whereRaw('(qty_on_hand - qty_reserved) < 10')
            ->count();

        return [
            Stat::make('Pesanan Hari Ini', number_format($ordersToday))
                ->description('Total pesanan masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Pembayaran Tertunda', number_format($pendingPayments))
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            Stat::make('Pendapatan Bulan Ini', 'Rp '.number_format($revenueThisMonth, 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Stok Rendah', number_format($lowStockCount))
                ->description('Di bawah 10 unit tersedia')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
