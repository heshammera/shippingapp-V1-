<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>طباعة جدول الشحنات</title>
    <style>
        body { font-family: 'Arial', sans-serif; direction: rtl; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; font-size: 13px; }
        th { background-color: #f5f5f5; }
        .table-success { background-color: #d1e7dd !important; }
        .table-danger { background-color: #f8d7da !important; }
        .table-primary { background-color: #cfe2ff !important; }
        .table-secondary { background-color: #e2e3e5 !important; }
    </style>
</head>
<body>
<h3 style="text-align: center;">🖨️ جدول الشحنات</h3>

<table>
    <thead>
        <tr>
            <th>رقم التتبع</th>
            <th>العميل</th>
            <th>المنتج</th>
            <th>اللون</th>
            <th>المقاس</th>
            <th>الكمية</th>
            <th>سعر القطعة</th>
            <th>الإجمالي</th>
            <th>شركة الشحن</th>
            <th>المندوب</th>
            <th>الحالة</th>
            <th>تاريخ الشحن</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shipments as $shipment)
        <tr class="{{ $shipment->status?->row_color ?? '' }}">
            <td>{{ $shipment->tracking_number }}</td>
            <td>{{ $shipment->customer_name }}</td>
            <td>{{ $shipment->product_name }}</td>
            <td>{{ $shipment->color ?? '-' }}</td>
            <td>{{ $shipment->size ?? '-' }}</td>
            <td>{{ $shipment->quantity }}</td>
            <td>{{ $shipment->selling_price }} ج.م</td>
            <td>{{ $shipment->total_amount ? number_format($shipment->total_amount, 2) . ' ج.م' : '-' }}</td>
            <td>{{ $shipment->shipping_company ?? 'غير محدد' }}</td>
            <td>{{ $shipment->deliveryAgent->name ?? '-' }}</td>
            <td>{{ $shipment->status->name ?? '-' }}</td>
            <td>{{ $shipment->shipping_date ? date('Y-m-d', strtotime($shipment->shipping_date)) : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
