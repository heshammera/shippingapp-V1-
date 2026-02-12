<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الشحنات (PDF)</title>
    <style>
        /* تضمين خط DejaVu Sans لدعم العربية في PDF */
@font-face {
    font-family: 'Cairo';
    src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
}
@font-face {
    font-family: 'Cairo-Bold';
    src: url('{{ public_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
    font-weight: bold;
}
    body {
            filter: grayscale(100%);
    font-family: 'Cairo', DejaVu Sans, sans-serif;
        direction: rtl;
        font-size: 12px;
        margin: 10px 15px;
        color: #000;
        background: #fff;
    text-align: right;
    unicode-bidi: embed; /* يحسن اتجاه النص */

    }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #004085;
        }
        p {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
            padding: 10px 15px;
            text-align: center;
        }
        .card h5 {
            margin-bottom: 5px;
            color: #333;
        }
        .card h2 {
            margin: 0;
            color: #007bff;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }
        .col-md-3 {
            flex: 1 1 22%;
            min-width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-family: 'DejaVu Sans', sans-serif;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
            line-height: 1.2;
        }
        table th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
        }
        table tbody tr:nth-child(even) {
            background-color: #f2f6ff;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>تقرير الشحنات</h1>
        <p>
            @if(isset($filters['date_from']) && $filters['date_from'])
                من تاريخ: {{ $filters['date_from'] }}
            @endif
            @if(isset($filters['date_to']) && $filters['date_to'])
                &nbsp; إلى تاريخ: {{ $filters['date_to'] }}
            @endif
        </p>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <h5>إجمالي الشحنات</h5>
                    <h2>{{ $totalShipments ?? 0 }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <h5>إجمالي التكلفة</h5>
                    <h2>{{ number_format($totalCost ?? 0, 2) }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <h5>إجمالي البيع</h5>
                    <h2>{{ number_format($totalSelling ?? 0, 2) }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <h5>إجمالي الربح</h5>
                    <h2>{{ number_format($totalProfit ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>رقم التتبع</th>
                        <th>العميل</th>
                        <th>اسم المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>سعر الشحن</th>
                        <th>الإجمالي</th>
                        <th>شركة الشحن</th>
                        <th>المندوب</th>
                        <th>الحالة</th>
                        <th>تاريخ الشحن</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shipments as $shipment)
                        @php
                            $unitPrice = $shipment->selling_price;
                            $quantity = $shipment->quantity;
                            $shippingCost = $shipment->shipping_cost ?? 0;
                            $total = ($unitPrice * $quantity) + $shippingCost;
                        @endphp
                        <tr>
                            <td>{{ $shipment->tracking_number }}</td>
                            <td>{{ $shipment->customer_name }}</td>
                            <td>{{ $shipment->product->name ?? 'غير محدد' }}</td>
                            <td>{{ $quantity }}</td>
                            <td>{{ number_format($unitPrice, 2) }}</td>
                            <td>{{ number_format($shippingCost, 2) }}</td>
                            <td>{{ number_format($total, 2) }}</td>
                            <td>{{ $shipment->shippingCompany->name ?? 'غير محدد' }}</td>
                            <td>{{ $shipment->deliveryAgent->name ?? 'غير محدد' }}</td>
                            <td>{{ $shipment->status->name ?? 'غير محدد' }}</td>
                            <td>{{ $shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
