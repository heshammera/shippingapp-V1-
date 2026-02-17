<?php

namespace App\Filament\Widgets;

use App\Models\Collection;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LatestCollectionsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'أحدث التحصيلات';
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Collection::query()
                    ->when($this->filters['startDate'] ?? null, fn ($query, $date) => $query->whereDate('collection_date', '>=', $date))
                    ->when($this->filters['endDate'] ?? null, fn ($query, $date) => $query->whereDate('collection_date', '<=', $date))
                    ->latest('collection_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('collection_date')
                    ->date('Y-m-d')
                    ->label('التاريخ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shippingCompany.name')
                    ->label('شركة الشحن')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EGP')
                    ->label('المبلغ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
