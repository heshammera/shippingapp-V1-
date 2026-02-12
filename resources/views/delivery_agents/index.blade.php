@extends('layouts.app')

@section('title', 'إدارة المندوبين')

@section('actions')
    <a href="{{ route('delivery-agents.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-person-plus"></i> إضافة مندوب جديد
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>قائمة المندوبين</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('delivery-agents.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="بحث بالاسم أو رقم الهاتف" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="company" class="form-select">
                        <option value="">كل الشركات</option>
                        @foreach($shippingCompanies as $company)
                            <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">الحالة</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('delivery-agents.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>

        @if($deliveryAgents->count() > 0)
            <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle text-center">

                    <thead>
    <tr>
        <th>#</th>
        <th>الاسم</th>
        <th>رقم الهاتف</th>
        <th>شركة الشحن</th>
        <th>تم التوصيل</th>
        <th>مرتجع</th>
        <th>عهدة</th>
        <th>حساب مستخدم</th>
        <th>الحالة</th>
        <th>الإجراءات</th>
    </tr>
</thead>

                    <tbody>
                        @foreach($deliveryAgents as $agent)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $agent->name }}</td>
                                <td>{{ $agent->phone }}</td>
                                <td>{{ $agent->shippingCompany->name ?? 'غير محدد' }}</td>
<td><span class="badge bg-success">{{ $agent->delivered_count }}</span></td>
<td><span class="badge bg-danger">{{ $agent->returned_count }}</span></td>
<td><span class="badge bg-info text-dark">{{ $agent->hold_count }}</span></td>

                                <td>
                                    @if($agent->user_id)
                                        <span class="badge bg-success">متاح</span>
                                    @else
                                        <span class="badge bg-secondary">غير متاح</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group flex-wrap" role="group">

                                        <a href="{{ route('delivery-agents.show', $agent) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('delivery-agents.edit', $agent) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/delivery-agents/{{ $agent->id }}/shipments" class="btn btn-sm btn-secondary">

                                            <i class="bi bi-box"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $agent->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Modal for Delete Confirmation -->
                                    <div class="modal fade" id="deleteModal{{ $agent->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $agent->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $agent->id }}">تأكيد الحذف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    هل أنت متأكد من حذف المندوب "{{ $agent->name }}"؟
                                                    @if($agent->user_id)
                                                        <div class="alert alert-warning mt-2">
                                                            <i class="bi bi-exclamation-triangle"></i>
                                                            سيتم أيضاً حذف حساب المستخدم المرتبط بهذا المندوب.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('delivery-agents.destroy', $agent) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
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
            
            <div class="d-flex justify-content-center mt-3">
                {{ $deliveryAgents->links() }}
            </div>
        @else
            <div class="alert alert-info">
                لا يوجد مندوبين مضافين حتى الآن. <a href="{{ route('delivery-agents.create') }}">إضافة مندوب جديد</a>
            </div>
        @endif
    </div>
</div>
@endsection
