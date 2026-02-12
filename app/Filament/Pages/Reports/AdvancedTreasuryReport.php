<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Livewire\WithPagination;
use App\Models\Collection;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvancedTreasuryReport extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'تقارير الخزنة';
    protected static ?string $navigationGroup = 'التقارير المتقدمة';
    protected static ?string $slug = 'reports-v2/treasury';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.reports.advanced-treasury-report';

    // Live Filter Properties
    public $dateFrom;
    public $dateTo;
    public $perPage = 20;

    // Query String
    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount()
    {
        // Default: Show all data
    }

    public function resetFilters()
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->resetPage();
    }

    private function getCollectionsQuery()
    {
        $query = Collection::query()->with('shippingCompany');
        
        if ($this->dateFrom && $this->dateTo) {
            $start = Carbon::parse($this->dateFrom)->startOfDay();
            $end = Carbon::parse($this->dateTo)->endOfDay();
            $query->whereBetween('collection_date', [$start, $end]);
        }
        
        return $query;
    }

    private function getExpensesQuery()
    {
        $query = Expense::query()->with('user');
        
        if ($this->dateFrom && $this->dateTo) {
            $start = Carbon::parse($this->dateFrom)->startOfDay();
            $end = Carbon::parse($this->dateTo)->endOfDay();
            $query->whereBetween('expense_date', [$start, $end]);
        }
        
        return $query;
    }

    public function getTransactionsProperty()
    {
        // We cannot use standard pagination easily with merged arrays from two queries.
        // So we fetch data, merge, sort, and then manually slice for pagination in rendering if needed.
        // HOWEVER, for a report like this, simple pagination on merged set is acceptable if set is not huge.
        // Or we can just paginate the result collection.

        // For efficiency, let's limit if no date is selected? No, user wants ALL data default.
        
        $collections = $this->getCollectionsQuery()->get();
        $expenses = $this->getExpensesQuery()->get();

        $merged = collect();

        foreach ($collections as $collection) {
            $merged->push([
                'date' => $collection->collection_date ? Carbon::parse($collection->collection_date) : null,
                'type' => 'collection',
                'description' => 'تحصيل من ' . ($collection->shippingCompany->name ?? 'غير محدد'),
                'amount' => $collection->amount,
                'notes' => $collection->notes,
                'user' => null, 
                'raw_date' => $collection->collection_date,
            ]);
        }

        foreach ($expenses as $expense) {
            $merged->push([
                'date' => $expense->expense_date ? Carbon::parse($expense->expense_date) : null,
                'type' => 'expense',
                'description' => $expense->title ?? 'مصروف',
                'amount' => $expense->amount,
                'notes' => $expense->notes,
                'user' => $expense->user->name ?? '',
                'raw_date' => $expense->expense_date,
            ]);
        }

        // Sort by date desc
        $sorted = $merged->sortByDesc('raw_date')->values();

        // Calculate running balance (optional, effectively hard to do on paginated/sorted desc view without full history)
        // For this view, we will just show the transaction amounts. 
        // Showing "Running Balance" per row is tricky if we paginate or filter date range (as it needs previous opens).
        // Let's stick to showing Income/Expense per row.

        // Manual Pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $this->perPage;
        $results = $sorted->slice(($page - 1) * $perPage, $perPage)->all();

        return new LengthAwarePaginator($results, $sorted->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
    }

    public function getKpisProperty()
    {
        $cacheKey = 'treasury_kpis_' . md5(json_encode([
            $this->dateFrom,
            $this->dateTo,
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $totalIncome = $this->getCollectionsQuery()->sum('amount') ?? 0;
            $totalExpense = $this->getExpensesQuery()->sum('amount') ?? 0;
            $netBalance = $totalIncome - $totalExpense;

            return compact(
                'totalIncome',
                'totalExpense',
                'netBalance'
            );
        });
    }

    public function getChartDataProperty()
    {
        $cacheKey = 'treasury_chart_' . md5(json_encode([
            $this->dateFrom, $this->dateTo
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            
            $collections = $this->getCollectionsQuery()
                ->select(DB::raw('DATE(collection_date) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date');
            
            $expenses = $this->getExpensesQuery()
                ->select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->get()
                ->pluck('total', 'date');
            
            // Merge Dates unique sorted
            $allDates = $collections->keys()->merge($expenses->keys())->unique()->sort()->values();
            
            $labels = [];
            $incomeData = [];
            $expenseData = [];
            $balanceData = [];
            $accumulatedBalance = 0; // This should technically start from previous balance if filtered, 
                                     // but for "Visible Range" chart usually starts at 0 or we calculate pre-range balance.
                                     // For simplicity in this logic, we calculate Balance Variation within this period or purely accumulations.
                                     // Let's do simple Daily Balance (Revenue - Expense) and maybe Cumulative.
                                     // User requested "Chart Controls", so likely looking for Trends.
            
            foreach ($allDates as $date) {
                $inc = $collections->get($date) ?? 0;
                $exp = $expenses->get($date) ?? 0;
                $labels[] = $date;
                $incomeData[] = $inc;
                $expenseData[] = $exp;
                
                $accumulatedBalance += ($inc - $exp);
                $balanceData[] = $accumulatedBalance;
            }

            return [
                'labels' => $labels,
                'income' => $incomeData,
                'expense' => $expenseData,
                'balance' => $balanceData,
            ];
        });
    }

    public function exportExcel()
    {
        session()->flash('info', 'سيتم إضافة التصدير إلى Excel قريباً');
    }

    public function exportPdf()
    {
        session()->flash('info', 'سيتم إضافة التصدير إلى PDF قريباً');
    }
}
