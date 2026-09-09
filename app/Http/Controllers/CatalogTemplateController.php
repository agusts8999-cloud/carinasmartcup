<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CatalogTemplateController extends Controller
{
    public function template(): BinaryFileResponse|Response
    {
        return $this->download('katalog-produk-template.csv', 'carinasmartcup-katalog-template.csv', 'text/csv; charset=UTF-8');
    }

    public function templateXlsx(): BinaryFileResponse|Response
    {
        return $this->download(
            'katalog-produk-template.xlsx',
            'carinasmartcup-katalog-template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
    }

    public function sample(): BinaryFileResponse|Response
    {
        return $this->download('katalog-produk-contoh.csv', 'carinasmartcup-katalog-contoh.csv', 'text/csv; charset=UTF-8');
    }

    private function download(string $filename, string $as, string $contentType): BinaryFileResponse|Response
    {
        $path = storage_path('app/templates/'.$filename);

        if (! File::exists($path)) {
            abort(404, 'Template tidak ditemukan.');
        }

        return response()->download($path, $as, [
            'Content-Type' => $contentType,
        ]);
    }
}
