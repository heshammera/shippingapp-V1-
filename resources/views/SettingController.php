<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
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
            'company_name' => Setting::getValue('company_name', 'شركة الملابس أونلاين'),
            'company_address' => Setting::getValue('company_address', ''),
            'company_phone' => Setting::getValue('company_phone', ''),
            'company_email' => Setting::getValue('company_email', ''),
            'company_logo' => Setting::getValue('company_logo', ''),
            'currency' => Setting::getValue('currency', 'جنيه'),
            'tax_rate' => Setting::getValue('tax_rate', '14'),
            'default_status_id' => Setting::getValue('default_status_id', '1'),
            'enable_sms_notifications' => Setting::getValue('enable_sms_notifications', '0'),
            'enable_email_notifications' => Setting::getValue('enable_email_notifications', '0'),
            'sms_api_key' => Setting::getValue('sms_api_key', ''),
            'sms_sender_id' => Setting::getValue('sms_sender_id', ''),
            'smtp_host' => Setting::getValue('smtp_host', ''),
            'smtp_port' => Setting::getValue('smtp_port', '587'),
            'smtp_username' => Setting::getValue('smtp_username', ''),
            'smtp_password' => Setting::getValue('smtp_password', ''),
            'smtp_encryption' => Setting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address' => Setting::getValue('smtp_from_address', ''),
            'smtp_from_name' => Setting::getValue('smtp_from_name', ''),
        ];
        
        return view('settings.index', compact('settings'));
    }
    
    /**
     * حفظ الإعدادات العامة
     */
     

public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'company_name' => 'required|string|max:255',
        'company_address' => 'nullable|string|max:500',
        'company_phone' => 'nullable|string|max:20',
        'company_email' => 'nullable|email|max:255',
        'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'currency' => 'required|string|max:10',
        'tax_rate' => 'nullable|numeric|min:0|max:100',
        'default_status_id' => 'required|exists:shipment_statuses,id',
        'enable_sms_notifications' => 'nullable|in:0,1',
        'enable_email_notifications' => 'nullable|in:0,1',
        'sms_api_key' => 'nullable|string|max:255',
        'sms_sender_id' => 'nullable|string|max:20',
        'smtp_host' => 'nullable|string|max:255',
        'smtp_port' => 'nullable|numeric|min:1|max:65535',
        'smtp_username' => 'nullable|string|max:255',
        'smtp_password' => 'nullable|string|max:255',
        'smtp_encryption' => 'nullable|in:tls,ssl',
        'smtp_from_address' => 'nullable|email|max:255',
        'smtp_from_name' => 'nullable|string|max:255',
        'settings.google_sheet_id' => $request->input('google_sheet_id'),
        'settings.google_sheet_range' => $request->input('google_sheet_range'),

    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // حفظ الشعار
if ($request->hasFile('company_logo')) {
    $logoPath = $request->file('company_logo')->store('logos', 'public');
    set_setting('company_logo', $logoPath, 'شعار الشركة');
}


    // حفظ باقي الإعدادات
    $data = $request->except(['_token', 'company_logo']);
    foreach ($data as $key => $value) {
        set_setting($key, is_array($value) ? json_encode($value) : $value);
    }

    return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح');
}

    
    /**
     * عرض صفحة إعدادات الإشعارات
     */
    public function notifications()
    {
        $settings = [
            'enable_sms_notifications' => Setting::getValue('enable_sms_notifications', '0'),
            'enable_email_notifications' => Setting::getValue('enable_email_notifications', '0'),
            'sms_api_key' => Setting::getValue('sms_api_key', ''),
            'sms_sender_id' => Setting::getValue('sms_sender_id', ''),
            'smtp_host' => Setting::getValue('smtp_host', ''),
            'smtp_port' => Setting::getValue('smtp_port', '587'),
            'smtp_username' => Setting::getValue('smtp_username', ''),
            'smtp_password' => Setting::getValue('smtp_password', ''),
            'smtp_encryption' => Setting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address' => Setting::getValue('smtp_from_address', ''),
            'smtp_from_name' => Setting::getValue('smtp_from_name', ''),
            'notification_status_change' => Setting::getValue('notification_status_change', '1'),
            'notification_new_shipment' => Setting::getValue('notification_new_shipment', '1'),
            'notification_delivery_date' => Setting::getValue('notification_delivery_date', '1'),
        ];
        
        return view('settings.notifications', compact('settings'));
    }
    
    /**
     * حفظ إعدادات الإشعارات
     */
    public function updateNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enable_sms_notifications' => 'nullable|in:0,1',
            'enable_email_notifications' => 'nullable|in:0,1',
            'sms_api_key' => 'nullable|string|max:255',
            'sms_sender_id' => 'nullable|string|max:20',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|numeric|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'notification_status_change' => 'nullable|in:0,1',
            'notification_new_shipment' => 'nullable|in:0,1',
            'notification_delivery_date' => 'nullable|in:0,1',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // حفظ الإعدادات
        $settings = $request->except(['_token']);
        
        foreach ($settings as $key => $value) {
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
            'pagination_limit' => Setting::getValue('pagination_limit', '15'),
            'date_format' => Setting::getValue('date_format', 'Y-m-d'),
            'time_format' => Setting::getValue('time_format', 'H:i'),
            'default_language' => Setting::getValue('default_language', 'ar'),
            'enable_activity_log' => Setting::getValue('enable_activity_log', '1'),
            'backup_enabled' => Setting::getValue('backup_enabled', '0'),
            'backup_frequency' => Setting::getValue('backup_frequency', 'daily'),
            'backup_retention' => Setting::getValue('backup_retention', '7'),
        ];
        
        return view('settings.system', compact('settings'));
    }
    
    /**
     * حفظ إعدادات النظام
     */
    public function updateSystem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pagination_limit' => 'required|numeric|min:5|max:100',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'default_language' => 'required|in:ar,en',
            'enable_activity_log' => 'nullable|in:0,1',
            'backup_enabled' => 'nullable|in:0,1',
            'backup_frequency' => 'nullable|in:daily,weekly,monthly',
            'backup_retention' => 'nullable|numeric|min:1|max:365',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // حفظ الإعدادات
        $settings = $request->except(['_token']);
        
        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value);
        }
        
        return redirect()->route('settings.system')->with('success', 'تم حفظ إعدادات النظام بنجاح');
    }
    
    /**
     * إنشاء نسخة احتياطية يدوية
     */
    public function createBackup()
    {
        // هنا يمكن إضافة كود لإنشاء نسخة احتياطية يدوية
        // يمكن استخدام حزمة spatie/laravel-backup لهذا الغرض
        
        return redirect()->route('settings.system')->with('success', 'تم إنشاء نسخة احتياطية بنجاح');
 
   }

