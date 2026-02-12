




@extends('layouts.app')

@section('title', 'إضافة شحنة جديدة')

@section('actions')

@if(auth()->user()->role == 'admin')
    <a href="{{ route('shipments.index') }}" class="btn btn-secondary">رجوع إلى قائمة الشحنات</a>
@endif

@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5>إضافة شحنة جديدة</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('shipments.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                       <label class="form-label">رقم التتبع</label>
                       <input type="text" name="tracking_number" class="form-control" value="{{ old('tracking_number', $trackingNumber) }}" readonly>
                    @error('tracking_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            
          <div class="row mb-3">
    <div class="col-md-4">
        <label for="customer_name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
        @error('customer_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="customer_phone" class="form-label">رقم هاتف العميل</label>
        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
        @error('customer_phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="alternate_phone" class="form-label">رقم الهاتف البديل</label>
        <input type="text" class="form-control @error('alternate_phone') is-invalid @enderror" id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone') }}">
        @error('alternate_phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

            
<div class="col-md-6">
    <label for="governorate" class="form-label">المحافظة</label>
    <select id="governorate" name="governorate" class="form-select" required>
        <option value="">اختر المحافظة</option>
        @foreach($governorates as $gov)
            <option value="{{ $gov }}" {{ old('governorate') == $gov ? 'selected' : '' }}>
                {{ $gov }}
            </option>
        @endforeach
    </select>
</div>

                <div class="col-md-6">
    <label for="shipping_price" class="form-label">سعر الشحن</label>
    <input type="number" step="0.01" class="form-control" id="shipping_price" name="shipping_price" value="{{ old('shipping_price') }}" >
</div>

            </div>

            
            <div class="mb-3">
                <label for="customer_address" class="form-label">عنوان العميل</label>
                <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="2" required>{{ old('customer_address') }}</textarea>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
<div id="products-container"></div>

<button type="button" id="add-product" class="btn btn-secondary mb-3">➕ إضافة منتج</button>
  
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">الإجمالي</label>
                    <input type="text" name="total_amount" id="total_amount" class="form-control" readonly>
                    
                </div>
<div class="col-md-6">
    <label for="shipping_company_id_display" class="form-label">شركة الشحن</label>

    {{-- input مخفي بيبعت القيمة الفعلية --}}
    <input type="hidden" name="shipping_company_id" value="{{ old('shipping_company_id', $defaultCompanyId) }}">

    {{-- select للعرض فقط --}}
    <select class="form-select" id="shipping_company_id_display" disabled>
        @foreach ($companies as $company)
            <option value="{{ $company->id }}"
                {{ (int)old('shipping_company_id', $defaultCompanyId) === (int)$company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
</div>


                
                
                
     <div class="row mb-3">




                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ
                </button>
            </div>
        </form>
    </div>
</div>
<script>

// لما تتغير الكمية أو المنتج
document.getElementById('quantity')?.addEventListener('input', updateTotal);
document.getElementById('product_id')?.addEventListener('change', function() {
    fetchProductDetails(); // جلب تفاصيل المنتج
    setTimeout(updateTotal, 300); // استنى شوية بعد تحديث السعر
});

// في سكربت fetchProductDetails() بعد ما يحدث السعر، ممكن تضيف:
function fetchProductDetails() {
    var productId = document.getElementById('product_id').value;
    if (!productId) return;

    fetch('/products/' + productId + '/details')
        .then(response => response.json())
        .then(data => {
            document.getElementById('selling_price').value = data.price;

            // الألوان
            let colorSelect = document.getElementById('color');
            colorSelect.innerHTML = '<option value="">-- اختر اللون --</option>';
            data.colors.forEach(function(color) {
                colorSelect.innerHTML += '<option value="' + color + '">' + color + '</option>';
            });

            // المقاسات
            let sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="">-- اختر المقاس --</option>';
            data.sizes.forEach(function(size) {
                sizeSelect.innerHTML += '<option value="' + size + '">' + size + '</option>';
            });

            // تحديث الإجمالي تلقائي لما المنتج يتغير
            updateTotalAmount();
        });
}
</script>

<script>
// أسعار المحافظات
function getShippingCost(governorate) {
    switch (governorate) {
        case 'القاهرة':
        case 'الجيزة':
        case 'القليوبية':
            return 60;
        case 'أسيوط':
        case 'الأقصر':
        case 'أسوان':
        case 'سوهاج':
            return 60;
        case 'الإسكندرية':
            return 60;
        default:
            return 60;
    }
}
document.getElementById('governorate')?.addEventListener('change', updateShippingPrice);

// لما المحافظة تتغير
function updateShippingPrice() {
    var governorate = document.getElementById('governorate').value;
    var shippingPrice = getShippingCost(governorate);
    document.getElementById('shipping_price').value = shippingPrice.toFixed(2);
    updateTotalAmount();
}

// تحديث الإجمالي
function updateTotalAmount() {
    let total = 0;

    document.querySelectorAll('.product-entry').forEach(entry => {
        const qty = parseFloat(entry.querySelector('.product-quantity')?.value || 0);
        const price = parseFloat(entry.querySelector('.product-price')?.value || 0);
        const lineTotal = qty * price;

        total += lineTotal;

        // ✅ هنا نحدث الإجمالي الظاهر لكل منتج
        const lineTotalSpan = entry.querySelector('.product-total');
        if (lineTotalSpan) {
            lineTotalSpan.textContent = lineTotal.toFixed(2);
        }
    });

    const shipping = parseFloat(document.getElementById('shipping_price')?.value || 0);
    total += shipping;

    document.getElementById('total_amount').value = total.toFixed(2);
}



// روابط الأحداث
document.getElementById('quantity')?.addEventListener('input', updateTotal);
document.getElementById('product_id')?.addEventListener('change', function() {
    fetchProductDetails();
    setTimeout(updateTotal, 300);
});

// لما المنتج يتغير وتتفعل الوان ومقاسات والسعر
function fetchProductDetails() {
    var productId = document.getElementById('product_id').value;
    if (!productId) return;

    fetch('/products/' + productId + '/details')
        .then(response => response.json())
        .then(data => {
            document.getElementById('selling_price').value = data.price;

            // الألوان
            let colorSelect = document.getElementById('color');
            colorSelect.innerHTML = '<option value="">-- اختر اللون --</option>';
            data.colors.forEach(function(color) {
                colorSelect.innerHTML += '<option value="' + color + '">' + color + '</option>';
            });

            // المقاسات
            let sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="">-- اختر المقاس --</option>';
            data.sizes.forEach(function(size) {
                sizeSelect.innerHTML += '<option value="' + size + '">' + size + '</option>';
            });

            updateTotal();
        });
}
</script>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    let productIndex = 0;

    document.getElementById('add-product').addEventListener('click', () => {
        const container = document.getElementById('products-container');

        const entry = document.createElement('div');
        entry.className = 'card mb-3 product-entry p-3 border';
        entry.setAttribute('data-index', productIndex);

entry.innerHTML = `
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label>المنتج</label>
            <select class="form-select product-select" name="products[${productIndex}][product_id]" required>
                <option value="">-- اختر --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label>اللون</label>
            <select class="form-select" name="products[${productIndex}][color]" required></select>
        </div>

        <div class="col-md-2">
            <label>المقاس</label>
            <select class="form-select" name="products[${productIndex}][size]" required></select>
        </div>

        <div class="col-md-2">
            <label>الكمية</label>
            <input type="number" class="form-control product-quantity" name="products[${productIndex}][quantity]" min="1" value="1" required>
        </div>

<div class="col-md-2 d-flex flex-column justify-content-start">
    <label>السعر</label>
    <input type="number" class="form-control product-price" name="products[${productIndex}][price]">
<div class="text-muted mt-1 product-line-total" style="font-size: 15px; margin-top: -10px;">
    الإجمالي: <span class="product-total">0.00</span> ج.م
</div>

</div>




        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-danger remove-product">❌</button>
        </div>
    </div>
`;



        container.appendChild(entry);
        productIndex++;
        attachEvents(entry);
        updateTotalAmount(); // 🟢 مهمة جداً لتحديث الإجمالي فوراً

    });





    // كمان اربطه بتغيير سعر الشحن
    document.getElementById('shipping_price')?.addEventListener('input', updateTotalAmount);
});


function attachEvents(entry) {
    const productSelect = entry.querySelector('.product-select');
    const colorSelect = entry.querySelector('[name*="[color]"]');
    const sizeSelect = entry.querySelector('[name*="[size]"]');
    const priceInput = entry.querySelector('.product-price');
    const qtyInput = entry.querySelector('.product-quantity');

    // 🟡 أول استدعاء للإجمالي بعد تحميل الصفحة
    updateTotalAmount();

    // تغيير المنتج => جلب التفاصيل
    productSelect.addEventListener('change', () => {
        const productId = productSelect.value;
        if (!productId) return;

        fetch(`/products/${productId}/details`)
            .then(res => res.json())
            .then(data => {
                // تحديث الألوان
                colorSelect.innerHTML = '';
                data.colors.forEach(color => {
                    colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
                });

                // تحديث المقاسات
                sizeSelect.innerHTML = '';
                data.sizes.forEach(size => {
                    sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
                });

                // تعيين السعر
                priceInput.value = data.price;

                // 🟢 حدث الإجمالي مباشرة بعد تحميل السعر
                updateTotalAmount();
            });
    });

    // عند تغيير الكمية أو السعر
    qtyInput.addEventListener('input', updateTotalAmount);
    priceInput.addEventListener('input', updateTotalAmount);
    // زر الحذف
    entry.querySelector('.remove-product').addEventListener('click', () => {
        entry.remove();
        updateTotalAmount(); // حدث الإجمالي بعد الحذف
    });
}


</script>






