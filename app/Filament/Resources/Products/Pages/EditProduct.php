<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->record
            ->getMedia('images')
            ->map(fn ($media) => $media->id.'/'.$media->file_name)
            ->all();

        return $data;
    }

    protected function afterSave(): void
    {
        $images = $this->data['images'] ?? [];

        if (empty($images)) {
            return;
        }

        $existing = $this->record
            ->getMedia('images')
            ->map(fn ($media) => $media->id.'/'.$media->file_name)
            ->all();

        foreach ((array) $images as $path) {
            if (in_array($path, $existing, true)) {
                continue;
            }

            if (Storage::disk('public')->exists($path)) {
                $this->record->addMedia(Storage::disk('public')->path($path))
                    ->toMediaCollection('images');
            }
        }
    }
}
