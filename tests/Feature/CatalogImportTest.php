<?php

use App\Models\Category;
use App\Models\InventoryLocation;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CatalogImportService;
use App\Services\TrigunaCatalogMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function seedImportCategoriesAndLocation(): void
{
    foreach ([
        'cup-plastik' => 'Cup Plastik',
        'paper-cup' => 'Paper Cup',
        'tutup-aksesori' => 'Tutup & Aksesori',
        'paket-usaha' => 'Paket Usaha',
    ] as $slug => $name) {
        Category::query()->create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }

    InventoryLocation::query()->create([
        'name' => 'Gudang Utama',
        'code' => 'MAIN',
        'is_default' => true,
        'is_active' => true,
        'pickup_available' => true,
    ]);
}

it('imports catalog rows from csv and upserts by sku', function () {
    seedImportCategoriesAndLocation();

    $path = storage_path('app/templates/katalog-produk-contoh.csv');

    $stats = app(CatalogImportService::class)->importFromPath($path);

    expect($stats['errors'])->toBeEmpty()
        ->and(Product::query()->where('slug', 'cup-pp-8oz-carina')->exists())->toBeTrue()
        ->and(ProductVariant::query()->where('sku', 'CSC-PP8-CLR-50')->exists())->toBeTrue()
        ->and((float) ProductVariant::query()->where('sku', 'CSC-PP8-CLR-50')->value('price_retail'))->toBe(18500.0)
        ->and(ProductVariant::query()->where('sku', 'CSC-PP8-CLR-50')->first()->availableQty())->toBe(1000)
        ->and($stats['images_missing'])->toBeGreaterThan(0);

    $temp = storage_path('app/tmp-import-update.csv');
    file_put_contents($temp, implode(',', CatalogImportService::HEADERS)."\n".
        'cup-plastik,Cup PP 8oz Carina,cup-pp-8oz-carina,CSC-PP8-CLR-50,Pack 50 Bening,,Bening,Datar,pack,50,20000,250,40,30,35,50,50,PP,240,8oz,Updated,,,published,1,1,900,'."\n");

    app(CatalogImportService::class)->importFromPath($temp);

    expect((float) ProductVariant::query()->where('sku', 'CSC-PP8-CLR-50')->value('price_retail'))->toBe(20000.0)
        ->and(ProductVariant::query()->where('sku', 'CSC-PP8-CLR-50')->first()->availableQty())->toBe(900);

    @unlink($temp);
});

it('fails clearly when category slug is missing', function () {
    InventoryLocation::query()->create([
        'name' => 'Gudang Utama',
        'code' => 'MAIN',
        'is_default' => true,
        'is_active' => true,
    ]);

    $temp = storage_path('app/tmp-import-bad-cat.csv');
    file_put_contents($temp, implode(',', CatalogImportService::HEADERS)."\n".
        'kategori-tidak-ada,Produk X,produk-x,SKU-X,Var,,,pcs,1,1000,10,,,,,1,1,,,,,,draft,0,1,10,'."\n");

    $stats = app(CatalogImportService::class)->importFromPath($temp);

    expect($stats['errors'])->not->toBeEmpty()
        ->and($stats['created_variants'])->toBe(0);

    @unlink($temp);
});

it('attaches local images from import/images via image_path', function () {
    seedImportCategoriesAndLocation();

    $imagesDir = base_path('import/images');
    if (! is_dir($imagesDir)) {
        mkdir($imagesDir, 0777, true);
    }

    $imageFile = $imagesDir.DIRECTORY_SEPARATOR.'test-cup.jpg';
    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');
    file_put_contents($imageFile, $jpeg);

    $temp = storage_path('app/tmp-import-image.csv');
    file_put_contents($temp, implode(',', CatalogImportService::HEADERS)."\n".
        'cup-plastik,Cup Gambar Carina,cup-gambar-carina,CSC-IMG-01,Pack 50,,Bening,Datar,pack,50,15000,200,40,30,35,50,50,PP,240,8oz,Deskripsi,,,published,1,1,100,test-cup.jpg'."\n");

    $stats = app(CatalogImportService::class)->importFromPath($temp);

    $product = Product::query()->where('slug', 'cup-gambar-carina')->first();

    expect($stats['errors'])->toBeEmpty()
        ->and($stats['images_attached'])->toBe(1)
        ->and($stats['images_missing'])->toBe(0)
        ->and($product)->not->toBeNull()
        ->and($product->getMedia('images'))->toHaveCount(1);

    @unlink($temp);
    @unlink($imageFile);
});

