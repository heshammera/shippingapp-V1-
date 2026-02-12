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
    position: relative; /* ⬅️ أضف هذا السطر */
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
    width: 100px;
    margin-bottom: 10px;
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
.left {
    width: 40%;
    height: 95%;
}
.right {
    width: 60%;
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


.print-date {
    position: absolute;
    top: 5px;
    left: 10px;
    font-size: 10px;
    color: #000;
}
.left td:first-child {
    width: 30%;
    text-align: right;
    font-weight: bold;
    vertical-align: middle;
}

.left td:last-child {
    width: 70%;
    text-align: center;
    vertical-align: middle;
}

.left tr {
    height: 30px; /* ارتفاع موحد */
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

<span class="print-date">{{ now()->format('Y-m-d') }}</span>



        <img src="{{ asset('logo.png') }}" class="logo" alt="Logo">
        


        <div class="invoice-content">
            
            <!-- العمود الأيسر (رقم تتبع + باركود + بيانات العميل الأساسية) -->
<div class="left">
  <table style="margin-bottom:0px;">
      
    <tr style="height:35px" >
        <td  style="width:30%; text-align:right;">باركود</td>
        <td style="text-align:center;">
            <img class="barcode" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->tracking_number, 'C128') }}" alt="barcode" style="width: 70%;">
        </td>
    </tr>
    <tr>
        <td  style="width:30%; text-align:right;">رقم التتبع :</td>
        <td class="tracking" style="text-align:center;">{{ $shipment->tracking_number }}</td>
    </tr>
    <tr>
        <td  style="width:30%; text-align:right;">اسم العميل :</td>
        <td style="width:80%; text-align:center;">{{ $shipment->customer_name }}</td>
    </tr>
    <tr>
        <td  style="width:30%; text-align:right;">رقم الهاتف :</td>
        <td class="tracking">{{ $shipment->customer_phone ?? '-' }}</td>
    </tr>
    <tr>
        <td  style="width:30%; text-align:right;">هاتف بديل :</td>
        <td class="tracking">{{ $shipment->alternate_phone ?? '-' }}</td>
    </tr>
    <tr>
        <td  style="width:20%; text-align:right;">المحافظة :</td>
         <td style="width:65%; text-align:center;">{{ $shipment->governorate ?? '-' }}</td>
    </tr>
<tr>
    <td style="width:30%; text-align:right; vertical-align: top;">العنوان :</td>
    <td style="width:65%; text-align:center; vertical-align: top;">{{ $shipment->customer_address ?? '-' }}</td>
</tr>


    <tr>
        <td style="width:20%; text-align:right;">ملاحظات :</td>
        <td style="width:65%; text-align:center;">{{ $shipment->notes ?? '-' }}</td>
    </tr>

</table>
</div>

            <!-- العمود الأيمن (بيانات المنتج) -->
<div class="right">
<table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 12px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid #ccc; padding: 5px;">المنتج</th>
            <th style="border: 1px solid #ccc; padding: 5px;">اللون</th>
            <th style="
    border: 1px solid #ccc;
    padding: 5px;
    max-width: 15px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;">المقاس</th>
            <th style="border: 1px solid #ccc; padding: 5px;">الكمية</th>
            <th style="border: 1px solid #ccc; padding: 5px;">الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shipment->products as $product)
            <tr>
                <td style="border: 1px solid #ccc; padding: 5px; white-space: nowrap;">{{ $product->name }}</td>
<td style="border: 1px solid #ccc; padding: 5px; white-space: normal; word-break: break-word;">
    {!! collect(explode("\n", $product->pivot->color))
        ->map(function($line) {
            // استخرج النوع بعد أول -
            preg_match('/نوع\s*[^-]*-\s*([^\|]+)/u', $line, $typeMatch);
            $type = trim($typeMatch[1] ?? '');

            // استخرج اللون بعد كلمة "اللون -"
            preg_match('/اللون\s*-\s*(.+)/u', $line, $colorMatch);
            $color = trim($colorMatch[1] ?? '');

            return $type && $color ? "$type - $color" : $line;
        })
        ->implode('<br>') !!}
</td>



<td style="
    border: 1px solid #ccc;
    padding: 5px;
    white-space: normal;
    word-break: break-word;
    text-align: center;">
    {{ $product->pivot->size }}
</td>

                <td style="border: 1px solid #ccc; padding: 5px;">{{ $product->pivot->quantity }}</td>
                <td style="border: 1px solid #ccc; padding: 5px; white-space: nowrap;">{{ number_format($product->pivot->price, 2) }} ج.م</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div style="display: flex; justify-content: space-between; font-weight: bold; margin-top: 0px; border-bottom: 1px dashed #999; padding-top: 0px;">
    <div style="width: 50%; text-align: right;">سعر الشحن: {{ number_format($shipment->shipping_price, 2) }} ج.م</div>
    <div style="width: 50%; text-align: left;">الإجمالي الكلي: {{ number_format($shipment->total_amount, 2) }} ج.م</div>
</div>


</div>


        </div>
        <p class="mt-2 text-muted small" style="font-size: 12px; margin-top: 20px;">
عميلنا العزيز يحق لك معاينة المنتج جيدا قبل الاستلام ويتم دفع مصاريف الشحن للمندوب في حالة الاستلام او عدم الاستلام .
</p>
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
