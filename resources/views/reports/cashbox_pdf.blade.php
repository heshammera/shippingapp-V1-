<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الخزنة</title>
    <style>
        body {
            font-family: 'Amiri', serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
        }
        td {
            vertical-align: middle;
        }
        .amount {
            text-align: left;
            direction: ltr;
        }
        h1 {
            margin-bottom: 5px;
        }
        p {
            margin: 0;
        }
    </style>
</head>
<body>

<h1>
  <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="#4CAF50" viewBox="0 0 24 24" style="vertical-align: middle; margin-left: 6px;">
    <path d="M20 7h-1V4a1 1 0 0 0-1-1H6a1 1 0 0 0-1 1v3H4a2 2 0 0 0-2 2v9a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-9a2 2 0 0 0-2-2zM7 5h10v2H7V5zm10 13H7a1 1 0 0 1-1-1v-6h12v6a1 1 0 0 1-1 1z"/>
    <circle cx="12" cy="14" r="1.5"/>
  </svg>
  تقرير الخزنة
</h1>
    <p>
        من تاريخ: {{ $dateFrom ?? '-' }} &nbsp; — &nbsp;
        إلى تاريخ: {{ $dateTo ?? '-' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>البيان</th>
                <th>الإيرادات</th>
                <th>المصروفات</th>
                <th>الرصيد</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allTransactions as $transaction)
                <tr>
                    <td>{{ $transaction['date'] ?? '-' }}</td>
                    <td>{{ $transaction['description'] ?? '-' }}</td>
                    <td class="amount">
                        @if(isset($transaction['type']) && $transaction['type'] == 'collection')
                            {{ number_format($transaction['amount'], 2) }} جنيه
                        @else
                            -
                        @endif
                    </td>
                    <td class="amount">
                        @if(isset($transaction['type']) && $transaction['type'] == 'expense')
                            {{ number_format($transaction['amount'], 2) }} جنيه
                        @else
                            -
                        @endif
                    </td>
                    <td class="amount">{{ isset($transaction['running_balance']) ? number_format($transaction['running_balance'], 2) . ' جنيه' : '-' }}</td>
                    <td>{{ $transaction['notes'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">لا توجد معاملات</td>
                </tr>
            @endforelse
        </tbody>
        @if(isset($totalCollections) && isset($totalExpenses) && isset($balance))
        <tfoot>
            <tr>
                <th colspan="2">الإجمالي</th>
                <th class="amount">{{ number_format($totalCollections, 2) }} جنيه</th>
                <th class="amount">{{ number_format($totalExpenses, 2) }} جنيه</th>
                <th class="amount">{{ number_format($balance, 2) }} جنيه</th>
                <th></th>
            </tr>
        </tfoot>
        @endif
    </table>

</body>
</html>
