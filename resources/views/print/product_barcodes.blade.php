<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة باركود المنتجات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 5mm;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            .barcode-label {
                page-break-inside: avoid;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 10mm;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .print-button {
            background: #3B82F6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-button:hover {
            background: #2563EB;
        }
        
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5mm;
        }
        
        .barcode-label {
            background: white;
            border: 2px solid #333;
            border-radius: 4px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 55mm;
            position: relative;
        }
        
        .product-name {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            color: #1a1a1a;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .variant-info {
            display: flex;
            gap: 4px;
            margin-bottom: 6px;
            font-size: 9px;
        }
        
        .variant-badge {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            color: #555;
        }
        
        .barcode-wrapper {
            width: 100%;
            height: 25mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }
        
        .barcode-wrapper svg {
            width: 100% !important;
            height: 100% !important;
            max-width: 60mm;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 3px 0;
            color: #000;
        }
        
        .price {
            direction: ltr;
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
            color: #000;
            background: #f5f5f5;
            padding: 3px 8px;
            border-radius: 3px;
        }
        
        .no-data {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header no-print">
            <h1 style="font-size: 20px; color: #1a1a1a;">طباعة باركود المنتجات</h1>
            <button onclick="window.print()" class="print-button">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <path d="M6 14h12v8H6z"/>
                </svg>
                طباعة
            </button>
        </div>

        <div class="labels-grid">
            @php
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            @endphp

            @forelse($products as $product)
                @foreach($product->variants as $variant)
                    <div class="barcode-label">
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        
                        @if($variant->color || $variant->size)
                            <div class="variant-info">
                                @if($variant->color)
                                    <span class="variant-badge">{{ $variant->color }}</span>
                                @endif
                                @if($variant->size)
                                    <span class="variant-badge">{{ $variant->size }}</span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="barcode-wrapper">
                            @if($variant->sku)
                                {!! $generator->getBarcode($variant->sku, $generator::TYPE_CODE_128, 2, 60) !!}
                            @else
                                <span style="color: #e74c3c; font-size: 11px;">لا يوجد SKU</span>
                            @endif
                        </div>
                        
                        <div class="barcode-text">{{ $variant->sku ?? 'N/A' }}</div>
                        <div class="price">{{ number_format($product->price, 0) }} EGP</div>
                    </div>
                @endforeach
            @empty
                <div class="no-data">
                    لا توجد منتجات محددة للطباعة
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
