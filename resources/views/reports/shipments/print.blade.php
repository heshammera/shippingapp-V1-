<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة تقرير الشحنات</title>
    <style>
@page {
    size: A4 landscape;
    margin: 10mm 15mm 10mm 15mm; /* أعلى، يمين، أسفل، يسار */
}


       body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif, DejaVu Sans;
    direction: rtl;
    background: #fff;
    color: #000;
    padding: 10px 20px;
    font-size: 12px; /* خط أصغر */
    filter: grayscale(100%);
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    font-size: inherit; /* يراعي حجم الخط في البودي */
}

th, td {
    padding: 4px 6px;       /* تقليل البادينج الداخلي */
    text-align: center;
    vertical-align: middle; /* النص في النص طولياً */
    line-height: 1.2;       /* تقليل ارتفاع السطر */
}


        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            color: #004085;
        }


        thead th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            border-bottom: 2px solid #0056b3;
            text-align: center;

            /* خلي الألوان تظهر في الطباعة */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: #f1f7ff;
            transition: background-color 0.3s ease;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* تخصيص وضع الطباعة */
        @media print {
            body {
                filter: grayscale(100%);
            }

            table {
                box-shadow: none;
            }

            tbody tr:hover {
                background-color: transparent !important;
            }
        }
    </style>
</head>
<body>

    <h2>تقرير الشحنات</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم العميل</th>
                <th>المنتجات</th>
                <th>سعر الشحن</th>
                <th>الإجمالي</th>
                <th>تاريخ الشحن</th>
                <th>شركة الشحن</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipments as $index => $shipment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $shipment->customer_name ?? '-' }}</td>
                    <td>
                        @php
                            $groupedProducts = $shipment->products->groupBy('id');
                        @endphp
                        @foreach($groupedProducts as $productId => $productsGroup)
                            {{ $productsGroup->first()->name }} × {{ $productsGroup->count() }}
                            ({{ number_format($productsGroup->first()->pivot->price, 2) }} ج.م)
                            <br>
                        @endforeach
                    </td>
                    <td>{{ number_format($shipment->shipping_price ?? 0, 2) }} ج.م</td>
                    <td>{{ number_format(($shipment->products->sum(fn($p) => $p->pivot->price)) + ($shipment->shipping_price ?? 0), 2) }} ج.م</td>
                    <td>{{ $shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $shipment->shippingCompany->name ?? '-' }}</td>
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
