<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&nbsp;</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 40mm 60mm;
            margin: 0mm !important;
        }
        html {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            color: #000;
            margin: 0 !important;
            padding: 0 !important;
        }
        .label-container {
            width: 40mm;
            height: 60mm;
            padding: 1mm 2mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            background: white;
            position: relative;
            
            /* Critical for preventing splits */
            /* Critical for preventing splits */
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            overflow: hidden;
        }
        .page-break {
            page-break-after: always;
            height: 0;
            display: block;
            visibility: hidden;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            margin-bottom: 1px;
            height: 8mm; /* Fixed height */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .company-name {
            font-size: 8px; /* Slightly larger */
            font-weight: 900;
            line-height: 1;
        }

        .section {
            border-bottom: 1px dashed #aaa;
            padding: 1px 0;
            flex-shrink: 0;
        }
        
        .address-section {
            flex-grow: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-bottom: none;
            min-height: 15mm;
        }

        .label {
            font-size: 6px;
            font-weight: bold;
            color: #444;
            line-height: 1;
            margin-bottom: 0;
        }
        .value {
            font-size: 8px;
            font-weight: 800;
            line-height: 1.1;
        }
        .value.big {
            font-size: 10px;
            font-weight: 900;
        }
        .value.address {
            font-size: 7px;
            font-weight: 700;
            line-height: 1.1;
            white-space: normal;
        }

        .footer {
            height: 8mm;
            border-top: 1px solid #000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1px;
        }
        .cod-box {
            border: 1px solid #000;
            padding: 0 3px;
            border-radius: 2px;
            font-weight: 900;
            font-size: 9px;
            line-height: 1.2;
        }
        
        .barcode-container {
            height: 12mm; /* Fixed height for barcode area */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            padding-bottom: 1px;
        }
        .barcode-img {
            width: 90%;
            height: 8mm; /* Strictly limit barcode height */
            object-fit: fill;
        }
        .tracking-number {
            font-family: monospace;
            font-size: 7px;
            font-weight: bold;
            line-height: 1;
            margin-top: 1px;
        }
    </style>
</head>
<body onload="window.print()">

@foreach($shipments as $shipment)
    <div class="label-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ \App\Models\Setting::getValue('company_name', 'Orderly') }}</div>
            <div style="font-size: 6px;">{{ date('Y-m-d') }} | #{{ $shipment->id }}</div>
        </div>

        <!-- Recipient Info -->
        <div class="section">
            <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                 <div>
                     <div class="label">العميل:</div>
                     <div class="value big">{{ Str::limit($shipment->customer_name, 20) }}</div>
                 </div>
                 <div class="value">{{ $shipment->customer_phone }}</div>
            </div>
        </div>

        <!-- Address (Full with overflow handling) -->
        <div class="address-section">
            <div class="label">العنوان:</div>
            <div class="value address">
                {{ $shipment->governorate }} - {{ $shipment->customer_address }}
            </div>
        </div>

        <!-- Footer / COD -->
        <div class="footer">
            <div style="display:flex; align-items:center; gap:2px;">
                <div class="label">تحصيل:</div>
                <div class="cod-box">{{ number_format($shipment->total_amount) }}</div>
            </div>
             <div style="font-size: 7px; font-weight:800;">
                 {{ $shipment->products->count() }} Pcs
             </div>
        </div>

        <!-- Barcode -->
        <div class="barcode-container">
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                $barcode = $generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 30);
                $base64 = base64_encode($barcode);
            @endphp
            <img src="data:image/svg+xml;base64,{{ $base64 }}" class="barcode-img">
            <div class="tracking-number">{{ $shipment->tracking_number }}</div>
        </div>
    </div>



    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
