@extends('layouts.app')

@section('title', 'تفاصيل المصروف')

@section('actions')
    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للمصاريف
    </a>
    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil"></i> تعديل
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تفاصيل المصروف</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">رقم المصروف</th>
                        <td>{{ $expense->id }}</td>
                    </tr>
                    <tr>
                        <th>عنوان المصروف</th>
                        <td>{{ $expense->title }}</td>
                    </tr>
                    <tr>
                        <th>المبلغ</th>
                        <td>{{ $expense->amount }} جنيه</td>
                    </tr>
                    <tr>
                        <th>تاريخ المصروف</th>
                        <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>ملاحظات</th>
                        <td>{{ $expense->notes ?? 'لا توجد ملاحظات' }}</td>
                    </tr>
                    <tr>
                        <th>تم بواسطة</th>
                        <td>{{ $expense->createdBy->name }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء</th>
                        <td>{{ $expense->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>آخر تحديث</th>
                        <td>{{ $expense->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
