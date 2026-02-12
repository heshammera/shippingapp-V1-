<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #000;
        }


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        tr.table-success { background-color: #d1e7dd !important; }
        tr.table-danger { background-color: #f8d7da !important; }
        tr.table-primary { background-color: #cfe2ff !important; }

        @media print {
            tr.table-success, tr.table-danger, tr.table-primary {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
