<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة فواتير الشحنات</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            width: 210mm;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 210mm;
            height: 296mm; /* Keeps 3 invoices perfectly */
            position: relative;
            page-break-after: always;
            overflow: hidden;
        }

        .invoice-container {
            width: 100%;
            height: 98.66mm; /* Exactly 1/3 of the page height */
            padding: 6mm 10mm;
            box-sizing: border-box;
            border-bottom: 2px dashed #ccc;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .invoice-container:last-child {
            border-bottom: none;
        }

        /* --- Header Section --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .logo-section img {
            height: 40px;
            filter: grayscale(100%) brightness(0);
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 16px;
            font-weight: 700;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: 700;
            background: #000;
            color: #fff;
            padding: 2px 10px;
            border-radius: 4px;
        }

        /* --- Content Grid --- */
        .content {
            display: flex;
            gap: 15px;
            flex: 1;
        }

        /* Right Column: Customer Info */
        .customer-info {
            flex: 1;
            border: 1px solid #000;
            border-radius: 6px;
            padding: 8px;
            position: relative;
        }

        .box-title {
            position: absolute;
            top: -10px;
            right: 10px;
            background: #fff;
            padding: 0 5px;
            font-weight: 700;
            font-size: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
            border-bottom: 1px dotted #ddd;
            padding-bottom: 2px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #555;
            width: 70px;
        }

        .value {
            font-weight: 700;
            color: #000;
            flex: 1;
            text-align: left;
        }

        /* Left Column: Shipment & Products */
        .shipment-details {
            flex: 1.4;
            display: flex;
            flex-direction: column;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .products-table th {
            background-color: #eee;
            border: 1px solid #999;
            padding: 4px;
            font-weight: 700;
        }

        .products-table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
            font-weight: 600;
        }

        /* --- Footer / Totals --- */
        .footer-section {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-top: auto;
            border-top: 2px solid #000;
            padding-top: 5px;
        }

        .barcode-section {
            text-align: center;
        }

        .barcode-img {
            height: 35px;
            width: 200px;
            display: block;
        }

        .tracking-num {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .totals {
            text-align: left;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .total-box {
            text-align: center;
            border: 1px solid #000;
            padding: 2px 10px;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .total-label {
            font-size: 10px;
            color: #555;
            display: block;
        }

        .total-value {
            font-size: 14px;
            font-weight: 800;
        }

        .notes-small {
            font-size: 9px;
            color: #777;
            margin-top: 2px;
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
            <div class="invoice-container">
                
                <!-- Header -->
                <div class="header">
                    <div class="logo-section">
                        @php
                            $logoPath = \App\Models\Setting::getValue('company_logo');
                            $logoUrl = $logoPath ? \Illuminate\Support\Facades\Storage::url($logoPath) : asset('logo.png');
                        @endphp
                        <img src="{{ $logoUrl }}" alt="Logo">
                    </div>
                    <div class="company-name">{{ \App\Models\Setting::getValue('company_name', 'اسم المتجر') }}</div>
                    <div class="invoice-title">فاتورة شحن</div>
                </div>

                <!-- Content -->
                <div class="content">
                    
                    <!-- Customer Box -->
                    <div class="customer-info">
                        <span class="box-title">بيانات العميل</span>
                        
                        <div class="info-row">
                            <span class="label">الاسم:</span>
                            <span class="value">{{ $shipment->customer_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">الهاتف:</span>
                            <span class="value">{{ $shipment->customer_phone }}</span>
                        </div>
                        @if($shipment->alternate_phone)
                        <div class="info-row">
                            <span class="label">هاتف 2:</span>
                            <span class="value">{{ $shipment->alternate_phone }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="label">المحافظة:</span>
                            <span class="value">{{ $shipment->governorate ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">العنوان:</span>
                            <span class="value" style="font-size: 10px;">{{ Str::limit($shipment->customer_address, 65) }}</span>
                        </div>
                        
                        @if($shipment->notes)
                        <div class="info-row" style="border:none; background: #fffbe6; padding:2px;">
                            <span class="label">ملاحظات:</span>
                            <span class="value" style="color:red;">{{ Str::limit($shipment->notes, 40) }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Products & Details -->
                    <div class="shipment-details">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>اللـون</th>
                                    <th style="width: 30px;">مقاس</th>
                                    <th style="width: 30px;">عدد</th>
                                    <th style="width: 50px;">سعر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipment->products->take(4) as $product)
                                <tr>
                                    <td>{{ Str::limit($product->name, 20) }}</td>
                                    <td>{{ $product->pivot->color ?? '-' }}</td>
                                    <td>{{ $product->pivot->size ?? '-' }}</td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>{{ number_format($product->pivot->price) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- If more than 4 products, show count -->
                        @if($shipment->products->count() > 4)
                            <div style="text-align: center; font-size: 9px; color: #666;">
                                + {{ $shipment->products->count() - 4 }} منتجات أخرى
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Footer -->
                <div class="footer-section">
                    
                    <div class="barcode-section">
                        @php
                            $code = $shipment->tracking_number;
                            try {
                                 $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                                 $svg = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 40);
                                 $svg = str_replace('<svg', '<svg preserveAspectRatio="none"', $svg);
                                 $base64 = base64_encode($svg);
                                 $barcodeSrc = "data:image/svg+xml;base64,{$base64}";
                            } catch (\Throwable $e) {
                                 $barcodeSrc = "";
                            }
                        @endphp
                        <img src="{{ $barcodeSrc }}" class="barcode-img">
                        <div class="tracking-num">{{ $code }}</div>
                    </div>

                    <div style="font-size: 10px; color: #555; text-align: center; margin: 0 10px;">
                        تاريخ: {{ now()->format('Y-m-d') }}
                        <div style="font-size: 8px;">يحق للعميل المعاينة قبل الاستلام</div>
                    </div>

                    <div class="totals">
                        <div class="total-box">
                            <span class="total-label">الشحن</span>
                            <span class="total-value">{{ number_format($shipment->shipping_price) }}</span>
                        </div>
                        <div class="total-box" style="background: #000; color: #fff;">
                            <span class="total-label" style="color: #ccc;">الإجمالي</span>
                            <span class="total-value">{{ number_format($shipment->total_amount) }}</span>
                        </div>
                    </div>

                </div>

            </div>
        @endforeach
    </div>
@endforeach

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
