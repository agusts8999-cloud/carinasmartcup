<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogPage extends Component
{
    use WithPagination;

    public string $q = '';

    public string $category = '';

    public string $material = '';

    public string $capacity = '';

    public string $lid_type = '';

    public string $unit_type = '';

    public string $sort = 'newest';

    public bool $featured = false;

    protected $queryString = [
        'q' => ['except' => ''],
        'category' => ['except' => ''],
        'material' => ['except' => ''],
        'capacity' => ['except' => ''],
        'lid_type' => ['except' => ''],
        'unit_type' => ['except' => ''],
        'sort' => ['except' => 'newest'],
        'featured' => ['except' => false],
    ];

    public function mount(): void
    {
        $this->q = request('q', '');
        $this->featured = (bool) request('featured', false);
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingMaterial(): void
    {
        $this->resetPage();
    }

    public function updatingCapacity(): void
    {
        $this->resetPage();
    }

    public function updatingLidType(): void
    {
        $this->resetPage();
    }

    public function updatingUnitType(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['q', 'category', 'material', 'capacity', 'lid_type', 'unit_type', 'featured']);
        $this->sort = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()
            ->published()
            ->with(['category', 'media', 'variants' => fn ($q) => $q->where('is_active', true)]);

        if ($this->featured) {
            // Promo hanya menampilkan produk unggulan yang punya gambar.
            $query->where('is_featured', true)->whereHas('media');
        }

        if ($this->q !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->q.'%')
                    ->orWhere('description', 'like', '%'.$this->q.'%')
                    ->orWhere('material', 'like', '%'.$this->q.'%');
            });
        }

        if ($this->category !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $this->category));
        }

        if ($this->material !== '') {
            $query->where('material', $this->material);
        }

        if ($this->capacity !== '') {
            $query->where('capacity_ml', (int) $this->capacity);
        }

        if ($this->lid_type !== '') {
            $query->whereHas('variants', fn ($q) => $q->where('lid_type', $this->lid_type)->where('is_active', true));
        }

        if ($this->unit_type !== '') {
            $query->whereHas('variants', fn ($q) => $q->where('unit_type', $this->unit_type)->where('is_active', true));
        }

        match ($this->sort) {
            'price_asc' => $query->orderByRaw('(SELECT MIN(price_retail) FROM product_variants WHERE product_variants.product_id = products.id AND is_active = 1) ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(price_retail) FROM product_variants WHERE product_variants.product_id = products.id AND is_active = 1) DESC'),
            default => $query->orderByDesc('published_at'),
        };

        $products = $query->paginate(12);

        $categories = Category::query()->where('is_active', true)->orderBy('sort_order')->get();

        $filterOptions = Product::query()
            ->published()
            ->select('material', 'capacity_ml')
            ->distinct()
            ->get();

        $materials = $filterOptions->pluck('material')->filter()->unique()->sort()->values();
        $capacities = $filterOptions->pluck('capacity_ml')->filter()->unique()->sort()->values();

        $variantOptions = Product::query()
            ->published()
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.is_active', true)
            ->select('product_variants.lid_type', 'product_variants.unit_type')
            ->distinct()
            ->get();

        $lidTypes = $variantOptions->pluck('lid_type')->filter()->unique()->sort()->values();
        $unitTypes = $variantOptions->pluck('unit_type')->filter()->unique()->sort()->values();

        return view('livewire.shop.catalog-page', [
            'products' => $products,
            'categories' => $categories,
            'materials' => $materials,
            'capacities' => $capacities,
            'lidTypes' => $lidTypes,
            'unitTypes' => $unitTypes,
        ])->layout('layouts.shop', ['title' => $this->featured ? 'Promo' : 'Belanja']);
    }
}
