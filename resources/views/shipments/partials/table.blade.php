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
.badge-price {
    background-color: #5a9bd4; /* لون أزرق مخصص مثلاً */
    color: white;
}






.badge-color-beige {
    color: #F5F5DC;
}
.badge-color-black {
    color: black;
}
.badge-color-white {
    color: white;
    text-shadow: 
       0 0 5px rgba(0, 0, 0, 0.9),
       0 0 10px rgba(0, 0, 0, 0.8),
       0 0 15px rgba(0, 0, 0, 0.7);
}


.badge-color-pepsi-blue {
    color: #005CBF;
}
.badge-color-petrol-blue {
    color: #007C91;
}
.badge-color-wine {
    color: #722F37;
}
.badge-color-olive {
    color: #808000;
}
.badge-color-dark-purple {
    color: #4B0082;
}
.badge-color-mint-green {
    color: #98FF98;
}
.badge-color-gray {
    color: gray;
}
.badge-color-fuchsia {
    color: #FF00FF;
}
.badge-color-pink {
    color: #FFC0CB;
}
.badge-color-blue {
    color: #0000FF;
}
.badge-color-default {
    color: black;
}
/* تقليل عرض العمود لو المستخدم shipping_agent */
@php
    $isShippingAgent = auth()->user()->role === 'shipping_agent';
@endphp

