<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RecentExpensesWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static bool $shouldRegisterWidget = true;

    protected int|string|array $columnSpan = 'half';
    protected static ?string $heading = 'أحدث المصاريف';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
                    ->when($this->filters['startDate'] ?? null, fn ($query, $date) => $query->whereDate('expense_date', '>=', $date))
                    ->when($this->filters['endDate'] ?? null, fn ($query, $date) => $query->whereDate('expense_date', '<=', $date))
                    ->latest('expense_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->date('Y-m-d')
                    ->label('التاريخ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('السبب/العنوان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EGP')
                    ->label('المبلغ')
                    ->sortable(),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
