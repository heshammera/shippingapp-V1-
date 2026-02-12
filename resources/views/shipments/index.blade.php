

<style>
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.page-item {
    margin: 0 3px;
}

.page-link {
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.5;
    border-radius: 6px;
}

.page-link svg, 
.page-link span {
    width: 14px;
    height: 14px;
}

.page-link:hover {
    background-color: #f0f0f0;
}
body, h1, h2, h3, h4, h5, h6, div, span, td, th, p, a, li {
    font-family: 'Cairo', sans-serif !important;
}


<style>
#shipments-table-wrapper {
    overflow-x: auto;
}

#bottom-scrollbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 20px;
    overflow-x: auto;
    z-index: 9999;
    background: #fff;
    border-top: 1px solid #ccc;
}

#bottom-scroll-inner {
    width: 1800px; /* نفس عرض الجدول */
    height: 1px;   /* مجرد عنصر وهمي علشان يظهر الاسكرول */
}
</style>

</style>


@extends('layouts.app')

@section('title', 'إدارة الشحنات')

@section('actions')

@php
    $isDeliveryAgent = auth()->user()->role === 'shipping_agent';
@endphp



@if(!$isDeliveryAgent)
<div class="btn-group" role="group">
    <a href="{{ route('shipments.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg"></i> إضافة شحنة جديدة
    </a>
    <a href="{{ route('shipments.import.form') }}" class="btn btn-sm btn-success">
        <i class="bi bi-file-earmark-excel"></i> استيراد من Excel
    </a>
    <a href="/admin/sync-google-sheet" class="btn btn-info btn-sm">
    🔁 مزامنة مع Google Sheet
</a>

</div>
@endif
@endsection

@section('content')
@if(!$isDeliveryAgent)
<div class="card mb-4">
    <div class="card-header">
        <h5>بحث وتصفية</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('shipments.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                   <label class="form-label">شركة الشحن</label>
<!-- شركة الشحن -->
<select name="company" class="form-select" id="company">
<option value="" {{ request('company') === null || request('company') === '' ? 'selected' : '' }}>الكل</option>
<option value="null" {{ request('company') === 'null' ? 'selected' : '' }}>غير محدد</option>
@foreach($companies as $company)
    <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
        {{ $company->name }}
    </option>
@endforeach

</select>

<!-- الحالة -->

                @endif


                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">الحالة</label>
                  <select name="status" class="form-select" id="status">
<option value="" {{ request('status') === null || request('status') === '' ? 'selected' : '' }}>الكل</option>
@foreach($statuses as $status)
    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
        {{ $status->name }}
    </option>
@endforeach


</select>

                </div>
                

                <div class="col-md-3 mb-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') ? substr(request('date_from'), 0, 10) : '' }}" >
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') ? substr(request('date_to'), 0, 10) : '' }}">

                </div>
                
<div class="col-md-3 mb-3">
    <label class="form-label">بحث</label>
    <div class="input-group">
        <input type="text" name="search" class="form-control" id="search" placeholder="رقم تتبع أو اسم عميل او رقم الهاتف..." value="{{ request('search') }}">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scannerModal">

        <i class="bi bi-upc-scan"></i>
</button>
    </div>
</div>
@if(!$isDeliveryAgent)
                <div class="col-md-3 mb-3">
                    <label class="form-label">حالة الطباعة</label>
                    <select name="printed" class="form-select" id="printed">
                        <option value="">الكل</option>
                        <option value="1" {{ request('printed') == '1' ? 'selected' : '' }}>تمت طباعتها</option>
                        <option value="0" {{ request('printed') == '0' ? 'selected' : '' }}>لم تُطبع بعد</option>
                    </select>
                </div>








                <div class="col-md-3 mb-3">
    <label class="form-label">المنتج</label>
    <select name="product_id" class="form-select" id="product_id">
        <option value="">الكل</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-3 mb-3">
    <label class="form-label">تاريخ الطباعة</label>
    <input type="date" name="print_date" id="print_date" class="form-control" value="{{ request('print_date') }}">
</div>

            </div>
            
            
            
            
            
            
            
            
            
            
            
            
            
            <!-- المودال -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scannerModalLabel">ماسح الباركود</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div id="scanner" style="width:100%; height:300px; background:#000;"></div>
            </div>
        </div>
    </div>
</div>

<!-- مكتبة Quagga -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<script>
let scannerRunning = false;

document.getElementById('scannerModal').addEventListener('shown.bs.modal', function () {
    // تنبيه لو الموبايل مش بالعرض
    if (window.innerHeight > window.innerWidth) {
        alert("📱 من فضلك امسك الموبايل بالعرض (90°) لقراءة الباركود بدقة.");
    }

    // بدء تشغيل الماسح
    if (!scannerRunning) {
        Quagga.init({
            inputStream: {
                type: "LiveStream",
                constraints: { facingMode: "environment" },
                target: document.querySelector('#scanner')
            },
            locator: { patchSize: "x-small", halfSample: false },
            decoder: { readers: ["code_128_reader", "ean_reader", "upc_e_reader", "ean_8_reader"] },
            locate: true
        }, function (err) {
            if (err) {
                console.error(err);
                return;
            }
            Quagga.start();
            scannerRunning = true;
        });
    }

    // عند اكتشاف الباركود
    Quagga.onDetected(function (result) {
        if (result?.codeResult?.code) {
            let code = result.codeResult.code;

            // وضع النتيجة في حقل البحث
            document.getElementById("search").value = code;
            document.getElementById("search").dispatchEvent(new Event("input"));

            // إيقاف الكاميرا والمودال فوراً
            Quagga.stop();
            scannerRunning = false;

            let modalEl = document.getElementById('scannerModal');
            let modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    });
});

// إيقاف الكاميرا عند إغلاق المودال
document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function () {
    if (scannerRunning) {
        Quagga.stop();
        scannerRunning = false;
    }
});


</script>


      
            
            <script>
    // لمنع إرسال الفورم يدويًا
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="shipments.index"]');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
        });
    }
});
</script>

        </form>
    </div>
