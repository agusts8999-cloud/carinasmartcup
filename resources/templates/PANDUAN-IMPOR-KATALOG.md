# Panduan impor katalog CarinaSmartCup

Mendukung dua format:

1. **Template Carina** (CSV/XLSX dengan kolom `category_slug`, `sku`, …)
2. **Triguna/Trifinity** (XLSX sheet `Produk` — diizinkan sesuai PRD mendesak)

## Impor Triguna (cepat)

```bash
php artisan carina:import-catalog import/triguna_katalog_produk.xlsx
```

Tanpa unduh gambar:

```bash
php artisan carina:import-catalog import/triguna_katalog_produk.xlsx --skip-images
```

Mapper akan:

- Membuat kategori otomatis dari kolom `Kategori`
- Membuat 1 produk + 1 varian per baris
- Mengisi harga dari `Harga minimum (Rp)`
- Mengunduh `URL gambar utama` ke media produk

## Template Carina

1. Pastikan kategori sudah ada (kecuali impor Triguna yang auto-create).
2. Unduh template CSV/XLSX dari Admin → Produk.
3. Satu baris = satu SKU/varian.
4. Letakkan gambar lokal di `import/images/` dan isi `image_path`.

```bash
php artisan carina:import-catalog import/carina-katalog.csv
```

## Slug kategori bawaan seed

- `cup-plastik`
- `paper-cup`
- `tutup-aksesori`
- `paket-usaha`

## Catatan angka (template Carina)

- `price_retail`: angka polos, contoh `18500`
- Boolean: `1` / `0`
- `status`: `draft` atau `published`
- `unit_type`: `pcs`, `pack`, atau `dus`

## Gambar

- **Triguna:** URL di kolom `URL gambar utama` diunduh ke `storage/app/imports/images/` lalu dipasang ke Spatie.
- **Carina:** file lokal di `import/images/{nama}` via kolom `image_path`.
- Produk yang sudah punya media tidak diduplikasi saat impor ulang.
