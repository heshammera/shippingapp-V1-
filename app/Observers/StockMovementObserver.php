<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\Models\InventoryLevel;
use App\Models\Batch;

class StockMovementObserver
{
    /**
     * Handle the StockMovement "created" event.
     */
    public function created(StockMovement $stockMovement): void
    {
        // 1. Update Warehouse Inventory
        $inventory = InventoryLevel::firstOrCreate([
            'warehouse_id' => $stockMovement->warehouse_id,
            'variant_id' => $stockMovement->variant_id,
        ]);

        $change = $stockMovement->quantity_change;

        // Apply change to inventory
        $inventory->increment('quantity', $change);

        // 2. Update Batch Inventory (if applicable)
        if ($stockMovement->batch_id) {
            $batch = Batch::find($stockMovement->batch_id);
            if ($batch) {
                // Determine if we should increment or decrement batch
                // Normally movement_change logic aligns with batch logic
                // e.g., Purchase (+10) -> Batch (+10)
                //      Sale (-5) -> Batch (-5)
                $batch->increment('quantity', $change);
            }
        }

        // 3. Update Product Variant Aggregate (Optional but recommended for speed)
        // We track reserved separately, so stock_quantity usually reflects physical stock
        $stockMovement->variant->increment('stock_quantity', $change);

        // 4. Check for Low Stock Alert
        $variant = $stockMovement->variant;
        $product = $variant->product; // Ensure relationship exists

        // If not unlimited and stock drops below or equal threshold
        if (!$variant->is_unlimited && $variant->stock_quantity <= $variant->low_stock_threshold) {
             // Avoid duplicate alerts? 
             // Maybe we only alert if it wasn't low before? 
             // For now, let's keep it simple: Alert every time it drops or stays low after an OUTWARD movement.
             
             // Only alert on deductic movements (out) or adjustments down
             if ($stockMovement->quantity_change < 0) {
                 \Filament\Notifications\Notification::make()
                    ->title('⚠️ تنبيه مخزون منخفض')
                    ->body("المنتج {$variant->full_name} وصل للحد الأدنى ({$variant->stock_quantity} قطعة)")
                    ->warning()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('عاش، اطلب المزيد')
                            ->url(route('filament.admin.resources.inventory-levels.index')), // Fixed route name to match resource
                    ])
                    ->sendToDatabase(\App\Models\User::all());
             }
        }
    }
}
