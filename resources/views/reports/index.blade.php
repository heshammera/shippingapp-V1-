@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
<div class="row">
   <div class="col-md-6 mb-4">
    <div class="card h-100">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">تقرير الشحنات</h5>
        </div>
        <div class="card-body">
            <p class="card-text">عرض وتصدير تقارير الشحنات حسب شركة الشحن، الحالة، المندوب، والتاريخ.</p>
            
            <form action="{{ route('reports.shipments') }}" method="GET" class="mb-4">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="shipping_company_id" class="form-label">شركة الشحن</label>
                        <select name="shipping_company_id" id="shipping_company_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($shippingCompanies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status_id" class="form-label">حالة الشحنة</label>
                        <select name="status_id" id="status_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($shipmentStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="delivery_agent_id" class="form-label">المندوب</label>
                        <select name="delivery_agent_id" id="delivery_agent_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($deliveryAgents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date_from" class="form-label">من تاريخ</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-6">
                        <label for="date_to" class="form-label">إلى تاريخ</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> عرض التقرير
                    </button>

                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download"></i> تصدير
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button type="submit" class="dropdown-item" formaction="{{ route('reports.shipments.excel') }}" formtarget="_blank">
                                    <i class="bi bi-file-excel"></i> تصدير Excel
                                </button>
                            </li>
                            <li>
                                <button type="submit" class="dropdown-item" formaction="{{ route('reports.shipments.pdf') }}" formtarget="_blank">
                                    <i class="bi bi-file-pdf"></i> تصدير PDF
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">تقرير التحصيلات</h5>
            </div>
            <div class="card-body">
                <p class="card-text">عرض وتصدير تقارير التحصيلات حسب شركة الشحن والتاريخ.</p>
                
                <form action="{{ route('collections.report') }}" method="GET" class="mb-4">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="shipping_company_id" class="form-label">شركة الشحن</label>
                            <select name="shipping_company_id" id="shipping_company_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($shippingCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-search"></i> عرض التقرير
                        </button>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> تصدير
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.collections.excel') }}" formtarget="_blank">
                                        <i class="bi bi-file-excel"></i> تصدير Excel
                                    </button>
                                </li>
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.collections.pdf') }}" formtarget="_blank">
                                        <i class="bi bi-file-pdf"></i> تصدير PDF
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">تقرير المصاريف</h5>
            </div>
            <div class="card-body">
                <p class="card-text">عرض وتصدير تقارير المصاريف حسب التاريخ.</p>
                
                <form action="{{ route('reports.expenses') }}" method="GET" class="mb-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-search"></i> عرض التقرير
                        </button>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> تصدير
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.expenses.excel') }}" formtarget="_blank">
                                        <i class="bi bi-file-excel"></i> تصدير Excel
                                    </button>
                                </li>
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.expenses.pdf') }}" formtarget="_blank">
                                        <i class="bi bi-file-pdf"></i> تصدير PDF
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">تقرير الخزنة</h5>
            </div>
            <div class="card-body">
                <p class="card-text">عرض وتصدير تقرير الخزنة (التحصيلات والمصاريف) حسب التاريخ.</p>
                
                <form action="{{ route('accounting.treasury-report') }}" method="GET" class="mb-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-search"></i> عرض التقرير
                        </button>
                        
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> تصدير
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.treasury.excel') }}" formtarget="_blank">
                                        <i class="bi bi-file-excel"></i> تصدير Excel
                                    </button>
                                </li>
                                <li>
                                    <button type="submit" class="dropdown-item" formaction="{{ route('reports.treasury.pdf') }}" formtarget="_blank">
                                        <i class="bi bi-file-pdf"></i> تصدير PDF
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
