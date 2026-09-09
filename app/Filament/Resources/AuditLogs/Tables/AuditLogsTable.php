<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->label('Aksi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                TextColumn::make('auditable_type')
                    ->label('Model')
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('ID'),
                TextColumn::make('ip_address')
                    ->label('IP'),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Aksi'),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