</div>
@endif





<form id="exportForm" method="GET" action="{{ route('shipments.export.print') }}" target="_blank">
    <input type="hidden" name="ids" id="exportIdsInput">
</form>



@php
    $isShippingAgent = auth()->user()->role === 'shipping_agent';
@endphp


   

<div class="row text-center mb-3">
    <div class="col-md-4">
        <div class="card shadow-sm border-primary">
            <div class="card-body">
                <h6 class="text-muted">إجمالي عدد الشحنات</h6>
                <h4 class="text-primary" id="total-shipments-count">{{ $totalShipments }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <h6 class="text-muted">إجمالي عدد القطع</h6>
                <h4 class="text-success" id="total-pieces-count">{{ $totalPieces }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-danger">
            <div class="card-body">
                <h6 class="text-muted">إجمالي السعر</h6>
                <h4 class="text-danger" id="total-amount-sum">{{ number_format($totalAmountSum) }} ج.م</h4>
            </div>
        </div>
    </div>
</div>


<!-- مودال تأكيد الحذف -->
<div class="modal fade" id="confirmBulkDeleteModal" tabindex="-1" aria-labelledby="confirmBulkDeleteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="bulkDeleteForm" method="POST" action="{{ route('shipments.bulk-delete') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="ids" id="bulkDeleteIdsInput">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmBulkDeleteLabel">تأكيد الحذف</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
        </div>
        <div class="modal-body">
          هل أنت متأكد من حذف الشحنات المحددة؟
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(!$isShippingAgent)

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            
        <button class="btn btn-primary btn-sm" onclick="printSelectedRows()">🖨️ طباعة جدول</button>
        <button class="btn btn-secondary btn-sm" onclick="printInvoices()">🧾 طباعة فواتير</button>
<a href="#" class="btn btn-success btn-sm" id="export-selected-excel">
    <i class="bi bi-file-earmark-excel"></i> تصدير المحدد Excel
</a>
    </div>

<button type="button" class="btn btn-danger btn-sm ms-auto" onclick="openDeleteModalIfSelected()">
    🗑️ حذف المحدد
</button>


    </div>
        
@endif





<div class="card">
    <div class="card-header">
        <h5>قائمة الشحنات</h5>
    </div>
<div id="shipments-table-wrapper" style="overflow-x: auto;">
                    @if($shipments->count() > 0)
    <div id="shipments-table" style="min-width: 1000px;">
          @include('shipments.partials.table', [
    'shipments' => $shipments,
    'statuses' => $statuses,
    'shippingCompanies' => $shippingCompanies,
    'deliveryAgents' => $deliveryAgents
])
    
</div>
        <div class="d-flex justify-content-center mt-4">
        </div>
        @else
        <div class="alert alert-info">
            لا توجد شحنات مطابقة لمعايير البحث.
        </div>
        @endif
    </div>
</div>
<div id="bottom-scrollbar">
    <div id="bottom-scroll-inner"></div>
</div>

<audio id="success-sound" src="{{ asset('sounds/success.mp3') }}"  preload="auto"></audio>

<script>
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast show text-white bg-success position-fixed bottom-0 end-0 m-3';
    toast.role = 'alert';
    toast.style.transition = 'opacity 0.5s';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    setTimeout(() => { toast.remove(); }, 3000);
}


function debounce(func, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => func.apply(this, args), delay);
    };
}


function bindShipmentEvents() {


    // نفس الكلام ممكن تعمله لأي عناصر تانية زي status أو التاريخ
}





function updateShipment(event, url, field, value) {
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // ✅ مهم جداً
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ field: field, value: value })
    })
    .then(async response => {
        // ✅ تأكد إن الاستجابة JSON فعلاً
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('الاستجابة ليست JSON:', text);
            throw new Error('الاستجابة ليست JSON صالحة');
        }
        return response.json();
    })
