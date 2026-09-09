# Product Requirements Document (PRD)

## CarinaSmartCup — Ecommerce Cup & Food-Beverage Packaging

**Versi:** 1.0  
**Tanggal:** 9 September 2026  
**Status:** Draft untuk validasi bisnis dan teknis  
**Pemilik produk:** CarinaSmartCup

## 1. Ringkasan produk

CarinaSmartCup adalah platform ecommerce B2C dan B2B untuk menjual cup serta perlengkapan penyajian minuman/makanan secara retail maupun grosir. Platform dirancang agar pelanggan dapat memilih ukuran dan tipe produk, mencampur beberapa SKU untuk memperoleh harga volume, melihat ketersediaan stok, menghitung ongkir, membayar, dan melacak pesanan dalam satu alur.

Prinsip utama produk:

- Harga dan ketersediaan transparan.
- Belanja campur untuk kebutuhan usaha kecil hingga pembelian grosir.
- Checkout sederhana, termasuk keranjang tamu.
- Informasi pengiriman dan estimasi tiba muncul sebelum pembayaran.
- Operasional admin, stok, pembayaran, dan retur tercatat dalam satu sistem.

## 2. Latar belakang dan masalah

Pelanggan usaha minuman, kafe, katering, reseller, dan pembeli rumah tangga sering mengalami:

- Kesulitan membandingkan ukuran, bahan, isi per pack, dan kompatibilitas tutup.
- Harga grosir hanya tersedia melalui chat dan harus dihitung manual.
- Ongkir sulit diperkirakan karena produk ber-volume besar.
- Stok dan estimasi pengiriman tidak jelas.
- Pesanan dari WhatsApp/marketplace sulit direkonsiliasi oleh admin.

CarinaSmartCup membutuhkan kanal penjualan langsung yang mampu melayani pembelian kecil sekaligus pesanan usaha, dengan biaya operasional chat yang lebih rendah.

## 3. Tujuan dan indikator keberhasilan

### Tujuan bisnis

1. Meningkatkan transaksi langsung melalui website.
2. Menaikkan nilai rata-rata pesanan melalui harga volume dan rekomendasi bundel.
3. Mengurangi pekerjaan manual untuk kalkulasi harga, stok, ongkir, dan status order.
4. Membangun basis pelanggan yang dapat dipelihara melalui repeat order, reward, dan reseller.

### Target awal yang perlu divalidasi

| Indikator | Target 90 hari setelah peluncuran |
|---|---:|
| Checkout berhasil dari keranjang | ≥ 3% sesi dengan produk |
| Pembayaran berhasil dari checkout | ≥ 70% checkout |
| Pesanan dengan status pembayaran otomatis | ≥ 85% |
| Repeat order pelanggan terdaftar | ≥ 20% |
| Pesanan yang membutuhkan koreksi admin | ≤ 15% |
| Waktu admin membuat order manual | turun ≥ 50% |

Target angka dapat disesuaikan setelah tersedia baseline penjualan.

## 4. Sasaran pengguna

### Persona utama

**Pemilik usaha minuman/kafe**  
Membeli cup dan tutup secara berkala, membutuhkan harga grosir, stok pasti, dan pengiriman cepat.

**UMKM makanan, katering, dan bakery**  
Membeli cup, wadah, dan aksesori dalam jumlah campuran; sensitif terhadap ongkir dan isi per pack.

**Reseller/dropshipper**  
Membutuhkan katalog, harga modal, link produk/keranjang, dan pengiriman langsung ke pelanggan.

**Pembeli retail**  
Membeli jumlah kecil untuk acara, rumah tangga, sampel, atau uji produk.

**Admin CarinaSmartCup**  
Mengelola produk, stok, order, pembayaran, pengiriman, promo, pelanggan, retur, dan konten.

## 5. Proposisi nilai

> “Belanja cup lebih mudah: pilih varian, campur kebutuhan, dapatkan harga terbaik, dan tahu biaya kirim sebelum bayar.”

Diferensiasi yang disarankan:

