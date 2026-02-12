<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use Carbon\Carbon;

class DemandForecastingService
{
    /**
     * Calculate Moving Average for daily consumption over a period.
     * 
     * @param int $variantId
     * @param int $days Period to analyze (default 30 days)
     * @return float Average daily consumption
     */
    public function calculateDailyUsage(int $variantId, int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days);

        // Sum of all negative movements (sales/deductions)
        // quantity_change is negative for outflows, so we sum absolute values
        $totalConsumed = StockMovement::query()
            ->where('variant_id', $variantId)
            ->where('quantity_change', '<', 0) // Outflows only
            ->where('created_at', '>=', $startDate)
            ->sum('quantity_change');

        $totalConsumed = abs($totalConsumed);

        if ($totalConsumed == 0) {
            return 0.0;
        }

        return round($totalConsumed / $days, 2);
    }

    /**
     * Predict stock needs for next X days.
     * 
     * @param int $variantId
     * @param int $daysToCover Days of stock coverage needed
     * @return int Quantity needed
     */
    public function predictNeed(int $variantId, int $daysToCover = 30): int
    {
        $dailyUsage = $this->calculateDailyUsage($variantId, 30); // Base forecast on last 30 days
        $currentStock = ProductVariant::find($variantId)?->available_quantity ?? 0;
        
        $neededTotal = ceil($dailyUsage * $daysToCover);
        
        $shortage = $neededTotal - $currentStock;

        return max(0, (int) $shortage);
    }
}
