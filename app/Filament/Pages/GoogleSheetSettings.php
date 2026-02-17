<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class GoogleSheetSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'اعدادات Google Sheets';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'إعدادات ربط Google Sheet';
    protected static ?string $slug = 'settings/google-sheet';
    protected static ?string $navigationGroup = '⚡ أدوات ذكية';
    
    protected static string $view = 'filament.pages.google-sheet-settings';

    // Form data property
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'column_index_tracking_number' => Setting::getValue('column_index_tracking_number', 10),
            'column_index_customer_name'   => Setting::getValue('column_index_customer_name', 0),
            'column_index_customer_phone'  => Setting::getValue('column_index_customer_phone', 1),
            'column_index_alternate_phone' => Setting::getValue('column_index_alternate_phone', 21),
            'column_index_governorate'     => Setting::getValue('column_index_governorate', 14),
            'column_index_customer_address'=> Setting::getValue('column_index_customer_address', 3),
            'column_index_unit_price'      => Setting::getValue('column_index_unit_price', 5),
            'column_index_total_amount'    => Setting::getValue('column_index_total_amount', 6),
            'column_index_product_name'    => Setting::getValue('column_index_product_name', 23),
            'column_index_color_type'      => Setting::getValue('column_index_color_type', 9),
            
            // Connection Settings
            'google_sheet_id'              => Setting::getValue('google_sheet_id', ''),
            'google_sheet_tab_name'        => Setting::getValue('google_sheet_tab_name', 'Sheet1'),
            'google_service_account_json'  => Setting::getValue('google_service_account_json', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('طريقة الربط (شرح مبسط)')
                    ->description('اتبع هذه الخطوات لربط ملف Google Sheet بالنظام بنجاح.')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('guide')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-3 leading-relaxed">
                                    <p><strong>1. تجهيز حساب الخدمة (Service Account):</strong><br>
                                    يجب أن يكون لديك ملف <code>credentials.json</code> الخاص بحساب الخدمة من Google Cloud Console. قم بفتح الملف ونسخ محتواه بالكامل وضعه في حقل "Service Account Credentials" في الأسفل.</p>
                                    
                                    <p><strong>2. مشاركة الملف (خطوة هامة جداً):</strong><br>
                                    داخل ملف JSON، ابحث عن <code>"client_email"</code>. انسخ هذا الإيميل (مثلاً: <code>shipping-app@project.iam.gserviceaccount.com</code>).<br>
                                    اذهب إلى ملف Google Sheet الخاص بك، واضغط <strong>Share (مشاركة)</strong> وأضف هذا الإيميل كـ <strong>Editor (محرر)</strong> أو Viewer.</p>
                                    
                                    <p><strong>3. الحصول على Google Sheet ID:</strong><br>
                                    من رابط الملف في المتصفح، انسخ الكود الموجود بين <code>/d/</code> و <code>/edit</code>.<br>
                                    مثال: <code>docs.google.com/spreadsheets/d/<strong>1AnltEwq3VhgXx-3oeZwRsmK_w5WHKeZWPjdP7JiLNVs</strong>/edit</code><br>
                                    الكود هو الجزء الغامق فقط.</p>

                                    <p><strong>4. اسم الورقة (Tab Name):</strong><br>
                                    تأكد من كتابة اسم الورقة التي تحتوي على البيانات كما هي تماماً أسفل الشيت (مثال: <code>Sheet1</code> أو <code>الطلبات</code>).</p>
                                </div>
                            ')),
                    ])
                    ->collapsed() // Collapsible to save space once learned
                    ->icon('heroicon-o-information-circle'),

                Section::make('بيانات الاتصال (Connection Settings)')
                    ->description('إعدادات الربط مع ملف Google Sheet وحساب الخدمة.')
                    ->schema([
                        TextInput::make('google_sheet_id')
                            ->label('Google Sheet ID')
                            ->helperText('معرف الملف من رابط المتصفح')
                            ->required(),
                        TextInput::make('google_sheet_tab_name')
                            ->label('اسم الورقة (Tab Name)')
                            ->default('Sheet1')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('google_service_account_json')
                            ->label('Service Account Credentials (JSON)')
                            ->helperText('محتوى ملف credentials.json بالكامل')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('أرقام الأعمدة في ملف Google Sheet')
                    ->description('أدخل رقم العمود (يبدأ من 0) المقابل لكل حقل.')
                    ->schema([
                        TextInput::make('column_index_tracking_number')->label('رقم التتبع')->numeric()->required(),
                        TextInput::make('column_index_customer_name')->label('اسم العميل')->numeric()->required(),
                        TextInput::make('column_index_customer_phone')->label('رقم الهاتف')->numeric()->required(),
                        TextInput::make('column_index_alternate_phone')->label('هاتف بديل')->numeric()->required(),
                        TextInput::make('column_index_governorate')->label('المحافظة')->numeric()->required(),
                        TextInput::make('column_index_customer_address')->label('العنوان')->numeric()->required(),
                        TextInput::make('column_index_unit_price')->label('سعر القطعة')->numeric()->required(),
                        TextInput::make('column_index_total_amount')->label('الإجمالي الكلي')->numeric()->required(),
                        TextInput::make('column_index_product_name')->label('اسم المنتج')->numeric()->required(),
                        TextInput::make('column_index_color_type')->label('اللون والمقاس')->numeric()->required(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
