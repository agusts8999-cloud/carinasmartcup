<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CatalogImportService
{
    /**
     * @var list<string>
     */
    public const HEADERS = [
        'category_slug',
        'product_name',
        'product_slug',
        'sku',
        'variant_name',
        'barcode',
        'color',
        'lid_type',
        'unit_type',
        'pack_size',
        'price_retail',
        'weight_gram',
        'length_cm',
        'width_cm',
        'height_cm',
        'min_order_qty',
        'order_multiple',
        'material',
        'capacity_ml',
        'size_label',
        'description',
        'usage_notes',
        'lid_compatibility',
        'status',
        'is_featured',
        'is_active',
        'stock_qty',
        'image_path',
    ];

    public function __construct(
        private readonly TrigunaCatalogMapper $trigunaMapper,
    ) {}

    /**
     * @return array{
     *     created_products: int,
     *     updated_products: int,
     *     created_variants: int,
     *     updated_variants: int,
     *     images_attached: int,
     *     images_missing: int,
     *     errors: list<string>
     * }
     */
    public function importFromPath(string $path, bool $skipImages = false): array
    {
        if (! is_readable($path)) {
            throw ValidationException::withMessages([
                'file' => 'File katalog tidak dapat dibaca.',
            ]);
        }

        $extension = Str::lower(pathinfo($path, PATHINFO_EXTENSION));

        $rows = match ($extension) {
            'csv', 'txt' => $this->readCsvRows($path),
            'xlsx', 'xls' => $this->readSpreadsheetRows($path),
            default => throw ValidationException::withMessages([
                'file' => 'Format tidak didukung. Gunakan CSV atau XLSX.',
            ]),
        };

        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'File katalog kosong atau tidak memiliki baris data.',
            ]);
        }

        $headers = array_map(
            fn ($h) => Str::of((string) $h)->trim()->lower()->toString(),
            array_shift($rows) ?? [],
        );

        $isTriguna = TrigunaCatalogMapper::isTrigunaHeader($headers);

        if (! $isTriguna) {
            $this->assertCarinaHeaders($headers);
        }

        $stats = [
            'created_products' => 0,
            'updated_products' => 0,
            'created_variants' => 0,
            'updated_variants' => 0,
            'images_attached' => 0,
            'images_missing' => 0,
            'errors' => [],
        ];

        $location = InventoryLocation::query()->where('is_default', true)->first()
            ?? InventoryLocation::query()->first();

        // Ensure default location exists for Triguna imports.
        if ($location === null) {
            $location = InventoryLocation::query()->create([
                'name' => 'Gudang Utama',
                'code' => 'MAIN',
                'is_default' => true,
                'is_active' => true,
                'pickup_available' => false,
            ]);
        }

        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;

            if ($this->rowIsEmpty($row)) {
                continue;
            }

            try {
                $mapped = $this->mapRow($headers, $row);

                if ($isTriguna) {
                    $data = $this->trigunaMapper->map($mapped, $rowNumber);
                } else {
                    $data = $mapped;
                }

                if ($skipImages) {
                    $data['image_path'] = '';
                    $data['image_url'] = '';
                }

                $result = $this->importRow($data, $location);

                $stats['created_products'] += $result['created_product'] ? 1 : 0;
                $stats['updated_products'] += $result['created_product'] ? 0 : 1;
                $stats['created_variants'] += $result['created_variant'] ? 1 : 0;
                $stats['updated_variants'] += $result['created_variant'] ? 0 : 1;
                $stats['images_attached'] += $result['image_attached'] ? 1 : 0;
                $stats['images_missing'] += $result['image_missing'] ? 1 : 0;
            } catch (\Throwable $e) {
                $stats['errors'][] = "Baris {$rowNumber}: ".$e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * @return list<list<string|null>>
     */
    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'Gagal membuka file CSV.',
            ]);
        }

        try {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $rows = [];

            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return list<list<string|null>>
     */
    private function readSpreadsheetRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        $sheet = $spreadsheet->getSheetByName('Produk')
            ?? $spreadsheet->getSheetByName('produk')
            ?? $spreadsheet->getActiveSheet();

        // Use raw cell values; formatted strings like "29.000" would be parsed as 29.
        $matrix = $sheet->toArray(null, true, false, false);
        $rows = [];

        foreach ($matrix as $row) {
            /** @var list<string|null> $normalized */
            $normalized = array_map(
                fn ($cell) => $cell === null ? '' : trim((string) $cell),
                array_values($row),
            );
            $rows[] = $normalized;
        }

        // Drop leading title rows until Carina or Triguna header found.
        while ($rows !== []) {
            $candidate = array_map(
                fn ($h) => Str::of((string) $h)->trim()->lower()->toString(),
                $rows[0],
            );

            if (
                (in_array('category_slug', $candidate, true) && in_array('sku', $candidate, true))
                || TrigunaCatalogMapper::isTrigunaHeader($candidate)
            ) {
                break;
            }

            array_shift($rows);
        }

        return $rows;
    }

    /**
     * @param  list<string>  $headers
     */
    private function assertCarinaHeaders(array $headers): void
    {
        $required = ['category_slug', 'product_name', 'product_slug', 'sku', 'price_retail'];

        foreach ($required as $column) {
            if (! in_array($column, $headers, true)) {
                throw ValidationException::withMessages([
                    'file' => "Kolom wajib '{$column}' tidak ada di header. Gunakan template Carina atau file Triguna lengkap.",
                ]);
            }
        }
    }

    /**
     * @param  list<string|null>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $headers
     * @param  list<string|null>  $row
     * @return array<string, mixed>
     */
    private function mapRow(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $data[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{created_product: bool, created_variant: bool, image_attached: bool, image_missing: bool}
     */
    private function importRow(array $data, ?InventoryLocation $location): array
    {
        return DB::transaction(function () use ($data, $location): array {
            $categorySlug = Str::slug((string) $data['category_slug']);

            if (! empty($data['ensure_category'])) {
                $category = Category::query()->firstOrCreate(
                    ['slug' => $categorySlug],
                    [
                        'name' => (string) ($data['category_name'] ?? Str::title(str_replace('-', ' ', $categorySlug))),
                        'description' => null,
                        'sort_order' => 0,
                        'is_active' => true,
                    ],
                );
            } else {
                $category = Category::query()->where('slug', $categorySlug)->first();

                if ($category === null) {
                    throw new \RuntimeException("Kategori '{$data['category_slug']}' tidak ditemukan. Buat kategori dulu di admin.");
                }
            }

            if ($data['sku'] === '' || $data['product_name'] === '' || $data['product_slug'] === '') {
                throw new \RuntimeException('sku, product_name, dan product_slug wajib diisi.');
            }

            $productSlug = Str::slug((string) $data['product_slug']);
            $status = $this->resolveStatus((string) ($data['status'] ?? 'draft'));

            $product = Product::query()->where('slug', $productSlug)->first();
            $createdProduct = $product === null;

            $productPayload = [
                'category_id' => $category->id,
                'name' => $data['product_name'],
                'slug' => $productSlug,
                'sku_prefix' => Str::upper(Str::substr($productSlug, 0, 6)),
                'description' => ($data['description'] ?? '') !== '' ? $data['description'] : null,
                'usage_notes' => ($data['usage_notes'] ?? '') !== '' ? $data['usage_notes'] : null,
                'lid_compatibility' => ($data['lid_compatibility'] ?? '') !== '' ? $data['lid_compatibility'] : null,
                'material' => ($data['material'] ?? '') !== '' ? $data['material'] : null,
                'capacity_ml' => $this->nullableInt($data['capacity_ml'] ?? null),
                'size_label' => ($data['size_label'] ?? '') !== '' ? $data['size_label'] : null,
                'status' => $status,
                'is_featured' => $this->toBool($data['is_featured'] ?? '0'),
                'published_at' => $status === ProductStatus::Published
                    ? ($product?->published_at ?? now())
                    : null,
            ];

            if ($createdProduct) {
                $product = Product::query()->create($productPayload);
            } else {
                $product->update($productPayload);
            }

            $variant = ProductVariant::query()->where('sku', $data['sku'])->first();
            $createdVariant = $variant === null;

            $variantPayload = [
                'product_id' => $product->id,
                'sku' => $data['sku'],
                'barcode' => ($data['barcode'] ?? '') !== '' ? $data['barcode'] : null,
                'name' => ($data['variant_name'] ?? '') !== '' ? $data['variant_name'] : null,
                'color' => ($data['color'] ?? '') !== '' ? $data['color'] : null,
                'lid_type' => ($data['lid_type'] ?? '') !== '' ? $data['lid_type'] : null,
                'unit_type' => ($data['unit_type'] ?? '') !== '' ? $data['unit_type'] : 'pcs',
                'pack_size' => $this->nullableInt($data['pack_size'] ?? null) ?? 1,
                'price_retail' => $this->requiredDecimal($data['price_retail'], 'price_retail'),
                'weight_gram' => $this->nullableInt($data['weight_gram'] ?? null) ?? 0,
                'length_cm' => $this->nullableDecimal($data['length_cm'] ?? null),
                'width_cm' => $this->nullableDecimal($data['width_cm'] ?? null),
                'height_cm' => $this->nullableDecimal($data['height_cm'] ?? null),
                'min_order_qty' => $this->nullableInt($data['min_order_qty'] ?? null) ?? 1,
                'order_multiple' => $this->nullableInt($data['order_multiple'] ?? null) ?? 1,
                'is_active' => $this->toBool($data['is_active'] ?? '1'),
            ];

            if ($createdVariant) {
                $variant = ProductVariant::query()->create($variantPayload);
            } else {
                $variant->update($variantPayload);
            }

            if ($location !== null && array_key_exists('stock_qty', $data) && $data['stock_qty'] !== '') {
                $qty = $this->nullableInt($data['stock_qty']) ?? 0;
                InventoryStock::query()->updateOrCreate(
                    [
                        'inventory_location_id' => $location->id,
                        'product_variant_id' => $variant->id,
                    ],
                    [
                        'qty_on_hand' => $qty,
                        'qty_reserved' => 0,
                    ],
                );
            }

            [$imageAttached, $imageMissing] = $this->attachProductImage($product, $data);

            return [
                'created_product' => $createdProduct,
                'created_variant' => $createdVariant,
                'image_attached' => $imageAttached,
                'image_missing' => $imageMissing,
            ];
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: bool, 1: bool}
     */
    private function attachProductImage(Product $product, array $data): array
    {
        if ($product->getMedia('images')->isNotEmpty()) {
            return [false, false];
        }

        $imageUrl = trim((string) ($data['image_url'] ?? ''));
        if ($imageUrl !== '' && preg_match('#^https?://#i', $imageUrl) === 1) {
            return $this->downloadAndAttachImage(
                $product,
                $imageUrl,
                (string) ($data['image_name'] ?? ''),
            );
        }

        $imagePath = trim((string) ($data['image_path'] ?? ''));
        if ($imagePath === '') {
            return [false, false];
        }

        // If image_path is a remote URL (legacy), download it.
        if (preg_match('#^https?://#i', $imagePath) === 1) {
            return $this->downloadAndAttachImage($product, $imagePath, '');
        }

        return $this->attachLocalImage($product, $imagePath);
    }

    /**
     * @return array{0: bool, 1: bool}
     */
    private function downloadAndAttachImage(Product $product, string $url, string $preferredName): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'CarinaSmartCupCatalogImport/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return [false, true];
            }

            $body = $response->body();
            if ($body === '') {
                return [false, true];
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)
                ?: pathinfo($preferredName, PATHINFO_EXTENSION)
                ?: 'jpg';
            $extension = Str::lower(preg_replace('/[^a-z0-9]/i', '', $extension) ?: 'jpg');

            $filename = Str::slug($product->slug).'-'.Str::random(6).'.'.$extension;
            $relative = 'imports/images/'.$filename;

            Storage::disk('local')->put($relative, $body);
            $absolute = Storage::disk('local')->path($relative);

            $product->addMedia($absolute)
                ->usingFileName($filename)
                ->toMediaCollection('images');

            return [true, false];
        } catch (\Throwable) {
            return [false, true];
        }
    }

    /**
     * @return array{0: bool, 1: bool}
     */
    private function attachLocalImage(Product $product, string $imagePath): array
    {
        $resolved = $this->resolveImagePath($imagePath);

        if ($resolved === null || ! is_file($resolved)) {
            return [false, true];
        }

        $product->addMedia($resolved)
            ->preservingOriginal()
            ->toMediaCollection('images');

        return [true, false];
    }

    private function resolveImagePath(string $imagePath): ?string
    {
        $imagePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($imagePath));

        if (preg_match('#^https?://#i', $imagePath) === 1) {
            return null;
        }

        $candidates = [];

        if ($this->isAbsolutePath($imagePath)) {
            $candidates[] = $imagePath;
        } else {
            $candidates[] = base_path('import'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.ltrim($imagePath, DIRECTORY_SEPARATOR));
            $candidates[] = base_path('import'.DIRECTORY_SEPARATOR.ltrim($imagePath, DIRECTORY_SEPARATOR));
            $candidates[] = base_path(ltrim($imagePath, DIRECTORY_SEPARATOR));
        }

        $importRoot = realpath(base_path('import')) ?: base_path('import');

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);

            if ($real === false || ! is_file($real)) {
                continue;
            }

            if (! str_starts_with($real, $importRoot)) {
                continue;
            }

            return $real;
        }

        return null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:\\\\/', $path) === 1
            || preg_match('/^[A-Za-z]:\//', $path) === 1;
    }

    private function resolveStatus(string $value): ProductStatus
    {
        $value = Str::lower(trim($value));

        return match ($value) {
            'published', 'publish', 'aktif', 'active' => ProductStatus::Published,
            'archived', 'arsip' => ProductStatus::Archived,
            default => ProductStatus::Draft,
        };
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = Str::lower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'y', 'ya'], true);
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) preg_replace('/[^\d-]/', '', (string) $value);
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return Money::parseIdr($value);
    }

    private function requiredDecimal(mixed $value, string $field): float
    {
        if ($value === null || trim((string) $value) === '') {
            throw new \RuntimeException("Kolom {$field} wajib diisi.");
        }

        return Money::parseIdr($value);
    }
}
