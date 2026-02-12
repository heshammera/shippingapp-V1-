@extends('layouts.app')

@section('title', 'تقرير المصاريف')

@section('actions')
    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للمصاريف
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
        <form action="{{ route('expenses.report') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-4">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">تصفية</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">تقرير المصاريف</h5>
                <div>
                    <span class="badge bg-danger">الإجمالي: {{ number_format($total, 2) }} جنيه</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>المبلغ</th>
                                <th>تاريخ المصروف</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->id }}</td>
                                    <td>{{ $expense->title }}</td>
                                    <td>{{ number_format($expense->amount, 2) }} جنيه</td>
                                    <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td>{{ $expense->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد مصاريف</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-danger">
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
                <h5 class="card-title mb-0">ملخص المصاريف حسب الشهر</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الشهر</th>
                                <th>عدد المصاريف</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expensesByMonth as $monthData)
                                <tr>
                                    <td>{{ $monthData['month_name'] }}</td>
                                    <td>{{ $monthData['count'] }}</td>
                                    <td>{{ number_format($monthData['total'], 2) }} جنيه</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-danger">
                                <th>الإجمالي</th>
                                <th>{{ $expenses->count() }}</th>
                                <th>{{ number_format($total, 2) }} جنيه</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="mt-4">
                    <canvas id="expensesChart" width="100%" height="200"></canvas>
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
        const monthNames = {!! json_encode($expensesByMonth->pluck('month_name')) !!};
        const monthTotals = {!! json_encode($expensesByMonth->pluck('total')) !!};
        
        // إنشاء الرسم البياني
        const ctx = document.getElementById('expensesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'المصاريف الشهرية',
                    data: monthTotals,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
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
