<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class AutoReorderService
{
    /**
     * Scan low stock items and generate draft Purchase Orders.
     * 
     * @return array Summary of generated orders
     */
    public function generateRestockOrders()
    {
        // 1. Find variants below threshold
        $lowStockVariants = ProductVariant::query()
            ->with(['product', 'product.supplier'])
            ->where('is_unlimited', false)
            ->whereRaw('(stock_quantity - reserved_quantity) <= low_stock_threshold')
            ->get();

        if ($lowStockVariants->isEmpty()) {
            return ['status' => 'info', 'message' => 'No items need restocking.'];
        }

        // 2. Group by Supplier
        $groupedBySupplier = $lowStockVariants->groupBy(function ($variant) {
            return $variant->product->supplier_id;
        });

        $createdOrders = 0;
        $defaultWarehouse = Warehouse::first(); // Assuming a default warehouse for delivery

        DB::transaction(function () use ($groupedBySupplier, $defaultWarehouse, &$createdOrders) {
            foreach ($groupedBySupplier as $supplierId => $variants) {
                if (!$supplierId) continue; // Skip if no supplier assigned

                // Create Draft PO
                $po = PurchaseOrder::create([
                    'supplier_id' => $supplierId,
                    'warehouse_id' => $defaultWarehouse?->id, // Fallback or logic to choose
                    'status' => 'draft',
                    'order_date' => now(),
                    'total_amount' => 0, // Will update after items
                ]);

                $totalAmount = 0;

                foreach ($variants as $variant) {
                    // Calculate quantity needed to reach reorder point + safety buffer (e.g. +10 or double threshold)
                    // For simplicity, let's order 50 units or replenish to a fixed amount. 
                    // Let's assume reorder quantity is defined or just flat 50.
                    $quantityToOrder = max(10, $variant->product->reorder_point ?? 10); 

                    $cost = $variant->product->cost_price ?? 0;
                    $lineTotal = $quantityToOrder * $cost;

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'variant_id' => $variant->id,
                        'quantity' => $quantityToOrder,
                        'unit_cost' => $cost,
                        'total_cost' => $lineTotal,
                    ]);

                    $totalAmount += $lineTotal;
                }

                $po->update(['total_amount' => $totalAmount]);
                $createdOrders++;
            }
        });

        return [
            'status' => 'success',
            'message' => "Successfully created {$createdOrders} draft Purchase Orders.",
            'orders_count' => $createdOrders
        ];
    }
}
