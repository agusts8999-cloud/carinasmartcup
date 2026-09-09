<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredReservations
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function handle(): int
    {
        $count = 0;

        Order::query()
            ->where('status', OrderStatus::PendingPayment)
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<', now())
            ->with(['items.productVariant', 'payments'])
            ->orderBy('id')
            ->chunkById(50, function ($orders) use (&$count): void {
                foreach ($orders as $order) {
                    DB::transaction(function () use ($order, &$count): void {
                        $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();

                        if ($locked === null
                            || $locked->status !== OrderStatus::PendingPayment
                            || $locked->reserved_until === null
                            || $locked->reserved_until->isFuture()
                        ) {
                            return;
                        }

                        $locked->loadMissing(['items.productVariant', 'payments']);

                        foreach ($locked->items as $item) {
                            if ($item->productVariant !== null) {
                                $this->inventoryService->release($item->productVariant, $item->quantity);
                            }
                        }

                        foreach ($locked->payments as $payment) {
                            if (in_array($payment->status, [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation], true)) {
                                $payment->update(['status' => PaymentStatus::Expired]);
                            }
                        }

                        $locked->update([
                            'status' => OrderStatus::Cancelled,
                            'cancelled_at' => now(),
                        ]);

                        $count++;
                    });
                }
            });

        return $count;
    }
}
