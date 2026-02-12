<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Shipment;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('dashboard_stats_overview', 300, function () {
            // 1. Basic Counts
            $totalShipments = Shipment::count();
            $deliveredCount = Shipment::whereIn('status_id', [1, 42])->count();
            $returnedCount = Shipment::where('status_id', 2)->count();
    
            // 2. Financials (Aggregated)
            $shippingCosts = Shipment::sum('shipping_price');
            $dueAmounts = Shipment::whereNotIn('status_id', [1, 2, 40, 42])->sum('total_amount');
            
            // Net Profit
            $netProfit = Shipment::whereIn('status_id', [1, 42])->sum(\Illuminate\Support\Facades\DB::raw('total_amount - shipping_price'));
    
            // 3. Treasury (Collections vs Expenses)
            $totalCollections = \App\Models\Collection::sum('amount');
            $totalExpenses = \App\Models\Expense::sum('amount');
            $treasuryBalance = $totalCollections - $totalExpenses;
    
            return [
                // Row 1: Shipments
                Stat::make('إجمالي الشحنات', number_format($totalShipments))
                    ->description('12% زيادة هذا الأسبوع')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('primary')
                    ->icon('heroicon-o-cube'),
    
                Stat::make('تم التسليم', number_format($deliveredCount))
                    ->description('شحنات ناجحة')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
    
                Stat::make('المرتجعات', number_format($returnedCount))
                    ->description('2% معدل')
                    ->descriptionIcon('heroicon-m-arrow-path')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-path'),
                
                // Row 2: Shipment Financials
                Stat::make('تكاليف الشحن', number_format($shippingCosts) . ' ج.م')
                    ->description('إجمالي مصاريف الشحن')
                    ->color('primary')
                    ->icon('heroicon-o-truck'),
    
                Stat::make('المبالغ المستحقة', number_format($dueAmounts) . ' ج.م')
                    ->description('بانتظار التحصيل')
                    ->color('warning')
                    ->icon('heroicon-o-clock'),
    
                Stat::make('صافي الربح', number_format($netProfit) . ' ج.م')
                    ->description('للشحنات المسلمة')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success')
                    ->chart([1500, 2000, 1800, 2200, 24500])
                    ->icon('heroicon-o-currency-dollar'),

                // Row 3: Treasury Status
                Stat::make('إجمالي التحصيلات', number_format($totalCollections) . ' ج.م')
                    ->description('الوارد للخزينة')
                    ->color('success')
                    ->icon('heroicon-o-banknotes'),

                Stat::make('إجمالي المصاريف', number_format($totalExpenses) . ' ج.م')
                    ->description('المصروفات التشغيلية')
                    ->color('danger')
                    ->icon('heroicon-o-receipt-percent'),

                Stat::make('رصيد الخزنة الحالي', number_format($treasuryBalance) . ' ج.م')
                    ->description($treasuryBalance >= 0 ? 'فائض' : 'عجز')
                    ->descriptionIcon($treasuryBalance >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                    ->color($treasuryBalance >= 0 ? 'success' : 'danger')
                    ->icon('heroicon-o-scale'),
            ];
        });
    }
}
