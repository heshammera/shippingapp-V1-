<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\ShipmentStatus;
use App\Models\ShippingCompany;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $navigationGroup = 'الإعدادات';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    /**
     * Helper to get value properly tailored for MultiSelect (array of strings/ints)
     */
    protected function getMultiSelectValue($key): array
    {
        $val = Setting::getValue($key);
        if (!$val) return [];
        
        // Try decoding as JSON
        $decoded = json_decode($val, true);
        if (is_array($decoded)) {
            return array_map('strval', $decoded); // Filament expects strings often for keys? or ints work too. Safe to map to strings just in case.
        }
        
        // If single value (old format), wrap in array
        return [(string)$val];
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('حفظ التغييرات')
                ->submit('save'),
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => Setting::getValue('company_name', 'شركة الملابس أونلاين'),
            'company_address' => Setting::getValue('company_address', ''),
            'company_phone' => Setting::getValue('company_phone', ''),
            'company_email' => Setting::getValue('company_email', ''),
            'company_logo' => Setting::getValue('company_logo', ''),
            'currency' => Setting::getValue('currency', 'جنيه'),
            'tax_rate' => Setting::getValue('tax_rate', '14'),
            'default_status_id' => (int) Setting::getValue('default_status_id', '1'),
            'default_shipping_company_id' => (int) Setting::getValue('default_shipping_company_id', null),
            'delivered_status_id' => $this->getMultiSelectValue('delivered_status_id'),
            'returned_status_id' => $this->getMultiSelectValue('returned_status_id'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('معلومات الشركة')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('اسم الشركة')
                            ->required(),
                        Textarea::make('company_address')
                            ->label('عنوان الشركة')
                            ->rows(3),
                        TextInput::make('company_phone')
                            ->label('رقم الهاتف')
                            ->tel(),
                        TextInput::make('company_email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        FileUpload::make('company_logo')
                            ->label('شعار الشركة')
                            ->image()
                            ->directory('logos')
                            ->visibility('public'),
                    ])->columns(2),

                Section::make('إعدادات المالية')
                    ->schema([
                        TextInput::make('currency')
                            ->label('العملة')
                            ->required()
                            ->default('جنيه'),
                        TextInput::make('tax_rate')
                            ->label('نسبة الضريبة (%)')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(2),

                Section::make('إعدادات الشحنات')
                    ->schema([
                        Select::make('default_status_id')
                            ->label('الحالة الافتراضية للشحنات')
                            ->options(ShipmentStatus::orderBy('sort_order')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        
                        Select::make('default_shipping_company_id')
                            ->label('شركة الشحن الافتراضية')
                            ->options(ShippingCompany::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->helperText('في المنطق الجديد: لو الشحنة خرجت من الشركة الافتراضية لشركة أخرى → يتخصم المخزون، والعكس يرجّع.'),

                        Select::make('delivered_status_id')
                            ->label('حالات التوصيل (خصم المخزون)')
                            ->multiple()
                            ->options(ShipmentStatus::orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->helperText('حدد جميع الحالات التي تعني أن الشحنة تم تسليمها (خصم من المخزون). مثل: تم التوصيل، تم التوصيل (كاش).'),

                        Select::make('returned_status_id')
                            ->label('حالات المرتجع (إعادة للمخزون)')
                            ->multiple()
                            ->options(ShipmentStatus::orderBy('sort_order')->pluck('name', 'id'))
                            ->searchable()
                            ->helperText('حدد جميع الحالات التي تعني أن الشحنة عادت للمخزن (إلغاء الخصم/الحجز).'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Handle file upload manually if needed, but FileUpload component usually handles storage.
        // However, we store the PATH in settings.
        // Default FileUpload stores path in $data['company_logo'].

        foreach ($data as $key => $value) {
            // If value is array (from multi-select), encode as JSON
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Setting::setValue($key, $value);
        }

        Notification::make() 
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
