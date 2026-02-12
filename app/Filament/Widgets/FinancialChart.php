<?php

namespace App\Filament\Widgets;

use App\Models\Collection;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Carbon\Carbon;

class FinancialChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'التحليل المالي (التحصيلات vs المصاريف)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        
        if (! $startDate || ! $endDate) {
            $minCollection = Collection::min('collection_date');
            $maxCollection = Collection::max('collection_date');
            $minExpense = Expense::min('expense_date');
            $maxExpense = Expense::max('expense_date');

             // Handle case where DB is empty
            $dbStart = ($minCollection && $minExpense) ? min($minCollection, $minExpense) : ($minCollection ?? $minExpense);
            $dbEnd = ($maxCollection && $maxExpense) ? max($maxCollection, $maxExpense) : ($maxCollection ?? $maxExpense);

            $startDate = $startDate ?? ($dbStart ?: now()->startOfMonth()->toDateString());
            $endDate = $endDate ?? ($dbEnd ?: now()->endOfMonth()->toDateString());
        }
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // helper to get date range
        $period = \Carbon\CarbonPeriod::create($start, $end);
        $labels = [];
        $collectionsData = [];
        $expensesData = [];

        // Fetch data array [date => amount]
        $collectionsQuery = Collection::query()
            ->whereBetween('collection_date', [$start, $end])
            ->selectRaw("TO_CHAR(collection_date, 'YYYY-MM-DD') as date, SUM(amount) as total") // Postgres format
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $expensesQuery = Expense::query()
            ->whereBetween('expense_date', [$start, $end])
            ->selectRaw("TO_CHAR(expense_date, 'YYYY-MM-DD') as date, SUM(amount) as total") // Postgres format
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $formattedDate;
            $collectionsData[] = $collectionsQuery[$formattedDate] ?? 0;
            $expensesData[] = $expensesQuery[$formattedDate] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'التحصيلات',
                    'data' => $collectionsData,
                    'borderColor' => '#10b981', // Emerald 500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'المصاريف',
                    'data' => $expensesData,
                    'borderColor' => '#ef4444', // Red 500
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