.then(data => {
    if (data.success && data.color && data.label) {
        showToast('تم الحفظ بنجاح!');
        document.getElementById('success-sound').play();

        const row = event.target.closest('tr');
        const badge = row.querySelector('.shipment-status-badge');

        if (field === 'status_id') {
            row.classList.remove('table-success', 'table-danger', 'table-primary', 'table-secondary');
            badge.classList.remove('bg-success', 'bg-danger', 'bg-primary', 'bg-secondary');

            row.className = data.color;
            badge.className = 'badge shipment-status-badge ' + data.color;
            badge.innerText = data.label;
        }
    } else {
        alert('حدث خطأ أثناء التحديث');
    }
})

    .catch(error => {
        alert('⚠️ حدث خطأ: ' + error.message);
        console.error('تفاصيل الخطأ:', error);
    });
}  






//document.querySelectorAll('.update-shipping-company').forEach(select => {
//    select.addEventListener('change', function () {
//        const shipmentId = this.dataset.id;  // هنا نأخذ الـ id من الـ dataset
//        const selectedCompanyId = this.value;
//
//        fetch(`/shipments/update-shipping-company/${shipmentId}`, {
//            method: 'POST',
//            headers: {
//                'Content-Type': 'application/json',
//                'X-CSRF-TOKEN': '{{ csrf_token() }}'
//            },
//            body: JSON.stringify({ shipping_company_id: selectedCompanyId })
//        })
//        .then(res => res.json())
//        .then(data => {
//            if (data.success) {
//                showToast('✅ تم تحديث شركة الشحن');
//                document.getElementById('success-sound').play();
//            } else {
//                alert('❌ لم يتم الحفظ');
//            }
//        })
//        .catch(err => {
//            alert('❌ خطأ في الاتصال بالسيرفر');
//            console.error(err);
//        });
//    });
//});





// تحديد كل الشحنات
function toggleAll(source) {
    document.querySelectorAll('.select-shipment').forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}