- Harga volume otomatis berdasarkan total belanja atau level pelanggan.
- Informasi produk yang operasional: ukuran, bahan, kapasitas, isi pack, berat, dimensi, dan pasangan tutup.
- Rekomendasi paket “cup + tutup + sedotan/seal”.
- Estimasi ongkir yang mempertimbangkan berat dan volume.
- Pemesanan ulang dari riwayat transaksi.

## 6. Ruang lingkup

### MVP — wajib untuk peluncuran

- Homepage dan katalog produk responsif.
- Kategori, pencarian, filter, sorting, dan halaman detail produk.
- Varian produk: ukuran, kapasitas, bahan, warna, tipe tutup, dan satuan jual.
- Keranjang tamu dan keranjang pelanggan.
- Harga retail dan harga volume bertingkat.
- Kupon/promo dasar.
- Registrasi/login menggunakan nomor WhatsApp atau email.
- Alamat lengkap dan pin lokasi opsional.
- Kalkulasi ongkir berdasarkan area, berat, dan/atau volume.
- Checkout, invoice, status pembayaran, dan notifikasi.
- Pembayaran transfer/VA/QRIS melalui payment gateway; COD hanya jika aturan wilayah mendukung.
- Pilihan pengiriman reguler, cargo, same-day/kurir lokal, dan ambil sendiri bila tersedia.
- Pelacakan status pesanan dan nomor resi.
- Dashboard admin untuk produk, stok, order, promo, pelanggan, dan laporan dasar.
- Kebijakan retur, FAQ, kontak bantuan, syarat & ketentuan, serta kebijakan privasi.

### Fase 2 — setelah MVP stabil

- Stok multi-gudang/cabang dan pemilihan gudang terdekat.
- Dropship dan reseller dengan link toko/markup.
- Poin reward dan referral.
- Faktur pajak dan data perusahaan.
- Paket sampel dan langganan/reorder berkala.
- Integrasi WhatsApp untuk notifikasi dan bantuan admin.
- Integrasi marketplace atau omnichannel inventory.
- Portal kurir internal dan optimasi rute.
- Custom printing/logo pada cup jika operasional sudah siap.

### Di luar scope MVP

- Marketplace multi-penjual.
- Produksi custom langsung tanpa alur quotation dan approval.
- Pembayaran cicilan atau paylater sebelum ada kebutuhan bisnis yang tervalidasi.
- Aplikasi native iOS/Android; MVP menggunakan web mobile-first.

## 7. Struktur informasi

- Beranda
- Belanja
  - Cup plastik
  - Paper cup
  - Cup dengan tutup
  - Cup seal/sealing
  - Tutup dan aksesori
  - Paket usaha
  - Paket sampel
- Promo dan harga grosir
- Cara belanja
- Cek ongkir
- Lacak pesanan
- Bantuan/FAQ
- Tentang CarinaSmartCup
- Akun
  - Profil
  - Alamat
  - Riwayat pesanan
  - Poin/reward (fase 2)
  - Reseller (fase 2)

## 8. Alur pengguna utama

### A. Belanja retail/grosir

1. Pengguna membuka homepage atau hasil pencarian.
2. Pengguna memfilter berdasarkan kapasitas, bahan, jenis tutup, dan satuan jual.
3. Pengguna membuka detail produk dan memilih varian serta jumlah.
4. Sistem menampilkan stok, minimum order, isi pack, harga satuan, dan kemungkinan diskon.
5. Pengguna menambahkan beberapa produk ke keranjang.
6. Sistem menghitung subtotal, diskon volume, berat/volume, dan estimasi ongkir.
7. Pengguna login atau melanjutkan sebagai tamu.
8. Pengguna mengisi alamat, memilih pengiriman, dan memilih pembayaran.
9. Sistem membuat nomor order/invoice dan menampilkan instruksi pembayaran.
10. Setelah pembayaran terverifikasi, order masuk ke proses picking dan packing.
11. Pengguna menerima notifikasi status sampai pesanan selesai.

### B. Keranjang untuk dibantu admin

