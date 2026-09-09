<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Actions\ConfirmManualPayment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->state(fn (Order $record): ?string => $record->payments()->latest()->first()?->status?->value),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s) => [$s->value => $s->label()])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('confirmPayment')
                    ->label('Konfirmasi Pembayaran')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->payments()
                        ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
                        ->exists())
                    ->action(function (Order $record): void {
                        $payment = $record->payments()->latest()->first();

                        if (! $payment) {
                            Notification::make()->title('Pembayaran tidak ditemukan')->danger()->send();

                            return;
                        }

                        app(ConfirmManualPayment::class)->handle($payment, auth()->user());

                        Notification::make()->title('Pembayaran dikonfirmasi')->success()->send();
                    }),
                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('status')
                            ->label('Status Baru')
                            ->options(collect(OrderStatus::cases())->mapWithKeys(fn (OrderStatus $s) => [$s->value => $s->label()]))
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update(['status' => $data['status']]);

                        Notification::make()->title('Status pesanan diperbarui')->success()->send();
                    }),
                Action::make('addTracking')
                    ->label('Tambah Resi')
                    ->icon('heroicon-o-truck')
                    ->form([
                        TextInput::make('courier_name')
                            ->label('Kurir')
                            ->required(),
                        TextInput::make('tracking_number')
                            ->label('No. Resi')
                            ->required(),
                        TextInput::make('tracking_url')
                            ->label('URL Lacak')
                            ->url(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        Shipment::create([
                            'order_id' => $record->id,
                            'courier_name' => $data['courier_name'],
                            'tracking_number' => $data['tracking_number'],
                            'tracking_url' => $data['tracking_url'] ?? null,
                            'status' => ShipmentStatus::Shipped,
                            'shipped_at' => now(),
                        ]);

                        if ($record->status === OrderStatus::Paid || $record->status === OrderStatus::Processing || $record->status === OrderStatus::Packed) {
                            $record->update(['status' => OrderStatus::Shipped]);
                        }

                        Notification::make()->title('Resi berhasil ditambahkan')->success()->send();
                    }),
            ]);
    }
}
