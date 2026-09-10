<?php

namespace App\Filament\Widgets;

use App\Support\DashboardMetrics;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $metrics = app(DashboardMetrics::class);
        $kpi = $metrics->kpi();
        $series = $metrics->lastSevenDays();

        return [
            Stat::make('Pesanan Hari Ini', number_format($kpi['orders_today']))
                ->description('Total pesanan masuk hari ini')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($series['orders'])
                ->chartColor('primary')
                ->color('primary')
                ->extraAttributes(['class' => 'enterprise-kpi dash-fade-up dash-delay-1']),
            Stat::make('Pembayaran Tertunda', number_format($kpi['pending_payments']))
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($series['pending_payments'])
                ->chartColor('warning')
                ->color('warning')
                ->extraAttributes(['class' => 'enterprise-kpi dash-fade-up dash-delay-2']),
            Stat::make('Pendapatan Bulan Ini', 'Rp '.number_format($kpi['revenue_month'], 0, ',', '.'))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart($series['revenue_millions'])
                ->chartColor('success')
                ->color('success')
                ->extraAttributes(['class' => 'enterprise-kpi dash-fade-up dash-delay-3']),
            Stat::make('Stok Rendah', number_format($kpi['low_stock']))
                ->description('Di bawah 10 unit tersedia')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart(array_fill(0, 7, max(1, $kpi['low_stock'])))
                ->chartColor('danger')
                ->color('danger')
                ->extraAttributes(['class' => 'enterprise-kpi dash-fade-up dash-delay-4']),
        ];
    }
}
