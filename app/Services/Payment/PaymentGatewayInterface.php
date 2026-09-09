<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order): Payment;

    public function confirm(Payment $payment, ?User $actor = null): void;

    public function reject(Payment $payment, string $reason, ?User $actor = null): void;
}