Pengguna dapat membagikan link keranjang berisi SKU dan kuantitas kepada admin. Link tidak boleh membuka data pribadi, dan harga/ongkir dihitung ulang ketika checkout.

### C. Komplain/retur

Pengguna mengajukan tiket dengan nomor order, alasan, foto/video bukti, dan jumlah unit terdampak. Admin memeriksa bukti, menentukan solusi, lalu memperbarui status tiket.

## 9. Persyaratan fungsional

### 9.1 Katalog dan produk

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| CAT-01 | Admin dapat membuat produk, kategori, atribut, foto, video, dan deskripsi. | P0 | Produk dapat disimpan sebagai draft atau dipublikasikan. |
| CAT-02 | Produk mendukung banyak varian/SKU. | P0 | Setiap SKU memiliki harga, stok, berat, dimensi, barcode/SKU internal, dan status aktif. |
| CAT-03 | Halaman produk menampilkan kapasitas, bahan, ukuran, isi per pack, minimum order, kecocokan tutup, dan petunjuk penggunaan. | P0 | Informasi dapat dibaca sebelum tombol tambah ke keranjang. |
| CAT-04 | Pengguna dapat mencari dengan nama, SKU, kapasitas, atau kata kunci. | P0 | Hasil relevan muncul dan dapat difilter. |
| CAT-05 | Sistem menampilkan produk terkait dan bundel kompatibel. | P1 | Produk terkait dapat dikonfigurasi admin. |

### 9.2 Harga, stok, dan promo

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| PRI-01 | Admin dapat mengatur harga retail, harga grosir, dan aturan harga per level. | P0 | Perubahan harga tercatat dengan waktu dan pengguna pengubah. |
| PRI-02 | Diskon volume dihitung dari total barang yang memenuhi aturan, termasuk opsi belanja campur. | P0 | Simulasi subtotal menampilkan level diskon yang dicapai dan sisa menuju level berikutnya. |
| PRI-03 | Sistem menerapkan minimum order dan kelipatan pembelian per SKU. | P0 | Kuantitas yang tidak valid ditolak dengan pesan yang jelas. |
| PRI-04 | Stok berkurang/ditahan sesuai status pembayaran dan aturan reservasi. | P0 | Stok tidak menjadi negatif; order gagal bayar dilepas setelah batas waktu. |
| PRI-05 | Admin dapat membuat kupon berdasarkan nominal, persentase, minimum belanja, produk, kategori, wilayah, dan masa berlaku. | P1 | Kupon tervalidasi di keranjang sebelum checkout. |

### 9.3 Keranjang dan checkout

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| CHK-01 | Keranjang dapat digunakan tanpa login. | P0 | Pengguna dapat menambah, mengubah, menghapus item, dan kembali belanja. |
| CHK-02 | Keranjang menampilkan subtotal, diskon, ongkir, total, berat, jumlah koli, dan estimasi volume jika tersedia. | P0 | Semua nilai diperbarui setelah kuantitas/alamat berubah. |
| CHK-03 | Checkout mendukung login, registrasi, dan checkout tamu. | P0 | Checkout tamu tetap meminta data kontak dan alamat minimum. |
| CHK-04 | Pengguna dapat menyimpan beberapa alamat. | P0 | Satu alamat dapat ditandai sebagai default. |
| CHK-05 | Sistem memvalidasi ulang harga, stok, promo, ongkir, dan metode pembayaran sebelum membuat order. | P0 | Jika berubah, pengguna diminta menyetujui perubahan sebelum bayar. |
| CHK-06 | Pengguna dapat menambahkan catatan pesanan. | P1 | Catatan terlihat oleh admin/packing sesuai izin. |

