<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use Livewire\WithPagination;
use App\Models\Collection;
use App\Models\ShippingCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvancedCollectionsReport extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'تقارير التحصيلات';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?int $navigationSort = 99;
    protected static ?string $slug = 'reports-v2/collections';
    protected static string $view = 'filament.pages.reports.advanced-collections-report';

    // Live Filter Properties
    public $dateFrom;
    public $dateTo;
    public $shippingCompanyId = '';
    public $perPage = 20;

    // Query String
    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'shippingCompanyId' => ['except' => ''],
    ];

    public function mount()
    {
        // Default: Show all data
    }

    public function updatedShippingCompanyId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->shippingCompanyId = '';
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = Collection::query()->with(['shippingCompany']);

        // Date filter
        if ($this->dateFrom && $this->dateTo) {
            $start = Carbon::parse($this->dateFrom)->startOfDay();
            $end = Carbon::parse($this->dateTo)->endOfDay();
            // Checking both 'date' and 'collection_date' to be safe, but prioritizing collection_date based on model
            $query->whereBetween('collection_date', [$start, $end]);
        }

        // Company filter
        if ($this->shippingCompanyId) {
            $query->where('shipping_company_id', $this->shippingCompanyId);
        }

        return $query;
    }

    public function getCollectionsProperty()
    {
        return $this->getFilteredQuery()
            ->latest('collection_date')
            ->paginate($this->perPage);
    }

    public function getKpisProperty()
    {
        // Cache key based on filters
        $cacheKey = 'collections_kpis_' . md5(json_encode([
            $this->dateFrom,
            $this->dateTo,
            $this->shippingCompanyId,
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $query = $this->getFilteredQuery();

            $totalCollections = $query->sum('amount') ?? 0;
            $count = $query->count();
            
            // Average collection amount
            $averageAmount = $count > 0 ? $totalCollections / $count : 0;

            return compact(
                'totalCollections',
                'count',
                'averageAmount'
            );
        });
    }

    public function getChartDataProperty()
    {
        $cacheKey = 'collections_chart_' . md5(json_encode([
            $this->dateFrom, $this->dateTo, $this->shippingCompanyId
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $data = $this->getFilteredQuery()
                ->reorder()
                ->select(
                    DB::raw('DATE(collection_date) as date'),
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

    public function getCompaniesProperty()
    {
        return ShippingCompany::pluck('name', 'id');
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
