<?php

namespace App\Filament\Widgets;

use App\Models\Collection;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class ShippingBalancesWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'half';
    protected static ?string $heading = 'التحصيلات حسب شركة الشحن';
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Collection::query()
                    ->select('shipping_company_id', 'shipping_company_id as id', DB::raw('SUM(amount) as total_amount'))
                    ->whereNotNull('shipping_company_id')
                    ->when($this->filters['startDate'] ?? null, fn ($query, $date) => $query->whereDate('collection_date', '>=', $date))
                    ->when($this->filters['endDate'] ?? null, fn ($query, $date) => $query->whereDate('collection_date', '<=', $date))
                    ->groupBy('shipping_company_id')
                    ->orderByDesc('total_amount')
            )
            ->columns([
                Tables\Columns\TextColumn::make('shippingCompany.name')
                    ->label('شركة الشحن')
                    ->default('غير محدد'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('EGP')
                    ->label('إجمالي التحصيلات')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
