@extends('layouts.app')

@section('title', 'تفاصيل الشحنة')

@section('styles')
<style>
/* تحسين الخطوط والأبعاد على الموبايل */
@media (max-width: 576px) {
    table.table td, table.table th {
        font-size: 12px !important;
        padding: 4px 6px !important;
        white-space: normal !important; /* يسمح بتقسيم الكلمات الطويلة */
    }

    /* تحسين التمرير الأفقي */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* تمرير سلس على أجهزة iOS */
    }

    /* تحسين مظهر scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }
    .card-body h5, .card-header h5 {
        font-size: 1.1rem;
    }
    .card-body h6 {
        font-size: 1rem;
    }
    .card-body p, table td, table th {
        font-size: 0.9rem;
        word-wrap: break-word;
    }
    .btn-group .btn {
        font-size: 0.85rem;
        padding: 0.3rem 0.5rem;
    }


        table.table thead th {
        font-size: 10px !important;
        padding: 3px 5px !important;
        white-space: normal !important;
    }

    table.table tbody td {
        font-size: 8px !important;
        padding: 4px 6px !important;
        white-space: normal !important;
    }
}

/* جعل الجداول قابلة للتمرير */
.table-responsive {
    overflow-x: auto;
}

/* تنسيق سجل التغييرات */
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 15px;
    height: 15px;
    border-radius: 50%;
}

.timeline-title {
    margin-bottom: 5px;
}

.timeline-text {
    margin-bottom: 10px;
    color: #6c757d;
}
</style>
@endsection

@section('content')
<div class="container">
    <!-- أزرار الإجراءات -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="{{ route('shipments.edit', $shipment) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> تعديل
            </a>
            <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-right"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <!-- معلومات الشحنة -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>معلومات الشحنة</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>رقم التتبع</th>
                    <td class="p-0">
                        <div class="d-flex justify-content-center align-items-center" style="min-height: 90px;">
                            <div class="barcode-wrapper text-center">
                                {!! DNS1D::getBarcodeHTML($shipment->tracking_number, 'C128', 1.5, 40) !!}
                                <div class="barcode-number">{{ $shipment->tracking_number }}</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr><th>اسم العميل</th><td>{{ $shipment->customer_name }}</td></tr>
<tr><th>رقم الهاتف</th><td>{{ $shipment->customer_phone }}</td></tr>
<tr><th>رقم الهاتف البديل</th><td>{{ $shipment->alternate_phone ?? '-' }}</td></tr>
                <tr><th>العنوان</th><td>{{ $shipment->customer_address }}</td></tr>
                <tr><th>المحافظة</th><td>{{ $shipment->governorate }}</td></tr>
                <tr><th>شركة الشحن</th><td>{{ $shipment->shippingCompany->name ?? '-' }}</td></tr>
                <tr><th>تاريخ الطباعة</th><td>{{ $shipment->print_date ? \Carbon\Carbon::parse($shipment->print_date)->format('Y-m-d') : '-' }}</td></tr>
            </table>
        </div>
    </div>

    <!-- تفاصيل المنتجات -->
    @if($shipment->products->count())
    <div class="card mb-4">
        <div class="card-header">
            <h5>تفاصيل المنتجات</h5>
        </div>
 <div class="card-body table-responsive">
    <table class="table table-bordered text-center">
        <!-- رأس الجدول -->
        <thead class="table-light">
            <tr>
                <th>PRD</th>
                <th>ل</th>
                <th>م</th>
                <th>ك</th>
                <th>TOT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipment->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                   @php
    $colorMap = [
        'بيج' => '#f5f5dc',
        'أسود' => '#000000',
        'اسود' => '#000000',
        'ابيض' => '#ffffff',
        'ازرق بيبسي' => '#004b9a',
        'بترولي' => '#004b9a',
        'نبيتي' => '#722f37',
        'زيتي' => '#708238',
        'موف' => '#4b0082',
        'منت جرين' => '#98ff98',
        'رصاصي' => '#808080',
        'فوشيا' => '#ff00ff',
        'بينك' => '#ffc0cb',
        'بلو' => '#0000ff',
    ];

    $rawColor = trim(mb_strtolower($product->pivot->color));
    $boxColor = '#6c757d'; // لون افتراضي (رمادي)

    foreach ($colorMap as $name => $hex) {
        if (mb_strpos($rawColor, mb_strtolower($name)) !== false) {
            $boxColor = $hex;
            break;
        }
    }
