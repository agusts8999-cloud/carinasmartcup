<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CmsContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->faqs() as $index => $faq) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $faq['slug']],
                [
                    'title' => $faq['title'],
                    'type' => 'faq',
                    'body' => $faq['body'],
                    'excerpt' => Str::limit(strip_tags($faq['body']), 160),
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        foreach ($this->pages() as $index => $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'type' => 'page',
                    'body' => $page['body'],
                    'excerpt' => Str::limit(strip_tags($page['body']), 160),
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        // Keep legacy slug used by older links.
        CmsPage::query()->updateOrCreate(
            ['slug' => 'retur'],
            [
                'title' => 'Kebijakan Retur',
                'type' => 'page',
                'body' => $this->pages()[2]['body'],
                'excerpt' => Str::limit(strip_tags($this->pages()[2]['body']), 160),
                'is_published' => true,
                'sort_order' => 99,
            ],
        );
    }

    /**
     * @return list<array{title: string, slug: string, body: string}>
     */
    private function faqs(): array
    {
        return [
            [
                'title' => 'Berapa minimum order?',
                'slug' => 'faq-minimum-order',
                'body' => '<p>Minimum order mengikuti <strong>pack size</strong> dan aturan MOQ tiap varian (biasanya 50 atau 100 pcs). Di halaman produk, jumlah pesanan otomatis menyesuaikan kelipatan order yang berlaku.</p>',
            ],
            [
                'title' => 'Apakah bisa ambil sendiri (pickup)?',
                'slug' => 'faq-pickup',
                'body' => '<p>Ya. Pickup tersedia di Gudang Utama pada jam operasional Senin–Sabtu. Pilih metode pengambilan saat checkout, lalu tunjukkan nomor pesanan saat datang.</p>',
            ],
            [
                'title' => 'Bagaimana cara pembayaran?',
                'slug' => 'faq-pembayaran',
                'body' => '<p>Pembayaran menggunakan <strong>transfer bank manual</strong>. Setelah checkout, transfer sesuai nominal invoice lalu unggah bukti pembayaran di halaman pesanan. Tim kami akan verifikasi sebelum proses packing.</p>',
            ],
            [
                'title' => 'Apakah ada diskon volume?',
                'slug' => 'faq-diskon-volume',
                'body' => '<p>Ya. Beberapa kategori mendapat diskon volume otomatis di keranjang sesuai tier pembelian. Semakin besar quantity/nilai belanja yang memenuhi syarat, semakin besar potongan yang diterapkan.</p>',
            ],
            [
                'title' => 'Berapa lama pengiriman?',
                'slug' => 'faq-pengiriman',
                'body' => '<p>Estimasi umum: Jabodetabek 1–2 hari, Jawa 2–4 hari, luar Jawa 4–7 hari kerja setelah pembayaran dikonfirmasi. Estimasi akhir mengikuti kurir dan area tujuan.</p>',
            ],
            [
                'title' => 'Bahan cup apa saja yang tersedia?',
                'slug' => 'faq-bahan',
                'body' => '<p>Kami menyediakan kemasan dari bahan umum industri F&amp;B seperti PP, PET, paper/kraft, serta aksesoris tutup dan perlengkapan pendukung. Spesifikasi bahan tercantum di halaman masing-masing produk.</p>',
            ],
            [
                'title' => 'Bagaimana kompatibilitas tutup cup?',
                'slug' => 'faq-kompatibilitas-tutup',
                'body' => '<p>Tutup (flat/dome/ulir) harus dicocokkan dengan ukuran dan tipe cup. Gunakan filter katalog atau cek catatan kompatibilitas pada detail produk. Jika ragu, hubungi WhatsApp support sebelum checkout.</p>',
            ],
            [
                'title' => 'Apakah produk aman untuk makanan & minuman?',
                'slug' => 'faq-keamanan-produk',
                'body' => '<p>Produk ditujukan untuk kebutuhan kemasan F&amp;B. Simpan di tempat bersih dan kering, jauhkan dari panas berlebih sesuai material. Ikuti panduan penggunaan pada deskripsi produk.</p>',
            ],
            [
                'title' => 'Bagaimana jika barang rusak atau salah kirim?',
                'slug' => 'faq-retur',
                'body' => '<p>Ajukan retur maksimal 7 hari setelah barang diterima, lengkap dengan nomor pesanan dan bukti foto/video. Detail lengkap ada di halaman <a href="/halaman/kebijakan-retur">Kebijakan Retur</a>.</p>',
            ],
        ];
    }

    /**
     * @return list<array{title: string, slug: string, body: string}>
     */
    private function pages(): array
    {
        return [
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'body' => <<<'HTML'
<p>Kebijakan Privasi ini menjelaskan bagaimana CarinaSmartCup mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi pelanggan saat menggunakan situs dan layanan kami.</p>
<h2>1. Data yang Kami Kumpulkan</h2>
<ul>
<li>Data identitas &amp; kontak: nama, email, nomor WhatsApp/telepon, alamat pengiriman.</li>
<li>Data transaksi: detail pesanan, pembayaran, bukti transfer, riwayat pengiriman.</li>
<li>Data teknis: alamat IP, jenis perangkat/browser, dan log aktivitas yang diperlukan untuk keamanan layanan.</li>
</ul>
<h2>2. Penggunaan Data</h2>
<ul>
<li>Memproses pesanan, pembayaran, fulfillment, dan layanan pelanggan.</li>
<li>Mengirim notifikasi status pesanan dan informasi terkait transaksi.</li>
<li>Meningkatkan kualitas layanan, mencegah penipuan, serta memenuhi kewajiban hukum.</li>
</ul>
<h2>3. Penyimpanan &amp; Keamanan</h2>
<p>Data disimpan pada sistem yang wajar secara teknis dan organisasional. Akses data dibatasi untuk pihak yang berkepentingan dalam operasional pesanan. Anda bertanggung jawab menjaga kerahasiaan akun dan bukti pembayaran.</p>
<h2>4. Berbagi Data kepada Pihak Ketiga</h2>
<p>Data dapat dibagikan kepada mitra pengiriman, gerbang pembayaran/bank (sebatas keperluan verifikasi), dan penyedia infrastruktur yang membantu operasional, dengan batasan tujuan layanan.</p>
<h2>5. Hak Pelanggan</h2>
<p>Anda dapat meminta akses, koreksi, atau penghapusan data pribadi sesuai kebijakan yang berlaku melalui WhatsApp support, sepanjang tidak bertentangan dengan kewajiban penyimpanan transaksi.</p>
<h2>6. Perubahan Kebijakan</h2>
<p>Kami dapat memperbarui kebijakan ini dari waktu ke waktu. Versi terbaru selalu ditampilkan pada halaman ini.</p>
<p>Pertanyaan terkait privasi: hubungi WhatsApp Support CarinaSmartCup.</p>
HTML,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'syarat-ketentuan',
                'body' => <<<'HTML'
<p>Dengan mengakses situs dan melakukan pemesanan di CarinaSmartCup, Anda menyetujui syarat &amp; ketentuan berikut.</p>
<h2>1. Akun &amp; Pemesanan</h2>
<ul>
<li>Informasi yang Anda berikan harus benar dan dapat dihubungi.</li>
<li>Pesanan dianggap aktif setelah checkout berhasil dibuat.</li>
<li>Jumlah order harus memenuhi minimum order dan kelipatan yang ditentukan tiap varian.</li>
</ul>
<h2>2. Harga, Stok &amp; Promo</h2>
<ul>
<li>Harga, stok, dan promo dapat berubah sewaktu-waktu sebelum pembayaran dikonfirmasi.</li>
<li>Diskon volume/kupon diterapkan otomatis sesuai aturan yang berlaku saat checkout.</li>
<li>Jika stok tidak mencukupi, kami dapat menghubungi Anda untuk penyesuaian atau pembatalan sebagian/seluruh item.</li>
</ul>
<h2>3. Pembayaran</h2>
<ul>
<li>Metode pembayaran utama: transfer bank manual sesuai invoice.</li>
<li>Pesanan dapat dibatalkan otomatis jika pembayaran tidak diterima dalam batas waktu reservasi.</li>
<li>Anda wajib mengunggah bukti transfer yang jelas dan sesuai nominal.</li>
</ul>
<h2>4. Pengiriman</h2>
<ul>
<li>Estimasi ongkir dan waktu kirim bersifat perkiraan berdasarkan zona dan layanan kurir.</li>
<li>Risiko keterlambatan akibat cuaca, force majeure, atau pihak kurir di luar kendali kami.</li>
<li>Pastikan alamat, nama penerima, dan nomor telepon sudah benar sebelum checkout.</li>
</ul>
<h2>5. Retur &amp; Komplain</h2>
<p>Ketentuan retur mengikuti halaman Kebijakan Retur. Komplain wajib dilengkapi nomor pesanan dan bukti pendukung.</p>
<h2>6. Batasan Tanggung Jawab</h2>
<p>CarinaSmartCup tidak bertanggung jawab atas kerugian tidak langsung yang timbul dari kesalahan penggunaan produk, kesalahan data pengiriman yang diisi pelanggan, atau gangguan di luar kendali wajar kami.</p>
<h2>7. Hukum yang Berlaku</h2>
<p>Syarat ini tunduk pada hukum Republik Indonesia.</p>
HTML,
            ],
            [
                'title' => 'Kebijakan Retur',
                'slug' => 'kebijakan-retur',
                'body' => <<<'HTML'
<p>Kebijakan Retur CarinaSmartCup mengatur pengajuan komplain untuk barang rusak, salah kirim, atau tidak sesuai pesanan.</p>
<h2>1. Periode Pengajuan</h2>
<p>Retur/komplain dapat diajukan maksimal <strong>7 hari kalender</strong> sejak barang diterima. Melewati batas waktu, pengajuan dapat ditolak.</p>
<h2>2. Kondisi yang Dapat Diproses</h2>
<ul>
<li>Barang rusak saat diterima (kemasan sobek parah, cacat produksi signifikan).</li>
<li>Salah kirim item/ukuran/varian dibanding invoice.</li>
<li>Jumlah barang kurang dari yang tertera di invoice (dengan bukti unboxing).</li>
</ul>
<h2>3. Kondisi yang Tidak Dapat Diproses</h2>
<ul>
<li>Perubahan pikiran setelah barang diterima dalam kondisi baik.</li>
<li>Kerusakan akibat penyimpanan/penggunaan yang tidak sesuai.</li>
<li>Selisih warna/cetak minor yang wajar pada produk massal.</li>
<li>Bukti tidak lengkap atau diajukan melewati batas waktu.</li>
</ul>
<h2>4. Cara Mengajukan</h2>
<ol>
<li>Siapkan nomor pesanan.</li>
<li>Siapkan foto/video unboxing dan kerusakan/ketidaksesuaian.</li>
<li>Hubungi WhatsApp Support atau ajukan melalui kanal retur di akun/pesanan Anda.</li>
</ol>
<h2>5. Penyelesaian</h2>
<p>Setelah diverifikasi, penyelesaian dapat berupa penggantian barang, pengiriman kekurangan, atau solusi lain yang disepakati. Biaya pengiriman retur mengikuti hasil investigasi kasus.</p>
HTML,
            ],
            [
                'title' => 'Cara Belanja',
                'slug' => 'cara-belanja',
                'body' => <<<'HTML'
<p>Ikuti langkah berikut untuk berbelanja di CarinaSmartCup:</p>
<ol>
<li><strong>Pilih produk</strong> di katalog, lalu buka detail untuk melihat varian, harga, dan minimum order.</li>
<li><strong>Masukkan ke keranjang</strong> dengan quantity sesuai kelipatan yang diizinkan.</li>
<li><strong>Cek keranjang</strong> untuk memastikan item, diskon volume, dan total sementara sudah benar.</li>
<li><strong>Checkout</strong> dengan mengisi data penerima, alamat, serta metode pengiriman/pickup.</li>
<li><strong>Bayar via transfer bank</strong> sesuai nominal invoice, lalu unggah bukti pembayaran.</li>
<li><strong>Tunggu konfirmasi</strong> dari admin. Setelah pembayaran diverifikasi, pesanan diproses dan dikirim.</li>
<li><strong>Lacak pesanan</strong> melalui menu Lacak Pesanan menggunakan nomor order Anda.</li>
</ol>
<p>Butuh bantuan memilih kemasan? Chat WhatsApp Support kami.</p>
HTML,
            ],
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'body' => <<<'HTML'
<p><strong>CarinaSmartCup</strong> adalah toko online kemasan cup dan perlengkapan F&amp;B untuk UMKM, kafe, resto, dan bisnis minuman.</p>
<p>Kami fokus menyediakan pilihan produk yang praktis dipesan ulang, dengan informasi harga, stok, dan pengiriman yang jelas agar proses belanja lebih cepat dan aman.</p>
<h2>Yang Kami Tawarkan</h2>
<ul>
<li>Katalog kemasan cup, tutup, dan aksesoris pendukung.</li>
<li>Pemesanan berbasis pack/MOQ yang transparan.</li>
<li>Diskon volume pada kategori tertentu.</li>
<li>Pembayaran transfer manual dengan verifikasi bukti.</li>
<li>Pengiriman ke berbagai zona di Indonesia serta opsi pickup.</li>
</ul>
<p>Untuk kerja sama atau pertanyaan stok khusus, hubungi WhatsApp Support CarinaSmartCup.</p>
HTML,
            ],
        ];
    }
}
