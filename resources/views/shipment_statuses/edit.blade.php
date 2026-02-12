@extends('layouts.app')

@section('title', 'تعديل الحالة')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>تعديل حالة الشحنة</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('shipment-statuses.update', $shipmentStatus->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">اسم الحالة</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $shipmentStatus->name }}" required>
            </div>
                        <div class="row mb-3">
                <div class="col-md-6">
                    <label>ترتيب العرض</label>
                    <input type="number" name="sort_order" value="{{ $shipmentStatus->sort_order }}" class="form-control" required>
                </div>
            <div class="col-md-6">
                <label for="color" class="form-label">لون الحالة (لون صف الشحنة)</label>
                <select class="form-select" name="color" id="color">
                    @foreach($availableColors as $color)
                        <option value="{{ $color }}" {{ $shipmentStatus->color === $color ? 'selected' : '' }}>
                            {{ $color }}
                        </option>
                    @endforeach
                </select>
            </div>
            </div>
</div>

            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
        </form>
    </div>
@endsection


