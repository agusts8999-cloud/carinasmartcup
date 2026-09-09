<?php

namespace App\Actions;

use App\Mail\PaymentConfirmedMail;
use App\Models\Payment;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Payment\ManualTransferGateway;
use App\Support\Analytics;
use App\Support\Audit;
use Illuminate\Support\Facades\Mail;

class ConfirmManualPayment
{
    public function __construct(
        private readonly ManualTransferGateway $paymentGateway,
        private readonly NotificationService $notificationService,
    ) {}

    public function handle(Payment $payment, User $actor): void
    {
        $oldStatus = $payment->status;

        $this->paymentGateway->confirm($payment, $actor);

        $payment->refresh();
        $order = $payment->order()->first();

        Audit::log(
            action: 'payment.confirmed',
            auditable: $payment,
            user: $actor,
            oldValues: ['status' => $oldStatus?->value ?? $oldStatus],
            newValues: ['status' => $payment->status?->value ?? $payment->status],
        );

        if ($order) {
            Analytics::track('payment_confirmed', [
                'order_id' => $order->id,
                'order_number' => $order->number,
            ]);

            $this->notificationService->send($order, 'email', 'payment_confirmed', [
                'order_number' => $order->number,
            ]);

            if (! empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new PaymentConfirmedMail($order));
            }
        }
    }
}
