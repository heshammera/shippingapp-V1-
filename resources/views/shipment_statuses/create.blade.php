@section('styles')
<style>
.table-pink td {
    background-color: #f7d6e6 !important;
    color: #842029 !important;
}

.table-orange td {
    background-color: #ffe5b4 !important;
    color: #663c00 !important;
}

.table-purple td {
    background-color: #e6d6ff !important;
    color: #4b0082 !important;
}
</style>
@endsection


@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">➕ إضافة حالة جديدة</h4>

    <form method="POST" action="{{ route('shipment-statuses.store') }}">
        @csrf
 <div class="row mb-3">
                <div class="col-md-6">
                    <label>اسم الحالة</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label>ترتيب العرض</label>
                    <input type="number" name="sort_order" class="form-control" value="0" required>
                </div>
            </div>
                                   <div class="row mb-3">
            <div class="col-md-6">
                <label>لون الصف (Bootstrap class)</label>
<select name="color" class="form-select">
    <option value="">بدون لون</option>
    <option value="table-success">أخضر - تم التسليم</option>
    <option value="table-warning">أصفر - جاري الشحن</option>
    <option value="table-danger">أحمر - مرتجع</option>
    <option value="table-primary">أزرق - قيد التنفيذ</option>
    <option value="table-info">سماوي - متابعة</option>
    <option value="table-secondary">رمادي - غير محدد</option>
    <option value="table-light">فاتح - عام</option>
    <option value="table-dark">داكن - منتهي</option>
    <option value="table-pink">بينكي</option>
<option value="table-orange">برتقالي</option>
<option value="table-purple">بنفسجي</option>

</select>
            </div>

            <div class="col-md-6">
             <button type="submit" class="btn btn-success">💾 حفظ</button>
            </div>
            </div>
    </form>
</div>
@endsection
