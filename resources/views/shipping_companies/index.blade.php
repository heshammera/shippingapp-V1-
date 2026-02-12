@extends('layouts.app')

@section('title', 'شركات الشحن')

@section('actions')
    <a href="{{ route('shipping-companies.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة شركة جديدة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>قائمة شركات الشحن</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($companies->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الشركة</th>
                            <th>جهة الاتصال</th>
                            <th>رقم الهاتف</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>عدد الشحنات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
@foreach($companies as $company)
    @continue(!$company instanceof \App\Models\ShippingCompany || !$company->id)

    <tr>
        <td>{{ $loop->iteration }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->contact_person ?? '-' }}</td>
                                <td>{{ $company->phone ?? '-' }}</td>
                                <td>{{ $company->email ?? '-' }}</td>
                                <td>
                                    @if($company->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>{{ $company->shipments_count ?? $company->shipments()->count() }}</td>
                                <td>
                                    <div class="btn-group" role="group">
<a href="{{ route('shipping-companies.show', ['shipping_company' => $company->id]) }}" class="btn btn-sm btn-info">
    <i class="bi bi-eye"></i>
</a>

                                        <a href="{{ route('shipping-companies.edit', ['shipping_company' => $company->id]) }}" class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>


                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $company->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal for Delete Confirmation -->
                                    <div class="modal fade" id="deleteModal{{ $company->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $company->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $company->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف شركة الشحن "{{ $company->name }}"؟
                                                    @if($company->shipments()->count() > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                            تحذير: هذه الشركة لديها {{ $company->shipments()->count() }} شحنة مرتبطة بها. حذف الشركة سيؤدي إلى حذف جميع الشحنات المرتبطة بها.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                   <form action="{{ route('shipping-companies.destroy', ['shipping_company' => $company->id]) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">حذف</button>
</form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                لا توجد شركات شحن مضافة حتى الآن. <a href="{{ route('shipping-companies.create') }}">إضافة شركة جديدة</a>
            </div>
        @endif
    </div>
</div>
@endsection
