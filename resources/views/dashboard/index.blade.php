@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">إجمالي الشحنات</h6>
                        <h2 class="mb-0">{{ $totalShipments }}</h2>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('shipments.index') }}" class="text-white-50 stretched-link">عرض التفاصيل</a>
                <i class="bi bi-chevron-left text-white-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">تم التسليم</h6>
                        <h2 class="mb-0">{{ $shipmentsByStatus->where('status_id', 1)->first()->total ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('shipments.index', ['status' => 1]) }}" class="text-white-50 stretched-link">عرض التفاصيل</a>
                <i class="bi bi-chevron-left text-white-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">مرتجع</h6>
                        <h2 class="mb-0">{{ $shipmentsByStatus->where('status_id', 2)->first()->total ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-arrow-return-left fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('shipments.index', ['status' => 2]) }}" class="text-white-50 stretched-link">عرض التفاصيل</a>
                <i class="bi bi-chevron-left text-white-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">عُهدة</h6>
                        <h2 class="mb-0">{{ $shipmentsByStatus->where('status_id', 3)->first()->total ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('shipments.index', ['status' => 3]) }}" class="text-white-50 stretched-link">عرض التفاصيل</a>
                <i class="bi bi-chevron-left text-white-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>إحصائيات آخر 6 أشهر</h5>
            </div>
            <div class="card-body">
                <canvas id="shipmentsChart" height="150"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>الربح الكلي</h5>
            </div>
            <div class="card-body text-center">
<h1 class="display-4 text-success fw-bold">{{ number_format($totalProfit, 2) }} ج.م</h1>
                <p class="text-muted">إجمالي الربح من الشحنات المسلمة</p>
                
                <hr>
                
                <h5>الشهر الحالي</h5>
                <h3 class="text-primary">{{ number_format($currentMonthProfit, 2) }} ج.م</h3>
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <p class="mb-0 text-muted">شحنات</p>
                        <h5>{{ $currentMonthShipments }}</h5>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">تم التسليم</p>
                        <h5 class="text-success">{{ $currentMonthDelivered }}</h5>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">مرتجع</p>
                        <h5 class="text-danger">{{ $currentMonthReturned }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header bg-danger text-white">
        🔔 منتجات قليلة المخزون
    </div>
    <div class="card-body">
        @if($lowItems->isEmpty())
            <p>لا توجد منتجات قليلة حالياً 🎉</p>
        @else
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>اللون</th>
                        <th>المقاس</th>
                        <th>الكمية</th>
                        <th>حد التنبيه</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->color }}</td>
                            <td>{{ $item->size }}</td>
                            <td class="text-danger fw-bold">{{ $item->quantity }}</td>
                            <td>{{ $item->low_stock_alert }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>شركات الشحن</h5>
                <a href="{{ route('shipping-companies.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الشركة</th>
                                <th>عدد الشحنات</th>
                                <th>المبلغ المستحق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipmentsByCompany as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->shipments_count }}</td>
                                    <td>
                                        {{ number_format($amountsByCompany->where('id', $company->id)->first()->total_amount ?? 0, 2) }} ج.م
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>الشحنات المتأخرة (أكثر من 10 أيام)</h5>
                <span class="badge bg-danger">{{ $delayedShipments->count() }}</span>
            </div>
            <div class="card-body">
                @if($delayedShipments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم التتبع</th>
                                    <th>العميل</th>
                                    <th>الشركة</th>
                                    <th>المندوب</th>
                                    <th>تاريخ الشحن</th>
                                    <th>المدة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delayedShipments as $shipment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('shipments.show', $shipment) }}">{{ $shipment->tracking_number }}</a>
                                        </td>
                                        <td>{{ $shipment->customer_name }}</td>
                                        <td>{{ $shipment->shippingCompany->name ?? '-' }}</td>
                                        <td>{{ $shipment->deliveryAgent->name ?? '-' }}</td>
                                        <td>{{ date('Y-m-d', strtotime($shipment->shipping_date)) }}</td>
                                        <td>{{ now()->diffInDays($shipment->shipping_date) }} يوم</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> لا توجد شحنات متأخرة
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // بيانات الرسم البياني
        const months = @json($last6Months->pluck('month'));
const deliveredData = @json($last6Months->pluck('delivered'));
const returnedData = @json($last6Months->pluck('returned'));
const profitData = @json($last6Months->pluck('profit'));

        // إنشاء الرسم البياني
        const ctx = document.getElementById('shipmentsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'تم التسليم',
                        data: deliveredData,
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'مرتجع',
                        data: returnedData,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'الربح (ج.م)',
                        data: profitData,
                        type: 'line',
                        yAxisID: 'y2',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.4)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }

                ]
            },
            options: {
    responsive: true,
    scales: {
        y: {
            beginAtZero: true,
            position: 'left'
        },
        y2: {
            beginAtZero: true,
            position: 'right',
            grid: { drawOnChartArea: false }
        }
    }
}

        });
    });
</script>
@endsection
