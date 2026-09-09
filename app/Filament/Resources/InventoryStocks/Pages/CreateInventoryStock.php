<?php

namespace App\Filament\Resources\InventoryStocks\Pages;

use App\Filament\Resources\InventoryStocks\InventoryStockResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryStock extends CreateRecord
{
    protected static string $resource = InventoryStockResource::class;
}
