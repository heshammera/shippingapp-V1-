<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IncomingPurchaseOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static bool $shouldRegisterWidget = false;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PurchaseOrder::query()
                    ->where('status', 'ordered')
                    ->whereNotNull('expected_delivery_date')
                    ->where('expected_delivery_date', '<=', now()->addDays(7))
                    ->orderBy('expected_delivery_date', 'asc')
            )
            ->heading('طلبات شراء متوقع وصولها قريباً (أو متأخرة)')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->sortable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('تاريخ التوصيل المتوقع')
                    ->date()
                    ->badge()
                    ->color(fn ($state) => $state < now()->format('Y-m-d') ? 'danger' : ($state == now()->format('Y-m-d') ? 'warning' : 'success'))
                    ->description(fn ($state) => \Carbon\Carbon::parse($state)->diffForHumans()),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('الأصناف'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('عرض')
                    ->url(fn (PurchaseOrder $record): string => \App\Filament\Resources\PurchaseOrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
