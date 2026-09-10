<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\InventoryStocks\InventoryStockResource;
use App\Filament\Resources\Orders\OrderResource;
use App\Support\DashboardMetrics;
use Filament\Widgets\Widget;

class AttentionQueueWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.attention-queue';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $metrics = app(DashboardMetrics::class);

        return [
            'payments' => $metrics->pendingPaymentRows(5),
            'stocks' => $metrics->lowStockRows(5),
            'ordersUrl' => OrderResource::getUrl('index'),
            'stockUrl' => InventoryStockResource::getUrl('index'),
        ];
    }
}
