@extends('layouts.app')

@section('title', 'لوحة الحسابات')

@section('actions')
    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-cash-coin"></i> التحصيلات
    </a>
    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-danger">
        <i class="bi bi-cash"></i> المصاريف
    </a>
    <a href="{{ route('accounting.treasury-report') }}" class="btn btn-sm btn-success">
        <i class="bi bi-file-earmark-text"></i> تقرير الخزنة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تصفية البيانات</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('accounting.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">إجمالي التحصيلات</h5>
                        <p class="mb-0">خلال الفترة المحددة</p>
                    </div>
                    <h2 class="mb-0">{{ number_format($totalCollections, 2) }}</h2>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="{{ route('collections.index') }}">عرض التفاصيل</a>
                <div class="small text-white"><i class="bi bi-arrow-left"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">إجمالي المصاريف</h5>
                        <p class="mb-0">خلال الفترة المحددة</p>
                    </div>
                    <h2 class="mb-0">{{ number_format($totalExpenses, 2) }}</h2>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="{{ route('expenses.index') }}">عرض التفاصيل</a>
                <div class="small text-white"><i class="bi bi-arrow-left"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card {{ $balance >= 0 ? 'bg-success' : 'bg-danger' }} text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">رصيد الخزنة</h5>
                        <p class="mb-0">التحصيلات - المصاريف</p>
                    </div>
                    <h2 class="mb-0">{{ number_format($balance, 2) }}</h2>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="{{ route('accounting.treasury-report') }}">عرض تقرير الخزنة</a>
                <div class="small text-white"><i class="bi bi-arrow-left"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">الرسم البياني للتحصيلات والمصاريف</h5>
            </div>
            <div class="card-body">
                <canvas id="financialChart" width="100%" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">التحصيلات حسب شركة الشحن</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>الشركة</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collectionsByCompany as $companyCollection)
                                <tr>
<td>{{ $companyCollection->shippingCompany->name ?? 'غير محدد' }}</td>
                                    <td>{{ number_format($companyCollection->total_amount, 2) }} جنيه</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">أحدث التحصيلات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>الشركة</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestCollections as $collection)
                                <tr>
                                    <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
                                    <td>{{ $collection->shippingCompany->name ?? '-'  }}</td>
                                    <td>{{ number_format($collection->amount, 2) }} جنيه</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد تحصيلات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-primary">عرض كل التحصيلات</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">أحدث المصاريف</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>العنوان</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestExpenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ number_format($expense->amount, 2) }} جنيه</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد مصاريف</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-danger">عرض كل المصاريف</a>
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
        const labels = {!! json_encode($chartData['labels']) !!};
        const collectionsData = {!! json_encode($chartData['collections']) !!};
        const expensesData = {!! json_encode($chartData['expenses']) !!};
        const balanceData = {!! json_encode($chartData['balance']) !!};
        
        // إنشاء الرسم البياني
        const ctx = document.getElementById('financialChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'التحصيلات',
                        data: collectionsData,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'المصاريف',
                        data: expensesData,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'الرصيد',
                        data: balanceData,
                        type: 'line',
                        fill: false,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.raw || 0;
                                return `${label}: ${value.toFixed(2)} جنيه`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
