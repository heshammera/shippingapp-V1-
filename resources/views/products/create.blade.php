@extends('layouts.app')

@section('title', 'إضافة منتج جديد')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>إضافة منتج جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">اسم المنتج</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">السعر</label>
                <input type="number" step="1.00" name="price" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="cost_price" class="form-label">تكلفة المنتج</label>
                <input type="number" step="1.00" name="cost_price" id="cost_price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">الألوان (افصل بين كل لون بفاصلة)</label>
                <input type="text" name="colors" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">المقاسات (افصل بين كل مقاس بفاصلة)</label>
                <input type="text" name="sizes" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">حفظ</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection
