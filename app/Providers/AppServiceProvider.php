<?php

namespace App\Providers;

use App\Services\CartService;
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
        View::composer('layouts.shop', function ($view): void {
            $cartService = app(CartService::class);
            $cart = $cartService->resolveCart(auth()->user(), request()->cookie('cart_token'));

            $view->with('cartItemCount', (int) $cart->items()->sum('quantity'));
        });
    }
}
