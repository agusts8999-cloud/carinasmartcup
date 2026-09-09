<?php

namespace App\Console\Commands;

use App\Services\CatalogImportService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class ImportCatalogCommand extends Command
{
    protected $signature = 'carina:import-catalog
        {path=import/triguna_katalog_produk.xlsx : Path ke file CSV/XLSX (Carina atau Triguna)}
        {--skip-images : Lewati unduhan/pemasangan gambar}';

    protected $description = 'Impor katalog produk/varian + gambar (Carina template atau Triguna/Trifinity XLSX)';

    public function handle(CatalogImportService $importer): int
    {
        $path = $this->argument('path');

        if (! str_starts_with($path, DIRECTORY_SEPARATOR) && ! preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
            $path = base_path($path);
        }

        $skipImages = (bool) $this->option('skip-images');

        $this->info("Mengimpor: {$path}".($skipImages ? ' (tanpa gambar)' : ''));

        try {
            $stats = $importer->importFromPath($path, $skipImages);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        }

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Produk dibuat', $stats['created_products']],
                ['Produk diperbarui', $stats['updated_products']],
                ['Varian dibuat', $stats['created_variants']],
                ['Varian diperbarui', $stats['updated_variants']],
                ['Gambar terpasang', $stats['images_attached']],
                ['Gambar hilang', $stats['images_missing']],
                ['Baris gagal', count($stats['errors'])],
            ],
        );

        foreach ($stats['errors'] as $error) {
            $this->warn($error);
        }

        return count($stats['errors']) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
