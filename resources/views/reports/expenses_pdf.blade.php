<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
@font-face {
    font-family: 'Cairo';
    src: url('{{ public_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
}
body {
    font-family: 'Cairo', 'Amiri', sans-serif;
    direction: rtl;
    text-align: right;
    font-size: 14px;
}
h1, h3 {
    text-align: center;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
table, th, td {
    border: 1px solid #000;
}
th, td {
    padding: 6px;
}
th {
    background-color: #f2f2f2;
}
.summary {
    margin-top: 20px;
    font-size: 16px;
}
</style>
</head>
<body>

<h1>تقرير المصاريف</h1>
<h3>
    من {{ request('date_from') ?? 'بداية المدة' }}
    إلى {{ request('date_to') ?? 'تاريخ اليوم' }}
</h3>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>التاريخ</th>
            <th>الوصف</th>
            <th>المبلغ</th>
            <th>المستخدم</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $expense)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $expense->date }}</td>
            <td>{{ $expense->description }}</td>
            <td>{{ number_format($expense->amount, 2) }} ج.م</td>
            <td>{{ $expense->user->name ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;">لا توجد بيانات</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="summary">
    <strong>إجمالي المصاريف:</strong> {{ number_format($total_expenses, 2) }} ج.م
</div>

</body>
</html>