### 9.4 Pembayaran dan order

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| ORD-01 | Sistem membuat nomor order/invoice unik. | P0 | Nomor order dapat dicari oleh admin dan pelanggan. |
| ORD-02 | Payment gateway mengirim status pembayaran melalui callback/webhook. | P0 | Status tidak hanya bergantung pada redirect browser. |
| ORD-03 | Sistem mendukung transfer/VA/QRIS; COD hanya pada wilayah/metode yang diizinkan. | P0 | Metode yang tidak eligible tidak ditampilkan. |
| ORD-04 | Order memiliki status: Menunggu Pembayaran, Dibayar, Diproses, Dikemas, Dikirim, Selesai, Dibatalkan, dan Retur. | P0 | Setiap perubahan status menyimpan timestamp dan aktor. |
| ORD-05 | Pelanggan dapat melihat riwayat order dan memesan ulang. | P1 | Produk yang sudah tidak aktif ditandai dan tidak otomatis masuk keranjang. |
| ORD-06 | Admin dapat memisahkan atau menggabungkan pemenuhan order bila stok/gudang mengharuskan. | P1 | Pelanggan mendapat informasi jika pengiriman terpisah. |

### 9.5 Pengiriman

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| SHP-01 | Sistem menghitung pilihan pengiriman berdasarkan alamat, berat, volume, area, dan nilai order. | P0 | Minimal satu opsi valid tampil sebelum pembayaran. |
| SHP-02 | Admin dapat mengatur zona, tarif, minimum order, subsidi, cutoff, dan estimasi hari. | P0 | Perubahan aturan tidak mengubah order yang sudah dibayar. |
| SHP-03 | Admin dapat memasukkan nomor resi dan URL pelacakan. | P0 | Pelanggan dapat melihatnya dari halaman lacak order. |
| SHP-04 | Ambil sendiri hanya muncul bila lokasi dan jam operasional tersedia. | P1 | Instruksi pengambilan muncul pada invoice. |
| SHP-05 | Sistem dapat menampilkan peringatan bahwa ongkir/volume adalah estimasi. | P0 | Pelanggan harus menyetujui estimasi sebelum pembayaran jika ada verifikasi manual. |

### 9.6 Akun, bantuan, dan retensi

| ID | Persyaratan | Prioritas | Kriteria penerimaan |
|---|---|---|---|
| CRM-01 | Login menggunakan OTP WhatsApp/email atau kombinasi nomor WhatsApp dan kata sandi sesuai keputusan implementasi. | P0 | Verifikasi berhasil dan sesi aman. |
| CRM-02 | Pelanggan dapat menghubungi admin melalui WhatsApp/live chat. | P0 | Tombol bantuan tersedia di halaman produk, keranjang, dan order. |
| CRM-03 | FAQ mencakup minimum order, bahan, kompatibilitas tutup, pengiriman, pembayaran, retur, dan keamanan produk. | P0 | FAQ dapat dikelola admin tanpa deploy. |
| CRM-04 | Sistem mengirim email/WhatsApp/in-app notification untuk order dan pembayaran. | P1 | Pengiriman notifikasi tercatat dan dapat diulang oleh admin. |
| CRM-05 | Poin reward/referral dan reseller menggunakan modul terpisah. | P2 | Tidak aktif di MVP bila aturan finansial belum final. |

## 10. Aturan bisnis awal

Aturan di bawah ini adalah usulan untuk divalidasi CarinaSmartCup; bukan angka final.

- Harga dapat berbeda antara pembelian retail, pack, dus, reseller, dan pelanggan bisnis.
- Diskon grosir dihitung dari total barang dalam satu order dan boleh mencampur SKU bila aturan kategori mengizinkan.
- Setiap SKU memiliki satuan jual, isi per pack, minimum order, dan kelipatan pembelian.
- Stok hanya ditahan setelah pembayaran berhasil, kecuali admin mengaktifkan reservasi dengan batas waktu.
- Pesanan dengan stok kurang dapat ditahan untuk verifikasi admin atau ditawarkan split shipment.
- COD hanya tersedia jika alamat, nilai order, jenis barang, dan metode pengiriman memenuhi aturan risiko.
- Pesanan yang sudah diproses gudang tidak dapat diedit langsung oleh pelanggan.
- Pengajuan retur harus menyertakan nomor order dan bukti; periode serta biaya retur ditentukan dalam kebijakan resmi CarinaSmartCup.
- Untuk produk food-contact, data bahan, batas penggunaan, dan sertifikasi harus bersumber dari dokumen resmi pemasok/manufaktur.
- Custom printing/logo tidak boleh dijual sebagai produk biasa; harus melalui quotation, minimum order, proof desain, lead time, dan approval.

