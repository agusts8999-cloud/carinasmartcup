<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageWebsiteSettings;
use App\Filament\Resources\InventoryStocks\InventoryStockResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Support\DashboardMetrics;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class EnterpriseHeroWidget extends Widget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.enterprise-hero';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $kpi = app(DashboardMetrics::class)->kpi();
        $hour = (int) now()->format('G');
        $greeting = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        return [
            'greeting' => $greeting,
            'userName' => Auth::user()?->name ?? 'Admin',
            'todayLabel' => now()->translatedFormat('l, d F Y'),
            'ordersToday' => $kpi['orders_today'],
            'pendingPayments' => $kpi['pending_payments'],
            'ordersUrl' => OrderResource::getUrl('index'),
            'stockUrl' => InventoryStockResource::getUrl('index'),
            'settingsUrl' => ManageWebsiteSettings::getUrl(),
        ];
    }
}
