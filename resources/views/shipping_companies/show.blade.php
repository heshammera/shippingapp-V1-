@extends('layouts.app')

@section('title', 'تفاصيل شركة الشحن')

@section('actions')
    <div class="btn-group" role="group">
        <a href="{{ route('shipping-companies.edit', $shippingCompany) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> تعديل
        </a>
        <a href="{{ route('shipping-companies.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-right"></i> العودة للقائمة
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>معلومات الشركة</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>اسم الشركة</th>
                        <td>{{ $shippingCompany->name }}</td>
                    </tr>
                    <tr>
                        <th>جهة الاتصال</th>
                        <td>{{ $shippingCompany->contact_person ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>رقم الهاتف</th>
                        <td>{{ $shippingCompany->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني</th>
                        <td>{{ $shippingCompany->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>العنوان</th>
                        <td>{{ $shippingCompany->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>الحالة</th>
                        <td>
                            @if($shippingCompany->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-danger">غير نشط</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>تاريخ الإضافة</th>
                        <td>{{ $shippingCompany->created_at->format('Y-m-d') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>إحصائيات</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="h3 mb-0">{{ $shippingCompany->shipments()->count() }}</div>
                        <div class="small text-muted">إجمالي الشحنات</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h3 mb-0">{{ $shippingCompany->deliveryAgents()->count() }}</div>
                        <div class="small text-muted">المندوبين</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h3 mb-0">{{ $shippingCompany->shipments()->where('status_id', 1)->count() }}</div>
                        <div class="small text-muted">تم التسليم</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h3 mb-0">{{ $shippingCompany->shipments()->where('status_id', 2)->count() }}</div>
                        <div class="small text-muted">مرتجع</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>الشحنات</h5>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> إضافة شحنة
                </a>
            </div>
            <div class="card-body">
                @if($shipments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>رقم التتبع</th>
                                    <th>العميل</th>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الشحن</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipments as $shipment)
                                    <tr>
                                        <td>{{ $shipment->tracking_number }}</td>
                                        <td>{{ $shipment->customer_name }}</td>
                                        <td>{{ $shipment->product_name }}</td>
                                        <td>{{ $shipment->selling_price }} ج.م</td>
                                        <td>
                                            @if($shipment->status_id == 1)
                                                <span class="badge bg-success">تم التسليم</span>
                                            @elseif($shipment->status_id == 2)
                                                <span class="badge bg-danger">مرتجع</span>
                                            @elseif($shipment->status_id == 3)
                                                <span class="badge bg-primary">عُهدة</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $shipment->status->name ?? 'غير محدد' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $shipments->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        لا توجد شحنات مسجلة لهذه الشركة حتى الآن.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
