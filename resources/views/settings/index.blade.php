@extends('layouts.app')

@section('title', 'الإعدادات العامة')

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('settings.index') }}">الإعدادات العامة</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.notifications') }}">إعدادات الإشعارات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.system') }}">إعدادات النظام</a>
            </li>
            <li class="nav-item">
    <a class="nav-link {{ request()->is('settings/google-sheet') ? 'active' : '' }}" href="{{ route('settings.google_sheet') }}">إعدادات Google Sheet</a>
</li>

        </ul>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="mb-3">معلومات الشركة</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="company_name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="company_address" class="form-label">عنوان الشركة</label>
                        <textarea class="form-control @error('company_address') is-invalid @enderror" id="company_address" name="company_address" rows="3">{{ old('company_address', $settings['company_address']) }}</textarea>
                        @error('company_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}">
                                @error('company_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('company_email') is-invalid @enderror" id="company_email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}">
                                @error('company_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
    <label for="company_logo" class="form-label">شعار الشركة</label>
    <input type="file" class="form-control @error('company_logo') is-invalid @enderror" id="company_logo" name="company_logo">
    @error('company_logo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if (!empty($settings['company_logo']))
        <div class="mt-2">
            <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="شعار الشركة" class="img-thumbnail" style="max-height: 100px;">
        </div>
    @endif
</div>

                </div>
            </div>
            
            <h5 class="mb-3">إعدادات المالية</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="currency" class="form-label">العملة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" value="{{ old('currency', $settings['currency']) }}" required>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_rate" class="form-label">نسبة الضريبة (%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}">
                        @error('tax_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
<h5 class="mb-3">إعدادات الشحنات</h5>

@php
    // لو كنت مرسّل $companies و $statuses من الكنترولر خلاص كويس.
    // لو مش مرسّلهم، السطور التالية تمنع الكراش و تجيبهم سريعًا من الموديل:
    $companies = $companies ?? \App\Models\ShippingCompany::orderBy('name')->get(['id','name']);
    $statuses  = $statuses  ?? \App\Models\ShipmentStatus::orderBy('sort_order')->orderBy('id')->get(['id','name']);
@endphp

<div class="row mb-4">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="default_status_id" class="form-label">الحالة الافتراضية للشحنات <span class="text-danger">*</span></label>
            <select class="form-select @error('default_status_id') is-invalid @enderror" id="default_status_id" name="default_status_id" required>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('default_status_id', $settings['default_status_id']) == $status->id ? 'selected' : '' }}>
                        {{ $status->name }} (ID: {{ $status->id }})
                    </option>
                @endforeach
            </select>
            @error('default_status_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- 👇 الجديد: شركة الشحن الافتراضية + حالة التوصيل الافتراضية + حالة المرتجع الافتراضية --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="default_shipping_company_id" class="form-label">شركة الشحن الافتراضية</label>
            <select class="form-select @error('default_shipping_company_id') is-invalid @enderror" id="default_shipping_company_id" name="default_shipping_company_id">
                <option value="">-- بدون --</option>
                @foreach($companies as $c)
<option value="{{ $c->id }}"
  @if((int) old('default_shipping_company_id', $settings['default_shipping_company_id']) === (int) $c->id) selected @endif>
  {{ $c->name }} (ID: {{ $c->id }})
</option>



                @endforeach
            </select>
            @error('default_shipping_company_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">
                في المنطق الجديد: لو الشحنة خرجت من الشركة الافتراضية لشركة أخرى → يتخصم المخزون، والعكس يرجّع.
            </small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="delivered_status_id" class="form-label">حالة التوصيل الافتراضية</label>
            <select class="form-select @error('delivered_status_id') is-invalid @enderror" id="delivered_status_id" name="delivered_status_id">
                <option value="">-- بدون --</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->id }}" {{ (int) old('delivered_status_id', $settings['delivered_status_id']) === (int) $s->id ? 'selected' : '' }}>

                        {{ $s->name }} (ID: {{ $s->id }})
                    </option>
                @endforeach
            </select>
            @error('delivered_status_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">
                لما الشحنة توصل للحالة دي بنوثّق وقت التسليم <code>delivered_at</code> فقط (بدون لمس المخزون).
            </small>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="returned_status_id" class="form-label">حالة المرتجع الافتراضية</label>
            <select class="form-select @error('returned_status_id') is-invalid @enderror" id="returned_status_id" name="returned_status_id">
                <option value="">-- بدون --</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->id }}" {{ old('returned_status_id', $settings['returned_status_id']) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} (ID: {{ $s->id }})
                    </option>
                @endforeach
            </select>
            @error('returned_status_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted d-block mt-1">
                لما الشحنة توصل للحالة دي بيرجع المخزون تلقائيًا وتتسجّل <code>returned_at</code>.
            </small>
        </div>
    </div>
</div>

            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
