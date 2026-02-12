@extends('layouts.app')

@section('title', 'إضافة مندوب جديد')

@section('actions')
    <a href="{{ route('delivery-agents.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>إضافة مندوب جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('delivery-agents.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="national_id" class="form-label">الرقم القومي</label>
                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" id="national_id" name="national_id" value="{{ old('national_id') }}">
                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="shipping_company_id" class="form-label">شركة الشحن <span class="text-danger">*</span></label>
                    <select class="form-select @error('shipping_company_id') is-invalid @enderror" id="shipping_company_id" name="shipping_company_id" required>
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
                    <label for="max_edit_count" class="form-label">عدد مرات التعديل المسموح بها <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_edit_count') is-invalid @enderror" id="max_edit_count" name="max_edit_count" value="{{ old('max_edit_count', 1) }}" min="1" required>
                    @error('max_edit_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">العنوان</label>
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">ملاحظات</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">نشط</label>
            </div>
            
            <hr>
            

            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    
                    <i class="bi bi-save"></i> حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createUserCheckbox = document.getElementById('create_user');
        const userAccountSection = document.getElementById('user_account_section');
        
        createUserCheckbox.addEventListener('change', function() {
            if (this.checked) {
                userAccountSection.classList.remove('d-none');
            } else {
                userAccountSection.classList.add('d-none');
            }
        });
    });
</script>
@endsection
