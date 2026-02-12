<div>
    {{-- Loading Overlay --}}
    <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.8); z-index: 9999;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-box-seam text-primary"></i> تقرير الشحنات المتقدم</h2>
            <p class="text-muted mb-0">تحليلات شاملة مع رسوم بيانية تفاعلية</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4" wire:loading.class="opacity-50">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي الشحنات</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($kpis['totalShipments']) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-box-seam text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي التكلفة</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($kpis['totalCost'], 2) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-cash-stack text-info fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي البيع</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($kpis['totalSelling'], 2) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-currency-dollar text-warning fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">صافي الربح</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($kpis['netProfit'], 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-graph-up-arrow text-success fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Live Filters Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> الفلاتر المباشرة</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">شركة الشحن</label>
                    <div class="position-relative">
                        <select wire:model.live="shippingCompanyId" class="form-select">
                            <option value="">الكل</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="shippingCompanyId" class="position-absolute top-50 end-0 translate-middle-y me-2" style="pointer-events: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">حالة الشحنة</label>
                    <div class="position-relative">
                        <select wire:model.live="statusId" class="form-select">
                            <option value="">الكل</option>
                            @foreach($statuses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="statusId" class="position-absolute top-50 end-0 translate-middle-y me-2" style="pointer-events: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">المندوب</label>
                    <div class="position-relative">
                        <select wire:model.live="deliveryAgentId" class="form-select">
                            <option value="">الكل</option>
                            @foreach($agents as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="deliveryAgentId" class="position-absolute top-50 end-0 translate-middle-y me-2" style="pointer-events: none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">الفترة الزمنية</label>
                    <div class="row g-2">
                        <div class="col">
                            <input type="date" wire:model.live.debounce.500ms="dateFrom" class="form-control" placeholder="من">
                        </div>
                        <div class="col">
                            <input type="date" wire:model.live.debounce.500ms="dateTo" class="form-control" placeholder="إلى">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div>
                    <button wire:click="resetFilters" type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> مسح الفلاتر
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> تصدير
                    </button>
                    <ul class="dropdown-menu">
                        <li><button wire:click="exportExcel" type="button" class="dropdown-item"><i class="bi bi-file-excel"></i> Excel</button></li>
                        <li><button wire:click="exportPdf" type="button" class="dropdown-item"><i class="bi bi-file-pdf"></i> PDF</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card border-0 shadow-sm mb-4" wire:loading.class="opacity-50">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> الشحنات عبر الزمن</h5>
        </div>
        <div class="card-body">
            <div id="shipmentsChart" style="min-height: 350px;"></div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bi bi-table"></i> قائمة الشحنات</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" wire:loading.class="opacity-50">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم التتبع</th>
                            <th>العميل</th>
                            <th>شركة الشحن</th>
                            <th>الحالة</th>
                            <th>التكلفة</th>
                            <th>البيع</th>
                            <th>الربح</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $index => $shipment)
                        <tr>
                            <td>{{ $shipments->firstItem() + $index }}</td>
                            <td><span class="badge bg-secondary">{{ $shipment->tracking_number }}</span></td>
                            <td>{{ $shipment->customer_name }}</td>
                            <td>{{ $shipment->shippingCompany->name ?? '-' }}</td>
                            <td>
                                @if($shipment->status)
                                <span class="badge" style="background-color: {{ $shipment->status->color ?? '#6c757d' }}">
                                    {{ $shipment->status->name }}
                                </span>
                                @else
                                <span class="badge bg-secondary">غير محدد</span>
                                @endif
                            </td>
                            <td>{{ number_format($shipment->cost_price, 2) }}</td>
                            <td>{{ number_format($shipment->selling_price, 2) }}</td>
                            <td class="{{ ($shipment->selling_price - $shipment->cost_price) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($shipment->selling_price - $shipment->cost_price, 2) }}
                            </td>
                            <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">لا توجد شحنات</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($shipments->hasPages())
        <div class="card-footer bg-white">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    renderChart();
});

// Listen for Livewire updates
window.addEventListener('load', function() {
    Livewire.hook('commit', ({component, commit, respond, succeed, fail}) => {
        succeed(({ snapshot, effect }) => {
            // Re-render chart after Livewire update
            setTimeout(renderChart, 100);
        });
    });
});

function renderChart() {
    const chartLabels = @json($chartData['labels'] ?? []);
    const chartValues = @json($chartData['values'] ?? []);
    
    if (chartLabels.length === 0) return;

    const options = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    zoom: true,
                    pan: true,
                    reset: true
                }
            },
            animations: {
                enabled: true,
                speed: 800
            }
        },
        series: [{
            name: 'عدد الشحنات',
            data: chartValues
        }],
        xaxis: {
            categories: chartLabels,
            labels: {
                rotate: -45,
                style: {
                    fontFamily: 'Cairo, sans-serif'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#0d6efd'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " شحنة";
                }
            }
        },
        grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 4
        }
    };

    // Clear existing chart
    document.querySelector("#shipmentsChart").innerHTML = '';
    
    const chart = new ApexCharts(document.querySelector("#shipmentsChart"), options);
    chart.render();
}
</script>
@endpush
