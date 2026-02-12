<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الشحنات المختارة</title>
    <style>
    
    @media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .no-print {
        display: none !important;
    }
}

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
        }

        h3, h5 {
            text-align: center;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }

        thead {
            background-color: #f0f0f0;
        }

        .table-success {
            background-color: #d1e7dd;
        }

        .table-danger {
            background-color: #f8d7da;
        }

        .table-primary {
            background-color: #cfe2ff;
        }

        .table-secondary {
            background-color: #e2e3e5;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 15px;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button onclick="window.print()">🖨️ اطبع الصفحة</button>

    <h3>شركة مايس ستور للتجارة</h3>
    <h5>📦 جدول الشحنات - صفوف محددة</h5>
 <p text-align="center" class="text-center mb-5">
📌 للتأكد من ظهور الألوان في الطباعة: فعّل خيار <u>"طباعة خلفيات الصفحة (Background graphics)"</u> من إعدادات الطابعة.
</p>
    <table>
        <thead>
            <tr>
                <th>رقم التتبع</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>المحافظة</th>
                <th>العنوان بالتفصيل</th>
                <th>المنتج</th>
                <th>اللون</th>
                <th>المقاس</th>
                <th>الكمية</th>
                <th>سعر القطعة</th>
                <th>الإجمالي</th>
                <th>شركة الشحن</th>
                <th>تاريخ الشحن</th>
                <th>ملاحظت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $shipment)
                <tr class="{{ $shipment->status?->row_color ?? '' }}">
<td style="text-align: center; vertical-align: middle;">
    <div style="display: inline-block; line-height: 1;">
        {!! DNS1D::getBarcodeHTML($shipment->tracking_number, 'C128', 1, 40) !!}
    </div>
        <div class="text-center small">{{ $shipment->tracking_number }}</div>

</td>
                   <td>{{ $shipment->customer_name }}</td>
                    <td>{{ $shipment->customer_phone }}</td>
                    <td>{{ $shipment->governorate }}</td>
                    <td>{{ $shipment->customer_address }}</td>
                    <td>{{ $shipment->product_name }}</td>
                    <td>{{ $shipment->color }}</td>
                    <td>{{ $shipment->size }}</td>
                    <td>{{ $shipment->quantity }}</td>
                    <td>{{ $shipment->selling_price }} ج.م</td>
                    <td>{{ number_format($shipment->total_amount, 2) }} ج.م</td>
                    <td>{{ $shipment->shipping_company ?? '-' }}</td>
                    <td>{{ $shipment->shipping_date ? \Carbon\Carbon::parse($shipment->shipping_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ $shipment->notes }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        الهاتف: 01011524234
    </div>

</body>
</html>
