<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-box-seam text-primary"></i> تقرير الشحنات المتقدم</h2>
            <p class="text-muted mb-0">تحليلات شاملة مع رسوم بيانية تفاعلية</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي الشحنات</p>
                            <h3 class="mb-0 fw-bold counter" data-count="{{ $totalShipments }}">0</h3>
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
                            <h3 class="mb-0 fw-bold">{{ number_format($totalCost, 2) }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ number_format($totalSelling, 2) }}</h3>
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
                            <p class="text-muted small mb-1">إجمالي الربح</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($netProfit, 2) }}</h3>
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

    {{-- Filters Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> الفلاتر</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">شركة الشحن</label>
                        <select name="shipping_company_id" class="form-select select2" data-placeholder="اختر شركة">
                            <option value="">الكل</option>
                            @foreach($shippingCompanies as $id => $name)
                                <option value="{{ $id }}" {{ request('shipping_company_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">حالة الشحنة</label>
                        <select name="status_id" class="form-select select2" data-placeholder="اختر الحالة">
                            <option value="">الكل</option>
                            @foreach($shipmentStatuses as $id => $name)
                                <option value="{{ $id }}" {{ request('status_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">المندوب</label>
                        <select name="delivery_agent_id" class="form-select select2" data-placeholder="اختر المندوب">
                            <option value="">الكل</option>
                            @foreach($deliveryAgents as $id => $name)
                                <option value="{{ $id }}" {{ request('delivery_agent_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">الفترة الزمنية</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="اختر الفترة">
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> تطبيق الفلاتر
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                            <i class="bi bi-x-circle"></i> مسح الفلاتر
                        </button>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> تصدير
                            </button>
                            <ul class="dropdown-menu">
                                <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.shipments.excel') }}" formtarget="_blank">
                                    <i class="bi bi-file-excel text-success"></i> Excel
                                </button></li>
                                <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.shipments.pdf') }}" formtarget="_blank">
                                    <i class="bi bi-file-pdf text-danger"></i> PDF
                                </button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card border-0 shadow-sm mb-4">
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
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم التتبع</th>
                            <th>العميل</th>
                            <th>شركة الشحن</th>
                            <th>المندوب</th>
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
                            <td>{{ $shipment->deliveryAgent->name ?? '-' }}</td>
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
                            <td>{{ $shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
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
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        dir: 'rtl',
        language: 'ar'
    });

    // Initialize Flatpickr
    const fp = flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: "ar",
        defaultDate: [
            document.getElementById('date_from').value,
            document.getElementById('date_to').value
        ],
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('date_from').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                document.getElementById('date_to').value = instance.formatDate(selectedDates[1], 'Y-m-d');
            }
        }
    });

    // Clear filters
    document.getElementById('clearFilters').addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });

    // CountUp for KPIs
    document.querySelectorAll('.counter').forEach(function(counter) {
        const count = parseInt(counter.getAttribute('data-count'));
        if (typeof CountUp !== 'undefined') {
            const countUp = new CountUp(counter, count);
            countUp.start();
        } else {
            counter.textContent = count;
        }
    });

    // ApexCharts
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);
    
    const options = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
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
                rotateAlways: false,
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
            x: {
                format: 'dd/MM/yyyy'
            },
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

    const chart = new ApexCharts(document.querySelector("#shipmentsChart"), options);
    chart.render();
});
</script>
@endpush
