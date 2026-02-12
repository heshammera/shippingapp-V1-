<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الشحنات</title>

    <!-- Bootstrap (اختياري لو تحب تنسق) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            color: #000;
            background: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background: #f0f0f0;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>

</head>
<body>

<div class="print-header">
    @if(file_exists(public_path('logo.png')))
        <img src="{{ asset('logo.png') }}" class="logo" alt="شعار الشركة">
    @endif
    <h3>قائمة الشحنات المختارة</h3>
    <p>تاريخ الطباعة: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th>رقم التتبع</th>
            <th>اسم العميل</th>
            <th>المنتج</th>
            <th>اللون</th>
            <th>المقاس</th>
            <th>الكمية</th>
            <th>سعر القطعة</th>
            <th>الإجمالي</th>
            <th>شركة الشحن</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shipments as $shipment)
            <tr>
                <td>{{ $shipment->tracking_number }}</td>
                <td>{{ $shipment->customer_name }}</td>
                <td>{{ $shipment->product_name }}</td>
                <td>{{ $shipment->color ?? '-' }}</td>
                <td>{{ $shipment->size ?? '-' }}</td>
                <td>{{ $shipment->quantity }}</td>
                <td>{{ $shipment->selling_price }} ج.م</td>
                <td>{{ $shipment->total_amount }} ج.م</td>
                <td>{{ $shipment->shipping_company ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>
