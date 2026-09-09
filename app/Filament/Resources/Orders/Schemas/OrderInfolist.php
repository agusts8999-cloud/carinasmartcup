<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('number')
                    ->label('No. Pesanan'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('customer_name')
                    ->label('Pelanggan'),
                TextEntry::make('customer_email')
                    ->label('Email'),
                TextEntry::make('customer_whatsapp')
                    ->label('WhatsApp'),
                TextEntry::make('payment_method')
                    ->label('Metode Pembayaran'),
                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR'),
                TextEntry::make('shipping_amount')
                    ->label('Ongkir')
                    ->money('IDR'),
                TextEntry::make('total')
                    ->label('Total')
                    ->money('IDR'),
                TextEntry::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime(),
                TextEntry::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }
}
