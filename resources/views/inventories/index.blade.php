@extends('layouts.app')
@section('title','المخزون')

@section('content')
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('warning')) <div class="alert alert-warning">{{ session('warning') }}</div> @endif
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <h4 class="mb-0">المخزون</h4>
    <a href="{{ route('inventories.create') }}" class="btn btn-primary">إضافة سجل جديد</a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form class="mb-0" method="get">
      <div class="row g-2">
        <div class="col-md-6">
          <input type="text"
                 name="search"
                 class="form-control"
                 placeholder="بحث بالاسم/اللون/المقاس"
                 value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
          <select name="show" class="form-select" onchange="this.form.submit()">
            <option value=""        {{ request('show') === null || request('show') === '' ? 'selected' : '' }}>النشطة فقط</option>
            <option value="trashed" {{ request('show') === 'trashed' ? 'selected' : '' }}>المحذوفة فقط</option>
            <option value="all"     {{ request('show') === 'all' ? 'selected' : '' }}>الكل</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-secondary w-100">تصفية</button>
        </div>
      </div>
    </form>
  </div>
</div>


<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle mb-0">
        <thead class="table-light">
          <tr class="text-center">
            <th>المنتج</th>
            <th>اللون</th>
            <th>المقاس</th>
            <th>الكمية</th>
            <th>حد التنبيه</th>
            <th style="min-width:540px">عمليات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inventories as $inv)
          @php
            $unlimited = property_exists($inv, 'is_unlimited') ? (bool)$inv->is_unlimited : is_null($inv->quantity);
          @endphp
          <tr class="{{ $inv->isLow() ? 'table-warning' : '' }}">
            <td>{{ $inv->product->name }}</td>
            <td>{{ $inv->color }}</td>
            <td>{{ $inv->size }}</td>
            <td class="text-center">
              @if($unlimited)
                <span class="badge bg-success">غير محدود</span>
              @else
                {{ $inv->quantity }}
              @endif
            </td>
            <td class="text-center">{{ $inv->low_stock_alert }}</td>
            <td>
              <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- تحديث حد التنبيه --}}
                <form action="{{ route('inventories.alert', $inv) }}" method="POST" class="d-flex align-items-center" style="gap:.25rem">
                  @csrf
                  @method('PUT')
                  <input type="number" name="low_stock_alert" class="form-control form-control-sm" value="{{ $inv->low_stock_alert }}" min="0" style="width:110px">
                  <button class="btn btn-sm btn-primary">تحديث</button>
                </form>

                {{-- إضافة كمية --}}
                <form action="{{ route('inventories.add',$inv) }}" method="post" class="d-flex align-items-center" style="gap:.25rem">
                  @csrf
                  <input type="number" name="qty" class="form-control form-control-sm" min="1" placeholder="+" style="width:90px" {{ $unlimited ? 'disabled' : '' }}>
                  <button class="btn btn-sm btn-success" {{ $unlimited ? 'disabled' : '' }}>إضافة</button>
                </form>

                {{-- خصم كمية --}}
                <form action="{{ route('inventories.remove',$inv) }}" method="post" class="d-flex align-items-center" style="gap:.25rem">
                  @csrf
                  <input type="number" name="qty" class="form-control form-control-sm" min="1" placeholder="-" style="width:90px" {{ $unlimited ? 'disabled' : '' }}>
                  <button class="btn btn-sm btn-danger" {{ $unlimited ? 'disabled' : '' }}>خصم</button>
                </form>

                @if($unlimited)
                  {{-- إظهار نموذج تحديد الكمية لإلغاء غير محدود --}}
                  <button type="button" class="btn btn-sm btn-secondary show-set-qty" data-row-id="{{ $inv->id }}">إلغاء غير محدود</button>
                @else
                  {{-- جعل الكمية غير محدودة مباشرة --}}
                  <form action="{{ route('inventories.setUnlimited', $inv) }}" method="post" class="d-inline-flex make-unlimited">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-sm btn-warning">غير محدود</button>
                  </form>
                @endif

                {{-- تحديد الكمية بعد ما كان غير محدود --}}
                <div class="set-qty-container d-none" data-row-id="{{ $inv->id }}">
                  <form action="{{ route('inventories.setQuantity', $inv) }}" method="post" class="d-flex align-items-center" style="gap:.25rem">
                    @csrf
                    @method('PUT')
                    <input type="number" name="quantity" class="form-control form-control-sm" min="0" placeholder="ادخل الكمية" style="width:120px">
                    <button class="btn btn-sm btn-info">تحديد الكمية</button>
                  </form>
                </div>
                
                
                
                
                {{-- حذف (Soft) --}}
<form action="{{ route('inventories.destroy', $inv) }}" method="POST" class="d-inline"
      onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟');">
  @csrf
  @method('DELETE')
  <button class="btn btn-sm btn-danger">حذف</button>
</form>

{{-- أزرار الاستعادة والحذف النهائي تظهر فقط لو السجل محذوف (Soft) --}}
@if(method_exists($inv, 'trashed') && $inv->trashed())
  <form action="{{ route('inventories.restore', $inv->id) }}" method="POST" class="d-inline">
    @csrf
    <button class="btn btn-sm btn-secondary">استعادة</button>
  </form>

  <form action="{{ route('inventories.forceDelete', $inv->id) }}" method="POST" class="d-inline"
        onsubmit="return confirm('حذف نهائي؟ لا يمكن التراجع.');">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-outline-danger">حذف نهائي</button>
  </form>
@endif

                
                
              </div>
            </td>
          </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">لا يوجد سجلات.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer">
    {{ $inventories->links() }}
  </div>
</div>

{{-- JS: إظهار/إخفاء حقل تحديد الكمية حسب الزر --}}
@push('scripts')
<script>
  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.show-set-qty');
    if (!btn) return;
    var rowId = btn.getAttribute('data-row-id');
    var container = document.querySelector('.set-qty-container[data-row-id="' + rowId + '"]');
    if (container) {
      container.classList.remove('d-none');
      btn.classList.add('d-none');
      var input = container.querySelector('input[name="quantity"]');
      if (input) input.focus();
    }
  });
</script>
@endpush
@endsection
