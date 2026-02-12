@extends('layouts.app')
@section('title', 'تعديل أسعار المنتج')

@section('content')
<div class="container">
    <h3>تعديل أسعار المنتج: {{ $product->name }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('product.prices.update', $product->id) }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الحد الأدنى للكمية</th>
                    <th>السعر لكل قطعة</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody id="priceRows">
                @foreach ($prices as $index => $price)
                <tr>
                    <td><input type="number" name="prices[{{ $index }}][min_qty]" class="form-control" value="{{ $price->min_qty }}" required></td>
                    <td><input type="number" name="prices[{{ $index }}][price]" class="form-control" step="0.01" value="{{ $price->price }}" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">✖</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" onclick="addRow()">➕ إضافة سعر جديد</button>
        <br><br>
        <button type="submit" class="btn btn-primary">💾 حفظ</button>
    </form>
</div>

<script>
function addRow() {
    let index = document.querySelectorAll('#priceRows tr').length;
    let row = `
        <tr>
            <td><input type="number" name="prices[${index}][min_qty]" class="form-control" required></td>
            <td><input type="number" name="prices[${index}][price_per_unit]" class="form-control" step="0.01" required></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">✖</button></td>
        </tr>`;
    document.getElementById('priceRows').insertAdjacentHTML('beforeend', row);
}
function removeRow(button) {
    button.closest('tr').remove();
}
</script>
@endsection
