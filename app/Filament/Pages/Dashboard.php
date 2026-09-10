<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AttentionQueueWidget;
use App\Filament\Widgets\DashboardMetricsChart;
use App\Filament\Widgets\EnterpriseHeroWidget;
use App\Filament\Widgets\RecentOrdersWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Ringkasan Operasional';

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            EnterpriseHeroWidget::class,
            StatsOverview::class,
            DashboardMetricsChart::class,
            AttentionQueueWidget::class,
            RecentOrdersWidget::class,
        ];
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}
