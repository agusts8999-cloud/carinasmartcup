<?php

namespace App\Filament\Resources\InventoryStocks;

use App\Filament\Resources\InventoryStocks\Pages\CreateInventoryStock;
use App\Filament\Resources\InventoryStocks\Pages\EditInventoryStock;
use App\Filament\Resources\InventoryStocks\Pages\ListInventoryStocks;
use App\Filament\Resources\InventoryStocks\Schemas\InventoryStockForm;
use App\Filament\Resources\InventoryStocks\Tables\InventoryStocksTable;
use App\Models\InventoryStock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryStockResource extends Resource
{
    protected static ?string $model = InventoryStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventori';

    protected static ?string $modelLabel = 'Stok';

    protected static ?string $pluralModelLabel = 'Stok Inventori';

    public static function form(Schema $schema): Schema
    {
        return InventoryStockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryStocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryStocks::route('/'),
            'create' => CreateInventoryStock::route('/create'),
            'edit' => EditInventoryStock::route('/{record}/edit'),
        ];
    }
}
