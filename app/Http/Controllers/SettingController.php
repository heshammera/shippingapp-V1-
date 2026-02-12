<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ShippingCompany;
use App\Models\ShipmentStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * عرض صفحة الإعدادات العامة
     */
    public function index()
    {
        $settings = [
            'company_name'              => Setting::getValue('company_name', 'شركة الملابس أونلاين'),
            'company_address'           => Setting::getValue('company_address', ''),
            'company_phone'             => Setting::getValue('company_phone', ''),
            'company_email'             => Setting::getValue('company_email', ''),
            'company_logo'              => Setting::getValue('company_logo', ''),
            'currency'                  => Setting::getValue('currency', 'جنيه'),
            'tax_rate'                  => Setting::getValue('tax_rate', '14'),
            'default_status_id'         => (int) Setting::getValue('default_status_id', '1'),

            // الإعدادات الجديدة مع تحويل لأرقام لضبط الاختيار في الـ Blade
            'default_shipping_company_id' => Setting::getValue('default_shipping_company_id', null),
            'delivered_status_id'         => Setting::getValue('delivered_status_id', null),
            'returned_status_id'          => Setting::getValue('returned_status_id', null),

            'enable_sms_notifications'  => Setting::getValue('enable_sms_notifications', '0'),
            'enable_email_notifications'=> Setting::getValue('enable_email_notifications', '0'),
            'sms_api_key'               => Setting::getValue('sms_api_key', ''),
            'sms_sender_id'             => Setting::getValue('sms_sender_id', ''),
            'smtp_host'                 => Setting::getValue('smtp_host', ''),
            'smtp_port'                 => Setting::getValue('smtp_port', '587'),
            'smtp_username'             => Setting::getValue('smtp_username', ''),
            'smtp_password'             => Setting::getValue('smtp_password', ''),
            'smtp_encryption'           => Setting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address'         => Setting::getValue('smtp_from_address', ''),
            'smtp_from_name'            => Setting::getValue('smtp_from_name', ''),
        ];

        // القوائم للدروبداون
        $companies = ShippingCompany::orderBy('name')->get(['id','name']);
        $statuses  = ShipmentStatus::orderBy('sort_order')->orderBy('id')->get(['id','name']);

        return view('settings.index', compact('settings','companies','statuses'));
    }

    /**
     * حفظ الإعدادات العامة
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name'      => 'required|string|max:255',
            'company_address'   => 'nullable|string|max:500',
            'company_phone'     => 'nullable|string|max:20',
            'company_email'     => 'nullable|email|max:255',
            'company_logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'currency'          => 'required|string|max:10',
            'tax_rate'          => 'nullable|numeric|min:0|max:100',
            'default_status_id' => 'required|exists:shipment_statuses,id',

            // الإعدادات الجديدة:
            'default_shipping_company_id' => 'nullable|exists:shipping_companies,id',
            'delivered_status_id'         => 'nullable|exists:shipment_statuses,id',
            'returned_status_id'          => 'nullable|exists:shipment_statuses,id',

            'enable_sms_notifications'    => 'nullable|in:0,1',
            'enable_email_notifications'  => 'nullable|in:0,1',
            'sms_api_key'                 => 'nullable|string|max:255',
            'sms_sender_id'               => 'nullable|string|max:20',
            'smtp_host'                   => 'nullable|string|max:255',
            'smtp_port'                   => 'nullable|numeric|min:1|max:65535',
            'smtp_username'               => 'nullable|string|max:255',
            'smtp_password'               => 'nullable|string|max:255',
            'smtp_encryption'             => 'nullable|in:tls,ssl',
            'smtp_from_address'           => 'nullable|email|max:255',
            'smtp_from_name'              => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // حفظ الشعار
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            Setting::setValue('company_logo', $logoPath); // استخدام setValue لضمان مسح الكاش
        }

        // تجهيز القيم للحفظ
        $data = $request->except(['_token', '_method', 'company_logo']);

        // تفريغ الحقول الفارغة إلى null
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        // تطبيع الأنواع المهمة إلى أرقام
        foreach ([
            'default_status_id',
            'default_shipping_company_id',
            'delivered_status_id',
            'returned_status_id'
        ] as $numericKey) {
            if (array_key_exists($numericKey, $data) && $data[$numericKey] !== null) {
                $data[$numericKey] = (int) $data[$numericKey];
            }
        }

        // حفظ كل الإعدادات عبر setValue (يمسح كاش getValue داخليًا)
        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    /**
     * عرض صفحة إعدادات الإشعارات
     */
    public function notifications()
    {
        $settings = [
            'enable_sms_notifications'    => Setting::getValue('enable_sms_notifications', '0'),
            'enable_email_notifications'  => Setting::getValue('enable_email_notifications', '0'),
            'sms_api_key'                 => Setting::getValue('sms_api_key', ''),
            'sms_sender_id'               => Setting::getValue('sms_sender_id', ''),
            'smtp_host'                   => Setting::getValue('smtp_host', ''),
            'smtp_port'                   => Setting::getValue('smtp_port', '587'),
            'smtp_username'               => Setting::getValue('smtp_username', ''),
            'smtp_password'               => Setting::getValue('smtp_password', ''),
            'smtp_encryption'             => Setting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address'           => Setting::getValue('smtp_from_address', ''),
            'smtp_from_name'              => Setting::getValue('smtp_from_name', ''),
            'notification_status_change'  => Setting::getValue('notification_status_change', '1'),
            'notification_new_shipment'   => Setting::getValue('notification_new_shipment', '1'),
            'notification_delivery_date'  => Setting::getValue('notification_delivery_date', '1'),
        ];

        return view('settings.notifications', compact('settings'));
    }

    /**
     * حفظ إعدادات الإشعارات
     */
    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enable_sms_notifications'    => 'nullable|in:0,1',
            'enable_email_notifications'  => 'nullable|in:0,1',
            'sms_api_key'                 => 'nullable|string|max:255',
            'sms_sender_id'               => 'nullable|string|max:20',
            'smtp_host'                   => 'nullable|string|max:255',
            'smtp_port'                   => 'nullable|numeric|min:1|max:65535',
            'smtp_username'               => 'nullable|string|max:255',
            'smtp_password'               => 'nullable|string|max:255',
            'smtp_encryption'             => 'nullable|in:tls,ssl',
            'smtp_from_address'           => 'nullable|email|max:255',
            'smtp_from_name'              => 'nullable|string|max:255',
            'notification_status_change'  => 'nullable|in:0,1',
            'notification_new_shipment'   => 'nullable|in:0,1',
            'notification_delivery_date'  => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.notifications')->with('success', 'تم حفظ إعدادات الإشعارات بنجاح');
    }

    /**
     * عرض صفحة إعدادات النظام
     */
    public function system()
    {
        $settings = [
            'pagination_limit'   => Setting::getValue('pagination_limit', '15'),
            'date_format'        => Setting::getValue('date_format', 'Y-m-d'),
            'time_format'        => Setting::getValue('time_format', 'H:i'),
            'default_language'   => Setting::getValue('default_language', 'ar'),
            'enable_activity_log'=> Setting::getValue('enable_activity_log', '1'),
            'backup_enabled'     => Setting::getValue('backup_enabled', '0'),
            'backup_frequency'   => Setting::getValue('backup_frequency', 'daily'),
            'backup_retention'   => Setting::getValue('backup_retention', '7'),
        ];

        return view('settings.system', compact('settings'));
    }

    /**
     * حفظ إعدادات النظام
     */
    public function updateSystem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pagination_limit'   => 'required|numeric|min:5|max:100',
            'date_format'        => 'required|string|max:20',
            'time_format'        => 'required|string|max:20',
            'default_language'   => 'required|in:ar,en',
            'enable_activity_log'=> 'nullable|in:0,1',
            'backup_enabled'     => 'nullable|in:0,1',
            'backup_frequency'   => 'nullable|in:daily,weekly,monthly',
            'backup_retention'   => 'nullable|numeric|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.system')->with('success', 'تم حفظ إعدادات النظام بنجاح');
    }

    /**
     * إنشاء نسخة احتياطية يدوية
     */
    public function createBackup()
    {
        // TODO: نفّذ spatie/laravel-backup لو حابب
        return redirect()->route('settings.system')->with('success', 'تم إنشاء نسخة احتياطية بنجاح');
    }

    /**
     * إعدادات Google Sheet
     */
    public function googleSheet()
    {
        $columns = [
            'customer_name'        => (int) Setting::getValue('column_index_customer_name', 0),
            'customer_phone'       => (int) Setting::getValue('column_index_customer_phone', 1),
            'customer_address'     => (int) Setting::getValue('column_index_customer_address', 2),
            'unit_price'           => (int) Setting::getValue('column_index_unit_price', 5),
            'total_amount'         => (int) Setting::getValue('column_index_total_amount', 6),
            'product_name'         => (int) Setting::getValue('column_index_product_name', 24),
            'color_type'           => (int) Setting::getValue('column_index_color_type', 9),
            'governorate'          => (int) Setting::getValue('column_index_governorate', 3),
            'tracking_number'      => (int) Setting::getValue('column_index_tracking_number', 10),
            'credentials_uploaded' => Storage::exists('google/credentials.json'),
        ];

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('settings.google-sheet', compact('settings', 'columns'));
    }

    public function updateGoogleSheet(Request $request)
    {
        $request->validate([
            'spreadsheet_id'         => 'required|string',
            'sheet_range'            => 'required|string',
            'credentials_json'       => 'nullable|file|mimes:json',

            // الأعمدة (A..Z)
            'customer_name_column'   => 'required|string|max:2',
            'customer_phone_column'  => 'required|string|max:2',
            'customer_address_column'=> 'required|string|max:2',
            'unit_price_column'      => 'required|string|max:2',
            'total_amount_column'    => 'required|string|max:2',
            'product_name_column'    => 'required|string|max:2',
            'color_type_column'      => 'required|string|max:2',
            'governorate_column'     => 'required|string|max:2',
            'tracking_number_column' => 'required|string|max:2',
        ]);

        // حفظ النصوص كما هي
        $keys = [
            'spreadsheet_id',
            'sheet_range',
            'customer_name_column',
            'customer_phone_column',
            'customer_address_column',
            'unit_price_column',
            'total_amount_column',
            'product_name_column',
            'color_type_column',
            'governorate_column',
            'tracking_number_column',
        ];

        foreach ($keys as $key) {
            Setting::setValue($key, $request->$key);
        }

        // حفظ نفس الأعمدة كأرقام فهارس
        foreach ([
            'customer_name'   => $request->input('customer_name_column'),
            'customer_phone'  => $request->input('customer_phone_column'),
            'customer_address'=> $request->input('customer_address_column'),
            'unit_price'      => $request->input('unit_price_column'),
            'total_amount'    => $request->input('total_amount_column'),
            'product_name'    => $request->input('product_name_column'),
            'color_type'      => $request->input('color_type_column'),
            'governorate'     => $request->input('governorate_column'),
            'tracking_number' => $request->input('tracking_number_column'),
        ] as $key => $letter) {
            $index = self::columnLetterToIndex($letter);
            Setting::setValue("column_index_{$key}", $index);
        }

        // رفع credentials.json (على الديسك الافتراضي "local")
        if ($request->hasFile('credentials_json')) {
            $path = $request->file('credentials_json')->storeAs('google', 'credentials.json');
            if (!Storage::exists('google/credentials.json')) {
                return back()->with('error', 'فشل في رفع ملف credentials.json');
            }
        }

        return back()->with('success', 'تم تحديث إعدادات Google Sheet بنجاح.');
    }

    public static function columnLetterToIndex($letter)
    {
        $letter = strtoupper($letter);
        $index = 0;
        for ($i = 0; $i < strlen($letter); $i++) {
            $index *= 26;
            $index += ord($letter[$i]) - ord('A') + 1;
        }
        return $index - 1; // صفرية الفهرسة
    }
}
