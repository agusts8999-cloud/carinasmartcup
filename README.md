# CarinaSmartCup

Ecommerce B2C/B2B untuk cup & food-beverage packaging (MVP).

## Stack

- Laravel 13 + Livewire 4 + Tailwind (Breeze)
- Filament 5 admin (`/admin`)
- Spatie Permission + Media Library
- SQLite default (bisa diganti MySQL Laragon)
- Pembayaran: transfer bank manual + konfirmasi admin
- Ongkir: zona & tarif custom

## Setup (Laragon)

1. Pastikan PHP 8.3+ dan Composer tersedia.
2. Di folder proyek:

```bash
composer install
cp .env.example .env   # jika perlu
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
```

3. Arahkan virtual host ke `public/` (mis. `http://carinasmartcup.local`).

Atau jalankan lokal:

```bash
php artisan serve
```

## Akun seed

| Peran | Email | Password |
|-------|-------|----------|
| Owner | owner@carinasmartcup.test | password |
| Finance | finance@carinasmartcup.test | password |
| Gudang | warehouse@carinasmartcup.test | password |

Kupon contoh: `WELCOME10` (10%, min Rp 100.000).

## Fitur MVP

- Katalog + varian SKU, filter, pencarian
- Harga volume campur SKU, MOQ & kelipatan
- Keranjang tamu + share link
- Checkout tamu/login, snapshot order
- Transfer manual + unggah bukti + konfirmasi Finance
- Zona ongkir custom + pickup
- Status order, resi, lacak, retur/tiket
- FAQ/CMS, audit log, dashboard stats
- Job `carina:release-expired-orders` (hourly)

## Impor katalog (CSV / XLSX + gambar)

PRD mendesak mengizinkan impor data **Triguna/Trifinity** dari `import/triguna_katalog_produk.xlsx`, selain template Carina.

### Triguna / Trifinity

```bash
php artisan carina:import-catalog import/triguna_katalog_produk.xlsx
php artisan carina:import-catalog import/triguna_katalog_produk.xlsx --skip-images
```

- Kategori dibuat otomatis
- Harga = harga minimum
- Gambar diunduh dari kolom URL gambar utama

### Template Carina + gambar lokal

```
import/
  carina-katalog.csv
  images/
    CSC-PP8-CLR-50.jpg
```

```bash
php artisan carina:import-catalog import/carina-katalog.csv
```

### Admin

**Katalog → Produk → Impor CSV / XLSX** menerima kedua format.

### Kolom template Carina

| Kolom | Keterangan |
|-------|------------|
| `category_slug` | Harus sudah ada (kecuali jalur Triguna) |
| `product_slug` | Identitas produk |
| `sku` | Unik per varian |
| `price_retail` | Angka polos |
| `stock_qty` | Stok awal |
| `image_path` | File di `import/images/` |

Template: `storage/app/templates/katalog-produk-template.csv` / `.xlsx`  
Panduan: `resources/templates/PANDUAN-IMPOR-KATALOG.md`

## Tes

```bash
php artisan test --filter=CatalogImport
```

## Konfigurasi

Lihat `.env` / `config/carina.php` untuk rekening bank, WhatsApp bantuan, dan jam reservasi stok.
