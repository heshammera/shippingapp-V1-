@extends('layouts.advanced_reports')

@section('title', 'تقرير الخزنة المتقدم')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-safe text-info"></i> تقرير الخزنة المتقدم</h2>
            <p class="text-muted mb-0">متابعة دقيقة للإيرادات والمصروفات والرصيد</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Collections --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي الإيرادات</p>
                            <h3 class="mb-0 fw-bold text-success">+{{ number_format($totalIncome, 2) }}</h3>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-arrow-down-left text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Expenses --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي المصروفات</p>
                            <h3 class="mb-0 fw-bold text-danger">-{{ number_format($totalExpense, 2) }}</h3>
                        </div>
                        <div class="icon-box bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-arrow-up-right text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Balance --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body bg-gradient text-white" style="background: linear-gradient(45deg, #0dcaf0, #0d6efd);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="text-white-50 small mb-1">الرصيد الحالي</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($netBalance, 2) }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="bi bi-wallet2 text-white fs-4"></i>
                        </div>
                    </div>
                    @if(isset($comparisonData))
                    <small class="text-white-50">
                        <i class="bi bi-{{ $comparisonData['is_positive'] ? 'graph-up-arrow' : 'graph-down-arrow' }} me-1"></i>
                        {{ $comparisonData['percent_change'] }}% مقارنة بالفترة السابقة
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" id="filterForm">
                <div class="row align-items-end g-3">
                    <div class="col-md-8">
                        <label class="form-label">الفترة الزمنية</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                            <input type="text" class="form-control" id="dateRange" placeholder="اختر الفترة">
                        </div>
                        <input type="hidden" name="date_from" id="date_from" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ $dateTo }}">
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-filter"></i> تصفية
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i> تصدير
                                </button>
                                <ul class="dropdown-menu">
                                    <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.treasury.excel') }}" formtarget="_blank">Excel</button></li>
                                    <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.treasury.pdf') }}" formtarget="_blank">PDF</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> التدفق النقدي والرصيد</h5>
        </div>
        <div class="card-body">
            <div id="treasuryChart" style="min-height: 400px;"></div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> سجل المعاملات</h5>
                <span class="badge bg-secondary">{{ count($allTransactions) }} عملية</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>البيان</th>
                            <th class="text-success">إيرادات</th>
                            <th class="text-danger">مصروفات</th>
                            <th class="fw-bold">الرصيد</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction['date'] }}</td>
                            <td>
                                @if($transaction['type'] == 'collection')
                                    <span class="badge bg-success bg-opacity-10 text-success">تحصيل</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">مصروف</span>
                                @endif
                            </td>
                            <td>{{ $transaction['description'] }}</td>
                            <td class="text-success">
                                @if($transaction['type'] == 'collection')
                                    {{ number_format($transaction['amount'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-danger">
                                @if($transaction['type'] == 'expense')
                                    {{ number_format($transaction['amount'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($transaction['running_balance'], 2) }}</td>
                            <td class="text-muted small">{{ Str::limit($transaction['notes'], 50) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">لا توجد معاملات في هذه الفترة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="3">الإجمالي</td>
                            <td class="text-success">{{ number_format($totalIncome, 2) }}</td>
                            <td class="text-danger">{{ number_format($totalExpense, 2) }}</td>
                            <td class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netBalance, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date Range Picker
    flatpickr("#dateRange", {
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

    // Chart
    const chartData = @json($chartData);
    
    const options = {
        chart: {
            height: 400,
            type: 'line',
            toolbar: { show: true },
            zoom: { enabled: true }
        },
        series: [{
            name: 'الإيرادات',
            type: 'column',
            data: chartData.collections
        }, {
            name: 'المصروفات',
            type: 'column',
            data: chartData.expenses
        }, {
            name: 'الرصيد التراكمي',
            type: 'line',
            data: chartData.balance
        }],
        stroke: {
            width: [0, 0, 4],
            curve: 'smooth'
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                borderRadius: 4
            }
        },
        colors: ['#198754', '#dc3545', '#0dcaf0'],
        xaxis: {
            categories: chartData.labels,
            labels: {
                rotate: -45,
                rotateAlways: false,
                style: { fontFamily: 'Cairo' }
            }
        },
        yaxis: [{
            title: { text: 'المعاملات اليومية' },
            labels: { formatter: (val) => val.toFixed(0) }
        }, {
            opposite: true,
            title: { text: 'الرصيد التراكمي' },
            labels: { formatter: (val) => val.toFixed(0) }
        }],
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toFixed(2) + " جنيه";
                }
            }
        },
        legend: {
            position: 'top'
        }
    };

    const chart = new ApexCharts(document.querySelector("#treasuryChart"), options);
    chart.render();
});
</script>
@endpush
