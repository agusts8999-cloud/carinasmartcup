<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $withRelations = ['category', 'media', 'variants' => fn ($q) => $q->where('is_active', true)];

        // Prefer featured products that already have images.
        $featuredProducts = Product::query()
            ->published()
            ->where('is_featured', true)
            ->whereHas('media')
            ->with($withRelations)
            ->latest('published_at')
            ->limit(8)
            ->get();

        // Fill remaining slots with latest published products that have images.
        if ($featuredProducts->count() < 8) {
            $extra = Product::query()
                ->published()
                ->whereHas('media')
                ->whereNotIn('id', $featuredProducts->pluck('id'))
                ->with($withRelations)
                ->latest('published_at')
                ->limit(8 - $featuredProducts->count())
                ->get();

            $featuredProducts = $featuredProducts->concat($extra);
        }

        $categories = Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['latestProductWithImage.media'])
            ->withCount(['products' => fn ($q) => $q->published()])
            ->get();

        return view('livewire.shop.home-page', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ])->layout('layouts.shop', ['title' => 'Beranda']);
    }
}
