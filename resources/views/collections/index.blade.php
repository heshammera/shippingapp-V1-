@extends('layouts.app')

@section('title', 'التحصيلات')

@section('actions')
    <a href="{{ route('collections.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة تحصيل جديد
    </a>
    <a href="{{ route('collections.report') }}" class="btn btn-sm btn-outline-secondary me-2">
        <i class="bi bi-file-earmark-text"></i> تقرير التحصيلات
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">تصفية التحصيلات</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('collections.index') }}" method="GET" class="row g-3">
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">قائمة التحصيلات</h5>
        <div>
            <span class="badge bg-primary">الإجمالي: {{ $collections->sum('amount') }} جنيه</span>
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
                        <th>تم بواسطة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($collections as $collection)
    <tr>
        <td>{{ $collection->id }}</td>
        <td>{{ $collection->shippingCompany->name ?? '-' }}</td>
        <td>{{ $collection->amount }} جنيه</td>
        <td>{{ $collection->collection_date->format('Y-m-d') }}</td>
        <td>{{ $collection->notes ?? '-' }}</td>
        <td>{{ $collection->createdBy->name }}</td>
        <td>
            @if($collection->id)
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('collections.show', ['collection' => $collection->id]) }}" class="btn btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('collections.edit', ['collection' => $collection->id]) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $collection->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <!-- Modal for Delete Confirmation -->
                <div class="modal fade" id="deleteModal{{ $collection->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $collection->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $collection->id }}">تأكيد الحذف</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                هل أنت متأكد من حذف هذا التحصيل؟
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('collections.destroy', ['collection' => $collection->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">حذف</button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </td>
    </tr>
@endforeach

                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $collections->links() }}
        </div>
    </div>
</div>
@endsection
