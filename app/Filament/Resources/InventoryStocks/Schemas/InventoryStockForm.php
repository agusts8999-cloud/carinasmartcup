<?php

namespace App\Filament\Resources\InventoryStocks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryStockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('inventory_location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('product_variant_id')
                    ->label('Varian Produk')
                    ->relationship('productVariant', 'sku')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('qty_on_hand')
                    ->label('Stok Tersedia')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('qty_reserved')
                    ->label('Stok Direservasi')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
