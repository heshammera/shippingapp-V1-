@extends('layouts.app')

@section('title', 'تعديل الشحنة')

@section('actions')
    <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>تعديل بيانات الشحنة: {{ $shipment->tracking_number }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('shipments.update', $shipment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tracking_number" class="form-label">رقم التتبع <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}" required readonly>
                    @error('tracking_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
               <div class="col-md-6">
    <label for="shipping_company_id" class="form-label">شركة الشحن</label>
    <select name="shipping_company_id" id="shipping_company_id" class="form-select" required>
        <option value="">اختر شركة الشحن</option>
        @foreach ($companies as $company)
            <option value="{{ $company->id }}" {{ old('shipping_company_id', $shipment->shipping_company_id) == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
</div>

            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $shipment->customer_name) }}" required>
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="customer_phone" class="form-label">رقم هاتف العميل</label>
                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $shipment->customer_phone) }}">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
    <label for="alternate_phone" class="form-label">رقم الهاتف البديل</label>
    <input
  type="text"
  class="form-control @error('alternate_phone') is-invalid @enderror"
  id="alternate_phone"
  name="alternate_phone"
  value="{{ old('alternate_phone', $shipment->alternate_phone) }}">

    @error('alternate_phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

               <div class="col-md-6">
    <label class="form-label">المحافظة</label>
    <select id="governorate" name="governorate" class="form-select" required>
        <option value="">اختر المحافظة</option>
        @foreach($governorates as $gov)
            <option value="{{ $gov }}" {{ old('governorate', $shipment->governorate) == $gov ? 'selected' : '' }}>
                {{ $gov }}
            </option>
        @endforeach
    </select>
</div>
                    <div class="col-md-6">
    <label class="form-label">سعر الشحن</label>
    <input type="number" step="0.01" id="shipping_price" name="shipping_price" class="form-control" value="{{ old('shipping_price', $shipment->shipping_price) }}" >
</div>


            </div>
            
            <div class="mb-3">
                <label for="customer_address" class="form-label">عنوان العميل</label>
                <textarea class="form-control @error('customer_address') is-invalid @enderror" id="customer_address" name="customer_address" rows="2">{{ old('customer_address', $shipment->customer_address) }}</textarea>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
           <div id="products-container">
    @foreach($shipment->products as $index => $product)
        <div class="card mb-3 product-entry p-3 border" data-index="{{ $index }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label>المنتج</label>
                    <select class="form-select product-select" name="products[{{ $index }}][product_id]" required>
                        <option value="">-- اختر --</option>
                        @foreach($products as $item)
                            <option value="{{ $item->id }}" {{ $product->id == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>اللون</label>
<select class="form-select" name="products[{{ $index }}][color]" required>
    @foreach($product->availableColors() as $color)
        <option value="{{ $color }}" {{ $product->pivot->color == $color ? 'selected' : '' }}>
            {{ $color }}
        </option>
    @endforeach
</select>

                </div>
                <div class="col-md-2">
                    <label>المقاس</label>
<select class="form-select" name="products[{{ $index }}][size]" required>
    @foreach($product->availableSizes() as $size)
        <option value="{{ $size }}" {{ $product->pivot->size == $size ? 'selected' : '' }}>
            {{ $size }}
        </option>
    @endforeach
</select>

                </div>
                <div class="col-md-2">
                    <label>الكمية</label>
                    <input type="number" name="products[{{ $index }}][quantity]" class="form-control product-quantity" value="{{ $product->pivot->quantity }}" min="1" required>
                </div>
<div class="col-md-2">
    <label>السعر</label>
<input type="number" name="products[{{ $index }}][price]" class="form-control product-price" value="{{ $product->pivot->price }}" required>
<small class="text-muted d-block mt-1 total-product-price">= {{ number_format($product->pivot->price * $product->pivot->quantity, 2) }} ج.م</small>

</div>

                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger remove-product">❌</button>
                </div>
            </div>
        </div>
    @endforeach
    
    <button type="button" id="add-product" class="btn btn-secondary mb-3">➕ إضافة منتج</button>

</div>
                    <div class="row mb-3">


                 <div class="col-md-6">
    <label class="form-label">الإجمالي</label>
    <input type="number" step="0.01" id="total_amount" name="total_amount" class="form-control" value="{{ old('total_amount', $shipment->total_amount) }}" readonly>
</div>

                 <div class="col-md-6">
    <label for="delivery_agent_id" class="form-label">المندوب</label>
    <select class="form-select" name="delivery_agent_id" id="delivery_agent_id">
        <option value="">اختر مندوب</option>
        @foreach($deliveryAgents as $agent)
            <option value="{{ $agent->id }}" {{ $shipment->delivery_agent_id == $agent->id ? 'selected' : '' }}>
                {{ $agent->name }}
            </option>
        @endforeach
    </select>
</div>
                    </div>


                    <div class="row mb-3">



              
                                <div class="col-md-6">
                    <label for="shipping_date" class="form-label">تاريخ الشحن</label>
                  @php
    $shippingDateFormatted = '';

    if (!empty($shipment->shipping_date)) {
        try {
            $shippingDateFormatted = \Carbon\Carbon::parse(str_replace('/', '-', $shipment->shipping_date))->format('Y-m-d');
        } catch (\Exception $e) {
            $shippingDateFormatted = '';
        }
    }
@endphp

<input type="date"
       class="form-control @error('shipping_date') is-invalid @enderror"
       id="shipping_date" name="shipping_date"
       value="{{ old('shipping_date', $shippingDateFormatted) }}">

                </div>
                
                  <div class="col-md-6">
                    <label for="status_id" class="form-label">الحالة <span class="text-danger">*</span></label>
                    <select class="form-select @error('status_id') is-invalid @enderror" id="status_id" name="status_id" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $shipment->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
</div>
            <div class="row mb-3">

                
                <div class="col-md-6">
                    <label for="delivery_date" class="form-label">تاريخ التسليم</label>
                    <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', $shipment->delivery_date ? date('Y-m-d', strtotime($shipment->delivery_date)) : '') }}">
                    @error('delivery_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="return_date" class="form-label">تاريخ الإرجاع</label>
                    <input type="date" class="form-control @error('return_date') is-invalid @enderror" id="return_date" name="return_date" value="{{ old('return_date', $shipment->return_date ? date('Y-m-d', strtotime($shipment->return_date)) : '') }}">
                    @error('return_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
           
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $shipment->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="agent_notes" class="form-label">ملاحظات المندوب</label>
                    <textarea class="form-control @error('agent_notes') is-invalid @enderror" id="agent_notes" name="agent_notes" rows="3">{{ old('agent_notes', $shipment->agent_notes) }}</textarea>
                    @error('agent_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

//<script>
//document.addEventListener('DOMContentLoaded', function() {
//    fetchProductDetails(true); // ضروري تبعت true أول مرة
//});
//
//    const initialColor = "{{ $shipment->color }}";
//    const initialSize = "{{ $shipment->size }}";
//</script>
//
//
//<script>

document.addEventListener('DOMContentLoaded', function () {
    updateTotalAmount();
 


    document.getElementById('governorate')?.addEventListener('change', updateShippingPrice);
        document.getElementById('shipping_price')?.addEventListener('input', updateTotalAmount);
document.querySelectorAll('.product-entry').forEach(entry => {
    attachEvents(entry); // دا بيعمل fetch للألوان والمقاسات والسعر تلقائي
});
});


function fetchProductDetails(isFirstLoad = false) {
    var productId = document.getElementById('product_id').value;
    if (!productId) return;

    fetch('/products/' + productId + '/details')
        .then(response => response.json())
        .then(data => {
            
            var colorSelect = document.getElementById('color');
            var sizeSelect = document.getElementById('size');

            colorSelect.innerHTML = '';
            sizeSelect.innerHTML = '';

            data.colors.forEach(function(color) {
                var option = document.createElement('option');
                option.value = color;
                option.textContent = color;

                // 🟢 هنا:
                if (isFirstLoad && color === initialColor) {
                    option.selected = true;
                }
                colorSelect.appendChild(option);
            });

            data.sizes.forEach(function(size) {
                var option = document.createElement('option');
                option.value = size;
                option.textContent = size;

                if (isFirstLoad && size === initialSize) {
                    option.selected = true;
                }
                sizeSelect.appendChild(option);
            });
// ✅ بعد التعديل:
if (!isFirstLoad) {
document.getElementById('selling_price').value = data.price || 0;
}            updateTotalAmount();

        });
        
}


function updateShippingPrice() {
    var gov = document.getElementById('governorate').value;
    var price = 0;
    if (['القاهرة', 'الجيزة', 'القليوبية'].includes(gov)) {
        price = 60;
    } else if (['أسيوط', 'الأقصر', 'أسوان', 'سوهاج'].includes(gov)) {
        price = 60;
    } else if (gov) {
        price = 60;
    }
    document.getElementById('shipping_price').value = price.toFixed(2);

    // ✅ احسب الإجمالي صح
    updateTotalAmount();
}


function updateTotalAmount() {
    let total = 0;

    document.querySelectorAll('.product-entry').forEach(entry => {
        const qty = parseInt(entry.querySelector('.product-quantity')?.value || 0);
        const price = parseFloat(entry.querySelector('.product-price')?.value || 0);
        const productTotal = qty * price;
        total += productTotal;

        const totalEl = entry.querySelector('.total-product-price');
        if (totalEl) {
            totalEl.textContent = `= ${productTotal.toFixed(2)} ج.م`;
        }
    });

    const shipping = parseFloat(document.getElementById('shipping_price')?.value || 0);
    total += shipping;

    document.getElementById('total_amount').value = total.toFixed(2);
}



</script>



<script>
function fetchShipments() {
    let params = new URLSearchParams({
        company: document.getElementById('company')?.value || '',
        status: document.getElementById('status')?.value || '',
        date_from: document.getElementById('date_from')?.value || '',
        date_to: document.getElementById('date_to')?.value || '',
        search: document.getElementById('search')?.value || '',
    });

    fetch(`/shipments?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        // تحديث محتوى الجدول
        document.getElementById('shipments-table').innerHTML = html;

        // إعادة تهيئة المودالات
        document.querySelectorAll('.modal').forEach(modal => {
            new bootstrap.Modal(modal);
        });

        // تفعيل زر الحذف
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const modalEl = document.querySelector(targetId);
                if (modalEl) {
                    const modalInstance = new bootstrap.Modal(modalEl);
                    modalInstance.show();
                }
            });
        });
    });
}

// ربط الأحداث لكل الفلاتر
['company', 'status', 'date_from', 'date_to'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', fetchShipments);
});
document.getElementById('search')?.addEventListener('input', debounce(fetchShipments, 300));

// تأخير التنفيذ عند الكتابة
function debounce(callback, delay) {
    let timeout;
    return function () {
        clearTimeout(timeout);
        timeout = setTimeout(callback, delay);
    };
    
}
rebindDeleteModals();

function rebindDeleteModals() {
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.removeEventListener('click', handleModalOpen); // منع التكرار
        button.addEventListener('click', handleModalOpen);
    });
}

function handleModalOpen() {
    const targetId = this.getAttribute('data-bs-target');
    const modalEl = document.querySelector(targetId);
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
}
</script>

<script>
let productIndex = {{ $shipment->products->count() }};

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
                    @foreach($products as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
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
                <input type="number" name="products[${productIndex}][quantity]" class="form-control product-quantity" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label>السعر</label>
                <input type="number" name="products[${productIndex}][price]" class="form-control product-price">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger remove-product">❌</button>
            </div>
        </div>
    `;

container.appendChild(entry);
attachEvents(entry);
updateTotalAmount(); // ✅ دي أهم حاجة
productIndex++;

});







function attachEvents(entry) {
    const productSelect = entry.querySelector('.product-select');
    const colorSelect = entry.querySelector('[name*="[color]"]');
    const sizeSelect = entry.querySelector('[name*="[size]"]');
    const priceInput = entry.querySelector('.product-price');
    const qtyInput = entry.querySelector('.product-quantity');
const priceContainer = priceInput.parentElement;
let totalDisplay = priceContainer.querySelector('.total-product-price');
if (!totalDisplay) {
    totalDisplay = document.createElement('small');
    totalDisplay.className = 'text-muted d-block mt-1 total-product-price';
    priceContainer.appendChild(totalDisplay);
}

    // ✅ لما أختار منتج، يجيب السعر + الألوان + المقاسات
    productSelect.addEventListener('change', () => {
        const productId = productSelect.value;
        if (!productId) return;

        fetch(`/products/${productId}/details`)
            .then(res => res.json())
            .then(data => {
                colorSelect.innerHTML = '';
                data.colors.forEach(color => {
                    colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
                });

                sizeSelect.innerHTML = '';
                data.sizes.forEach(size => {
                    sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
                });

                priceInput.value = data.price;

                updateTotalAmount();
            });
    });

    qtyInput.addEventListener('input', updateTotalAmount);
    priceInput.addEventListener('input', updateTotalAmount);
productSelect.addEventListener('change', updateTotalAmount);
document.getElementById('shipping_price')?.addEventListener('input', updateTotalAmount);

    entry.querySelector('.remove-product').addEventListener('click', () => {
        entry.remove();
        updateTotalAmount();
    });
}












function updateTotalAmount() {
    let total = 0;

    document.querySelectorAll('.product-entry').forEach(entry => {
        const qty = parseInt(entry.querySelector('.product-quantity')?.value || 0);
        const price = parseFloat(entry.querySelector('.product-price')?.value || 0);
        const productTotal = qty * price;
        total += productTotal;

        const totalEl = entry.querySelector('.total-product-price');
        if (totalEl) {
            totalEl.textContent = `= ${productTotal.toFixed(2)} ج.م`;
        }
    });

    const shipping = parseFloat(document.getElementById('shipping_price')?.value || 0);
    total += shipping;

    document.getElementById('total_amount').value = total.toFixed(2);
}



</script>

@endsection
