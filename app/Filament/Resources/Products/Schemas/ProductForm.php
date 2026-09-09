<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255),
                TextInput::make('sku_prefix')
                    ->label('Prefix SKU')
                    ->maxLength(50),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(ProductStatus::cases())->mapWithKeys(fn (ProductStatus $s) => [$s->value => ucfirst($s->value)]))
                    ->default(ProductStatus::Draft->value)
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Unggulan'),
                DateTimePicker::make('published_at')
                    ->label('Dipublikasikan'),
                FileUpload::make('images')
                    ->label('Gambar')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull()
                    ->dehydrated(false),
                Repeater::make('variants')
                    ->label('Varian')
                    ->relationship()
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('price_retail')
                            ->label('Harga Retail')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('weight_gram')
                            ->label('Berat (gram)')
                            ->numeric(),
                        TextInput::make('pack_size')
                            ->label('Isi Pak')
                            ->numeric()
                            ->default(1),
                        TextInput::make('min_order_qty')
                            ->label('Min. Order')
                            ->numeric()
                            ->default(1),
                        TextInput::make('order_multiple')
                            ->label('Kelipatan Order')
                            ->numeric()
                            ->default(1),
                        TextInput::make('color')
                            ->label('Warna'),
                        TextInput::make('lid_type')
                            ->label('Tipe Tutup'),
                        TextInput::make('unit_type')
                            ->label('Tipe Unit'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        TextInput::make('length_cm')
                            ->label('Panjang (cm)')
                            ->numeric(),
                        TextInput::make('width_cm')
                            ->label('Lebar (cm)')
                            ->numeric(),
                        TextInput::make('height_cm')
                            ->label('Tinggi (cm)')
                            ->numeric(),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
