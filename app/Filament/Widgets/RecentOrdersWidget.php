<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Pesanan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest('id')->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('number')
                    ->label('No.')
                    ->weight('medium'),
                TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->limit(18),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id'),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Buka')
                    ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