## 11. Dashboard admin

### Modul wajib

- Ringkasan penjualan, order, pembayaran, produk terlaris, dan order bermasalah.
- Produk, kategori, atribut, bundel, media, dan SEO dasar.
- Inventori dan adjustment stok.
- Order, invoice, pembayaran, fulfillment, resi, pembatalan, dan retur.
- Harga volume, kupon, promo, serta aturan ongkir.
- Pelanggan dan segmentasi dasar.
- FAQ, banner, artikel, halaman legal, dan notifikasi.
- Audit log untuk perubahan harga, stok, status order, dan pengaturan pembayaran.

### Peran dan akses

- Owner: seluruh akses.
- Admin ecommerce: katalog, promo, pelanggan, order.
- Gudang: picking, packing, stok, pengiriman.
- Finance: pembayaran, refund, invoice, laporan.
- Customer service: pelanggan, order, tiket, tanpa akses ubah harga pokok.

## 12. Model data inti

- User, Address, CustomerSegment
- Category, Product, ProductVariant, ProductMedia, ProductBundle
- InventoryLocation, InventoryStock, StockAdjustment
- PriceList, VolumeRule, Coupon, Promotion
- Cart, CartItem
- Order, OrderItem, Payment, Invoice
- ShippingZone, ShippingRate, Shipment, TrackingEvent
- ReturnRequest, SupportTicket, Notification
- AuditLog

Setiap order menyimpan snapshot nama produk, SKU, harga, diskon, alamat, ongkir, dan aturan pajak saat transaksi dibuat agar histori tidak berubah ketika katalog diperbarui.

## 13. Persyaratan nonfungsional

- Mobile-first dan nyaman digunakan pada koneksi seluler.
- Waktu tampil halaman katalog utama idealnya di bawah 3 detik pada koneksi 4G untuk halaman yang sudah ter-cache.
- Ketersediaan layanan target MVP minimal 99,5% per bulan, tidak termasuk maintenance terjadwal.
- Semua transaksi menggunakan HTTPS; password di-hash; data pembayaran sensitif tidak disimpan oleh CarinaSmartCup.
- Webhook pembayaran tervalidasi, idempotent, dan memiliki log kegagalan.
- Sistem mencegah overselling melalui transaksi database/reservasi stok.
- Data pribadi memiliki mekanisme akses, koreksi, dan penghapusan sesuai kebijakan privasi yang berlaku.
- Gambar produk dikompresi, memiliki alt text, dan tersedia dalam ukuran yang sesuai perangkat.
- URL produk, metadata, sitemap, dan schema markup disiapkan untuk SEO.
- Error kepada pengguna harus menjelaskan tindakan berikutnya tanpa membocorkan detail teknis.

## 14. Analitik dan event

Event minimum:

- view_home, search_product, filter_product, view_product
- select_variant, add_to_cart, view_cart, share_cart
- begin_checkout, select_shipping, select_payment, purchase
- payment_failed, order_cancelled, shipment_created, order_completed
- return_requested, repeat_order

Dashboard metrik: conversion rate, add-to-cart rate, checkout abandonment, average order value, gross margin, diskon per order, biaya ongkir yang disubsidi, repeat order, refund/retur rate, dan waktu pemrosesan order.

## 15. Rencana pelaksanaan

### Tahap 0 — discovery dan validasi, 1–2 minggu

- Finalisasi katalog awal dan struktur SKU.
- Validasi aturan harga, minimum order, ongkir, pembayaran, retur, dan pajak.
- Pilih payment gateway dan sumber tarif pengiriman.
- Uji 5–10 pelanggan target dengan prototipe alur checkout.

### Tahap 1 — MVP, 4–6 minggu

