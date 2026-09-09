<?php

namespace App\Filament\Resources\ReturnRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReturnRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->label('Pesanan')
                    ->relationship('order', 'number')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Pelanggan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('status')
                    ->label('Status')
                    ->required()
                    ->maxLength(50),
                Textarea::make('reason')
                    ->label('Alasan')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->columnSpanFull(),
                TextInput::make('resolution')
                    ->label('Resolusi')
                    ->maxLength(255),
            ]);
    }
}
