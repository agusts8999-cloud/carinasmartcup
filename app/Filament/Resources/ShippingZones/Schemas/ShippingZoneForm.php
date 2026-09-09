<?php

namespace App\Filament\Resources\ShippingZones\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Zona')
                    ->required()
                    ->maxLength(255),
                TagsInput::make('provinces')
                    ->label('Provinsi')
                    ->columnSpanFull(),
                TagsInput::make('cities')
                    ->label('Kota')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Repeater::make('rates')
                    ->label('Tarif Pengiriman')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('code')
                            ->label('Kode')
                            ->required(),
                        TextInput::make('base_price')
                            ->label('Harga Dasar')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('price_per_kg')
                            ->label('Harga/kg')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('price_per_koli')
                            ->label('Harga/Koli')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('eta_days_min')
                            ->label('ETA Min (hari)')
                            ->numeric(),
                        TextInput::make('eta_days_max')
                            ->label('ETA Max (hari)')
                            ->numeric(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
