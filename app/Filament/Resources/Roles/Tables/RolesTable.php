<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge(),
                TextColumn::make('users_count')
                    ->label('Pengguna')
                    ->counts('users'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records): void {
                            $protected = ['owner', 'admin', 'warehouse', 'finance', 'cs', 'customer'];
                            foreach ($records as $record) {
                                if (in_array($record->name, $protected, true)) {
                                    $action->cancel();
                                    \Filament\Notifications\Notification::make()
                                        ->title('Role sistem tidak boleh dihapus')
                                        ->body('Role '.$record->name.' dilindungi.')
                                        ->danger()
                                        ->send();

                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }
}
