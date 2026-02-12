<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-cash-coin text-success"></i> تقرير التحصيلات</h2>
            <p class="text-muted mb-0">تحليلات شاملة للتحصيلات</p>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي التحصيلات</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalCollections, 2) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-cash-coin text-success fs-4"></i>
                        </div>
                    </div>
                    @if(isset($comparisonData))
                    <small class="d-flex align-items-center {{ $comparisonData['is_positive'] ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-{{ $comparisonData['is_positive'] ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"></i>
                        {{ abs($comparisonData['percent_change']) }}% عن الفترة السابقة
                    </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">عدد العمليات</p>
                    <h3 class="mb-0 fw-bold">{{ $collections->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-funnel"></i> الفلاتر</h5></div>
        <div class="card-body">
            <form action="" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">شركة الشحن</label>
                        <select name="shipping_company_id" class="form-select select2">
                            <option value="">الكل</option>
                            @foreach($shippingCompanies as $id => $name)
                                <option value="{{ $id }}" {{ request('shipping_company_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الفترة الزمنية</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="اختر الفترة">
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> تطبيق</button>
                        <button type="button" class="btn btn-outline-secondary" id="clearFilters"><i class="bi bi-x-circle"></i> مسح</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-download"></i> تصدير</button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.collections.excel') }}" formtarget="_blank"><i class="bi bi-file-excel"></i> Excel</button></li>
                            <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.collections.pdf') }}" formtarget="_blank"><i class="bi bi-file-pdf"></i> PDF</button></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-graph-up"></i> التحصيلات عبر الزمن</h5></div>
        <div class="card-body"><div id="collectionsChart" style="min-height: 350px;"></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-table"></i> قائمة التحصيلات</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>التاريخ</th><th>شركة الشحن</th><th>المبلغ</th><th>ملاحظات</th></tr>
                    </thead>
                    <tbody>
                        @forelse($collections as $index => $collection)
                        <tr>
                            <td>{{ $collections->firstItem() + $index }}</td>
                            <td>{{ $collection->date ? \Carbon\Carbon::parse($collection->date)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $collection->shippingCompany->name ?? '-' }}</td>
                            <td><span class="badge bg-success">{{ number_format($collection->amount, 2) }}</span></td>
                            <td>{{ Str::limit($collection->notes ?? '', 50) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-2 text-muted">لا توجد تحصيلات</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($collections->hasPages())<div class="card-footer bg-white">{{ $collections->links() }}</div>@endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2').select2({theme: 'bootstrap-5', dir: 'rtl', language: 'ar'});
    flatpickr("#dateRange", {mode: "range", dateFormat: "Y-m-d", locale: "ar", defaultDate: [document.getElementById('date_from').value, document.getElementById('date_to').value], onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            document.getElementById('date_from').value = instance.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('date_to').value = instance.formatDate(selectedDates[1], 'Y-m-d');
        }
    }});
    document.getElementById('clearFilters').addEventListener('click', function() {window.location.href = window.location.pathname;});
    
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);
    
    new ApexCharts(document.querySelector("#collectionsChart"), {
        chart: {type: 'bar', height: 350, toolbar: {show: true}},
        series: [{name: 'التحصيلات', data: chartValues}],
        xaxis: {categories: chartLabels, labels: {rotate: -45, style: {fontFamily: 'Cairo, sans-serif'}}},
        yaxis: {labels: {formatter: function(val) {return val.toFixed(2);}}},
        dataLabels: {enabled: false},
        colors: ['#198754'],
        tooltip: {y: {formatter: function(val) {return val + " جنيه";}}}
    }).render();
});
</script>
@endpush
