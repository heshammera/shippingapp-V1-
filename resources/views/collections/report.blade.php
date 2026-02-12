@extends('layouts.app')

@section('title', 'تقرير التحصيلات')

@section('actions')
    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للتحصيلات
    </a>
    <button class="btn btn-sm btn-success" onclick="window.print()">
        <i class="bi bi-printer"></i> طباعة التقرير
    </button>
    <a href="#" class="btn btn-sm btn-primary" id="exportPdf">
        <i class="bi bi-file-pdf"></i> تصدير PDF
    </a>
    <a href="#" class="btn btn-sm btn-success" id="exportExcel">
        <i class="bi bi-file-excel"></i> تصدير Excel
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تصفية التقرير</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('collections.report') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="shipping_company_id" class="form-label">شركة الشحن</label>
                <select name="shipping_company_id" id="shipping_company_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($shippingCompanies as $company)
                        <option value="{{ $company->id }}" {{ request('shipping_company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">تقرير التحصيلات</h5>
                <div>
                    <span class="badge bg-primary">الإجمالي: {{ number_format($total, 2) }} جنيه</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>شركة الشحن</th>
                                <th>المبلغ</th>
                                <th>تاريخ التحصيل</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collections as $collection)
                                <tr>
                                    <td>{{ $collection->id }}</td>
                                    <td>{{ $collection->shippingCompany->name }}</td>
                                    <td>{{ number_format($collection->amount, 2) }} جنيه</td>
                                    <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
                                    <td>{{ $collection->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد تحصيلات</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="2">الإجمالي</th>
                                <th>{{ number_format($total, 2) }} جنيه</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">ملخص التحصيلات حسب الشركة</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الشركة</th>
                                <th>عدد التحصيلات</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collectionsByCompany as $companyData)
                                <tr>
                                    <td>{{ $companyData['company_name'] }}</td>
                                    <td>{{ $companyData['count'] }}</td>
                                    <td>{{ number_format($companyData['total'], 2) }} جنيه</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th>الإجمالي</th>
                                <th>{{ $collections->count() }}</th>
                                <th>{{ number_format($total, 2) }} جنيه</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-4">
                    <canvas id="collectionsChart" width="100%" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // بيانات الرسم البياني
        const companyNames = {!! json_encode($collectionsByCompany->pluck('company_name')) !!};
        const companyTotals = {!! json_encode($collectionsByCompany->pluck('total')) !!};
        
        // إنشاء الرسم البياني
        const ctx = document.getElementById('collectionsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: companyNames,
                datasets: [{
                    data: companyTotals,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796',
                        '#5a5c69', '#6f42c1', '#20c9a6', '#fd7e14'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b', '#6b6d7d',
                        '#484a54', '#5d36a4', '#1aa88f', '#f57102'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                return `${label}: ${value.toFixed(2)} جنيه`;
                            }
                        }
                    }
                },
            },
        });
    });
</script>
@endsection
