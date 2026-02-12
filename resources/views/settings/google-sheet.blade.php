@extends('layouts.app')

@section('title', 'إعدادات Google Sheet')

@section('content')
<div class="card">
    <div class="card-header"><strong><i class="bi bi-google"></i> إعدادات استيراد من Google Sheet</strong></div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.google_sheet.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Google Sheet Info --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="google_sheet_id" class="form-label">📄 Google Sheet ID</label>
<input type="text" name="spreadsheet_id" id="google_sheet_id"
    class="form-control @error('spreadsheet_id') is-invalid @enderror"
    value="{{ old('spreadsheet_id', $settings['spreadsheet_id'] ?? '') }}" >

                </div>

                <div class="col-md-6 mb-3">
                    <label for="google_sheet_range" class="form-label">📌 النطاق (Range)</label>
<input type="text" name="sheet_range" id="google_sheet_range"
    class="form-control @error('sheet_range') is-invalid @enderror"
    value="{{ old('sheet_range', $settings['sheet_range'] ?? 'Sheet1!A2:Z') }}" >

                </div>
            </div>

            <hr>

            {{-- Column Settings --}}
            <h5 class="mb-3"><i class="bi bi-table"></i> تحديد الأعمدة (بالحرف)</h5>
            <div class="row">
                @foreach([
                    'tracking_number_column' => 'رقم التتبع',
                    'customer_name_column' => 'اسم العميل',
                    'customer_phone_column' => 'الهاتف',
                    'governorate_column' => 'المحافظة',
                    'customer_address_column' => 'العنوان',
                    'unit_price_column' => 'سعر القطعة',
                    'total_amount_column' => 'الإجمالي',
                    'product_name_column' => 'اسم المنتج',
                    'color_type_column' => 'اللون والنوع',
                ] as $key => $label)
                    <div class="col-md-4 mb-3">
                        <label for="{{ $key }}" class="form-label">حرف عمود {{ $label }}</label>
                        <input type="text" name="{{ $key }}" id="{{ $key }}"
                            class="form-control"
                            value="{{ old($key, $settings[$key] ?? '') }}"
                            maxlength="2" placeholder="مثلاً: A أو Z" >
                    </div>
                @endforeach
            </div>

            <hr>

            {{-- Credentials File --}}
            <div class="mb-3">
                <label for="credentials_json" class="form-label"><i class="bi bi-file-earmark-lock"></i> ملف Google credentials.json</label>
                <input type="file" name="credentials_json" id="credentials_json" class="form-control">
                @if(isset($settings['credentials_uploaded']) && $settings['credentials_uploaded'])
                    <small class="text-success">✅ تم رفع الملف مسبقًا</small>
                @else
                    <small class="form-text text-muted">يجب أن يكون ملف بصيغة .json</small>
                @endif
            </div>

            {{-- Submit --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-circle"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
