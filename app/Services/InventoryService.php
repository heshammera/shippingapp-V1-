<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class InventoryService
{
    /**
     * Reserve stock for a shipment
     * Called when shipment moves to external shipping company
     */
    public function reserveForShipment(Shipment $shipment, string $reason = 'reserve_for_shipment'): void
    {
        DB::transaction(function() use ($shipment, $reason) {
            foreach ($shipment->products as $product) {
                $this->reserveItem(
                    $shipment,
                    $product->id,
                    $product->pivot->color,
                    $product->pivot->size,
                    $product->pivot->quantity,
                    $reason
                );
            }
        });
    }

    /**
     * Reserve stock for a single item (Pivot)
     * Used especially when items are attached one by one
     */
    public function reserveItem(Shipment $shipment, int $productId, ?string $color, ?string $size, int $quantity, string $reason): void
    {
        // Find matching variant
        $variant = ProductVariant::where('product_id', $productId)
            ->where('color', $color)
            ->where('size', $size)
            ->lockForUpdate()
            ->first();

        // If no variant found, we can't reserve. 
        // We log warning but don't throw exception to avoid breaking the UI for legacy data
        if (!$variant) {
            \Log::warning("InventoryService: Variant NOT FOUND for product #{$productId} ({$color}/{$size})");
            return;
        }

        // Skip if unlimited
        if ($variant->is_unlimited) {
            return;
        }

        // Check if enough available stock
        if (!$variant->canFulfill($quantity)) {
            \Log::error("InventoryService: Insufficient stock for variant #{$variant->id}. Available: {$variant->available_quantity}, Needed: {$quantity}");
            // We verify availability but might choose to allow negative reservation 
            // depending on business logic. For now, we enforce strict check.
            throw new Exception(
                "Insufficient stock for {$variant->full_name}. Available: {$variant->available_quantity}, Needed: {$quantity}"
            );
        }

        // Reserve the quantity
        $quantityBefore = $variant->reserved_quantity;
        $variant->reserved_quantity += $quantity;
        $variant->save();

        // Log movement
        $this->logMovement($variant, $shipment, 'reserve', $quantity, 
            $quantityBefore, $variant->reserved_quantity, $reason);
    }

    /**
     * Release reserved stock
     * Called when shipment is cancelled or returned to default company
     */
    public function releaseForShipment(Shipment $shipment, string $reason = 'release_reserved'): void
    {
        DB::transaction(function() use ($shipment, $reason) {
            foreach ($shipment->products as $product) {
                $quantity = $product->pivot->quantity;
                $color = $product->pivot->color;
                $size = $product->pivot->size;

                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('color', $color)
                    ->where('size', $size)
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->is_unlimited) {
                    continue;
                }

                // Release the reservation
                $quantityBefore = $variant->reserved_quantity;
                $variant->reserved_quantity = max(0, $variant->reserved_quantity - $quantity);
                $variant->save();

                // Log movement
                $this->logMovement($variant, $shipment, 'release', -$quantity,
                    $quantityBefore, $variant->reserved_quantity, $reason);
            }
        });
    }

    /**
     * Deduct stock when shipment is delivered
     * This is the actual stock reduction
     */
    public function deductForShipment(Shipment $shipment, string $reason = 'delivered_to_customer'): void
    {
        DB::transaction(function() use ($shipment, $reason) {
            foreach ($shipment->products as $product) {
                $quantity = $product->pivot->quantity;
                $color = $product->pivot->color;
                $size = $product->pivot->size;

                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('color', $color)
                    ->where('size', $size)
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->is_unlimited) {
                    continue;
                }

                // Deduct from stock and reserved
                $stockBefore = $variant->stock_quantity;
                $reservedBefore = $variant->reserved_quantity;

                $variant->stock_quantity = max(0, $variant->stock_quantity - $quantity);
                $variant->reserved_quantity = max(0, $variant->reserved_quantity - $quantity);
                $variant->save();

                // Log movement (for stock_quantity change)
                $this->logMovement($variant, $shipment, 'deduct', -$quantity,
                    $stockBefore, $variant->stock_quantity, $reason);
            }
        });
    }

    /**
     * Return stock when shipment is returned
     */
    public function returnToStockOnReturn(Shipment $shipment, string $reason = 'returned_from_customer'): void
    {
        DB::transaction(function() use ($shipment, $reason) {
            foreach ($shipment->products as $product) {
                $quantity = $product->pivot->quantity;
                $color = $product->pivot->color;
                $size = $product->pivot->size;

                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('color', $color)
                    ->where('size', $size)
                    ->lockForUpdate()
                    ->first();

                if (!$variant || $variant->is_unlimited) {
                    continue;
                }

                // Return to stock and release reservation
                $stockBefore = $variant->stock_quantity;
                $reservedBefore = $variant->reserved_quantity;

                $variant->stock_quantity += $quantity;
                $variant->reserved_quantity = max(0, $variant->reserved_quantity - $quantity);
                $variant->save();

                // Log movement
                $this->logMovement($variant, $shipment, 'return', $quantity,
                    $stockBefore, $variant->stock_quantity, $reason);
            }
        });
    }

    /**
     * Manual stock adjustment
     */
    public function adjust(ProductVariant $variant, int $quantityChange, string $reason): void
    {
        DB::transaction(function() use ($variant, $quantityChange, $reason) {
            $variant->lockForUpdate();
            
            $stockBefore = $variant->stock_quantity;
            $variant->stock_quantity += $quantityChange;
            
            if ($variant->stock_quantity < 0) {
                throw new Exception("Stock cannot be negative");
            }
            
            $variant->save();

            // Log movement
            $this->logMovement($variant, null, 'adjustment', $quantityChange,
                $stockBefore, $variant->stock_quantity, $reason);
        });
    }

    /**
     * Transfer stock between warehouses
     */
    public function transfer(
        ProductVariant $variant,
        \App\Models\Warehouse $fromWarehouse,
        \App\Models\Warehouse $toWarehouse,
        int $quantity,
        string $reason,
        ?string $reference = null
    ): void {
        DB::transaction(function () use ($variant, $fromWarehouse, $toWarehouse, $quantity, $reason, $reference) {
            $variant->lockForUpdate();

            // 1. Check Source Level
            $sourceLevel = \App\Models\InventoryLevel::firstOrCreate(
                ['warehouse_id' => $fromWarehouse->id, 'variant_id' => $variant->id],
                ['stock_quantity' => 0]
            );

            if ($sourceLevel->stock_quantity < $quantity) {
                 throw new Exception("Insufficient stock in source warehouse {$fromWarehouse->name} for {$variant->full_name}");
            }

            // 2. Transact Source
            $sourceBefore = $sourceLevel->stock_quantity;
            $sourceLevel->decrement('stock_quantity', $quantity);
            
            // Log Source Movement
            StockMovement::create([
                'warehouse_id' => $fromWarehouse->id,
                'variant_id' => $variant->id,
                'movement_type' => 'transfer', // or transfer_out
                'quantity_change' => -$quantity,
                'quantity_before' => $sourceBefore,
                'quantity_after' => $sourceLevel->stock_quantity,
                'reason' => "Transfer OUT to {$toWarehouse->name}: " . $reason,
                'reference_number' => $reference,
                'user_id' => Auth::id(),
            ]);

            // 3. Transact Destination
            $destLevel = \App\Models\InventoryLevel::firstOrCreate(
                ['warehouse_id' => $toWarehouse->id, 'variant_id' => $variant->id],
                ['stock_quantity' => 0]
            );
            
            $destBefore = $destLevel->stock_quantity;
            $destLevel->increment('stock_quantity', $quantity);

            // Log Destination Movement
            StockMovement::create([
                'warehouse_id' => $toWarehouse->id,
                'variant_id' => $variant->id,
                'movement_type' => 'transfer', // or transfer_in
                'quantity_change' => $quantity,
                'quantity_before' => $destBefore,
                'quantity_after' => $destLevel->stock_quantity,
                'reason' => "Transfer IN from {$fromWarehouse->name}: " . $reason,
                'reference_number' => $reference,
                'user_id' => Auth::id(),
            ]);
        });
    }

    /**
     * Log stock movement for audit trail
     */
    private function logMovement(
        ProductVariant $variant,
        ?Shipment $shipment,
        string $type,
        int $quantityChange,
        int $quantityBefore,
        int $quantityAfter,
        string $reason
    ): void {
        StockMovement::create([
            'variant_id' => $variant->id,
            'shipment_id' => $shipment?->id,
            'movement_type' => $type,
            'quantity_change' => $quantityChange,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reason' => $reason,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }
}
