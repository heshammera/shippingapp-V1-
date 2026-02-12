@extends('layouts.app')

@section('title', 'تعديل المنتج')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>تعديل المنتج</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">اسم المنتج</label>
                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">السعر</label>
                <input type="number"  name="price" class="form-control" value="{{ $product->price }}" step="1.00" required>
            </div>
            
            <div class="mb-3">
                <label for="cost_price" class="form-label">تكلفة المنتج</label>
                <input type="number" step="1.00" name="cost_price" id="cost_price" class="form-control" value="{{ $product->cost_price }}">
            </div>

            <div class="mb-3">
                <label class="form-label">الألوان (افصل بين كل لون بفاصلة)</label>
                <input type="text" name="colors" class="form-control" value="{{ $product->colors }}">
            </div>

            <div class="mb-3">
                <label class="form-label">المقاسات (افصل بين كل مقاس بفاصلة)</label>
                <input type="text" name="sizes" class="form-control" value="{{ $product->sizes }}">
            </div>

            <button type="submit" class="btn btn-success">تحديث</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection
