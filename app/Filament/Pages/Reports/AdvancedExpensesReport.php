<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvancedExpensesReport extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'تقارير المصاريف';
    protected static ?string $navigationGroup = 'التقارير المتقدمة';
    protected static ?string $slug = 'reports-v2/expenses';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.reports.advanced-expenses-report';

    // Live Filter Properties
    public $dateFrom;
    public $dateTo;
    public $userId = '';
    public $perPage = 20;

    // Query String
    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'userId' => ['except' => ''],
    ];

    public function mount()
    {
        // Default: Show all data
    }

    public function updatedUserId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->userId = '';
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = Expense::query()->with(['requester']);

        // Date filter
        if ($this->dateFrom && $this->dateTo) {
            $start = Carbon::parse($this->dateFrom)->startOfDay();
            $end = Carbon::parse($this->dateTo)->endOfDay();
            $query->whereBetween('expense_date', [$start, $end]);
        }

        // User filter (Requester)
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        return $query;
    }

    public function getExpensesProperty()
    {
        return $this->getFilteredQuery()
            ->latest('expense_date')
            ->paginate($this->perPage);
    }

    public function getKpisProperty()
    {
        $cacheKey = 'expenses_kpis_' . md5(json_encode([
            $this->dateFrom,
            $this->dateTo,
            $this->userId,
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $query = $this->getFilteredQuery();

            $totalExpenses = $query->sum('amount') ?? 0;
            $count = $query->count();
            
            // Average daily expense (if date range selected) or average per expense
            if ($this->dateFrom && $this->dateTo) {
                $days = Carbon::parse($this->dateFrom)->diffInDays(Carbon::parse($this->dateTo)) + 1;
                $dailyAverage = $days > 0 ? $totalExpenses / $days : $totalExpenses;
            } else {
                // If no date range, maybe just average per transaction? Or fallback to a default period logic?
                // Let's stick to Average per Transaction for general view if no date is set, 
                // BUT user might prefer Daily Average. 
                // Let's do Average per Count for now as it's more deterministic without date range.
                $dailyAverage = $count > 0 ? $totalExpenses / $count : 0;
            }

            return compact(
                'totalExpenses',
                'count',
                'dailyAverage'
            );
        });
    }

    public function getChartDataProperty()
    {
        $cacheKey = 'expenses_chart_' . md5(json_encode([
            $this->dateFrom, $this->dateTo, $this->userId
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $data = $this->getFilteredQuery()
                ->reorder()
                ->select(
                    DB::raw('DATE(expense_date) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'labels' => $data->pluck('date')->toArray(),
                'values' => $data->pluck('total')->toArray(),
            ];
        });
    }

    public function getUsersProperty()
    {
        return User::pluck('name', 'id');
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
