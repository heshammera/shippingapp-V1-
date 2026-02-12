<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تصدير الشحنات</title>
    <style>
        body {
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            padding: 20px;
        }
        h3, h5 {
            text-align: center;
            margin: 0;
        }
        h5 {
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <table>
        <thead>
            <tr>
                <th>رقم التتبع</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>المحافظة</th>
                <th>العنوان</th>
                <th>المنتج</th>
                <th>اللون</th>
                <th>المقاس</th>
                <th>الكمية</th>
                <th>سعر القطعة</th>
                <th>الإجمالي</th>
                <th>شركة الشحن</th>
                <th>المندوب</th>
                <th>تاريخ الشحن</th>
            </tr>
        </thead>
        <tbody>
@foreach($shipments as $shipment)
    @php
        $productCount = $shipment->products->count();
        $totalQuantity = $shipment->products->sum('pivot.quantity');
        $productsTotal = $shipment->products->sum(function ($p) {
            return $p->pivot->price * $p->pivot->quantity;
        });
        $grandTotal = $productsTotal + $shipment->shipping_price;
    @endphp

    @foreach($shipment->products as $i => $product)
        <tr>
            @if($i === 0)
                <td rowspan="{{ $productCount }}">{{ $shipment->tracking_number }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->customer_name }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->customer_phone ?? '-' }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->governorate ?? '-' }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->customer_address ?? '-' }}</td>
            @endif

            <td>{{ $product->name }}</td>
            <td>{{ $product->pivot->color ?? '-' }}</td>
            <td>{{ $product->pivot->size ?? '-' }}</td>
@if($i === 0)
    <td rowspan="{{ $productCount }}">
        {{ $totalQuantity }}
    </td>
@endif
            <td>{{ number_format($product->pivot->price, 2) }} ج.م</td>

            @if($i === 0)
                <td rowspan="{{ $productCount }}">{{ number_format($grandTotal, 2) }} ج.م</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->shippingCompany->name ?? 'غير محدد' }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->deliveryAgent->name ?? 'غير محدد' }}</td>
                <td rowspan="{{ $productCount }}">{{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}</td>
            @endif
        </tr>
    @endforeach
@endforeach




        </tbody>
    </table>

    <div class="footer">الهاتف: 01003705046</div>
</div>

</body>
</html>
