<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('action')
                    ->label('Aksi'),
                TextEntry::make('user.name')
                    ->label('Pengguna'),
                TextEntry::make('auditable_type')
                    ->label('Model'),
                TextEntry::make('auditable_id')
                    ->label('ID Record'),
                TextEntry::make('old_values')
                    ->label('Nilai Lama')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->columnSpanFull(),
                TextEntry::make('new_values')
                    ->label('Nilai Baru')
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('IP'),
                TextEntry::make('created_at')
                    ->label('Waktu')
                    ->dateTime(),
            ]);
    }
}
