<?php

namespace App\Filament\Resources\InventoryLocations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InventoryLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(50),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
                Toggle::make('is_default')
                    ->label('Lokasi Default'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Toggle::make('pickup_available')
                    ->label('Pickup Tersedia'),
            ]);
    }
}