// طباعة الفواتير
function printInvoices() {
    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
    if (selected.length == 0) {
        alert('اختر شحنة واحدة على الأقل');
        return;
    }
    window.open("{{ route('shipments.print.invoices') }}?ids=" + selected.join(','), '_blank');

}
//function printSelectedShipments() {
//    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
//    if (selected.length == 0) {
//        alert('اختر شحنة واحدة على الأقل');
//        return;
//    }
//    window.open("{{ route('shipments.print.selected') }}?ids=" + selected.join(','), '_blank');
//}        <button class="btn btn-primary btn-sm" onclick="printSelectedShipments()">🖨️ طباعة جدول</button>


function fetchShipments() {
    let params = new URLSearchParams({
        company: document.getElementById('company')?.value || '',
        status: document.getElementById('status')?.value || '',
        date_from: document.getElementById('date_from')?.value || '',
        date_to: document.getElementById('date_to')?.value || '',
        search: document.getElementById('search')?.value || '',
        printed: document.getElementById('printed')?.value || '',
        product_id: document.getElementById('product_id')?.value || '',
        print_date: document.getElementById('print_date')?.value || '',
    });

    // هنا حدث رابط المتصفح بالفلترة الجديدة
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState(null, '', newUrl);

    fetch(`/shipments?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('shipments-table').innerHTML = data.table;
        document.getElementById('total-shipments-count').innerText = data.total_shipments;
        document.getElementById('total-pieces-count').innerText = data.total_pieces;
        document.getElementById('total-amount-sum').innerText = data.total_amount_sum + ' ج.م';

        rebindEvents();
        bindPaginationLinks();

    })
    .catch(error => {
        console.error('خطأ في تحميل الشحنات:', error);
    });
}



// ربط الفلاتر
['company', 'status', 'date_from', 'date_to', 'printed', 'product_id', 'print_date'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', fetchShipments);
});
document.getElementById('search')?.addEventListener('input', debounce(fetchShipments, 500));


rebindEvents(); // مهم علشان يرجع يربط كل الأحداث



    


    
function rebindEvents() {
    // شركة الشحن
    document.querySelectorAll('.update-shipping-company').forEach(select => {
        select.addEventListener('change', function () {
            const shipmentId = this.dataset.id;
            const selectedCompanyId = this.value;

            fetch(`/shipments/update-shipping-company/${shipmentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ shipping_company_id: selectedCompanyId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ تم تحديث شركة الشحن');
                    document.getElementById('success-sound').play();
                }
            });
        });
    });

    // الحالة
    document.querySelectorAll('.update-status').forEach(select => {
        select.addEventListener('change', function (e) {
            updateShipment(e, this.dataset.url, 'status_id', this.value);
        });
    });

    // تاريخ الشحن
    document.querySelectorAll('input[name="shipping_date"]').forEach(input => {
        input.addEventListener('change', function (e) {
            updateShipment(e, this.dataset.url, 'shipping_date', this.value);
        });
    });

    // تاريخ الإرجاع
    document.querySelectorAll('.return-date-input').forEach(input => {
        input.addEventListener('change', function () {
            const shipmentId = this.dataset.id;
            const returnDate = this.value;

            fetch(`/shipments/${shipmentId}/update-return-date`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ return_date: returnDate })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ تم تحديث تاريخ الإرجاع');
                    document.getElementById('success-sound').play();
                }
            });
        });
    });

    // تعيين المندوب
    document.querySelectorAll('.assign-agent').forEach(select => {
        select.addEventListener('change', function () {
            const shipmentId = this.dataset.id;
            const agentId = this.value;

            fetch(`/shipments/${shipmentId}/assign-agent`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ delivery_agent_id: agentId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ تم تعيين المندوب');
                    document.getElementById('success-sound').play();
                }
            });
        });
    });
}



// ابدأ ربط الأحداث فور تحميل الصفحة
document.addEventListener('DOMContentLoaded', function () {
    rebindEvents(); // ربط الأحداث
});


    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
// (Duplicate code removed)

