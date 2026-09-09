<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case AwaitingConfirmation = 'awaiting_confirmation';
    case Paid = 'paid';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Refunded = 'refunded';
}
