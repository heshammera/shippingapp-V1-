<?php

namespace App\Filament\Widgets;

use App\Models\Collection;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class FinancialStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $totalCollections = Collection::query()
            ->when($startDate, fn ($q) => $q->whereDate('collection_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('collection_date', '<=', $endDate))
            ->sum('amount');
            
        $totalExpenses = Expense::query()
            ->when($startDate, fn ($q) => $q->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('expense_date', '<=', $endDate))
            ->sum('amount');
        $balance = $totalCollections - $totalExpenses;

        return [
            Stat::make('إجمالي التحصيلات', number_format($totalCollections, 2) . ' جنيه')
                ->description('خلال الفترة المحددة')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Placeholder chart for visuals

            Stat::make('إجمالي المصاريف', number_format($totalExpenses, 2) . ' جنيه')
                ->description('خلال الفترة المحددة')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([3, 12, 4, 15, 3, 10, 2]), // Placeholder chart

            Stat::make('رصيد الخزنة', number_format($balance, 2) . ' جنيه')
                ->description($balance >= 0 ? 'فائض' : 'عجز')
                ->descriptionIcon($balance >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
