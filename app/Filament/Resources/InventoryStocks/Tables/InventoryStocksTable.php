<?php

namespace App\Filament\Resources\InventoryStocks\Tables;

use App\Models\InventoryStock;
use App\Models\StockAdjustment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryStocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('productVariant.sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('productVariant.product.name')
                    ->label('Produk')
                    ->sortable(),
                TextColumn::make('qty_on_hand')
                    ->label('Stok')
                    ->sortable(),
                TextColumn::make('qty_reserved')
                    ->label('Reservasi')
                    ->sortable(),
                TextColumn::make('available')
                    ->label('Tersedia')
                    ->state(fn (InventoryStock $record): int => $record->qty_on_hand - $record->qty_reserved)
                    ->color(fn (InventoryStock $record): string => ($record->qty_on_hand - $record->qty_reserved) < 10 ? 'danger' : 'success'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('adjust')
                    ->label('Sesuaikan')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        TextInput::make('quantity_delta')
                            ->label('Perubahan Jumlah')
                            ->integer()
                            ->required()
                            ->helperText('Positif = tambah, negatif = kurangi'),
                        TextInput::make('reason')
                            ->label('Alasan')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('notes')
                            ->label('Catatan'),
                    ])
                    ->action(function (InventoryStock $record, array $data): void {
                        $record->update([
                            'qty_on_hand' => $record->qty_on_hand + (int) $data['quantity_delta'],
                        ]);

                        StockAdjustment::create([
                            'inventory_stock_id' => $record->id,
                            'user_id' => auth()->id(),
                            'quantity_delta' => (int) $data['quantity_delta'],
                            'reason' => $data['reason'],
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Stok berhasil disesuaikan')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
