<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class DelayedShipmentsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;
    
    protected static ?string $heading = '‼️ شحنات متأخرة (> 10 أيام)';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Shipment::query()
                    ->where('status_id', '!=', 4) // Not Delivered
                    ->where('status_id', '!=', 6) // Not Returned
                    ->where('created_at', '<', Carbon::now()->subDays(10))
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('رقم التتبع')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('shipping_company.name')
                    ->label('الشركة'),
                Tables\Columns\TextColumn::make('status.name')
                    ->label('الحالة')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->since(),
            ])
            ->paginated(false);
    }
}
