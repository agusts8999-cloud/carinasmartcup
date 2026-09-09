<?php

namespace App\Services;

use App\Models\InventoryLocation;
use App\Models\InventoryStock;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function reserve(ProductVariant $variant, int $qty, ?InventoryLocation $location = null): void
    {
        if ($qty <= 0) {
            return;
        }

        DB::transaction(function () use ($variant, $qty, $location): void {
            $stock = $this->lockStock($variant, $location);
            $available = $stock->qty_on_hand - $stock->qty_reserved;

            if ($available < $qty) {
                throw new RuntimeException(
                    "Stok tidak mencukupi untuk {$variant->sku}. Tersedia: {$available}, diminta: {$qty}."
                );
            }

            $stock->qty_reserved += $qty;
            $stock->save();
        });
    }

    public function release(ProductVariant $variant, int $qty, ?InventoryLocation $location = null): void
    {
        if ($qty <= 0) {
            return;
        }

        DB::transaction(function () use ($variant, $qty, $location): void {
            $stock = $this->lockStock($variant, $location);

            if ($stock->qty_reserved < $qty) {
                throw new RuntimeException(
                    "Tidak dapat melepas reservasi melebihi jumlah yang direservasi untuk {$variant->sku}."
                );
            }

            $stock->qty_reserved -= $qty;
            $stock->save();
        });
    }

    public function commit(ProductVariant $variant, int $qty, ?InventoryLocation $location = null): void
    {
        if ($qty <= 0) {
            return;
        }

        DB::transaction(function () use ($variant, $qty, $location): void {
            $stock = $this->lockStock($variant, $location);

            if ($stock->qty_on_hand < $qty || $stock->qty_reserved < $qty) {
                throw new RuntimeException(
                    "Tidak dapat mengcommit stok untuk {$variant->sku}. On hand: {$stock->qty_on_hand}, reserved: {$stock->qty_reserved}."
                );
            }

            $stock->qty_on_hand -= $qty;
            $stock->qty_reserved -= $qty;
            $stock->save();
        });
    }

    public function adjust(ProductVariant $variant, int $delta, string $reason, ?User $user = null): void
    {
        if ($delta === 0) {
            return;
        }

        DB::transaction(function () use ($variant, $delta, $reason, $user): void {
            $stock = $this->lockStock($variant, null);

            $newOnHand = $stock->qty_on_hand + $delta;

            if ($newOnHand < 0) {
                throw new RuntimeException(
                    "Penyesuaian stok akan membuat qty_on_hand negatif untuk {$variant->sku}."
                );
            }

            if ($newOnHand < $stock->qty_reserved) {
                throw new RuntimeException(
                    "Penyesuaian stok tidak boleh kurang dari qty_reserved untuk {$variant->sku}."
                );
            }

            $stock->qty_on_hand = $newOnHand;
            $stock->save();

            StockAdjustment::query()->create([
                'inventory_stock_id' => $stock->id,
                'user_id' => $user?->id,
                'quantity_delta' => $delta,
                'reason' => $reason,
            ]);
        });
    }

    public function defaultLocation(): InventoryLocation
    {
        $location = InventoryLocation::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if ($location === null) {
            throw new RuntimeException('Lokasi inventori default tidak ditemukan.');
        }

        return $location;
    }

    private function lockStock(ProductVariant $variant, ?InventoryLocation $location): InventoryStock
    {
        $location ??= $this->defaultLocation();

        $stock = InventoryStock::query()
            ->where('inventory_location_id', $location->id)
            ->where('product_variant_id', $variant->id)
            ->lockForUpdate()
            ->first();

        if ($stock === null) {
            $stock = InventoryStock::query()->create([
                'inventory_location_id' => $location->id,
                'product_variant_id' => $variant->id,
                'qty_on_hand' => 0,
                'qty_reserved' => 0,
            ]);

            $stock = InventoryStock::query()
                ->whereKey($stock->id)
                ->lockForUpdate()
                ->firstOrFail();
        }

        return $stock;
    }
}