function bindPaginationLinks() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(this.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('shipments-table').innerHTML = data.table;
                document.getElementById('total-shipments-count').innerText = data.total_shipments;
                document.getElementById('total-pieces-count').innerText = data.total_pieces;
                rebindEvents();
                bindPaginationLinks(); // 🟢 مهم علشان روابط الصفحات الجديدة تشتغل
            });
        });
    });
}


function printSelectedRows() {
    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        alert('يرجى تحديد شحنة واحدة على الأقل للطباعة');
        return;
    }

    window.open("{{ route('shipments.print.selected') }}?ids=" + selected.join(','), '_blank');
}



function exportSelectedToExcel() {
    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        alert('يرجى تحديد شحنة واحدة على الأقل للتصدير');
        return;
    }

    window.open(`/shipments/export-print?ids=${selected.join(',')}`, '_blank');
}



</script>

@section('scripts')

<script>

let isExporting = false;

document.getElementById('export-selected-excel').addEventListener('click', function (e) {
    e.preventDefault();
    if (isExporting) return;

    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        alert('يرجى تحديد شحنات أولاً.');
        return;
    }

    const url = "{{ route('shipments.export.print') }}?ids=" + selected.join(',');
    isExporting = true;
    window.open(url, '_blank');

    setTimeout(() => {
        isExporting = false;
    }, 3000);
});
</script>



<script>

function openDeleteModalIfSelected() {
    const selected = [...document.querySelectorAll('.select-shipment:checked')].map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('❌ لم يتم تحديد أي شحنات للحذف.');
        return;
    }

    document.getElementById('bulkDeleteIdsInput').value = selected.join(',');
    const modal = new bootstrap.Modal(document.getElementById('confirmBulkDeleteModal'));
    modal.show();
}

//document.querySelector('[data-bs-target="#confirmBulkDeleteModal"]').addEventListener('click', function () {
//    const selected = [...document.querySelectorAll('.select-shipment:checked')]
//        .map(cb => cb.value);
//
//    if (selected.length === 0) {
//        alert('يرجى تحديد شحنات أولاً.');
//        // منع فتح المودال
//        const modalEl = document.getElementById('confirmBulkDeleteModal');
//        const modal = bootstrap.Modal.getInstance(modalEl);
//        if (modal) modal.hide();
//        return;
//    }
//
//    document.getElementById('bulkDeleteIdsInput').value = selected.join(',');
//    
//});





document.addEventListener('change', function (e) {
    if (e.target.classList.contains('update-shipping-company')) {
        const row = e.target.closest('tr');
        const agentSelect = row.querySelector('.assign-agent');

        if (e.target.value == 7) {
            agentSelect.disabled = false;
        } else {
            agentSelect.disabled = true;
            agentSelect.value = '';
        }
    }
});


























</script>



<script>

document.querySelectorAll('.assign-agent').forEach(select => {
    select.addEventListener('change', function () {
        const shipmentId = this.dataset.id;
        const agentId = this.value;

        const url = `/shipments/${shipmentId}/assign-agent`; // ✅ لازم يكون معرف

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ delivery_agent_id: agentId }) // ✅ هنا تمام
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('✅ تم تعيين المندوب بنجاح');
                document.getElementById('success-sound').play();

            } else {
                showToast('❌ فشل في الحفظ');
            }
        })
        .catch(error => {
            console.error('❌', error);
            showToast('⚠️ خطأ في الاتصال بالسيرفر');
        });
    });
});



</script>





<script>
document.addEventListener('DOMContentLoaded', function () {
    const topScroll = document.getElementById('shipments-table-wrapper');
    const bottomScroll = document.getElementById('bottom-scrollbar');

    bottomScroll.addEventListener('scroll', () => {
        topScroll.scrollLeft = bottomScroll.scrollLeft;
    });

    topScroll.addEventListener('scroll', () => {
        bottomScroll.scrollLeft = topScroll.scrollLeft;
    });
});
</script>


@endsection


@endsection
