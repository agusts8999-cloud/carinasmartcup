<?php

namespace App\Filament\Resources\VolumeRules\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VolumeRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Toggle::make('applies_to_all')
                    ->label('Berlaku Semua Produk'),
                Toggle::make('allows_mix')
                    ->label('Izinkan Mix Produk'),
                TextInput::make('basis')
                    ->label('Basis')
                    ->maxLength(50),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Repeater::make('tiers')
                    ->label('Tier Diskon')
                    ->relationship()
                    ->schema([
                        TextInput::make('min_value')
                            ->label('Min. Nilai')
                            ->numeric()
                            ->required(),
                        TextInput::make('discount_type')
                            ->label('Tipe Diskon')
                            ->required()
                            ->placeholder('percent / fixed'),
                        TextInput::make('discount_value')
                            ->label('Nilai Diskon')
                            ->numeric()
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