- Katalog, pencarian, detail produk, keranjang, checkout, pembayaran, order, admin, dan notifikasi.
- Pengujian integrasi pembayaran, stok, ongkir, dan mobile.

### Tahap 2 — soft launch, 1–2 minggu

- Rilis terbatas ke pelanggan/area terpilih.
- Pantau order gagal, selisih stok, akurasi ongkir, dan keluhan pengguna.

### Tahap 3 — scale, setelah KPI MVP stabil

- Multi-gudang, reseller/dropship, reward, reorder, integrasi WhatsApp, dan analitik lanjutan.

## 16. Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data isi pack/ukuran tidak konsisten | Komplain dan retur | Template data produk wajib + approval sebelum publish. |
| Ongkir produk besar tidak akurat | Margin turun atau checkout gagal | Simpan berat/dimensi, gunakan zona, sediakan verifikasi manual untuk order besar. |
| Stok website berbeda dari gudang | Pembatalan order | Stock opname rutin, audit adjustment, dan reservasi saat pembayaran. |
| Diskon terlalu agresif | Margin tergerus | Simulasi margin per SKU dan approval promo. |
| COD berisiko gagal | Biaya operasional tinggi | Batasi wilayah/nilai order, skor pelanggan, dan konfirmasi sebelum kirim. |
| Pelanggan bingung antara pack dan dus | Salah jumlah beli | Tampilkan contoh kuantitas dan harga per unit pada halaman produk. |
| Perubahan harga saat checkout | Hilang kepercayaan | Snapshot harga, validasi ulang, dan pesan perubahan yang transparan. |

## 17. Kriteria go-live

- Minimal 30 SKU aktif dengan data varian, foto, isi pack, berat, dan dimensi lengkap.
- Minimal satu metode pembayaran otomatis lulus uji sukses, gagal, kedaluwarsa, refund, dan webhook ganda.
- Minimal satu metode pengiriman lulus uji beberapa zona dan skenario berat/volume.
- Tidak ada bug P0/P1 terbuka pada katalog, checkout, pembayaran, stok, atau order.
- Admin dapat memproses order end-to-end tanpa spreadsheet terpisah.
- Kebijakan harga, pembayaran, pengiriman, retur, privasi, dan syarat transaksi sudah disetujui.
- Backup, monitoring, audit log, dan prosedur pemulihan sudah diuji.
- UAT disetujui oleh pemilik bisnis, admin ecommerce, gudang, finance, dan customer service.

## 18. Pertanyaan terbuka untuk keputusan bisnis

1. Produk utama CarinaSmartCup apa saja dan apakah termasuk cup custom printing?
2. Wilayah pengiriman awal: nasional, kota tertentu, atau sekitar gudang?
3. Apakah ada gudang/cabang lebih dari satu?
4. Apakah harga grosir dihitung dari total nilai order, kuantitas per SKU, atau keduanya?
5. Berapa minimum order, isi pack, dan kelipatan pembelian setiap kategori?
6. Payment gateway, ekspedisi, dan kanal notifikasi apa yang sudah tersedia?
7. Apakah COD, ambil sendiri, dropship, reseller, referral, dan reward diperlukan sejak MVP?
8. Apakah pelanggan bisnis memerlukan invoice pajak?
9. Bagaimana kebijakan retur untuk barang rusak, salah kirim, atau berubah pikiran?
10. Berapa margin minimum per SKU setelah diskon dan subsidi ongkir?

## 19. Referensi dan batasan adaptasi

Dokumen ini menggunakan situs Triguna Jaya Sentosa sebagai referensi pola ecommerce: katalog produk dengan variasi, harga volume, keranjang tanpa login, informasi stok, kalkulasi pengiriman, pembayaran, pelacakan order, reseller/dropship, dan FAQ operasional.

CarinaSmartCup harus menggunakan identitas visual, konten, data produk, kebijakan harga, dan aturan operasional miliknya sendiri. Tidak ada teks, gambar, merek, atau konfigurasi komersial dari situs referensi yang boleh disalin tanpa izin.

**URL referensi:** https://trigunajayasentosaplastik.com/

