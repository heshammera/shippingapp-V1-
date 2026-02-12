@extends('layouts.advanced_reports')

@section('title', 'التقارير المتقدمة')

@section('content')
<div class="container-fluid">
    {{-- عنوان مع زر الوصول للموقع القديم --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📊 التقارير المتقدمة</h2>
            <p class="text-muted mb-0">تقارير تفاعلية مع رسوم بيانية ومؤشرات أداء</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right"></i> التقارير الكلاسيكية
        </a>
    </div>

    {{-- بطاقات التقارير --}}
    <div class="row g-4">
        {{-- تقرير الشحنات --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-box-seam" style="font-size: 80px;"></i>
                    </div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-box-seam fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">تقرير الشحنات</h6>
                                <h3 class="mb-0 fw-bold">المتقدم</h3>
                            </div>
                        </div>
                        <p class="small text-muted mb-4">رسوم بيانية تفاعلية + مؤشرات الأداء + فلاتر ذكية</p>
                        <a href="{{ route('reports-v2.shipments') }}" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-graph-up"></i> عرض التقرير
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-primary bg-opacity-10 border-0 text-center py-2">
                    <small class="text-primary fw-bold">
                        <i class="bi bi-stars"></i> ميزات جديدة
                    </small>
                </div>
            </div>
        </div>

        {{-- تقرير التحصيلات --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-cash-coin" style="font-size: 80px;"></i>
                    </div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-cash-coin fs-3 text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">تقرير التحصيلات</h6>
                                <h3 class="mb-0 fw-bold">المتقدم</h3>
                            </div>
                        </div>
                        <p class="small text-muted mb-4">تحليلات عميقة + مقارنات زمنية + تصدير احترافي</p>
                        <a href="{{ route('reports-v2.collections') }}" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-graph-up"></i> عرض التقرير
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-success bg-opacity-10 border-0 text-center py-2">
                    <small class="text-success fw-bold">
                        <i class="bi bi-stars"></i> ميزات جديدة
                    </small>
                </div>
            </div>
        </div>

        {{-- تقرير المصاريف --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-wallet2" style="font-size: 80px;"></i>
                    </div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-danger bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-wallet2 fs-3 text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">تقرير المصاريف</h6>
                                <h3 class="mb-0 fw-bold">المتقدم</h3>
                            </div>
                        </div>
                        <p class="small text-muted mb-4">تصنيف ذكي + رسوم بيانية + تنبيهات تلقائية</p>
                        <a href="{{ route('reports-v2.expenses') }}" class="btn btn-danger btn-sm w-100">
                            <i class="bi bi-graph-up"></i> عرض التقرير
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-danger bg-opacity-10 border-0 text-center py-2">
                    <small class="text-danger fw-bold">
                        <i class="bi bi-stars"></i> ميزات جديدة
                    </small>
                </div>
            </div>
        </div>

        {{-- تقرير الخزنة --}}
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-safe" style="font-size: 80px;"></i>
                    </div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-info bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-safe fs-3 text-info"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-0">تقرير الخزنة</h6>
                                <h3 class="mb-0 fw-bold">المتقدم</h3>
                            </div>
                        </div>
                        <p class="small text-muted mb-4">رصيد تراكمي + مقارنات + تقارير شاملة</p>
                        <a href="{{ route('reports-v2.treasury') }}" class="btn btn-info btn-sm w-100">
                            <i class="bi bi-graph-up"></i> عرض التقرير
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-info bg-opacity-10 border-0 text-center py-2">
                    <small class="text-info fw-bold">
                        <i class="bi bi-stars"></i> ميزات جديدة
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- قسم الميزات الجديدة --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <h4 class="mb-4"><i class="bi bi-rocket-takeoff"></i> الميزات الجديدة في التقارير المتقدمة</h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill fs-4 me-3 flex-shrink-0"></i>
                                <div>
                                    <h6 class="mb-1">رسوم بيانية تفاعلية</h6>
                                    <small class="opacity-75">ApexCharts احترافية</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill fs-4 me-3 flex-shrink-0"></i>
                                <div>
                                    <h6 class="mb-1">مؤشرات أداء KPIs</h6>
                                    <small class="opacity-75">مقارنات زمنية ذكية</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill fs-4 me-3 flex-shrink-0"></i>
                                <div>
                                    <h6 class="mb-1">فلاتر محسّنة</h6>
                                    <small class="opacity-75">Flatpickr + Select2</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle-fill fs-4 me-3 flex-shrink-0"></i>
                                <div>
                                    <h6 class="mb-1">تصدير احترافي</h6>
                                    <small class="opacity-75">PDF/Excel محسّن</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.opacity-10 {
    opacity: 0.1;
}
</style>
@endsection
