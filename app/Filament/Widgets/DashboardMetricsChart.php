<?php

namespace App\Filament\Widgets;

use App\Support\DashboardMetrics;
use Filament\Widgets\ChartWidget;

class DashboardMetricsChart extends ChartWidget
{
    protected ?string $heading = 'Tren 7 Hari';

    protected ?string $description = 'Pesanan harian (garis) dan pendapatan dalam juta Rupiah (batang).';

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $series = app(DashboardMetrics::class)->lastSevenDays();

        return [
            'datasets' => [
                [
                    'type' => 'line',
                    'label' => 'Pesanan',
                    'data' => $series['orders'],
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'tension' => 0.35,
                    'fill' => true,
                    'yAxisID' => 'y',
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Pendapatan (Juta Rp)',
                    'data' => $series['revenue_millions'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.55)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $series['labels'],
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
            'animation' => [
                'duration' => 900,
                'easing' => 'easeOutQuart',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'grid' => [
                        'drawOnChartArea' => true,
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
