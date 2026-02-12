@extends('layouts.app')

@section('content')
<style>
    table {
        width: 100%;
        border-collapse: collapse !important;
    }

    thead th {
        background-color: #444 !important;
        color: #fff !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    tbody td {
        background-color: #fefefe !important;
        color: #000 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .table-bordered th, 
    .table-bordered td {
        border: 1px solid #ddd !important;
    }

    .btn-group {
        display: flex;
        justify-content: center;
        gap: 5px;
    }
</style>


<div class="container">
    <h4 class="mb-4">📋 إدارة الحالات</h4>

    <a href="{{ route('shipment-statuses.create') }}" class="btn btn-success mb-3">➕ إضافة حالة جديدة</a>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>م</th>
                <th>اسم الحالة</th>
                <th>ترتيب العرض</th>
                <th>التحكم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statuses as $status)
                <tr>
                    <td>{{ $status->id }}</td>
                    <td>{{ $status->name }}</td>
                    <td>{{ $status->sort_order }}</td>
                    <td>

    <div class="btn-group" role="group">
        <a href="/shipment-statuses/{{ $status->id }}/edit" class="btn btn-warning btn-sm">تعديل</a>

        <form action="/shipment-statuses/{{ $status->id }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
        </form>
    </div>
</td>


                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
