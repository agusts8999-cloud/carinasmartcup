<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applyWebsiteSettings();

        View::composer('layouts.shop', function ($view): void {
            $cartService = app(CartService::class);
            $cart = $cartService->resolveCart(auth()->user(), request()->cookie('cart_token'));
            $settings = app(SettingsService::class);

            $view->with([
                'cartItemCount' => (int) $cart->items()->sum('quantity'),
                'websiteName' => $settings->siteName(),
                'websiteLogoUrl' => $settings->logoUrl(),
                'websiteFaviconUrl' => $settings->faviconUrl(),
                'websiteWhatsapp' => $settings->whatsapp(),
                'websiteThemePreset' => $settings->themePreset(),
                'websiteThemeStyle' => $settings->themeStyleString(),
            ]);
        });
    }

    private function applyWebsiteSettings(): void
    {
        try {
            app(SettingsService::class)->applyRuntimeConfig();
        } catch (\Throwable) {
            // Settings table may not exist yet during early migrate/install.
        }
    }
}