@if($isShippingAgent)
.address-col {
    max-width: 200px; /* عرض أقل */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
@endif
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@stack('scripts')
@if($shipments->count() > 0)
  <div class="table-responsive">


<table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th> <!-- ✅ زرار تحديد الكل -->
                        <th class="text-center">رقم التتبع</th>
                        <th class="text-center">العميل</th>
                        <th class="text-center">رقم الهاتف</th>
                        <th class="text-center">المحافظة </th>
                        <th class="text-center address-col">العنوان </th>

                        <th class="text-center">Product</th>
                        <th class="text-center">TOT</th>

                        <th class="text-center">شركة الشحن</th>
@if(auth()->user()->role !== 'shipping_agent')
    <th class="text-center">المندوب</th>
@endif

                        <th class="text-center">الحالة</th>
                        <th class="text-center">تاريخ الشحن</th>
                        @if(auth()->user()->role !== 'shipping_agent')
                        <th class="text-center">تاريخ الإرجاع</th>

                        <th class="text-center">🖨</th>

                        
        <th>الإجراءات</th>
                        @endif

                    </tr>
                </thead>
                <tbody>
                    @foreach($shipments as $shipment)
                    
<tr id="shipment-row-{{ $shipment->id }}" class="{{ $shipment->status->color ?? 'table-default' }}">
<td>
      
        <input type="checkbox" class="select-shipment" value="{{ $shipment->id }}">
    </td>
<td style="text-align: center; vertical-align: middle; padding: 0;">
    <div style="display: inline-block; margin: 0; padding: 0; line-height: 1;">
        {!! DNS1D::getBarcodeHTML($shipment->tracking_number, 'C128', .6, 40) !!}
    </div>
        <div class="text-center small">{{ $shipment->tracking_number }}</div>

</td>                        <td>{{ $shipment->customer_name }}</td>
                        <td>{{ $shipment->customer_phone }}</td>
                        <td>{{ $shipment->governorate }}</td>
                        <td class="address-col">{{ $shipment->customer_address }}</td>
<td>
    @foreach($shipment->products as $product)
    @php
    $colorClasses = [
        'بيج' => 'badge-color-beige',
        'أسود' => 'badge-color-black',
        'اسود' => 'badge-color-black',
        'ابيض' => 'badge-color-white',
        'ازرق بيبسي' => 'badge-color-pepsi-blue',
        'بترولي' => 'badge-color-petrol-blue',
        'نبيتي' => 'badge-color-wine',
        'زيتي' => 'badge-color-olive',
        'موف' => 'badge-color-dark-purple',
        'منت جرين' => 'badge-color-mint-green',
        'رصاصي' => 'badge-color-gray',
        'فوشيا' => 'badge-color-fuchsia',
        'بينك' => 'badge-color-pink',
        'بلو' => 'badge-color-blue',
    ];

    // خذ كلاس اللون أو خلي كلاس افتراضي لو اللون غير معروف
    $colorClass = $colorClasses[$product->pivot->color] ?? 'badge-color-default';
@endphp

        <div class="text-center">
            <div><strong>{{ $product->name }}</strong></div>
            <div><small>{{ $product->pivot->size ?? 'غير محدد' }}</small></div>
<div><strong class="{{ $colorClass }}">{{ $product->pivot->color }}</strong></div>
<span class="badge badge-price">
    {{ $product->pivot->quantity }} × {{ number_format($product->pivot->price, 2) }} ج.م
</span>

        </div>
        @if(!$loop->last)
            <hr style="margin: 7px 0;">
        @endif
    @endforeach
</td>





                        <td>{{ is_numeric($shipment->total_amount) ? number_format($shipment->total_amount, 0) : '—' }}</td>

 

                        <td>
                             @if(auth()->user()->role === 'shipping_agent')
        {{ $shipment->shippingCompany->name ?? '-' }}
    @else
<select class="form-select form-select update-shipping-company form-select-sm"
        @if($shipment?->id)
    data-url="/shipments/{{ $shipment->id }}/quick-update"
@endif

        data-id="{{ $shipment->id }}">
        @foreach($shippingCompanies as $company)
            <option value="{{ $company->id }}" 
                {{ $shipment->shipping_company_id == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
         @endif
    </select>
                        </td>
                        
                        
@if(auth()->user()->role !== 'shipping_agent')
    <td>
        <select class="form-select assign-agent form-select-sm"
                data-id="{{ $shipment->id }}"
                @if($shipment?->id)
                    data-url="{{ route('shipments.assignAgent', $shipment->id) }}"
                @endif
                {{ $shipment->shipping_company_id == 7 ? '' : 'disabled' }}>
            <option value="" {{ is_null($shipment->delivery_agent_id) ? 'selected' : '' }}>غير محدد</option>
            @foreach($deliveryAgents as $agent)
                <option value="{{ $agent->id }}" {{ $shipment->delivery_agent_id == $agent->id ? 'selected' : '' }}>
                    {{ $agent->name }}
                </option>
            @endforeach
        </select>
    </td>
@endif



                        
<td>
    @if(auth()->user()->role === 'shipping_agent')
        {{ $shipment->status->name ?? 'غير محدد' }}
    @else
<select class="form-select update-status form-select-sm" 
        data-url="/shipments/{{ $shipment->id }}/quick-update">
    @foreach($statuses as $status)
        <option value="{{ $status->id }}" {{ $shipment->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
    @endforeach
</select>

                            
<span class="badge shipment-status-badge {{ $shipment->status->color ?? 'bg-secondary' }}">
    {{ $shipment->status->name ?? 'غير محدد' }}
</span>

    @endif
</td>

                        
{{-- تاريخ الشحن --}}
<td>
    @if(auth()->user()->role !== 'shipping_agent')
        <input type="date"
            class="form-control form-control-sm"
            name="shipping_date"
            data-url="/shipments/{{ $shipment->id }}/quick-update"
            value="{{ $shipment->shipping_date ? \Carbon\Carbon::parse($shipment->shipping_date)->format('Y-m-d') : '' }}">
    @else
        {{ $shipment->shipping_date ? \Carbon\Carbon::parse($shipment->shipping_date)->format('Y-m-d') : '-' }}
    @endif
</td>

{{-- تاريخ الإرجاع --}}
@if(auth()->user()->role !== 'shipping_agent')
<td>
    <input type="date"
        class="form-control return-date-input form-control-sm"
        data-id="{{ $shipment->id }}"
        value="{{ $shipment->return_date ? \Carbon\Carbon::parse($shipment->return_date)->format('Y-m-d') : '' }}">
</td>
@endif

{{-- هل اتطبعت --}}
@if(auth()->user()->role !== 'shipping_agent')
<td>
    @if($shipment->is_printed)
        ✅
    @else
        ❌
    @endif
</td>
@endif

{{-- تاريخ الطباعة + أزرار --}}
<td style="position: relative; padding-top: 18px;">
    @if(auth()->user()->role !== 'shipping_agent')
        {{-- تاريخ الطباعة --}}
        <div style="font-size: 10px; color: gray; position: absolute; top: 4px; left: 4px;">
            {{ $shipment->print_date ? \Carbon\Carbon::parse($shipment->print_date)->format('Y-m-d') : '-' }}
        </div>
    @endif

    {{-- الأزرار --}}
    <div class="btn-group" role="group">
        @if(auth()->user()->role !== 'shipping_agent')
            <a href="/shipments/{{ $shipment->id }}" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i>
            </a>
            <a href="/shipments/{{ $shipment->id }}/edit" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button" class="btn btn-sm btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal{{ $shipment->id }}">
                <i class="bi bi-trash"></i>
            </button>
        @endif
    </div>

    {{-- مودال الحذف --}}
    @if(auth()->user()->role !== 'shipping_agent')
        <div class="modal fade" id="deleteModal{{ $shipment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $shipment->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد أنك تريد حذف الشحنة رقم "{{ $shipment->tracking_number }}"؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        @if($shipment->id)
                            <form action="/shipments/{{ $shipment->id }}/quick-delete" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">حذف</button>
                            </form>
                        @else
                            <span class="text-danger">ID مفقود</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</td>







                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
                </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $shipments->appends(request()->query())->links() }}
    </div>
@else
    <div class="alert alert-info">
        لا توجد شحنات مطابقة لمعايير البحث.
    </div>
@endif
<script>
    // ✅ تشغيل بعد تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.collection-checkbox');
        const exportBtn = document.getElementById('export-selected');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', function (e) {
                e.preventDefault();

                const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                if (selected.length === 0) {
                    alert('يرجى تحديد صفوف أولاً.');
                    return;
                }

                const url = "{{ route('reports.collections.excel') }}?ids=" + selected.join(',');
                window.open(url, '_blank');
            });
        }
    });
</script>
