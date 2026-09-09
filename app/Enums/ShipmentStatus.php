<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'pending';
    case Packed = 'packed';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Failed = 'failed';
}
