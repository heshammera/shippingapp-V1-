@extends('layouts.app')

@section('title', 'تعديل بيانات المستخدم')

@section('actions')
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')
@php
    $roleLabels = [
        'admin' => 'مشرف',
        'moderator' => 'مودريتور',
        'delivery_agent' => 'مندوب',
        'viewer' => 'مشاهد فقط',
        'accountant' => 'محاسب',
                'shipping_agent' => 'مندوب شركة الشحن',

        
    ];
@endphp
<div class="card">
    <div class="card-header">
        <h5>تعديل بيانات المستخدم: {{ $user->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="text" name="email" class="form-control" value="{{ $user->email }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">كلمة المرور <small class="text-muted">(اتركها فارغة إذا لم ترغب في تغييرها)</small></label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="role_id" class="form-label">الدور <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">اختر الدور</option>
                        @foreach(\Spatie\Permission\Models\Role::all() as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) || $user->role == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div id="shipping-company-wrapper" style="display: none;">
    <label for="shipping_company_id">شركة الشحن</label>
<select name="shipping_company_id" class="form-control">
    @foreach($shippingCompanies as $company)
        <option value="{{ $company->id }}" {{ $user->shipping_company_id == $company->id ? 'selected' : '' }}>
            {{ $company->name }}
        </option>
    @endforeach
</select>

</div>

            <div class="mb-3">
                <label for="address" class="form-label">العنوان</label>
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="row mb-3">
            <div class="col-md-6 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">نشط</label>
            </div>
            <div class="col-md-6">
    <div class="col-md-6">
        <label for="expires_days" class="form-label">مدة الصلاحية (بالأيام)</label>
        <input type="number" name="expires_days" id="expires_days"
               class="form-control"
               value="{{ old('expires_days', $user->expires_at ? $user->expires_at->diffInDays(now()) : '') }}"
               placeholder="مثلاً: 30"
               min="1"
               {{ $user->expires_at === null ? 'readonly' : '' }}>
    </div>
@if($user->expires_at)
    <div class="text-muted mt-1">
        تاريخ الانتهاء: {{ $user->expires_at }} / الأيام المتبقية: {{ $expires_days }}
    </div>
@endif

    <div class="col-md-6 d-flex align-items-center">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="expires_lifetime" id="expires_lifetime"
                   onchange="toggleExpires()" {{ $user->expires_at === null ? 'checked' : '' }}>
            <label class="form-check-label" for="expires_lifetime">مدى الحياة</label>
        </div>
    </div>
</div>
</div>

<script>
    function toggleExpires() {
        const checkbox = document.getElementById('expires_lifetime');
        const input = document.getElementById('expires_days');
        if (checkbox.checked) {
            input.value = '';
            input.setAttribute('readonly', 'readonly');
        } else {
            input.removeAttribute('readonly');
        }
    }
</script>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const shippingCompanyWrapper = document.getElementById('shipping-company-wrapper');

function toggleShippingCompany() {
    if (roleSelect.value === 'delivery_agent' || roleSelect.value === 'shipping_agent') {
        shippingCompanyWrapper.style.display = 'block';
    } else {
        shippingCompanyWrapper.style.display = 'none';
        document.getElementById('shipping_company_id').value = '';
    }
}


        roleSelect.addEventListener('change', toggleShippingCompany);

        // تفعيل الحالة الحالية عند تحميل الصفحة (لتحقق لو كانت صفحة تعديل)
        toggleShippingCompany();
    });
</script>

@endsection
