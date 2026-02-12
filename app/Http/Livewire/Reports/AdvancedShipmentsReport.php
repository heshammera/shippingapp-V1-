<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shipment;
use App\Models\ShippingCompany;
use App\Models\ShipmentStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvancedShipmentsReport extends Component
{
    use WithPagination;

    // Filter Properties (Live)
    public $dateFrom;
    public $dateTo;
    public $shippingCompanyId = '';
    public $statusId = '';
    public $deliveryAgentId = '';

    // UI State
    public $perPage = 20;
    public $isLoading = false;

    // Query String parameters for shareable URLs
    protected $queryString = [
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'shippingCompanyId' => ['except' => ''],
        'statusId' => ['except' => ''],
        'deliveryAgentId' => ['except' => ''],
    ];

    public function mount()
    {
        // Set default date range (current month)
        if (!$this->dateFrom) {
            $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->dateTo) {
            $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function updatedShippingCompanyId()
    {
        $this->resetPage();
    }

    public function updatedStatusId()
    {
        $this->resetPage();
    }

    public function updatedDeliveryAgentId()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->shippingCompanyId = '';
        $this->statusId = '';
        $this->deliveryAgentId = '';
        $this->resetPage();
    }

    private function getFilteredQuery()
    {
        $query = Shipment::query()->with(['shippingCompany', 'status']);

        // Date filter
        if ($this->dateFrom && $this->dateTo) {
            $start = Carbon::parse($this->dateFrom)->startOfDay();
            $end = Carbon::parse($this->dateTo)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Company filter
        if ($this->shippingCompanyId) {
            $query->where('shipping_company_id', $this->shippingCompanyId);
        }

        // Status filter
        if ($this->statusId) {
            $query->where('status_id', $this->statusId);
        }

        // Delivery agent filter
        if ($this->deliveryAgentId) {
            $query->where('delivery_agent_id', $this->deliveryAgentId);
        }

        return $query;
    }

    public function getShipmentsProperty()
    {
        return $this->getFilteredQuery()
            ->latest('created_at')
            ->paginate($this->perPage);
    }

    public function getKpisProperty()
    {
        $cacheKey = 'shipments_kpis_' . md5(json_encode([
            $this->dateFrom,
            $this->dateTo,
            $this->shippingCompanyId,
            $this->statusId,
            $this->deliveryAgentId
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $query = clone $this->getFilteredQuery();

            $totalShipments = $query->count();

            // Find delivered status
            $deliveredStatus = ShipmentStatus::where('name', 'LIKE', '%تم التوصيل%')->first();
            $deliveredCount = 0;
            if ($deliveredStatus) {
                $deliveredCount = (clone $query)->where('status_id', $deliveredStatus->id)->count();
            }

            $deliveryRate = $totalShipments > 0 ? round(($deliveredCount / $totalShipments) * 100, 2) : 0;

            // Financial KPIs
            $totalCod = (clone $query)->sum('total_amount') ?? 0;
            $totalShippingFees = (clone $query)->sum('shipping_price') ?? 0;
            $totalSelling = (clone $query)->sum('selling_price') ?? 0;
            $totalCost = (clone $query)->sum('cost_price') ?? 0;
            $netProfit = $totalSelling - $totalCost;

            // Growth rate (compare with previous period)
            $periodLength = Carbon::parse($this->dateFrom)->diffInDays(Carbon::parse($this->dateTo));
            $previousStart = Carbon::parse($this->dateFrom)->subDays($periodLength + 1);
            $previousEnd = Carbon::parse($this->dateFrom)->subDay();
            
            $previousCount = Shipment::whereBetween('created_at', [$previousStart, $previousEnd])->count();
            $growthRate = $previousCount > 0 
                ? round((($totalShipments - $previousCount) / $previousCount) * 100, 2) 
                : 0;

            return compact(
                'totalShipments',
                'deliveredCount',
                'deliveryRate',
                'totalCod',
                'totalShippingFees',
                'totalSelling',
                'totalCost',
                'netProfit',
                'growthRate'
            );
        });
    }

    public function getChartDataProperty()
    {
        $cacheKey = 'shipments_chart_' . md5(json_encode([
            $this->dateFrom, $this->dateTo, $this->shippingCompanyId, $this->statusId
        ]));

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $data = $this->getFilteredQuery()
                ->reorder()
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'labels' => $data->pluck('date')->toArray(),
                'values' => $data->pluck('count')->toArray(),
            ];
        });
    }

    public function getCompaniesProperty()
    {
        return ShippingCompany::pluck('name', 'id');
    }

    public function getStatusesProperty()
    {
        return ShipmentStatus::pluck('name', 'id');
    }

    public function getAgentsProperty()
    {
        return User::pluck('name', 'id');
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        session()->flash('info', 'سيتم إضافة التصدير إلى Excel قريباً');
    }

    public function exportPdf()
    {
        // TODO: Implement PDF export
        session()->flash('info', 'سيتم إضافة التصدير إلى PDF قريباً');
    }

    public function render()
    {
        return view('livewire.reports.advanced-shipments');
    }
}
