<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LowStockWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;

    protected static bool $shouldRegisterWidget = false;
    protected static ?int $sort = 5;
    
    protected function getStats(): array
    {
        $lowStockCount = ProductVariant::lowStock()->count();
        $outOfStockCount = ProductVariant::outOfStock()->count();
        $totalVariants = ProductVariant::count();
        $totalStockValue = ProductVariant::with('product')
            ->get()
            ->sum(function ($variant) {
                return $variant->stock_quantity * ($variant->product->cost_price ?? 0);
            });

        return [
            Stat::make('مخزون منخفض', $lowStockCount)
                ->description('منتج بحاجة لإعادة طلب')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->url(route('filament.admin.resources.product-variants.index', ['tableFilters[low_stock][isActive]' => true])),
            
            Stat::make('نفد من المخزون', $outOfStockCount)
                ->description('منتج غير متوفر')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.product-variants.index', ['tableFilters[out_of_stock][isActive]' => true])),
            
            Stat::make('إجمالي الأنواع', $totalVariants)
                ->description('عدد جميع أنواع المنتجات')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),
            
            Stat::make('قيمة المخزون', 'EGP ' . number_format($totalStockValue, 2))
                ->description('بناءً على سعر التكلفة')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