it('detects triguna headers', function () {
    expect(TrigunaCatalogMapper::isTrigunaHeader([
        'no.', 'kategori', 'nama produk', 'harga minimum (rp)', 'url gambar utama',
    ]))->toBeTrue()
        ->and(TrigunaCatalogMapper::isTrigunaHeader([
            'category_slug', 'product_name', 'sku', 'price_retail',
        ]))->toBeFalse();
});

it('imports triguna format rows and downloads images', function () {
    InventoryLocation::query()->create([
        'name' => 'Gudang Utama',
        'code' => 'MAIN',
        'is_default' => true,
        'is_active' => true,
    ]);

    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');

    Http::fake([
        'https://cdn.example.test/*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $temp = storage_path('app/tmp-import-triguna.csv');
    $headers = [
        'No.',
        'Kategori',
        'Nama produk',
        'Harga minimum (Rp)',
        'Harga maksimum (Rp)',
        'Harga tampil',
        'Rating',
        'Ulasan',
        'SKU',
        'Stok cabang',
        'Kemasan / detail ringkas',
        'Deskripsi / detail',
        'URL produk',
        'Nama gambar utama',
        'URL gambar utama',
    ];

    $row = [
        '1',
        'Paper Cup',
        'Paper Cup 12oz Trifinity Test',
        '15.000',
        '20.000',
        'Rp15,000 - 20,000',
        '5',
        '10',
        'TRI-PC12',
        '250 ready',
        'Pack 50',
        'Paper cup hot drink',
        'https://trigunajayasentosaplastik.com/paper-cup-12oz',
        'pc12.jpg',
        'https://cdn.example.test/pc12.jpg',
    ];

    $fh = fopen($temp, 'w');
    fputcsv($fh, $headers);
    fputcsv($fh, $row);
    fclose($fh);

    $stats = app(CatalogImportService::class)->importFromPath($temp);

    $product = Product::query()->where('name', 'Paper Cup 12oz Trifinity Test')->first();

    expect($stats['errors'])->toBeEmpty()
        ->and($stats['created_products'])->toBe(1)
        ->and($stats['images_attached'])->toBe(1)
        ->and(Category::query()->where('slug', 'paper-cup')->exists())->toBeTrue()
        ->and(ProductVariant::query()->where('sku', 'TRI-PC12')->exists())->toBeTrue()
        ->and((float) ProductVariant::query()->where('sku', 'TRI-PC12')->value('price_retail'))->toBe(15000.0)
        ->and($product)->not->toBeNull()
        ->and($product->getMedia('images'))->toHaveCount(1)
        ->and(ProductVariant::query()->where('sku', 'TRI-PC12')->first()->availableQty())->toBe(250);

    @unlink($temp);
});

it('can skip images on triguna import', function () {
    InventoryLocation::query()->create([
        'name' => 'Gudang Utama',
        'code' => 'MAIN',
        'is_default' => true,
        'is_active' => true,
    ]);

    Http::fake();

    $temp = storage_path('app/tmp-import-triguna-skip.csv');
    $fh = fopen($temp, 'w');
    fputcsv($fh, ['Kategori', 'Nama produk', 'Harga minimum (Rp)', 'SKU', 'URL gambar utama']);
    fputcsv($fh, ['Thinwall', 'Thinwall Cup Test', '10000', 'TRI-TW-1', 'https://cdn.example.test/a.jpg']);
    fclose($fh);

    $stats = app(CatalogImportService::class)->importFromPath($temp, skipImages: true);

    expect($stats['errors'])->toBeEmpty()
        ->and($stats['images_attached'])->toBe(0)
        ->and(ProductVariant::query()->where('sku', 'TRI-TW-1')->exists())->toBeTrue();

    Http::assertNothingSent();

    @unlink($temp);
});
