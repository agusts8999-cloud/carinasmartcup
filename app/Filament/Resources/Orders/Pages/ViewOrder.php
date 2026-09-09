<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Actions\ConfirmManualPayment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('confirmPayment')
                ->label('Konfirmasi Pembayaran')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => $record->payments()
                    ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
                    ->exists())
                ->action(fn (Order $record) => app(ConfirmManualPayment::class)->handle(
                    $record->payments()->latest()->first(),
                    auth()->user(),
                )),
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
                    TextInput::make('courier_name')->label('Kurir')->required(),
                    TextInput::make('tracking_number')->label('No. Resi')->required(),
                    TextInput::make('tracking_url')->label('URL Lacak')->url(),
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

                    if (in_array($record->status, [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Packed], true)) {
                        $record->update(['status' => OrderStatus::Shipped]);
                    }

                    Notification::make()->title('Resi berhasil ditambahkan')->success()->send();
                }),
        ];
    }
}