@endphp

<td>
    <span style="display:inline-block; width:15px; height:15px; background-color: {{ $boxColor }}; border-radius: 3px; margin-right: 8px; vertical-align: middle; border: 1px solid #ccc;"></span>
    {{ $product->pivot->color }}
</td>

                    <td>{{ $product->pivot->size }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>{{ number_format($product->pivot->price, 2) }} ج.م</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


            <!-- ملخص الإجمالي -->
            <div class="row mt-4 px-2">
                <div class="col-12 col-md-6 mb-3">
                    <div class="bg-light border rounded p-3 text-start d-flex justify-content-between align-items-center">
                        <div class="fw-bold">سعر الشحن:</div>
                        <div class="text-primary fw-bold fs-5">
                            {{ number_format($shipment->shipping_price, 2) }} ج.م
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="bg-light border border-2 border-success rounded p-3 text-start d-flex justify-content-between align-items-center">
                        <div class="fw-bold">الإجمالي الكلي:</div>
                        <div class="text-success fw-bold fs-4">
                            {{ number_format($shipment->total_amount, 2) }} ج.م
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- تواريخ الشحنة -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>تواريخ الشحنة</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-12 col-md-4 mb-3">
                    <div class="h5">تاريخ الشحن</div>
                    <div class="h4">{{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}</div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="h5">تاريخ التسليم</div>
                    <div class="h4">{{ $shipment->delivery_date ? date('Y-m-d', strtotime($shipment->delivery_date)) : '-' }}</div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="h5">تاريخ الإرجاع</div>
                    <div class="h4">{{ $shipment->return_date ? date('Y-m-d', strtotime($shipment->return_date)) : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- الملاحظات -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>الملاحظات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <h6>ملاحظات عامة</h6>
                    <p>{{ $shipment->notes ?? 'لا توجد ملاحظات' }}</p>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <h6>ملاحظات المندوب</h6>
                    <p>{{ $shipment->agent_notes ?? 'لا توجد ملاحظات' }}</p>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <h6>تاريخ الطباعة</h6>
                    <p>{{ $shipment->print_date ? \Carbon\Carbon::parse($shipment->print_date)->format('Y-m-d') : 'لا توجد ملاحظات' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- تحديث بيانات مندوبي التوصيل -->
    @php
        $user = auth()->user();
        $isDeliveryAgent = $user->role === 'delivery_agent';
    @endphp
    @if ($isDeliveryAgent || $user->role === 'admin')
    <div class="card mb-4">
        <div class="card-header">
            <h5>تحديث بيانات مندوبي التوصيل</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('shipments.updateStatus', $shipment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status_id" class="form-label">الحالة الجديدة</label>
                    <select class="form-select" id="status_id" name="status_id" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $shipment->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="delivery_date" class="form-label">تاريخ التسليم</label>
                    <input type="date" class="form-control" name="delivery_date" value="{{ $shipment->delivery_date ? date('Y-m-d', strtotime($shipment->delivery_date)) : '' }}">
                </div>

                <div class="mb-3">
                    <label for="agent_notes" class="form-label">ملاحظات المندوب</label>
                    <textarea class="form-control" id="agent_notes" name="agent_notes" rows="3">{{ $shipment->agent_notes }}</textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- سجل التغييرات -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>سجل التغييرات</h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">إنشاء الشحنة</h6>
                        <p class="timeline-text">{{ $shipment->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                @if($shipment->shipping_date)
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">تم الشحن</h6>
                        <p class="timeline-text">{{ date('Y-m-d', strtotime($shipment->shipping_date)) }}</p>
                    </div>
                </div>
                @endif
                @if($shipment->delivery_date)
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">تم التسليم</h6>
                        <p class="timeline-text">{{ date('Y-m-d', strtotime($shipment->delivery_date)) }}</p>
                    </div>
                </div>
                @endif
                @if($shipment->return_date)
                <div class="timeline-item">
                    <div class="timeline-marker bg-danger"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">تم الإرجاع</h6>
                        <p class="timeline-text">{{ date('Y-m-d', strtotime($shipment->return_date)) }}</p>
                    </div>
                </div>
                @endif
                <div class="timeline-item">
                    <div class="timeline-marker bg-secondary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">آخر تحديث</h6>
                        <p class="timeline-text">{{ $shipment->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
