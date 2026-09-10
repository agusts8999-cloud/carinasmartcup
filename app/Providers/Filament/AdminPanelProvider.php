<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DashboardMetricsChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = app(\App\Services\SettingsService::class);
        $brandName = $settings->siteName();
        $logoUrl = $settings->logoUrl();

        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName($brandName)
            ->colors([
                'primary' => Color::Emerald,
            ]);

        if (filled($logoUrl)) {
            $panel->brandLogo($logoUrl)->brandLogoHeight('2rem');
        }

        return $panel
            ->navigationGroups([
                NavigationGroup::make('Katalog'),
                NavigationGroup::make('Penjualan'),
                NavigationGroup::make('Inventori'),
                NavigationGroup::make('Pengiriman'),
                NavigationGroup::make('Pemasaran'),
                NavigationGroup::make('Pelanggan'),
                NavigationGroup::make('Konten'),
                NavigationGroup::make('Sistem'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DashboardMetricsChart::class,
                StatsOverview::class,
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
