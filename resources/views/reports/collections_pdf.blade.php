<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير التحصيلات</title>
    <style>
        body {
            font-family: 'amiri', 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        h1, h3, h4, h5 {
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        .filters {
            font-size: 13px;
            color: #555;
            margin-bottom: 10px;
            text-align: center;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border: 1px solid #000;
            padding: 10px;
        }
        .summary div {
            flex: 1;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
        }
        table th {
            background: #f0f0f0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
<h1>
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align: middle; margin-left:6px;" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8 0a5.53 5.53 0 0 1 5.5 5.5c0 4.2-4.5 8-5.5 8S2.5 9.7 2.5 5.5A5.53 5.53 0 0 1 8 0zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"/>
    </svg>
    تقرير التحصيلات
</h1>    <div class="filters">
        @if(request('date_from'))
            من تاريخ: {{ request('date_from') }}
        @endif
        @if(request('date_to'))
            &nbsp; إلى تاريخ: {{ request('date_to') }}
        @endif
    </div>
</div>

<div class="summary">
    <div>
        <h4>إجمالي التحصيل</h4>
        <h3>{{ number_format($total_collection, 2) }} جنيه</h3>
    </div>
    <div>
        <h4>عدد العمليات</h4>
        <h3>{{ $collections->count() }}</h3>
    </div>
</div>

<h4>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" style="vertical-align: middle; margin-left:4px;" fill="currentColor" viewBox="0 0 16 16">
        <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM2 1h12a1 1 0 0 1 1 1v3H1V2a1 1 0 0 1 1-1zm0 14a1 1 0 0 1-1-1V6h14v8a1 1 0 0 1-1 1H2z"/>
    </svg>
    قائمة التحصيلات
</h4>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>التاريخ</th>
            <th>المبلغ</th>
            <th>شركة الشحن</th>
            <th>الملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($collections as $index => $collect)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $collect->date ?? '-' }}</td>
                <td>{{ number_format($collect->amount, 2) }}</td>
                <td>{{ $collect->shippingCompany->name ?? 'غير معروف' }}</td>
                <td>{{ $collect->notes ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;">لا توجد تحصيلات</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h4>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" style="vertical-align: middle; margin-left:4px;" fill="currentColor" viewBox="0 0 16 16">
        <path d="M6.5 0v1H2v15h12V1h-4.5V0h-3zm0 2h3v2h-3V2zM3 2h2v2H3V2zm0 3h2v2H3V5zm0 3h2v2H3V8zm0 3h2v2H3v-2zm3 0h3v2h-3v-2zm5 0h2v2h-2v-2zm0-3h2v2h-2V8zm0-3h2v2h-2V5zm0-3h2v2h-2V2z"/>
    </svg>
    ملخص حسب شركة الشحن
</h4>
<table>
    <thead>
        <tr>
            <th>الشركة</th>
            <th>عدد العمليات</th>
            <th>إجمالي المبلغ</th>
        </tr>
    </thead>
    <tbody>
        @forelse($collectionsByCompany as $company)
            <tr>
                <td>{{ $company['company_name'] }}</td>
                <td>{{ $company['count'] }}</td>
                <td>{{ number_format($company['total'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center;">لا توجد بيانات</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    تقرير تم إنشاؤه بواسطة النظام - {{ now()->format('Y-m-d H:i') }}
</div>

</body>
</html>
