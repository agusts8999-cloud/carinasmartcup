<?php

namespace App\Services;

use Illuminate\Support\Str;

class TrigunaCatalogMapper
{
    /**
     * @param  list<string>  $headers  Lowercased headers
     */
    public static function isTrigunaHeader(array $headers): bool
    {
        $normalized = array_map(
            fn (string $h) => Str::of($h)->lower()->replaceMatches('/\s+/', ' ')->trim()->toString(),
            $headers,
        );

        $hasName = in_array('nama produk', $normalized, true)
            || in_array('nama_produk', $normalized, true);
        $hasImageUrl = in_array('url gambar utama', $normalized, true)
            || in_array('url_gambar_utama', $normalized, true);

        return $hasName && $hasImageUrl;
    }

    /**
     * Map a Triguna row (keyed by original/lower headers) into CatalogImportService row data.
     *
     * @param  array<string, mixed>  $row  Keys are lowercased source headers
     * @return array<string, mixed>
     */
    public function map(array $row, int $rowNumber): array
    {
        $get = function (string ...$keys) use ($row): string {
            foreach ($keys as $key) {
                $key = Str::lower($key);
                if (array_key_exists($key, $row) && trim((string) $row[$key]) !== '') {
                    return trim((string) $row[$key]);
                }
            }

            return '';
        };

        $categoryName = $get('kategori');
        $productName = $get('nama produk', 'nama_produk');

        if ($productName === '') {
            throw new \RuntimeException('Nama produk kosong.');
        }

        if ($categoryName === '') {
            $categoryName = 'Lainnya';
        }

        $categorySlug = Str::slug($categoryName) ?: 'lainnya';
        $baseSlug = Str::slug($productName) ?: 'produk-'.$rowNumber;
        $productSlug = $baseSlug.'-'.$rowNumber;

        $sku = $get('sku');
        if ($sku === '') {
            $sku = 'CSC-'.$rowNumber.'-'.Str::upper(Str::substr($baseSlug, 0, 8));
        }

        $priceMin = $get('harga minimum (rp)', 'harga minimum', 'harga_minimum_rp');
        $priceMax = $get('harga maksimum (rp)', 'harga maksimum', 'harga_maksimum_rp');
        $priceRetail = $priceMin !== '' ? $priceMin : ($priceMax !== '' ? $priceMax : '0');

        $priceDisplay = $get('harga tampil');
        $packaging = $get('kemasan / detail ringkas', 'kemasan / detail', 'kemasan');
        $description = $get('deskripsi / detail', 'deskripsi');
        if ($priceDisplay !== '') {
            $description = trim($description."\nHarga tampil: ".$priceDisplay);
        }

        $stockRaw = $get('stok cabang', 'stok');
        $stockQty = $this->parseStock($stockRaw);

        $imageUrl = $get('url gambar utama', 'url_gambar_utama');
        $imageName = $get('nama gambar utama', 'nama_gambar_utama');

        return [
            'category_slug' => $categorySlug,
            'category_name' => $categoryName,
            'ensure_category' => true,
            'product_name' => $productName,
            'product_slug' => $productSlug,
            'sku' => $sku,
            'variant_name' => $packaging !== '' ? Str::limit($packaging, 120, '') : null,
            'barcode' => '',
            'color' => '',
            'lid_type' => '',
            'unit_type' => 'pcs',
            'pack_size' => '1',
            'price_retail' => $priceRetail,
            'weight_gram' => '0',
            'length_cm' => '',
            'width_cm' => '',
            'height_cm' => '',
            'min_order_qty' => '1',
            'order_multiple' => '1',
            'material' => '',
            'capacity_ml' => '',
            'size_label' => $packaging !== '' ? Str::limit($packaging, 80, '') : '',
            'description' => $description,
            'usage_notes' => '',
            'lid_compatibility' => '',
            'status' => 'published',
            'is_featured' => '0',
            'is_active' => '1',
            'stock_qty' => (string) $stockQty,
            'image_path' => '',
            'image_url' => $imageUrl,
            'image_name' => $imageName,
            'source_url' => $get('url produk', 'url_produk'),
        ];
    }

    private function parseStock(string $raw): int
    {
        if ($raw === '') {
            return 100;
        }

        if (preg_match_all('/\d+/', $raw, $matches) && $matches[0] !== []) {
            return max(0, (int) $matches[0][0]);
        }

        return 100;
    }
}
