<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\CatalogImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Unduh Template CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('catalog.template'))
                ->openUrlInNewTab(),
            Action::make('downloadTemplateXlsx')
                ->label('Unduh Template XLSX')
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->url(route('catalog.template.xlsx'))
                ->openUrlInNewTab(),
            Action::make('downloadSample')
                ->label('Unduh Contoh CSV')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(route('catalog.sample'))
                ->openUrlInNewTab(),
            Action::make('importCsv')
                ->label('Impor CSV / XLSX')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Impor katalog')
                ->modalDescription('Menerima template Carina atau file Triguna/Trifinity (sheet Produk). Gambar dari URL akan diunduh otomatis; untuk template Carina letakkan file di import/images/ dan isi image_path.')
                ->form([
                    FileUpload::make('file')
                        ->label('File CSV atau XLSX')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->disk('local')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data, CatalogImportService $importer): void {
                    $relative = $data['file'];
                    $path = Storage::disk('local')->path($relative);

                    try {
                        $stats = $importer->importFromPath($path);
                    } catch (ValidationException $e) {
                        Notification::make()
                            ->title('Impor ditolak')
                            ->body(collect($e->errors())->flatten()->implode(' '))
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    $body = sprintf(
                        'Produk baru: %d, diperbarui: %d. Varian baru: %d, diperbarui: %d. Gambar: %d terpasang, %d hilang.',
                        $stats['created_products'],
                        $stats['updated_products'],
                        $stats['created_variants'],
                        $stats['updated_variants'],
                        $stats['images_attached'],
                        $stats['images_missing'],
                    );

                    if (count($stats['errors']) > 0) {
                        $body .= ' Gagal: '.count($stats['errors']).' baris. '.implode(' | ', array_slice($stats['errors'], 0, 5));

                        Notification::make()
                            ->title('Impor selesai dengan peringatan')
                            ->body($body)
                            ->warning()
                            ->persistent()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Impor katalog berhasil')
                        ->body($body)
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
