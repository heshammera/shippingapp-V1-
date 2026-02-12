@extends('layouts.app')

@section('title', 'المصاريف')

@section('actions')
    <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة مصروف جديد
    </a>
<a href="{{ route('expenses.report') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="bi bi-file-earmark-text"></i> تقرير المصاريف
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تصفية المصاريف</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('expenses.index') }}" method="GET" class="row g-3">
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">قائمة المصاريف</h5>
        <div>
            <span class="badge bg-danger">الإجمالي: {{ $expenses->sum('amount') }} جنيه</span>
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
                        <th>تم بواسطة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->id }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->amount }} جنيه</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>{{ $expense->notes ?? '-' }}</td>
                            <td>{{ $expense->createdBy->name }}</td>
                            <td>
<div class="btn-group btn-group-sm">
    {{-- زر عرض --}}
    <a href="{{ $expense->id ? route('expenses.show', $expense->id) : '#' }}" 
       class="btn btn-info {{ !$expense->id ? 'disabled' : '' }}">
        <i class="bi bi-eye"></i>
    </a>

    {{-- زر تعديل --}}
    <a href="{{ $expense->id ? route('expenses.edit', $expense->id) : '#' }}" 
       class="btn btn-primary {{ !$expense->id ? 'disabled' : '' }}">
        <i class="bi bi-pencil"></i>
    </a>

    {{-- زر حذف --}}
    @if($expense->id)
        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
            @csrf
            @method('DELETE')
             <button type="submit" class="btn btn-danger" data-bs-toggle="modal">
                 <i class="bi bi-trash"></i>
                 </button>
        </form>
    @else
        <button type="button" class="btn btn-danger" disabled>حذف</button>
    @endif
</div>

                                
                                <!-- Modal for Delete Confirmation -->
                                <div class="modal fade" id="deleteModal{{ $expense->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $expense->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $expense->id }}">تأكيد الحذف</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                هل أنت متأكد من حذف هذا المصروف؟
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
<form action="{{ $expense->id ? route('expenses.destroy', $expense->id) : '#' }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" {{ !$expense->id ? 'disabled' : '' }}>حذف</button>
</form>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد مصاريف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection
