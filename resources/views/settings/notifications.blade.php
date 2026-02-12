@extends('layouts.app')

@section('title', 'إعدادات الإشعارات')

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.index') }}">الإعدادات العامة</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('settings.notifications') }}">إعدادات الإشعارات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('settings.system') }}">إعدادات النظام</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('settings.update-notifications') }}" method="POST">
            @csrf

            <h5 class="mb-3">إعدادات الإشعارات</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="hidden" name="enable_sms_notifications" value="0">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_sms_notifications" name="enable_sms_notifications" value="1"
                               {{ old('enable_sms_notifications', $settings['enable_sms_notifications'] ?? '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_sms_notifications">تفعيل إشعارات الرسائل القصيرة</label>
                    </div>

                    <input type="hidden" name="enable_email_notifications" value="0">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_email_notifications" name="enable_email_notifications" value="1"
                               {{ old('enable_email_notifications', $settings['enable_email_notifications'] ?? '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_email_notifications">تفعيل إشعارات البريد الإلكتروني</label>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">إعدادات الرسائل القصيرة</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sms_api_key" class="form-label">API Key</label>
                        <input type="text" class="form-control" name="sms_api_key" value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sms_sender_id" class="form-label">معرف المرسل</label>
                        <input type="text" class="form-control" name="sms_sender_id" value="{{ old('sms_sender_id', $settings['sms_sender_id'] ?? '') }}">
                    </div>
                </div>
            </div>

            <h5 class="mb-3">إعدادات البريد الإلكتروني</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_host" class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" name="smtp_host" value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_port" class="form-label">SMTP Port</label>
                        <input type="number" class="form-control" name="smtp_port" value="{{ old('smtp_port', $settings['smtp_port'] ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_username" class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" name="smtp_username" value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_password" class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" name="smtp_password" value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_encryption" class="form-label">تشفير SMTP</label>
                        <select class="form-select" name="smtp_encryption">
                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_from_address" class="form-label">عنوان البريد</label>
                        <input type="email" class="form-control" name="smtp_from_address" value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="smtp_from_name" class="form-label">اسم المرسل</label>
                        <input type="text" class="form-control" name="smtp_from_name" value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? '') }}">
                    </div>
                </div>
            </div>

            <h5 class="mb-3">أنواع الإشعارات</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="hidden" name="notification_status_change" value="0">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="notification_status_change" value="1" {{ old('notification_status_change', $settings['notification_status_change'] ?? '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label">إشعار عند تغيير حالة الشحنة</label>
                    </div>

                    <input type="hidden" name="notification_new_shipment" value="0">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="notification_new_shipment" value="1" {{ old('notification_new_shipment', $settings['notification_new_shipment'] ?? '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label">إشعار عند إضافة شحنة جديدة</label>
                    </div>

                    <input type="hidden" name="notification_delivery_date" value="0">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="notification_delivery_date" value="1" {{ old('notification_delivery_date', $settings['notification_delivery_date'] ?? '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label">إشعار عند تحديد موعد التسليم</label>
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
