@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>قائمة المنتجات</h5>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">➕ إضافة منتج جديد</a>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>اسم المنتج</th>
                            <th>السعر</th>
                            <th>التكلفة</th>
                            <th>الربح لكل قطعة</th>
                            <th>الألوان</th>
                            <th>المقاسات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->price }} ج.م</td>
                                <td>{{ number_format($product->cost_price, 2) }} ج.م</td>
                                <td>{{ number_format($product->price - $product->cost_price, 2) }} ج.م</td>
                                <td>{{ $product->colors }}</td>
                                <td>{{ $product->sizes }}</td>
                                <td>
                                  <a href="/products/{{ $product->id }}/edit" class="btn btn-warning btn-sm">تعديل</a>
                                    <form action="/products/{{ $product->id }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('هل أنت متأكد من الحذف؟')" class="btn btn-danger btn-sm">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">لا توجد منتجات مضافة حتى الآن.</div>
        @endif
    </div>
</div>
@endsection
