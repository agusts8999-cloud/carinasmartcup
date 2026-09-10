<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\InventoryStock;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class DashboardMetrics
{
    /**
     * @return array{
     *     orders_today: int,
     *     pending_payments: int,
     *     revenue_month: float,
     *     low_stock: int
     * }
     */
    public function kpi(): array
    {
        return [
            'orders_today' => Order::query()->whereDate('created_at', today())->count(),
            'pending_payments' => Payment::query()
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
                ->count(),
            'revenue_month' => (float) Order::query()
                ->where('status', '!=', OrderStatus::Cancelled)
                ->whereNotNull('paid_at')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'low_stock' => InventoryStock::query()
                ->whereRaw('(qty_on_hand - qty_reserved) < 10')
                ->count(),
        ];
    }

    /**
     * Last 7 calendar days inclusive of today.
     *
     * @return array{
     *     labels: list<string>,
     *     dates: list<string>,
     *     orders: list<int>,
     *     revenue_millions: list<float>,
     *     pending_payments: list<int>
     * }
     */
    public function lastSevenDays(): array
    {
        $start = today()->subDays(6)->startOfDay();
        $end = today()->endOfDay();

        $ordersByDay = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at'])
            ->groupBy(fn (Order $order): string => $order->created_at->toDateString())
            ->map->count();

        $revenueByDay = Order::query()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$start, $end])
            ->get(['paid_at', 'total'])
            ->groupBy(fn (Order $order): string => $order->paid_at->toDateString())
            ->map(fn (Collection $rows): float => (float) $rows->sum('total'));

        $pendingByDay = Payment::query()
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
            ->whereBetween('created_at', [$start, $end])
            ->get(['created_at'])
            ->groupBy(fn (Payment $payment): string => $payment->created_at->toDateString())
            ->map->count();

        $labels = [];
        $dates = [];
        $orders = [];
        $revenueMillions = [];
        $pending = [];

        foreach (CarbonPeriod::create($start->toDateString(), $end->toDateString()) as $date) {
            /** @var Carbon $date */
            $key = $date->toDateString();
            $labels[] = $date->translatedFormat('d M');
            $dates[] = $key;
            $orders[] = (int) ($ordersByDay[$key] ?? 0);
            $revenueMillions[] = round(((float) ($revenueByDay[$key] ?? 0)) / 1_000_000, 2);
            $pending[] = (int) ($pendingByDay[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'dates' => $dates,
            'orders' => $orders,
            'revenue_millions' => $revenueMillions,
            'pending_payments' => $pending,
        ];
    }

    /**
     * @return Collection<int, Order>
     */
    public function recentOrders(int $limit = 8): Collection
    {
        return Order::query()
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'number', 'customer_name', 'status', 'total', 'created_at']);
    }

    /**
     * @return Collection<int, Payment>
     */
    public function pendingPaymentRows(int $limit = 5): Collection
    {
        return Payment::query()
            ->with('order:id,number')
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::AwaitingConfirmation])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, InventoryStock>
     */
    public function lowStockRows(int $limit = 5): Collection
    {
        return InventoryStock::query()
            ->with(['productVariant.product:id,name'])
            ->whereRaw('(qty_on_hand - qty_reserved) < 10')
            ->orderByRaw('(qty_on_hand - qty_reserved) asc')
            ->limit($limit)
            ->get();
    }
}
