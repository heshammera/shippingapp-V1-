@extends('layouts.app')

@section('title', 'تفاصيل المندوب')

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('delivery-agents.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة للقائمة
        </a>
        <a href="{{ route('delivery-agents.edit', $deliveryAgent) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('delivery-agents.shipments', $deliveryAgent) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-box"></i> عرض الشحنات
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>بيانات المندوب</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl mb-3">
                        <span class="avatar-initial rounded-circle bg-primary">
                            {{ substr($deliveryAgent->name, 0, 1) }}
                        </span>
                    </div>
                    <h4>{{ $deliveryAgent->name }}</h4>
                    <p class="text-muted">
                        {{ $deliveryAgent->shippingCompany->name ?? 'غير محدد' }}
                    </p>
                    
                    @if($deliveryAgent->is_active)
                        <span class="badge bg-success">نشط</span>
                    @else
                        <span class="badge bg-danger">غير نشط</span>
                    @endif
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-telephone"></i> رقم الهاتف</span>
                        <span>{{ $deliveryAgent->phone }}</span>
                    </li>
                    
                    @if($deliveryAgent->email)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-envelope"></i> البريد الإلكتروني</span>
                        <span>{{ $deliveryAgent->email }}</span>
                    </li>
                    @endif
                    
                    @if($deliveryAgent->national_id)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-badge"></i> الرقم القومي</span>
                        <span>{{ $deliveryAgent->national_id }}</span>
                    </li>
                    @endif
                    
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-pencil-square"></i> عدد مرات التعديل المسموح بها</span>
                        <span>{{ $deliveryAgent->max_edit_count }}</span>
                    </li>
                    
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-person-circle"></i> حساب مستخدم</span>
                        @if($deliveryAgent->user_id)
                            <span class="badge bg-success">متاح</span>
                        @else
                            <span class="badge bg-secondary">غير متاح</span>
                        @endif
                    </li>
                </ul>
                
                @if($deliveryAgent->address)
                <div class="mt-3">
                    <h6><i class="bi bi-geo-alt"></i> العنوان</h6>
                    <p>{{ $deliveryAgent->address }}</p>
                </div>
                @endif
                
                @if($deliveryAgent->notes)
                <div class="mt-3">
                    <h6><i class="bi bi-journal-text"></i> ملاحظات</h6>
                    <p>{{ $deliveryAgent->notes }}</p>
                </div>
                @endif
            </div>
            <div class="card-footer text-muted">
                <small>تاريخ الإضافة: {{ $deliveryAgent->created_at->format('Y-m-d') }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>إحصائيات الشحنات</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $shipmentStats['total'] }}</h3>
                                <p class="mb-0">إجمالي الشحنات</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $shipmentStats['delivered'] }}</h3>
                                <p class="mb-0">تم التسليم</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-danger text-white mb-3">
                            <div class="card-body text-center">
                                <h3 class="mb-0">{{ $shipmentStats['returned'] }}</h3>
                                <p class="mb-0">مرتجع</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
    <div class="card bg-primary text-white mb-3">
        <div class="card-body text-center">
            <h3 class="mb-0">{{ $shipmentStats['hold'] }}</h3>
            <p class="mb-0">عهدة أو تدوير</p>
        </div>
    </div>
</div>

                    @php
    $successRate = $shipmentStats['total'] > 0 ? round(($shipmentStats['delivered'] / $shipmentStats['total']) * 100) : 0;
@endphp
<p class="text-muted text-center">نسبة التوصيل: <strong class="text-success">{{ $successRate }}%</strong></p>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('delivery-agents.shipments', $deliveryAgent) }}" class="btn btn-primary">
                        <i class="bi bi-box"></i> عرض جميع الشحنات
                    </a>
                </div>
            </div>
        </div>
        
        @if($deliveryAgent->shipments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5>آخر الشحنات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>رقم الشحنة</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
@foreach($deliveryAgent->shipments->sortByDesc('created_at')->take(5) as $shipment)
                                <tr>
                                    <td>{{ $shipment->tracking_number }}</td>
                                    <td>{{ $shipment->customer_name }}</td>
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

<td>
    <span class="badge text-white {{ $badgeClass }}">
        {{ $shipment->status->name ?? 'غير محدد' }}
    </span>
</td>

                                    <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
