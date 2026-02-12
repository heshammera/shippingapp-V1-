<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-wallet2 text-danger"></i> تقرير المصاريف</h2>
            <p class="text-muted mb-0">تحليلات شاملة للمصاريف</p>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="text-muted small mb-1">إجمالي المصاريف</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($totalExpenses, 2) }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded">
                            <i class="bi bi-wallet2 text-danger fs-4"></i>
                        </div>
                    </div>
                    @if(isset($comparisonData))
                    <small class="d-flex align-items-center {{ $comparisonData['is_positive'] ? 'text-danger' : 'text-success' }}">
                        <i class="bi bi-{{ $comparisonData['is_positive'] ? 'arrow-up' : 'arrow-down' }}-circle-fill me-1"></i>
                        {{ abs($comparisonData['percent_change']) }}% عن الفترة السابقة
                    </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">عدد المصاريف</p>
                    <h3 class="mb-0 fw-bold">{{ $expenses->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-funnel"></i> الفلاتر</h5></div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">الفترة الزمنية</label>
                        <input type="text" class="form-control" id="dateRange" placeholder="اختر الفترة">
                        <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-search"></i> تطبيق</button>
                        <button type="button" class="btn btn-outline-secondary" id="clearFilters"><i class="bi bi-x-circle"></i> مسح</button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-danger dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-download"></i> تصدير</button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.expenses.excel') }}" formtarget="_blank"><i class="bi bi-file-excel"></i> Excel</button></li>
                            <li><button type="submit" class="dropdown-item" formaction="{{ route('reports.expenses.pdf') }}" formtarget="_blank"><i class="bi bi-file-pdf"></i> PDF</button></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-graph-up"></i> المصاريف عبر الزمن</h5></div>
        <div class="card-body"><div id="expensesChart" style="min-height: 350px;"></div></div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom"><h5 class="mb-0"><i class="bi bi-table"></i> قائمة المصاريف</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>التاريخ</th><th>العنوان</th><th>المبلغ</th><th>المستخدم</th><th>ملاحظات</th></tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $expenses->firstItem() + $index }}</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>{{ $expense->title }}</td>
                            <td><span class="badge bg-danger">{{ number_format($expense->amount, 2) }}</span></td>
                            <td>{{ $expense->user->name ?? '-' }}</td>
                            <td>{{ Str::limit($expense->notes ?? '', 50) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5"><i class="bi bi-inbox fs-1 text-muted"></i><p class="mt-2 text-muted">لا توجد مصاريف</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())<div class="card-footer bg-white">{{ $expenses->links() }}</div>@endif
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#dateRange", {mode: "range", dateFormat: "Y-m-d", locale: "ar", defaultDate: [document.getElementById('date_from').value, document.getElementById('date_to').value], onChange: function(selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            document.getElementById('date_from').value = instance.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('date_to').value = instance.formatDate(selectedDates[1], 'Y-m-d');
        }
    }});
    document.getElementById('clearFilters').addEventListener('click', function() {window.location.href = window.location.pathname;});
    
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);
    
    new ApexCharts(document.querySelector("#expensesChart"), {
        chart: {type: 'line', height: 350, toolbar: {show: true}},
        series: [{name: 'المصاريف', data: chartValues}],
        xaxis: {categories: chartLabels, labels: {rotate: -45, style: {fontFamily: 'Cairo, sans-serif'}}},
        stroke: {curve: 'smooth', width: 2},
        colors: ['#dc3545'],
        fill: {type: 'gradient', gradient: {shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2}},
        tooltip: {y: {formatter: function(val) {return val + " جنيه";}}}
    }).render();
});
</script>
@endpush