public function googleSheet()
{
    $settings = [
        'google_sheet_id'           => Setting::getValue('spreadsheet_id'),
        'google_sheet_range'        => Setting::getValue('sheet_range'),
        'customer_name_column'      => Setting::getValue('customer_name_column'),
        'customer_phone_column'     => Setting::getValue('customer_phone_column'),
        'customer_address_column'   => Setting::getValue('customer_address_column'),
        'unit_price_column'         => Setting::getValue('unit_price_column'),
        'total_amount_column'       => Setting::getValue('total_amount_column'),
        'product_name_column'       => Setting::getValue('product_name_column'),
        'color_type_column'         => Setting::getValue('color_type_column'),

        // ✅ نضيف دي هنا:
        'credentials_uploaded'      => \Storage::exists('google/credentials.json'),
    ];

    return view('settings.google-sheet', compact('settings'));
}



public function updateGoogleSheet(Request $request)
{
    $request->validate([
        'spreadsheet_id' => 'required|string',
        'sheet_range'    => 'required|string',
        'credentials_json' => 'nullable|file|mimes:json',

        // الأعمدة
        'customer_name_column' => 'required|string|max:2',
        'customer_phone_column' => 'required|string|max:2',
        'customer_address_column' => 'required|string|max:2',
        'unit_price_column' => 'required|string|max:2',
        'total_amount_column' => 'required|string|max:2',
        'product_name_column' => 'required|string|max:2',
        'color_type_column' => 'required|string|max:2',
    ]);

    // حفظ الإعدادات بالنصوص
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
    ];

    foreach ($keys as $key) {
        Setting::updateOrCreate(['key' => $key], ['value' => $request->$key]);
    }

    // حفظ نفس الأعمدة كـ index رقمي
    foreach ([
        'customer_name'     => $request->input('customer_name_column'),
        'customer_phone'    => $request->input('customer_phone_column'),
        'customer_address'  => $request->input('customer_address_column'),
        'unit_price'        => $request->input('unit_price_column'),
        'total_amount'      => $request->input('total_amount_column'),
        'product_name'      => $request->input('product_name_column'),
        'color_type'        => $request->input('color_type_column'),
    ] as $key => $letter) {
        $index = $this->columnLetterToIndex($letter);
        Setting::updateOrCreate(['key' => "column_index_{$key}"], ['value' => $index]);
    }

    // حفظ ملف json
    if ($request->hasFile('credentials_json')) {
        $path = $request->file('credentials_json')->storeAs('google', 'credentials.json');
        if (!Storage::exists('google/credentials.json')) {
            return back()->with('error', 'فشل في رفع ملف credentials.json');
        }
    }

    return back()->with('success', 'تم تحديث إعدادات Google Sheet بنجاح.');
}

protected function columnLetterToIndex($letter)
{
    $letter = strtoupper($letter);
    $index = 0;
    for ($i = 0; $i < strlen($letter); $i++) {
        $index = $index * 26 + (ord($letter[$i]) - 64);
    }
    return $index - 1;
}


}
