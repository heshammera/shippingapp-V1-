@extends('layouts.app')

@section('title', 'تقرير الخزنة (PDF)')

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <h1>تقرير الخزنة</h1>
        <p>
            من تاريخ: {{ $dateFrom }}
            إلى تاريخ: {{ $dateTo }}
        </p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">إجمالي التحصيلات</h5>
                        </div>
                        <h2 class="mb-0">{{ number_format($totalCollections, 2) }} جنيه</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">إجمالي المصاريف</h5>
                        </div>
                        <h2 class="mb-0">{{ number_format($totalExpenses, 2) }} جنيه</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">رصيد الخزنة</h5>
                        </div>
                        <h2 class="mb-0">{{ number_format($balance, 2) }} جنيه</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>البيان</th>
                    <th>الإيرادات</th>
                    <th>المصروفات</th>
                    <th>الرصيد</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction['date'] }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td>
                            @if($transaction['type'] == 'collection')
                                {{ number_format($transaction['amount'], 2) }} جنيه
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($transaction['type'] == 'expense')
                                {{ number_format($transaction['amount'], 2) }} جنيه
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($transaction['running_balance'], 2) }} جنيه</td>
                        <td>{{ $transaction['notes'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">لا توجد معاملات</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">الإجمالي</th>
                    <th>{{ number_format($totalCollections, 2) }} جنيه</th>
                    <th>{{ number_format($totalExpenses, 2) }} جنيه</th>
                    <th>{{ number_format($balance, 2) }} جنيه</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
