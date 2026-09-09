<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderNumberGenerator
{
    public function generate(): string
    {
        return DB::transaction(function (): string {
            $date = now()->format('Ymd');
            $prefix = "CSC-{$date}-";

            $lastNumber = Order::query()
                ->where('number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('number')
                ->value('number');

            $sequence = 1;

            if ($lastNumber !== null) {
                $sequence = (int) substr($lastNumber, -4) + 1;
            }

            return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        });
    }
}
