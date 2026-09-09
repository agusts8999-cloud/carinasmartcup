<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(50),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->label('Tipe')
                    ->required()
                    ->placeholder('percent / fixed'),
                TextInput::make('value')
                    ->label('Nilai')
                    ->numeric()
                    ->required(),
                TextInput::make('min_subtotal')
                    ->label('Min. Subtotal')
                    ->numeric(),
                TextInput::make('max_uses')
                    ->label('Maks. Penggunaan')
                    ->numeric(),
                DateTimePicker::make('starts_at')
                    ->label('Mulai'),
                DateTimePicker::make('ends_at')
                    ->label('Berakhir'),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
