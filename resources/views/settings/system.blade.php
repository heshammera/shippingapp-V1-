@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.index') }}">الإعدادات العامة</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.notifications') }}">إعدادات الإشعارات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('settings.system') }}">إعدادات النظام</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update-system') }}" method="POST">
            @csrf
            
            <h5 class="mb-3">إعدادات العرض</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pagination_limit" class="form-label">عدد العناصر في الصفحة <span class="text-danger">*</span></label>
                        <input type="number" min="5" max="100" class="form-control @error('pagination_limit') is-invalid @enderror" id="pagination_limit" name="pagination_limit" value="{{ old('pagination_limit', $settings['pagination_limit']) }}" required>
                        @error('pagination_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="default_language" class="form-label">اللغة الافتراضية <span class="text-danger">*</span></label>
                        <select class="form-select @error('default_language') is-invalid @enderror" id="default_language" name="default_language" required>
                            <option value="ar" {{ old('default_language', $settings['default_language']) == 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="en" {{ old('default_language', $settings['default_language']) == 'en' ? 'selected' : '' }}>الإنجليزية</option>
                        </select>
                        @error('default_language')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_format" class="form-label">تنسيق التاريخ <span class="text-danger">*</span></label>
                        <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format" required>
                            <option value="Y-m-d" {{ old('date_format', $settings['date_format']) == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-04-26)</option>
                            <option value="d-m-Y" {{ old('date_format', $settings['date_format']) == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (26-04-2025)</option>
                            <option value="d/m/Y" {{ old('date_format', $settings['date_format']) == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (26/04/2025)</option>
                            <option value="m/d/Y" {{ old('date_format', $settings['date_format']) == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (04/26/2025)</option>
                        </select>
                        @error('date_format')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="time_format" class="form-label">تنسيق الوقت <span class="text-danger">*</span></label>
                        <select class="form-select @error('time_format') is-invalid @enderror" id="time_format" name="time_format" required>
                            <option value="H:i" {{ old('time_format', $settings['time_format']) == 'H:i' ? 'selected' : '' }}>24 ساعة (14:30)</option>
                            <option value="h:i A" {{ old('time_format', $settings['time_format']) == 'h:i A' ? 'selected' : '' }}>12 ساعة (02:30 PM)</option>
                        </select>
                        @error('time_format')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">إعدادات النظام المتقدمة</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_activity_log" name="enable_activity_log" value="1" {{ old('enable_activity_log', $settings['enable_activity_log']) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_activity_log">تفعيل سجل النشاطات</label>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">إعدادات النسخ الاحتياطي</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="backup_enabled" name="backup_enabled" value="1" {{ old('backup_enabled', $settings['backup_enabled']) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="backup_enabled">تفعيل النسخ الاحتياطي التلقائي</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="backup_frequency" class="form-label">تكرار النسخ الاحتياطي</label>
                        <select class="form-select @error('backup_frequency') is-invalid @enderror" id="backup_frequency" name="backup_frequency">
                            <option value="daily" {{ old('backup_frequency', $settings['backup_frequency']) == 'daily' ? 'selected' : '' }}>يومي</option>
                            <option value="weekly" {{ old('backup_frequency', $settings['backup_frequency']) == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
                            <option value="monthly" {{ old('backup_frequency', $settings['backup_frequency']) == 'monthly' ? 'selected' : '' }}>شهري</option>
                        </select>
                        @error('backup_frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="backup_retention" class="form-label">مدة الاحتفاظ بالنسخ الاحتياطية (بالأيام)</label>
                        <input type="number" min="1" max="365" class="form-control @error('backup_retention') is-invalid @enderror" id="backup_retention" name="backup_retention" value="{{ old('backup_retention', $settings['backup_retention']) }}">
                        @error('backup_retention')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <a href="{{ route('settings.create-backup') }}" class="btn btn-secondary">
                            <i class="bi bi-download"></i> إنشاء نسخة احتياطية الآن
                        </a>
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
