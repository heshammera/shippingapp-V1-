<?php

namespace App\Filament\Widgets;

use App\Models\ShippingCompany;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ShippingCompanyPerformanceWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'أداء شركات الشحن';

    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ShippingCompany::query()
                    ->withCount('shipments')
                    ->orderByDesc('shipments_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الشركة')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('shipments_count')
                    ->counts('shipments')
                    ->label('إجمالي الشحنات'),

                // Custom calculation column for delivery percentage would vary based on actual relationships
                // For now, simpler implementation:
                Tables\Columns\TextColumn::make('delivered_shipments_count')
                    ->counts('shipments', fn (Builder $query) => $query->where('status_id', 4))
                    ->label('تم التسليم')
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
