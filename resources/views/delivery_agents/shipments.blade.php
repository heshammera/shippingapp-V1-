@extends('layouts.app')

@section('title', 'شحنات المندوب')

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('delivery-agents.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة لقائمة المندوبين
        </a>
        <a href="{{ route('delivery-agents.show', $agent) }}">
            <i class="bi bi-person"></i> عرض بيانات المندوب
        </a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>شحنات المندوب: {{ $agent->name }}</h5>
        <span class="badge bg-primary">إجمالي الشحنات: {{ $shipments->total() }}</span>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('delivery-agents.shipments', $agent) }}" method="GET" class="row g-3">

                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" placeholder="بحث برقم الشحنة أو اسم العميل" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" placeholder="من تاريخ" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>

        @if($shipments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم الشحنة</th>
                            <th>العميل</th>
                            <th>رقم الهاتف</th>
                            <th>العنوان</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                   <tbody>
    @foreach($shipments as $shipment)
        @php
        
            $color = $shipment->status->row_color ?? '#ffffff';
            $total = $shipment->products->sum(fn($p) => $p->pivot->price * $p->pivot->quantity);
        @endphp
        <tr>
            <td style="background-color: {{ $color }}">{{ $loop->iteration }}</td>
            <td style="background-color: {{ $color }}">{{ $shipment->tracking_number }}</td>
            <td style="background-color: {{ $color }}">{{ $shipment->customer_name }}</td>
            <td style="background-color: {{ $color }}">{{ $shipment->customer_phone }}</td>
            <td style="background-color: {{ $color }}">{{ Str::limit($shipment->customer_address, 30) }}</td>
            <td style="background-color: {{ $color }}">{{ number_format($total, 2) }} جنيه</td>
            <td style="background-color: {{ $color }}">
@php
    $bootstrapColors = [
        'table-success' => 'bg-success',
        'table-danger' => 'bg-danger',
        'table-primary' => 'bg-primary',
        'table-warning' => 'bg-warning',
        'table-dark' => 'bg-dark',
        'table-light' => 'bg-light',
    ];

    $badgeClass = $bootstrapColors[$shipment->status->color] ?? 'bg-secondary';
@endphp

<span class="badge text-white {{ $badgeClass }}">
    {{ $shipment->status->name ?? 'غير محدد' }}
</span>



            </td>
            <td style="background-color: {{ $color }}">{{ $shipment->created_at->format('Y-m-d') }}</td>
            <td style="background-color: {{ $color }}">
                <div class="btn-group" role="group">
                    <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal{{ $shipment->id }}">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="updateStatusModal{{ $shipment->id }}" tabindex="-1" aria-labelledby="updateStatusModalLabel{{ $shipment->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateStatusModalLabel{{ $shipment->id }}">تحديث حالة الشحنة</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('shipments.update-status', $shipment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="status_id{{ $shipment->id }}" class="form-label">الحالة</label>
                                        <select class="form-select" id="status_id{{ $shipment->id }}" name="status_id" required>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->id }}" {{ $shipment->status_id == $status->id ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="agent_notes{{ $shipment->id }}" class="form-label">ملاحظات المندوب</label>
                                        <textarea class="form-control" id="agent_notes{{ $shipment->id }}" name="agent_notes" rows="3">{{ $shipment->agent_notes }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>

                </table>
                @php
    $totalDelivered = $shipments->filter(fn($s) => $s->status->name === 'تم التوصيل')
        ->flatMap->products
        ->sum(fn($p) => $p->pivot->quantity * $p->pivot->price);
@endphp

<div class="card mt-4 shadow-sm border-success">
    <div class="card-body d-flex justify-content-between align-items-center">
        <h6 class="mb-0">إجمالي السعر للشحنات <span class="badge bg-success">تم التوصيل</span></h6>
        <h5 class="text-success mb-0 fw-bold">{{ number_format($totalDelivered, 2) }} ج.م</h5>
    </div>
</div>

            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $shipments->links() }}
            </div>
        @else
            <div class="alert alert-info">
                لا توجد شحنات مسجلة لهذا المندوب حتى الآن.
            </div>
        @endif
    </div>
</div>
@endsection
