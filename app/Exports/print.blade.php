<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تصدير فواتير Excel</title>
    <style>
        @page {
            size: A4;
            margin: 5mm;
        }

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .page {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 287mm;
        }

        .invoice {
            width: 100%;
            height: 95mm;
            border: 1px solid #ccc;
            padding: 5px;
            box-sizing: border-box;
            margin-bottom: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .logo {
            width: 100px;
            margin: 0 auto 4px;
            display: block;
        }

        .invoice-content {
            display: flex;
            justify-content: space-between;
        }

        .left, .right {
            width: 49%;
        }

        table {
            width: 100%;
            font-size: 12px;
            font-weight: bold;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #ccc;
            padding: 4px;
            text-align: center;
        }

        .barcode {
            margin: 5px auto;
            display: block;
        }

        .footer-note {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
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
            <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
            <div class="invoice-content">
                <div class="left">
                    <table>
                        <tr>
                            <td>باركود</td>
                            <td>
                                <img class="barcode" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->tracking_number, 'C128') }}" alt="barcode" style="width:80%;">
                            </td>
                        </tr>
                        <tr><td>رقم التتبع</td><td>{{ $shipment->tracking_number }}</td></tr>
                        <tr><td>العميل</td><td>{{ $shipment->customer_name }}</td></tr>
                        <tr><td>الهاتف</td><td>{{ $shipment->customer_phone }}</td></tr>
                        <tr><td>المحافظة</td><td>{{ $shipment->governorate }}</td></tr>
                        <tr><td>العنوان</td><td>{{ $shipment->customer_address }}</td></tr>
                    </table>
                </div>
                <div class="right">
                    <table>
                        <tr><td>المنتج</td><td>{{ $shipment->product_name }}</td></tr>
                        <tr><td>اللون</td><td>{{ $shipment->color }}</td></tr>
                        <tr><td>المقاس</td><td>{{ $shipment->size }}</td></tr>
                        <tr><td>الكمية</td><td>{{ $shipment->quantity }}</td></tr>
                        <tr><td>السعر</td><td>{{ number_format($shipment->selling_price, 2) }} ج.م</td></tr>
                        <tr><td>الإجمالي</td><td>{{ number_format($shipment->total_amount, 2) }} ج.م</td></tr>
                    </table>
                </div>
            </div>
            <div class="footer-note">
                عميلنا العزيز يحق لك معاينة المنتج قبل الاستلام. مصاريف الشحن تُدفع في كل الأحوال.
            </div>
        </div>
    @endforeach

    @for($i = $chunk->count(); $i < 3; $i++)
        <div class="invoice"></div>
    @endfor
</div>
@endforeach

</body>
</html>
