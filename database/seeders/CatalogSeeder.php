<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Coupon;
use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use App\Models\VolumeRule;
use App\Models\VolumeRuleTier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $location = $this->seedInventoryLocation();
        $categories = $this->seedCategories();
        $this->seedProducts($categories, $location);
        $this->seedVolumeRules($categories['cup-plastik']);
        $this->seedCoupons();
        $this->seedShipping($location);
        $this->seedCmsPages();
        $this->seedSettings();
    }

    private function seedInventoryLocation(): InventoryLocation
    {
        return InventoryLocation::query()->updateOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Gudang Utama',
                'address' => 'Jl. Industri Kemasan No. 88, Jakarta Utara',
                'is_default' => true,
                'is_active' => true,
                'pickup_available' => true,
                'pickup_hours' => [
                    'monday' => ['09:00', '17:00'],
                    'tuesday' => ['09:00', '17:00'],
                    'wednesday' => ['09:00', '17:00'],
                    'thursday' => ['09:00', '17:00'],
                    'friday' => ['09:00', '17:00'],
                    'saturday' => ['09:00', '14:00'],
                    'sunday' => null,
                ],
            ],
        );
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $definitions = [
            'cup-plastik' => ['name' => 'Cup Plastik', 'sort_order' => 1],
            'paper-cup' => ['name' => 'Paper Cup', 'sort_order' => 2],
            'tutup-aksesori' => ['name' => 'Tutup & Aksesori', 'sort_order' => 3],
            'paket-usaha' => ['name' => 'Paket Usaha', 'sort_order' => 4],
        ];

        $categories = [];

        foreach ($definitions as $slug => $data) {
            $categories[$slug] = Category::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => "Kategori {$data['name']} CarinaSmartCup.",
                    'sort_order' => $data['sort_order'],
                    'is_active' => true,
                ],
            );
        }

        return $categories;
    }

    /**
     * @param  array<string, Category>  $categories
     */
    private function seedProducts(array $categories, InventoryLocation $location): void
    {
        $products = [
            [
                'category' => 'cup-plastik',
                'name' => 'PP Cup 8oz Bening',
                'slug' => 'pp-cup-8oz-bening',
                'material' => 'PP',
                'capacity_ml' => 240,
                'size_label' => '8oz',
                'variants' => [
                    ['sku' => 'PP8-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'pack_size' => 50, 'price_retail' => 175, 'weight_gram' => 8, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PP8-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'pack_size' => 100, 'price_retail' => 320, 'weight_gram' => 16, 'min_order_qty' => 100, 'order_multiple' => 100],
                    ['sku' => 'PP8-RED-50', 'name' => 'Merah Pack 50', 'color' => 'Merah', 'pack_size' => 50, 'price_retail' => 185, 'weight_gram' => 8, 'min_order_qty' => 50, 'order_multiple' => 50],
                ],
            ],
            [
                'category' => 'cup-plastik',
                'name' => 'PP Cup 12oz Bening',
                'slug' => 'pp-cup-12oz-bening',
                'material' => 'PP',
                'capacity_ml' => 350,
                'size_label' => '12oz',
                'variants' => [
                    ['sku' => 'PP12-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'pack_size' => 50, 'price_retail' => 210, 'weight_gram' => 10, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PP12-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'pack_size' => 100, 'price_retail' => 390, 'weight_gram' => 20, 'min_order_qty' => 100, 'order_multiple' => 100],
                    ['sku' => 'PP12-BLK-50', 'name' => 'Hitam Pack 50', 'color' => 'Hitam', 'pack_size' => 50, 'price_retail' => 220, 'weight_gram' => 10, 'min_order_qty' => 50, 'order_multiple' => 50],
                ],
            ],
            [
                'category' => 'cup-plastik',
                'name' => 'PP Cup 16oz Bening',
                'slug' => 'pp-cup-16oz-bening',
                'material' => 'PP',
                'capacity_ml' => 470,
                'size_label' => '16oz',
                'variants' => [
                    ['sku' => 'PP16-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'pack_size' => 50, 'price_retail' => 260, 'weight_gram' => 12, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PP16-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'pack_size' => 100, 'price_retail' => 480, 'weight_gram' => 24, 'min_order_qty' => 100, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'cup-plastik',
                'name' => 'PET Cup 8oz',
                'slug' => 'pet-cup-8oz',
                'material' => 'PET',
                'capacity_ml' => 240,
                'size_label' => '8oz',
                'variants' => [
                    ['sku' => 'PET8-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'pack_size' => 50, 'price_retail' => 195, 'weight_gram' => 9, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PET8-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'pack_size' => 100, 'price_retail' => 360, 'weight_gram' => 18, 'min_order_qty' => 100, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'cup-plastik',
                'name' => 'PET Cup 12oz',
                'slug' => 'pet-cup-12oz',
                'material' => 'PET',
                'capacity_ml' => 350,
                'size_label' => '12oz',
                'variants' => [
                    ['sku' => 'PET12-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'pack_size' => 50, 'price_retail' => 235, 'weight_gram' => 11, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PET12-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'pack_size' => 100, 'price_retail' => 430, 'weight_gram' => 22, 'min_order_qty' => 100, 'order_multiple' => 100],
                    ['sku' => 'PET12-BLU-50', 'name' => 'Biru Pack 50', 'color' => 'Biru', 'pack_size' => 50, 'price_retail' => 245, 'weight_gram' => 11, 'min_order_qty' => 50, 'order_multiple' => 50],
                ],
            ],
            [
                'category' => 'paper-cup',
                'name' => 'Paper Cup 8oz Single Wall',
                'slug' => 'paper-cup-8oz-single',
                'material' => 'Paper',
                'capacity_ml' => 240,
                'size_label' => '8oz',
                'variants' => [
                    ['sku' => 'PW8-WHT-50', 'name' => 'Putih Pack 50', 'color' => 'Putih', 'pack_size' => 50, 'price_retail' => 280, 'weight_gram' => 12, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PW8-KRAFT-50', 'name' => 'Kraft Pack 50', 'color' => 'Kraft', 'pack_size' => 50, 'price_retail' => 295, 'weight_gram' => 12, 'min_order_qty' => 50, 'order_multiple' => 50],
                ],
            ],
            [
                'category' => 'paper-cup',
                'name' => 'Paper Cup 12oz Single Wall',
                'slug' => 'paper-cup-12oz-single',
                'material' => 'Paper',
                'capacity_ml' => 350,
                'size_label' => '12oz',
                'variants' => [
                    ['sku' => 'PW12-WHT-50', 'name' => 'Putih Pack 50', 'color' => 'Putih', 'pack_size' => 50, 'price_retail' => 330, 'weight_gram' => 15, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PW12-WHT-100', 'name' => 'Putih Pack 100', 'color' => 'Putih', 'pack_size' => 100, 'price_retail' => 620, 'weight_gram' => 30, 'min_order_qty' => 100, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'paper-cup',
                'name' => 'Paper Cup 16oz Double Wall',
                'slug' => 'paper-cup-16oz-double',
                'material' => 'Paper',
                'capacity_ml' => 470,
                'size_label' => '16oz',
                'variants' => [
                    ['sku' => 'PD16-WHT-50', 'name' => 'Putih Pack 50', 'color' => 'Putih', 'pack_size' => 50, 'price_retail' => 420, 'weight_gram' => 20, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PD16-KRAFT-50', 'name' => 'Kraft Pack 50', 'color' => 'Kraft', 'pack_size' => 50, 'price_retail' => 440, 'weight_gram' => 20, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'PD16-WHT-100', 'name' => 'Putih Pack 100', 'color' => 'Putih', 'pack_size' => 100, 'price_retail' => 800, 'weight_gram' => 40, 'min_order_qty' => 100, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'tutup-aksesori',
                'name' => 'Tutup Dome 8oz',
                'slug' => 'tutup-dome-8oz',
                'material' => 'PP',
                'size_label' => '8oz',
                'variants' => [
                    ['sku' => 'LD8-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'lid_type' => 'Dome', 'pack_size' => 50, 'price_retail' => 150, 'weight_gram' => 5, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'LD8-CLR-100', 'name' => 'Bening Pack 100', 'color' => 'Bening', 'lid_type' => 'Dome', 'pack_size' => 100, 'price_retail' => 270, 'weight_gram' => 10, 'min_order_qty' => 100, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'tutup-aksesori',
                'name' => 'Tutup Flat 12oz',
                'slug' => 'tutup-flat-12oz',
                'material' => 'PP',
                'size_label' => '12oz',
                'variants' => [
                    ['sku' => 'LF12-CLR-50', 'name' => 'Bening Pack 50', 'color' => 'Bening', 'lid_type' => 'Flat', 'pack_size' => 50, 'price_retail' => 165, 'weight_gram' => 6, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'LF12-BLK-50', 'name' => 'Hitam Pack 50', 'color' => 'Hitam', 'lid_type' => 'Flat', 'pack_size' => 50, 'price_retail' => 175, 'weight_gram' => 6, 'min_order_qty' => 50, 'order_multiple' => 50],
                    ['sku' => 'LF12-WHT-50', 'name' => 'Putih Pack 50', 'color' => 'Putih', 'lid_type' => 'Flat', 'pack_size' => 50, 'price_retail' => 170, 'weight_gram' => 6, 'min_order_qty' => 50, 'order_multiple' => 50],
                ],
            ],
            [
                'category' => 'tutup-aksesori',
                'name' => 'Sedotan Plastik Premium',
                'slug' => 'sedotan-plastik-premium',
                'material' => 'PP',
                'variants' => [
                    ['sku' => 'STR-PREM-100', 'name' => 'Pack 100 pcs', 'color' => 'Transparan', 'pack_size' => 100, 'price_retail' => 450, 'weight_gram' => 25, 'min_order_qty' => 100, 'order_multiple' => 100],
                    ['sku' => 'STR-PREM-500', 'name' => 'Pack 500 pcs', 'color' => 'Transparan', 'pack_size' => 500, 'price_retail' => 2000, 'weight_gram' => 120, 'min_order_qty' => 500, 'order_multiple' => 100],
                ],
            ],
            [
                'category' => 'paket-usaha',
                'name' => 'Paket Usaha Kafe Kecil',
                'slug' => 'paket-usaha-kafe-kecil',
                'material' => 'Mixed',
                'variants' => [
                    ['sku' => 'PKT-KAFE-S', 'name' => 'Starter 500 pcs', 'pack_size' => 1, 'price_retail' => 850000, 'weight_gram' => 5000, 'min_order_qty' => 1, 'order_multiple' => 1],
                    ['sku' => 'PKT-KAFE-M', 'name' => 'Medium 1000 pcs', 'pack_size' => 1, 'price_retail' => 1550000, 'weight_gram' => 10000, 'min_order_qty' => 1, 'order_multiple' => 1],
                    ['sku' => 'PKT-KAFE-L', 'name' => 'Large 2000 pcs', 'pack_size' => 1, 'price_retail' => 2900000, 'weight_gram' => 20000, 'min_order_qty' => 1, 'order_multiple' => 1],
                ],
            ],
        ];

        foreach ($products as $definition) {
            $category = $categories[$definition['category']];

            $product = Product::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $definition['name'],
                    'sku_prefix' => Str::upper(Str::substr($definition['slug'], 0, 4)),
                    'description' => "Produk {$definition['name']} berkualitas untuk bisnis minuman.",
                    'material' => $definition['material'] ?? null,
                    'capacity_ml' => $definition['capacity_ml'] ?? null,
                    'size_label' => $definition['size_label'] ?? null,
                    'status' => ProductStatus::Published,
                    'is_featured' => in_array($definition['slug'], ['pp-cup-8oz-bening', 'paper-cup-12oz-single', 'paket-usaha-kafe-kecil'], true),
                    'published_at' => now(),
                ],
            );

            foreach ($definition['variants'] as $variantData) {
                $variant = ProductVariant::query()->updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'color' => $variantData['color'] ?? null,
                        'lid_type' => $variantData['lid_type'] ?? null,
                        'unit_type' => 'pcs',
                        'pack_size' => $variantData['pack_size'],
                        'price_retail' => $variantData['price_retail'],
                        'weight_gram' => $variantData['weight_gram'],
                        'length_cm' => 8,
                        'width_cm' => 8,
                        'height_cm' => 12,
                        'min_order_qty' => $variantData['min_order_qty'],
                        'order_multiple' => $variantData['order_multiple'],
                        'is_active' => true,
                    ],
                );

                InventoryStock::query()->updateOrCreate(
                    [
                        'inventory_location_id' => $location->id,
                        'product_variant_id' => $variant->id,
                    ],
                    [
                        'qty_on_hand' => fake()->numberBetween(500, 2000),
                        'qty_reserved' => 0,
                    ],
                );
            }
        }
    }

    private function seedVolumeRules(Category $cupPlastik): void
    {
        $rule = VolumeRule::query()->updateOrCreate(
            [
                'name' => 'Diskon Volume Cup Plastik',
                'category_id' => $cupPlastik->id,
            ],
            [
                'applies_to_all' => false,
                'allows_mix' => true,
                'is_active' => true,
                'basis' => 'quantity',
            ],
        );

        $tiers = [
            ['min_value' => 500, 'discount_value' => 15, 'sort_order' => 3],
            ['min_value' => 300, 'discount_value' => 10, 'sort_order' => 2],
            ['min_value' => 100, 'discount_value' => 5, 'sort_order' => 1],
        ];

        foreach ($tiers as $tier) {
            VolumeRuleTier::query()->updateOrCreate(
                [
                    'volume_rule_id' => $rule->id,
                    'min_value' => $tier['min_value'],
                ],
                [
                    'discount_type' => 'percent',
                    'discount_value' => $tier['discount_value'],
                    'sort_order' => $tier['sort_order'],
                ],
            );
        }
    }

    private function seedCoupons(): void
    {
        Coupon::query()->updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'name' => 'Welcome 10%',
                'type' => 'percent',
                'value' => 10,
                'min_subtotal' => 100000,
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addYear(),
            ],
        );
    }

    private function seedShipping(InventoryLocation $location): void
    {
        $jabodetabek = ShippingZone::query()->updateOrCreate(
            ['name' => 'Jabodetabek'],
            [
                'provinces' => ['DKI Jakarta', 'Jawa Barat', 'Banten'],
                'cities' => ['Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara', 'Bekasi', 'Depok', 'Tangerang', 'Bogor'],
                'is_active' => true,
            ],
        );

        ShippingRate::query()->updateOrCreate(
            ['shipping_zone_id' => $jabodetabek->id, 'code' => 'JKT-REG'],
            [
                'name' => 'Reguler Jabodetabek',
                'base_price' => 12000,
                'price_per_kg' => 3000,
                'price_per_koli' => 5000,
                'eta_days_min' => 1,
                'eta_days_max' => 2,
                'is_pickup' => false,
                'is_active' => true,
            ],
        );

        ShippingRate::query()->updateOrCreate(
            ['shipping_zone_id' => $jabodetabek->id, 'code' => 'JKT-PICKUP'],
            [
                'name' => 'Ambil di Gudang',
                'base_price' => 0,
                'price_per_kg' => 0,
                'price_per_koli' => 0,
                'eta_days_min' => 0,
                'eta_days_max' => 0,
                'is_pickup' => true,
                'is_active' => true,
            ],
        );

        $jawa = ShippingZone::query()->updateOrCreate(
            ['name' => 'Jawa'],
            [
                'provinces' => ['Jawa Tengah', 'Jawa Timur', 'DI Yogyakarta'],
                'cities' => ['Semarang', 'Surabaya', 'Yogyakarta', 'Solo', 'Malang'],
                'is_active' => true,
            ],
        );

        ShippingRate::query()->updateOrCreate(
            ['shipping_zone_id' => $jawa->id, 'code' => 'JAWA-REG'],
            [
                'name' => 'Reguler Jawa',
                'base_price' => 18000,
                'price_per_kg' => 4500,
                'price_per_koli' => 7000,
                'eta_days_min' => 2,
                'eta_days_max' => 4,
                'is_pickup' => false,
                'is_active' => true,
            ],
        );

        $luarJawa = ShippingZone::query()->updateOrCreate(
            ['name' => 'Luar Jawa'],
            [
                'provinces' => ['Sumatera Utara', 'Sumatera Selatan', 'Kalimantan Timur', 'Sulawesi Selatan', 'Bali', 'Nusa Tenggara Barat'],
                'cities' => null,
                'is_active' => true,
            ],
        );

        ShippingRate::query()->updateOrCreate(
            ['shipping_zone_id' => $luarJawa->id, 'code' => 'LNJ-REG'],
            [
                'name' => 'Reguler Luar Jawa',
                'base_price' => 35000,
                'price_per_kg' => 8000,
                'price_per_koli' => 12000,
                'eta_days_min' => 4,
                'eta_days_max' => 7,
                'is_pickup' => false,
                'is_active' => true,
            ],
        );

        unset($location);
    }

    private function seedCmsPages(): void
    {
        $faqs = [
            ['title' => 'Berapa minimum order?', 'slug' => 'faq-minimum-order', 'body' => 'Minimum order mengikuti pack size masing-masing varian, biasanya 50 atau 100 pcs.'],
            ['title' => 'Apakah bisa ambil sendiri?', 'slug' => 'faq-pickup', 'body' => 'Ya, pickup tersedia di Gudang Utama dengan jam operasional Senin–Sabtu.'],
            ['title' => 'Bagaimana cara bayar?', 'slug' => 'faq-pembayaran', 'body' => 'Pembayaran via transfer bank manual. Upload bukti setelah checkout.'],
            ['title' => 'Apakah ada diskon volume?', 'slug' => 'faq-diskon-volume', 'body' => 'Ya, kategori Cup Plastik mendapat diskon hingga 15% untuk pembelian volume.'],
            ['title' => 'Berapa lama pengiriman?', 'slug' => 'faq-pengiriman', 'body' => 'Estimasi 1–2 hari untuk Jabodetabek, 2–4 hari untuk Jawa, 4–7 hari luar Jawa.'],
        ];

        foreach ($faqs as $index => $faq) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $faq['slug']],
                [
                    'title' => $faq['title'],
                    'type' => 'faq',
                    'body' => $faq['body'],
                    'excerpt' => Str::limit($faq['body'], 120),
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        $pages = [
            ['title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi', 'body' => '<p>Kebijakan privasi CarinaSmartCup melindungi data pelanggan.</p>'],
            ['title' => 'Syarat & Ketentuan', 'slug' => 'syarat-ketentuan', 'body' => '<p>Syarat dan ketentuan berlaku untuk semua transaksi.</p>'],
            ['title' => 'Kebijakan Retur', 'slug' => 'kebijakan-retur', 'body' => '<p>Retur dapat diajukan maksimal 7 hari setelah barang diterima.</p>'],
            ['title' => 'Cara Belanja', 'slug' => 'cara-belanja', 'body' => '<p>Pilih produk, atur jumlah, checkout, bayar, dan tunggu pengiriman.</p>'],
            ['title' => 'Tentang Kami', 'slug' => 'tentang-kami', 'body' => '<p>CarinaSmartCup menyediakan kemasan cup berkualitas untuk UMKM F&B.</p>'],
        ];

        foreach ($pages as $index => $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'type' => 'page',
                    'body' => $page['body'],
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'bank.name', 'value' => config('carina.bank.name'), 'group' => 'payment'],
            ['key' => 'bank.account_number', 'value' => config('carina.bank.account_number'), 'group' => 'payment'],
            ['key' => 'bank.account_name', 'value' => config('carina.bank.account_name'), 'group' => 'payment'],
            ['key' => 'whatsapp.support', 'value' => config('carina.whatsapp_support'), 'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                ],
            );
        }
    }
}
