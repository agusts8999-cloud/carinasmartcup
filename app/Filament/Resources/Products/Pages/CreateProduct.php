<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $this->syncProductImages();
    }

    protected function syncProductImages(): void
    {
        $images = $this->data['images'] ?? [];

        foreach ((array) $images as $path) {
            if (Storage::disk('public')->exists($path)) {
                $this->record->addMedia(Storage::disk('public')->path($path))
                    ->toMediaCollection('images');
            }
        }
    }
}
