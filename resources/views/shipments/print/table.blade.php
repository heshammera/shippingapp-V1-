
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
    width: 130px;
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

tbody tr {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
}



</style>



@extends('layouts.print')
@php
    $totalShipments = $shipments->count();
$totalPieces = $shipments->flatMap->products->sum('pivot.quantity');
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
        <img src="{{ asset('logo.png') }}" class="logo" alt="Logo">
        <h3 class="Slogan">شركة مايس ستور للتجارة</h3>

        <!-- ✨ نضيف "جدول الشحنات" هنا تحتها بمحاذاة اليمين -->
    </div>
</div>






    <table class="table table-bordered table-striped align-middle">
        
        
         <thead class="table-light">
            <tr>
                <th>رقم التتبع</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>رقم بديل</th>
                <th>المحافظة</th>
                <th>العنوان</th>
                <th>العدد</th>
                <th>الإجمالي</th>
                <th>شركة الشحن</th>
                <th>المندوب</th>
                                <th>الحالة</th>
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
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->tracking_number, 'C128') }}" class="barcode" style="width: 80px; height: 30px;"><br>
<div style="font-size: 11px; text-align: center;">{{ $shipment->tracking_number }}</div>
                    </td>                     
                    <td>
                        <div style="font-size: 13px; text-align: center; white-space: nowrap;">
                        {{ $shipment->customer_name }}
                        </div>
                        </td>
                    <td>{{ $shipment->customer_phone ?? '-' }}</td>
                    <td>{{ $shipment->alternate_phone ?? '-' }}</td>
                    <td>{{ $shipment->governorate ?? '-' }}</td>
                    <td>{{ $shipment->customer_address ?? '-' }}</td>

<td style="font-size: 14px; text-align: center;">
    {{ $shipment->products->sum('pivot.quantity') }}
</td>



                    <td>
                  <div style="white-space: nowrap;"><strong>      {{ number_format($shipment->total_amount) }}</strong> ج.م  </div>
                        
                        </td>
                    <td>{{ $shipment->shipping_company ?? '-' }}</td>
<td>{{ $shipment->deliveryAgent->name ?? 'غير محدد' }}</td>
<td>{{ $shipment->status->name ?? 'غير محدد' }}</td>

                    <td>
<div style="font-size: 13px; text-align: center; white-space: nowrap;">
    {{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}
</div>
                        </td>
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
