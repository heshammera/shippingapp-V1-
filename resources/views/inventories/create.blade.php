@extends('layouts.app')
@section('title','إضافة سجل مخزون')

@section('content')
@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="post" action="{{ route('inventories.store') }}" class="card card-body">
  @csrf

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="productTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing" type="button" role="tab">
        اختيار منتج موجود
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab">
        إضافة منتج جديد
      </button>
    </li>
  </ul>

  <div class="tab-content" id="productTabContent">

    <!-- اختيار منتج موجود -->
    <div class="tab-pane fade show active" id="existing" role="tabpanel" aria-labelledby="existing-tab">
      <div class="mb-3">
        <label class="form-label">اختر منتج</label>
        <select name="product_id" id="productSelect" class="form-select">
          <option value="">— اختر —</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}"
                    data-colors='@json($p->colors ?? [])'
                    data-sizes='@json($p->sizes ?? [])'>
              {{ $p->name }} (#{{ $p->id }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- قوائم اللون والمقاس خاصة بمنتج موجود فقط -->
      <div id="variantSelectors" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">اللون</label>
          <select name="color" id="colorSelect" class="form-select" required>
            <option value="">— اختر اللون —</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">المقاس</label>
          <select name="size" id="sizeSelect" class="form-select" required>
            <option value="">— اختر المقاس —</option>
          </select>
        </div>
      </div>
    </div>

    <!-- إضافة منتج جديد -->
    <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
      <div class="mb-3">
        <label class="form-label">اسم المنتج الجديد</label>
        <input type="text" name="new_product_name" class="form-control" value="{{ old('new_product_name') }}">
      </div>

      <div class="mb-3">
        <label class="form-label">السعر</label>
        <input type="number" name="new_product_price" class="form-control" step="0.01" value="{{ old('new_product_price', 0) }}">
      </div>

      <div class="mb-3">
        <label class="form-label">الألوان المتاحة (CSV)</label>
        <input type="text" name="new_colors" id="newColorsInput" class="form-control"
               placeholder="أحمر, أسود, رمادي فاتح" value="{{ old('new_colors') }}">
        <small class="text-muted d-block mt-1">افصل بين الألوان بفاصلة، مثال: أحمر, أسود, رمادي فاتح</small>
      </div>

      <div class="mb-3">
        <label class="form-label">المقاسات المتاحة (CSV)</label>
        <input type="text" name="new_sizes" id="newSizesInput" class="form-control"
               placeholder="S, M, L" value="{{ old('new_sizes') }}">
        <small class="text-muted d-block mt-1">مثال: S, M, L أو مقاس (1), مقاس (2)</small>
      </div>
      
      <input type="hidden" name="color" id="newColorValue">
<input type="hidden" name="size"  id="newSizeValue">

      <!-- مفيش قوائم لون/مقاس هنا -->
    </div>

  </div>

  <!-- الحقول المشتركة بين الحالتين -->
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label">الكمية</label>
      <input type="number" name="quantity" class="form-control" min="0" value="{{ old('quantity', 0) }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">حد التنبيه</label>
      <input type="number" name="low_stock_alert" class="form-control" min="0" value="{{ old('low_stock_alert', 5) }}" required>
    </div>
  </div>

  <div class="mt-3">
    <button class="btn btn-primary">حفظ</button>
  </div>
</form>

{{-- سكربت لتعبئة الألوان والمقاسات --}}
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script>
$(function () {
  function fillSelect($select, placeholder, items) {
    $select.empty().append($('<option>', { value: '', text: placeholder }));
    (items || []).forEach(function (val) {
      val = (val ?? '').toString().trim();
      if (val) $select.append($('<option>', { value: val, text: val }));
    });
    if (items && items.length) $select.prop('selectedIndex', 1);
  }

  // NEW: يفك CSV (يدعم الفاصلة العربية والإنجليزية)
  function splitCsv(raw) {
    if (!raw) return [];
    return raw.split(/[\u060C,]/).map(s => s.trim()).filter(Boolean);
  }

  function loadFromExistingProduct() {
    const id = $('#productSelect').val();
    fillSelect($('#colorSelect'), '— اختر اللون —', []);
    fillSelect($('#sizeSelect'),  '— اختر المقاس —', []);
    if (!id) return;

    $.getJSON("{{ route('products.options', ['product' => '___ID___']) }}".replace('___ID___', id))
      .done(function(resp){
        fillSelect($('#colorSelect'), '— اختر اللون —', resp.colors || []);
        fillSelect($('#sizeSelect'),  '— اختر المقاس —', resp.sizes  || []);
      })
      .fail(function(xhr){ console.error('Failed to load options', xhr.responseText); });
  }

  // NEW: يملأ الحقول المخفية في تبويب "جديد" بأول لون/مقاس من الـ CSV
  function loadFromNewInputs() {
    const colors = splitCsv($('#newColorsInput').val());
    const sizes  = splitCsv($('#newSizesInput').val());
    $('#newColorValue').val(colors[0] || '');
    $('#newSizeValue').val(sizes[0]  || '');
  }

  function showExistingSelectors() {
    $('#variantSelectors').removeClass('d-none');
    $('#colorSelect, #sizeSelect').prop('disabled', false).attr('required', true);
  }

  function hideExistingSelectors() {
    $('#variantSelectors').addClass('d-none');
    $('#colorSelect, #sizeSelect').prop('disabled', true).removeAttr('required');
  }

  // تغيير المنتج في تبويب "موجود"
  $('#productSelect').on('change', function(){
    if ($('#existing').hasClass('active')) loadFromExistingProduct();
  });

  // NEW: أي تعديل في حقول CSV داخل تبويب "جديد" يحدّث الحقول المخفية
  $(document).on('input', '#newColorsInput, #newSizesInput', function(){
    if ($('#new').hasClass('active')) loadFromNewInputs();
  });

  // تبديل التبويبات
  $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr('data-bs-target');
    if (target === '#existing') {
      showExistingSelectors();
      loadFromExistingProduct();
    } else if (target === '#new') {
      hideExistingSelectors();
      loadFromNewInputs(); // NEW
    }
  });

  // حالة أولية
  if ($('#existing').hasClass('active')) {
    showExistingSelectors();
    $('#productSelect').trigger('change');
  } else {
    hideExistingSelectors();
    loadFromNewInputs(); // NEW
  }
});
</script>

@endsection
