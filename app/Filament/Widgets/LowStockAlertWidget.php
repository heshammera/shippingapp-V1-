<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlertWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->lowStock()
                    ->orderBy('stock_quantity', 'asc')
            )
            ->heading('تنبيهات انخفاض المخزون')
            ->emptyStateHeading('المخزون بوضع جيد')
            ->emptyStateDescription('لا توجد منتجات تحت حد الطلب حالياً.')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('المنتج')
                    ->description(fn (ProductVariant $record) => $record->size . ' - ' . $record->color)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('الكمية الحالية')
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('حد الطلب')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('shortage')
                    ->label('العجز')
                    ->state(fn (ProductVariant $record) => $record->low_stock_threshold - $record->stock_quantity)
                    ->numeric()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('create_po')
                    ->label('طلب شراء')
                    ->icon('heroicon-m-shopping-cart')
                    ->button()
                    ->url(fn (ProductVariant $record) => route('filament.admin.resources.purchase-orders.create', ['items' => [['variant_id' => $record->id, 'quantity_ordered' => ($record->low_stock_threshold - $record->stock_quantity) + 10]]])) // Pre-fill suggestion
                    // Note: Pre-filling complex repeater data via URL is tricky in Filament without custom handling in CreatePage mount(), 
                    // but for now, we direct to create page. 
                    // Better approach: Open a modal to Quick Create PO or Redirect to Create PO page.
                    // Let's just redirect to Purchase Order Create page for now.
                    ->url(route('filament.admin.resources.purchase-orders.create')),
                    
                Tables\Actions\Action::make('adjust')
                    ->label('تعديل مخزون')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->color('gray')
                    ->url(fn (ProductVariant $record) => route('filament.admin.resources.inventory-levels.index', ['tableFilters[search][value]' => $record->sku])),
            ]);
    }
}
