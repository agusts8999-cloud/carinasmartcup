<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

it('shows category image when category has product media', function () {
    Storage::fake('public');

    $withImage = Category::factory()->create([
        'name' => 'Kategori Bergambar',
        'slug' => 'kategori-bergambar',
        'sort_order' => 1,
    ]);

    $withoutImage = Category::factory()->create([
        'name' => 'Kategori Tanpa Gambar',
        'slug' => 'kategori-tanpa-gambar',
        'sort_order' => 2,
    ]);

    $productWithImage = Product::factory()->create([
        'category_id' => $withImage->id,
        'is_featured' => false,
    ]);

    Product::factory()->create([
        'category_id' => $withoutImage->id,
        'is_featured' => false,
    ]);

    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');
    $tmp = storage_path('app/test-category-image.jpg');
    file_put_contents($tmp, $jpeg);

    $productWithImage
        ->addMedia($tmp)
        ->usingFileName('test-category-image.jpg')
        ->toMediaCollection('images');

    @unlink($tmp);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Kategori Bergambar')
        ->assertSee('Kategori Tanpa Gambar')
        ->assertSee('alt="Kategori Bergambar"', false)
        ->assertSee('src="/storage/', false)
        ->assertSee('📦', false);
});

it('returns null when category has no product image', function () {
    Storage::fake('public');

    $category = Category::factory()->create();

    Product::factory()->create([
        'category_id' => $category->id,
    ]);

    expect($category->imageUrl())->toBeNull();
});

it('shows images in featured products section instead of seed products without media', function () {
    Storage::fake('public');

    $category = Category::factory()->create();

    // Featured seed-like product without media should not block the section.
    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Featured Without Image',
        'is_featured' => true,
    ]);

    $withImage = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Featured With Image',
        'is_featured' => false,
    ]);

    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');
    $tmp = storage_path('app/test-featured-image.jpg');
    file_put_contents($tmp, $jpeg);
    $withImage->addMedia($tmp)->usingFileName('featured.jpg')->toMediaCollection('images');
    @unlink($tmp);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Featured With Image')
        ->assertDontSee('Featured Without Image')
        ->assertSee('src="/storage/', false)
        ->assertDontSee('🥤');
});

it('shows images on promo catalog page and hides featured products without media', function () {
    Storage::fake('public');

    $category = Category::factory()->create();

    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Promo Without Image',
        'is_featured' => true,
    ]);

    $withImage = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Promo With Image',
        'is_featured' => true,
    ]);

    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');
    $tmp = storage_path('app/test-promo-image.jpg');
    file_put_contents($tmp, $jpeg);
    $withImage->addMedia($tmp)->usingFileName('promo.jpg')->toMediaCollection('images');
    @unlink($tmp);

    $response = $this->get(route('catalog', ['featured' => 1]));

    $response->assertOk()
        ->assertSee('Promo')
        ->assertSee('Promo With Image')
        ->assertDontSee('Promo Without Image')
        ->assertSee('src="/storage/', false)
        ->assertDontSee('🥤');
});

it('uses latest published product with image when newer draft exists', function () {
    Storage::fake('public');

    $category = Category::factory()->create();

    $published = Product::factory()->create([
        'category_id' => $category->id,
        'published_at' => now()->subHour(),
    ]);

    $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxAQEBUQEBAVFRUVFRUVFRUVFRUVFRUWFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EADUQAAIBAwIEBAMEBwAAAAAAAAECAwAEERIhBTFBEyJRYXGBkaEFMrHB0fAGFEJSYvH/xAAZAQADAQEBAAAAAAAAAAAAAAABAgMABAX/xAAhEQACAgICAgMBAAAAAAAAAAAAAQIRAxIhMQRBIlFhBf/aAAwDAQACEQMRAD8A9oAAAAAAAAAAAAAAAAf/2Q==');
    $tmp = storage_path('app/test-category-image-latest.jpg');
    file_put_contents($tmp, $jpeg);
    $published->addMedia($tmp)->usingFileName('latest.jpg')->toMediaCollection('images');
    @unlink($tmp);

    Product::factory()->create([
        'category_id' => $category->id,
        'status' => \App\Enums\ProductStatus::Draft,
        'published_at' => now()->addHour(),
    ]);

    $category->load('latestProductWithImage.media');

    expect($category->imageUrl())->toStartWith('/storage/');
});
