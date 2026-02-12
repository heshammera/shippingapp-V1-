@extends('layouts.app')

@section('title', 'تقرير المصاريف')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📊 تقرير المصاريف</h4>
    <div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">🔙 العودة للتقارير</a>
<a href="{{ route('reports.expenses.print', request()->query()) }}" target="_blank" class="btn btn-success">
    🖨 طباعة التقرير
</a>

<a href="{{ route('reports.expenses.pdf', request()->query()) }}" target="_blank" class="btn btn-danger">
    📄 تصدير PDF
</a>

<a href="{{ route('reports.expenses.excel', request()->query()) }}" target="_blank" class="btn btn-primary">
    📥 تصدير Excel
</a>

    </div>
</div>

<form method="GET" action="{{ route('reports.expenses') }}" class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="date_from" class="form-label">من تاريخ</label>
        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
    </div>
    <div class="col-md-4">
        <label for="date_to" class="form-label">إلى تاريخ</label>
        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
    </div>
    <div class="col-md-4 align-self-end">
        <button type="submit" class="btn btn-primary w-100">🔍 تصفية</button>
    </div>
</form>

<div class="row text-center mb-4">
    <div class="col-md-3">
        <div class="bg-danger text-white rounded py-3 shadow-sm">
            <h5>إجمالي المصاريف</h5>
            <h3>{{ number_format($total_expenses, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bg-info text-white rounded py-3 shadow-sm">
            <h5>عدد العمليات</h5>
            <h3>{{ $expenses->count() }}</h3>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white">
        قائمة المصاريف
    </div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>التاريخ</th>
                    <th>الوصف</th>
                    <th>المبلغ</th>
                    <th>الموظف/المستخدم</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
<td>{{ $expense->expense_date ?? 'غير محدد' }}</td>
<td>{{ $expense->title ?? '-' }}</td>
<td>{{ number_format($expense->amount, 2) }}</td>
<td>{{ $expense->user->name ?? 'غير معروف' }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">لا توجد مصاريف في هذه الفترة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
