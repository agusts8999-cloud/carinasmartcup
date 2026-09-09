<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->label('No. Pesanan')
                    ->disabled(),
                Select::make('status')
                    ->label('Status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s) => [$s->value => $s->label()]))
                    ->required(),
                TextInput::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->disabled(),
                TextInput::make('customer_email')
                    ->label('Email')
                    ->disabled(),
                TextInput::make('customer_whatsapp')
                    ->label('WhatsApp')
                    ->disabled(),
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->prefix('Rp')
                    ->disabled(),
                TextInput::make('shipping_amount')
                    ->label('Ongkir')
                    ->prefix('Rp')
                    ->disabled(),
                TextInput::make('total')
                    ->label('Total')
                    ->prefix('Rp')
                    ->disabled(),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
