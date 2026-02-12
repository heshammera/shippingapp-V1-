@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد')

@section('actions')
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-right"></i> العودة للقائمة
    </a>
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5>إضافة مستخدم جديد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
           
    <div class="row mb-3">
        <div class="col-md-6">
        <label class="form-label">اسم المستخدم</label>
        <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" class="form-control" required>
        </div>
    </div>


    <div class="row mb-3">
        <div class="col-md-6">
        <label class="form-label">كلمة المرور</label>
        <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6">
        <label class="form-label">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="form-control" required>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
        <label class="form-label">رقم الهاتف</label>
        <input type="text" name="phone" class="form-control">
        </div>
        <div class="col-md-6">
        <label class="form-label">العنوان</label>
        <input type="text" name="address" class="form-control">
        </div>
    </div>

    <div class="row mb-3">
<div class="col-md-6">
    <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
    <select name="role" id="role" class="form-select" required>
        <option value="">اختر الدور</option>
        @foreach (\Spatie\Permission\Models\Role::all() as $role)
            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>



        <div class="col-md-6">
        <label for="expires_days" class="form-label">مدة الصلاحية (بالأيام)</label>
        
<input type="number" name="expires_days" id="expires_days" class="form-control" placeholder="مثلاً: 30" min="1" value="{{ old('expires_days', 30) }}">
        </div>
    </div>

<div id="shipping-company-wrapper" style="display: none;">
    <label for="shipping_company_id">شركة الشحن</label>
    <select name="shipping_company_id" id="shipping_company_id" class="form-control">
        <option value="">اختر شركة الشحن</option>
        @foreach ($shippingCompanies as $company)
            <option value="{{ $company->id }}" {{ old('shipping_company_id', $user->shipping_company_id ?? '') == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="expires_lifetime" id="expires_lifetime" onchange="toggleExpires()">
    <label class="form-check-label" for="expires_lifetime">مدى الحياة</label>
</div>

<script>
function toggleExpires() {
    const checkbox = document.getElementById('expires_lifetime');
    const input = document.getElementById('expires_days');
    input.readonly = checkbox.checked;
    if (checkbox.checked) {
        input.value = '';
    }
}
</script>


           
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">نشط</label>
            </div>
            
    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>

        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function toggleExpires() {
    const checkbox = document.getElementById('expires_lifetime');
    const input = document.getElementById('expires_days');

    if (checkbox.checked) {
        input.setAttribute('readonly', 'readonly');
    } else {
        input.removeAttribute('readonly');
    }
}
</script>
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

