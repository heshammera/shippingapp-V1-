<?php

namespace App\Filament\Widgets;

use App\Models\Supplier;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SupplierPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static bool $shouldRegisterWidget = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'تقرير أداء الموردين (Supplier Performance)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Supplier::query()
                    ->withCount('purchaseOrders')
                    ->withSum('purchaseOrders', 'total_amount')
                    ->withAvg('purchaseOrders', 'total_amount')
                    ->orderByDesc('purchase_orders_sum_total_amount')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('جهة الاتصال')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('purchase_orders_count')
                    ->label('عدد الطلبات')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('purchase_orders_sum_total_amount')
                    ->label('إجمالي المشتريات')
                    ->money('EGP')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('EGP')->label('الإجمالي'))
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('purchase_orders_avg_total_amount')
                    ->label('متوسط قيمة الطلب')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('completion_rate')
                    ->label('معدل الإكمال')
                    ->state(function (Supplier $record) {
                        $total = $record->purchaseOrders()->count();
                        if ($total === 0) return 'N/A';
                        
                        $completed = $record->purchaseOrders()->where('status', 'received')->count();
                        $rate = ($completed / $total) * 100;
                        return round($rate, 1) . '%';
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        if ($state === 'N/A') return 'gray';
                        $rate = (float) str_replace('%', '', $state);
                        if ($rate >= 90) return 'success';
                        if ($rate >= 70) return 'warning';
                        return 'danger';
                    }),

                Tables\Columns\TextColumn::make('rating')
                    ->label('التقييم')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state >= 4.0 => 'success',
                        $state >= 3.0 => 'warning',
                        default => 'danger',
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_orders')
                    ->label('عرض الطلبات')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Supplier $record) => route('filament.admin.resources.purchase-orders.index', [
                        'tableFilters' => [
                            'supplier_id' => ['value' => $record->id]
                        ]
                    ])),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_only')
                    ->label('الموردين النشطين فقط')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->default(),
                    
                Tables\Filters\Filter::make('has_orders')
                    ->label('لديهم طلبات')
                    ->query(fn (Builder $query): Builder => $query->has('purchaseOrders'))
                    ->default(),
            ]);
    }
}
