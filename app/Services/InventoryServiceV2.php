<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shipment;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryServiceV2
{
    protected function shadow(): bool
    {
        return (bool) config('inventory.v2.shadow_mode', true);
    }

    protected function move(Product $product, Shipment $shipment, int $delta, string $reason, array $meta = []): void
    {
        // سجل الحركة دائماً
        InventoryMovement::create([
            'product_id'  => $product->id,
            'shipment_id' => $shipment->id,
            'qty_change'  => $delta,
            'reason'      => $reason,
            'meta'        => $meta,
        ]);

        // لو Shadow: ما نلمسش الرصيد
        if ($this->shadow()) return;

        // قفل الصف وتعديل الرصيد بأمان
        DB::transaction(function() use ($product, $delta) {
            $p = Product::whereKey($product->id)->lockForUpdate()->first();
            if ($delta < 0 && $p->stock < abs($delta)) {
                abort(422, 'المخزون غير كافي');
            }
            if ($delta < 0) $p->decrement('stock', abs($delta));
            else            $p->increment('stock', $delta);
        });
    }

    public function reserveForShipment(Shipment $shipment, string $reason = 'reserve_on_company_exit'): void
    {
        foreach ($shipment->products as $product) {
            $qty = (int) ($product->pivot->qty ?? 0);
            if ($qty > 0) $this->move($product, $shipment, -$qty, $reason);
        }
        $shipment->forceFill(['inventory_reserved_at'=>now()])->saveQuietly();
    }

    public function releaseForShipment(Shipment $shipment, string $reason = 'release_on_company_back'): void
    {
        foreach ($shipment->products as $product) {
            $qty = (int) ($product->pivot->qty ?? 0);
            if ($qty > 0) $this->move($product, $shipment, +$qty, $reason);
        }
        $shipment->forceFill(['inventory_released_at'=>now()])->saveQuietly();
    }

    public function returnToStockOnReturn(Shipment $shipment, string $reason = 'return_on_status'): void
    {
        foreach ($shipment->products as $product) {
            $qty = (int) ($product->pivot->qty ?? 0);
            if ($qty > 0) $this->move($product, $shipment, +$qty, $reason);
        }
        $shipment->forceFill(['inventory_returned_at'=>now()])->saveQuietly();
    }
}
