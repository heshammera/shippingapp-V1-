<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة فواتير الشحنات</title>
<style>
@page {
    size: 210mm 297mm;
    margin: 0;
}


body {
    font-family: 'Cairo', sans-serif;
    margin: 0;
    padding: 0;
    width: 210mm;
    height: 297mm;
    webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
* {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.page {
    width: 210mm;
    height: 297mm;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
}
.invoice {
    width: 100%;
    height: 99mm; /* 99 × 3 = 297mm بالظبط */
    border: 1px solid #ccc;
    padding: 5px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
}
.logo {
    width: 130px;
    margin-bottom: 0;
    display: block;
    margin-left: auto;
    margin-right: auto;
    filter: grayscale(100%) brightness(0%);
}
.invoice-content {
    width: 100%;
    height: 95%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-top: 0px;
}
.left, .right {
    width: 50%;
    height: 95%;

}
.left table, .right table {
    width: 100%;
    height: 95%;
    font-size: 12px;
    font-weight: bold;

    border-collapse: collapse;
}
.left td, .right td {
    padding: 4px;
    border: 1px solid #ddd;
}
.left tr:nth-child(even),
.right tr:nth-child(even) {
    background-color: #ececec;
}
.barcode {
    width: 90%;
    margin: 0px auto;
    display: block;
}
.tracking {
    font-weight: bold;
    font-size: 16px;
}
</style>

</head>
<body>

@php
$chunks = $shipments->chunk(3);
@endphp

@foreach($chunks as $chunk)
<div class="page">
    @foreach($chunk as $shipment)
    <div class="invoice">

        <img src="{{ asset('logo.png') }}" class="logo" alt="Logo">

        <div class="invoice-content">
            
            <!-- العمود الأيسر (رقم تتبع + باركود + بيانات العميل الأساسية) -->
            <div class="left">
  <table style="margin-bottom:0px;">
    <tr style="height:35px" >
        <td  style="width:20%; text-align:right;">باركود</td>
        <td style="text-align:center;">
            <img class="barcode" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->tracking_number, 'C128') }}" alt="barcode" style="width: 70%;">
        </td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">رقم التتبع :</td>
        <td class="tracking" style="text-align:center;">{{ $shipment->tracking_number }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">اسم العميل :</td>
        <td style="width:80%; text-align:center;">{{ $shipment->customer_name }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">رقم الهاتف :</td>
        <td class="tracking">{{ $shipment->customer_phone ?? '-' }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">المحافظة :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->governorate ?? '-' }}</td>
    </tr>
    <tr>
        <td style="height:90px"  style="width:20%; text-align:right;">العنوان :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->customer_address ?? '-' }}</td>
    </tr>

</table>

            </div>

            <!-- العمود الأيمن (بيانات المنتج) -->
            <div class="right">
           <table>
    <tr>
        <td  style="width:20%; text-align:right;">المنتج :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->product_name }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">اللون :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->color ?? '-' }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">المقاس :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->size ?? '-' }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">الكمية :</td>
         <td style="width:80%; text-align:center;">{{ $shipment->quantity }}</td>
    </tr>
    <tr>
        <td style="width:20%; text-align:right;">السعر :</td>
        <td style="width:80%; text-align:center;">{{ number_format($shipment->selling_price, 2) }} ج.م</td>
    </tr>

    <tr style="height:100px">
        <td  style="width:20%; text-align:right;">الإجمالي :</td>
        <td class="tracking"  style="width:80%; text-align:center;">{{ number_format($shipment->total_amount, 2) }} ج.م</td>
</table>
    </tr>
            </div>

        </div>
            <div style="font-size:14px; font-weight: bold; margin-bottom: 5px;">عميلنا العزيز يحق لك معاينة المنتج جيدا قبل الاستلام ويتم دفع مصاريف الشحن للمندوب في حالة الاستلام او عدم الاستلام</div>

    </div>

    @endforeach

    {{-- فواتير فاضية لو ناقص --}}
    @for($i = $chunk->count(); $i < 3; $i++)
        <div class="invoice"></div>
    @endfor
</div>
@endforeach

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>
