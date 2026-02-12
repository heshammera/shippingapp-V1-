
<style>
@page {
    size: A4 landscape;
    margin: 30;
}

@media print {
    body {
        margin: 15;
    }
}
.logo {
    width: 120px;
    margin-bottom: 5px;
}

.Slogan {
    margin: 0;
    font-size: 16px;
    text-align: right;
}

.Addrss {
    font-size: 20px;
    margin: 15px 0 0 0; /* تنزل الكلمة شويه لتحت */
    padding: 0;
    text-align: center; /* علشان تبقى في نفس محاذاة "شركة نسيج ستور" */
}




</style>



@extends('layouts.print')
@php
    $totalShipments = $shipments->count();
    $totalPieces = $shipments->sum('quantity');
@endphp
@section('title', 'طباعة جدول الشحنات')
<style>
@media print {
     thead tr {
        background-color: #f0f0f0 !important;
    }
    
}

</style>

@section('content')

<div class="d-flex justify-content-between align-items-start" style="width: 100%;">
    <!-- العمود الأيمن: اللوجو + اسم الشركة -->
    <div class="text-end">
        <img src="{{ asset('logo1.png') }}" class="logo" alt="Logo">
        <h3 class="Slogan">شركة نسيج ستور للتجارة</h3>

        <!-- ✨ نضيف "جدول الشحنات" هنا تحتها بمحاذاة اليمين -->
        <h4 class="Addrss mt-3">📦 جدول الشحنات</h4>
    </div>
</div>






    <table class="table table-bordered table-striped align-middle">
        
        
         <thead class="table-light">
            <tr>
                <th>رقم التتبع</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>الهاتف البديل</th>

                <th>المحافظة</th>
                <th>العنوان</th>
<th colspan="6">تفاصيل المنتجات</th>

                <th>الإجمالي</th>
                <th>شركة الشحن</th>
                <th>المندوب</th>
                <th>تاريخ الشحن</th>
            </tr>
        </thead>
        
        
        
<div style="position: absolute; top: 20px; left: 30px; font-size: 16px; z-index: 999;">
    <strong>عدد الشحنات:</strong> {{ $totalShipments }}<br>
    <strong>إجمالي القطع:</strong> {{ $totalPieces }}
</div>




        <tbody>
            @foreach($shipments as $shipment)
                <tr class="{{ $shipment->status?->row_color }}">
                    <td>
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->tracking_number, 'C128') }}" class="barcode" style="width: 100px; height: 30px;"><br>
                        {{ $shipment->tracking_number }}
                    </td>                     <td>{{ $shipment->customer_name }}</td>
                    <td>{{ $shipment->customer_phone ?? '-' }}</td>
                    <td>{{ $shipment->alternate_phone ?? '-' }}</td>

                    <td>{{ $shipment->governorate ?? '-' }}</td>
                    <td>{{ $shipment->customer_address ?? '-' }}</td>
<td colspan="6">
    @foreach($shipment->products as $product)
        <div style="padding: 4px 0; border-bottom: 1px dashed #ccc; font-size: 13px;">
            <strong>{{ $product->name }}</strong><br>
            اللون: {{ $product->pivot->color }} |
            المقاس: {{ $product->pivot->size }} |
            الكمية: {{ $product->pivot->quantity }} |
            السعر: {{ number_format($product->pivot->price) }} ج.م |
            <strong>الإجمالي 💰:</strong> {{ number_format($product->pivot->price * $product->pivot->quantity) }} ج.م
        </div>
    @endforeach
    <div style="margin-top: 6px;">
        <span>🛵 <strong>سعر الشحن:</strong> {{ number_format($shipment->shipping_price) }} ج.م</span><br>
    </div>
</td>


                    <td>{{ number_format($shipment->total_amount) }} ج.م</td>
                    <td>{{ $shipment->shipping_company ?? '-' }}</td>
<td>{{ $shipment->deliveryAgent->name ?? 'غير محدد' }}</td>

                    <td>{{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        
        
    
        
        
       

</div>

<script>
    window.onload = function() {
        
        window.print();
    };
</script>
@endsection
