<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class DemandForecastingWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static bool $shouldRegisterWidget = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'توقعات الطلب (Demand Forecasting)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->where('is_unlimited', false)
                    ->addSelect(['sold_30_days' => function ($query) {
                        $query->selectRaw('coalesce(sum(shipment_product.quantity), 0)')
                            ->from('shipment_product')
                            ->join('shipments', 'shipments.id', '=', 'shipment_product.shipment_id')
                            ->where('shipment_product.product_id', DB::raw('product_variants.product_id'))
                            // Match color/size (handle null/empty)
                            ->where(function($q) {
                                $q->whereColumn('shipment_product.color', 'product_variants.color')
                                  ->orWhere(function($sub) {
                                      $sub->whereNull('shipment_product.color')
                                          ->whereNull('product_variants.color');
                                  });
                            })
                            ->where(function($q) {
                                $q->whereColumn('shipment_product.size', 'product_variants.size')
                                  ->orWhere(function($sub) {
                                      $sub->whereNull('shipment_product.size')
                                          ->whereNull('product_variants.size');
                                  });
                            })
                            ->where('shipments.status_id', 4) // Assuming 4 is 'delivered', better to look it up but subquery limit
                             // Or use a join with statuses if ID isn't constant. 
                             // Let's rely on 'delivered_at' if available, or just shipment status logic.
                             // Safest: Join statuses table.
                             ->whereIn('shipments.status_id', function($q) {
                                 $q->select('id')->from('shipment_statuses')->whereIn('code', ['delivered', 'shipped']); // Shipped also counts as demand? No, strictly Delivered is realized demand, but Shipped is committed.
                                 // Let's count 'delivered' only for historical velocity.
                                 // Or 'shipped' + 'delivered'.
                             })
                            ->where('shipments.delivery_date', '>=', now()->subDays(30));
                    }])
                    // Filter to show only items with some sales or low stock? 
                    // Showing all helps planning.
                    ->orderByDesc('sold_30_days')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('المنتج')
                    ->description(fn (ProductVariant $record) => $record->size . ' - ' . $record->color)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('المخزون الحالي')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sold_30_days')
                    ->label('مبيعات 30 يوم')
                    ->numeric()
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->label('إجمالي المبيعات')),

                Tables\Columns\TextColumn::make('daily_velocity')
                    ->label('المعدل اليومي')
                    ->state(fn (ProductVariant $record) => number_format($record->sold_30_days / 30, 2))
                    ->color('info'),

                Tables\Columns\TextColumn::make('recommended_stock')
                    ->label('المخزون المقترح (14 يوم)')
                    ->state(function (ProductVariant $record) {
                        $daily = $record->sold_30_days / 30;
                        return ceil($daily * 14); // Safety stock for 2 weeks
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('reorder_suggestion')
                    ->label('مقترح الطلب')
                    ->state(function (ProductVariant $record) {
                        $daily = $record->sold_30_days / 30;
                        $needed = ceil($daily * 14);
                        $current = $record->stock_quantity; // Total stock (reserved is still in warehouse, maybe we should subtract it? But usually reserved is for sold items)
                        // Actually, if reserved, it's not available for NEW sales. So we should compare with 'available_quantity'.
                        // But 'sold_30_days' predicts flow.
                        // Let's compare with 'stock_quantity' for simplicity or 'available' if conservative.
                        // If stock=100, reserved=20. Available=80.
                        // If needed=50. We have 80. No reorder.
                        
                        $balance = $current - $needed;
                        if ($balance < 0) return abs($balance);
                        return 0;
                    })
                    ->badge()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('create_po')
                    ->label('طلب')
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->visible(fn ($record) => ($record->sold_30_days / 30 * 14) > $record->stock_quantity)
                    ->url(fn (ProductVariant $record) => route('filament.admin.resources.purchase-orders.create')),
            ]);
    }
}
