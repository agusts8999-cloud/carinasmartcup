<?php

namespace App\Services\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class ManualTransferGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    public function createPayment(Order $order): Payment
    {
        $bank = config('carina.bank');

        return Payment::query()->create([
            'order_id' => $order->id,
            'method' => PaymentMethod::BankTransfer,
            'status' => PaymentStatus::Pending,
            'amount' => $order->total,
            'bank_name' => $bank['name'] ?? null,
            'account_number' => $bank['account_number'] ?? null,
            'account_name' => $bank['account_name'] ?? null,
            'reference' => $order->number,
        ]);
    }

    public function confirm(Payment $payment, ?User $actor = null): void
    {
        DB::transaction(function () use ($payment, $actor): void {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::query()->whereKey($payment->order_id)->lockForUpdate()->firstOrFail();

            if ($payment->status === PaymentStatus::Paid) {
                return;
            }

            $now = now();

            $payment->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => $now,
                'confirmed_by' => $actor?->id,
                'confirmed_at' => $now,
            ]);

            $order->loadMissing('items.productVariant');

            foreach ($order->items as $item) {
                if ($item->productVariant !== null) {
                    $this->inventoryService->commit($item->productVariant, $item->quantity);
                }
            }

            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => $now,
            ]);
        });
    }

    public function reject(Payment $payment, string $reason, ?User $actor = null): void
    {
        DB::transaction(function () use ($payment, $reason, $actor): void {
            $payment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = Order::query()->whereKey($payment->order_id)->lockForUpdate()->firstOrFail();

            if (in_array($payment->status, [PaymentStatus::Paid, PaymentStatus::Rejected, PaymentStatus::Expired], true)) {
                return;
            }

            $order->loadMissing('items.productVariant');

            foreach ($order->items as $item) {
                if ($item->productVariant !== null) {
                    $this->inventoryService->release($item->productVariant, $item->quantity);
                }
            }

            $payment->update([
                'status' => PaymentStatus::Rejected,
                'rejected_reason' => $reason,
                'confirmed_by' => $actor?->id,
                'confirmed_at' => now(),
            ]);

            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]);
        });
    }
}
