<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Pelanggan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('order_id')
                    ->label('Pesanan')
                    ->relationship('order', 'number')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('subject')
                    ->label('Subjek')
                    ->required()
                    ->maxLength(255),
                Textarea::make('message')
                    ->label('Pesan')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->label('Status')
                    ->required()
                    ->maxLength(50),
                Textarea::make('admin_reply')
                    ->label('Balasan Admin')
                    ->columnSpanFull(),
            ]);
    }
}
