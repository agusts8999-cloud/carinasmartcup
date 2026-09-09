<?php

use App\Livewire\Account\OrderHistory;
use App\Livewire\Cart\CartPage;
use App\Livewire\Cart\ShareCart;
use App\Livewire\Checkout\CheckoutPage;
use App\Livewire\Order\OrderShow;
use App\Livewire\Order\TrackOrder;
use App\Livewire\Shop\CatalogPage;
use App\Livewire\Shop\CmsPageShow;
use App\Livewire\Shop\FaqPage;
use App\Livewire\Shop\HomePage;
use App\Livewire\Shop\ProductDetail;
use App\Livewire\Shop\ShippingCheck;
use App\Http\Controllers\CatalogTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/templates/katalog.csv', [CatalogTemplateController::class, 'template'])->name('catalog.template');
Route::get('/templates/katalog.xlsx', [CatalogTemplateController::class, 'templateXlsx'])->name('catalog.template.xlsx');
Route::get('/templates/katalog-contoh.csv', [CatalogTemplateController::class, 'sample'])->name('catalog.sample');
Route::get('/belanja', CatalogPage::class)->name('catalog');
Route::get('/produk/{slug}', ProductDetail::class)->name('product.show');
Route::get('/keranjang', CartPage::class)->name('cart');
Route::get('/keranjang/bagikan/{token}', ShareCart::class)->name('cart.share');
Route::get('/checkout', CheckoutPage::class)->name('checkout');
Route::get('/pesanan/{number}', OrderShow::class)->name('order.show');
Route::get('/lacak-pesanan', TrackOrder::class)->name('order.track');
Route::get('/cek-ongkir', ShippingCheck::class)->name('shipping.check');
Route::get('/bantuan', FaqPage::class)->name('faq');
Route::get('/halaman/{slug}', CmsPageShow::class)->name('cms.show');

Route::get('/dashboard', function () {
    if (auth()->check()) {
        return redirect()->route('account.orders');
    }

    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/akun/pesanan', OrderHistory::class)->name('account.orders');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
