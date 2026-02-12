@extends('layouts.app')

@section('title', 'إضافة تحصيل جديد')

@section('actions')
    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للتحصيلات
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">إضافة تحصيل جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('collections.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="shipping_company_id" class="form-label">شركة الشحن <span class="text-danger">*</span></label>
                    <select name="shipping_company_id" id="shipping_company_id" class="form-select @error('shipping_company_id') is-invalid @enderror" required>
                        <option value="">اختر شركة الشحن</option>
                        @foreach($shippingCompanies as $company)
                            <option value="{{ $company->id }}" {{ old('shipping_company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('shipping_company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                        <span class="input-group-text">جنيه</span>
                    </div>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="collection_date" class="form-label">تاريخ التحصيل <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('collection_date') is-invalid @enderror" id="collection_date" name="collection_date" value="{{ old('collection_date', date('Y-m-d')) }}" required>
                    @error('collection_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ التحصيل
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
