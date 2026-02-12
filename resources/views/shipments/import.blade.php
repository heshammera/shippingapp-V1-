@extends('layouts.app')

@section('title', 'استيراد الشحنات من ملف Excel')

@section('actions')
    <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>استيراد الشحنات من ملف Excel</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h5><i class="bi bi-info-circle"></i> إرشادات الاستيراد</h5>
            <p>يرجى اتباع الإرشادات التالية لضمان استيراد البيانات بشكل صحيح:</p>
            <ol>
                <li>يجب أن يحتوي ملف Excel على الأعمدة التالية: <strong>tracking_number, customer_name, customer_phone, customer_address, product_name, product_description, quantity, cost_price, selling_price, shipping_date, notes</strong></li>
                <li>يجب أن يكون الصف الأول من الملف هو أسماء الأعمدة</li>
                <li>يجب أن تكون قيم الأعمدة الرقمية (quantity, cost_price, selling_price) أرقاماً صحيحة</li>
                <li>يجب أن يكون تنسيق التاريخ (shipping_date) بصيغة YYYY-MM-DD</li>
                <li>سيتم تعيين حالة جميع الشحنات المستوردة إلى "عُهدة" بشكل افتراضي</li>
                <li>سيتم ربط جميع الشحنات المستوردة بشركة الشحن التي تختارها</li>
            </ol>
        </div>
        
        <form action="{{ route('shipments.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
<div class="row mt-4 px-2">
        <div class="col-md-6 mb-3">

                <label for="shipping_company_id" class="form-label">شركة الشحن <span class="text-danger">*</span></label>
                <select class="form-select @error('shipping_company_id') is-invalid @enderror" id="shipping_company_id" name="shipping_company_id" required>
                    <option value="">اختر شركة الشحن</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('shipping_company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('shipping_company_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
    <div class="col-md-6 mb-3">
                <label for="file" class="form-label">ملف Excel <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">الصيغ المدعومة: xlsx, xls, csv</div>
            </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> استيراد
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
