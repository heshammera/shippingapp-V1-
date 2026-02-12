<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'إعدادات النظام';
    protected static ?string $navigationGroup = '⚙️ النظام';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.settings-page';

    public ?array $data = [];

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
            'pagination_limit' => Setting::getValue('pagination_limit', '15'),
            'default_language' => Setting::getValue('default_language', 'ar'),
            'date_format' => Setting::getValue('date_format', 'Y-m-d'),
            'time_format' => Setting::getValue('time_format', 'H:i'),
            'enable_activity_log' => (boolean) Setting::getValue('enable_activity_log', '1'),
            'backup_enabled' => (boolean) Setting::getValue('backup_enabled', '0'),
            'backup_frequency' => Setting::getValue('backup_frequency', 'daily'),
            'backup_retention' => Setting::getValue('backup_retention', '7'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('إعدادات العرض')
                    ->schema([
                        TextInput::make('pagination_limit')
                            ->label('عدد العناصر في الصفحة')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(100)
                            ->required(),
                        Select::make('default_language')
                            ->label('اللغة الافتراضية')
                            ->options([
                                'ar' => 'العربية',
                                'en' => 'الإنجليزية',
                            ])
                            ->required(),
                        Select::make('date_format')
                            ->label('تنسيق التاريخ')
                            ->options([
                                'Y-m-d' => 'YYYY-MM-DD (2025-04-26)',
                                'd-m-Y' => 'DD-MM-YYYY (26-04-2025)',
                                'd/m/Y' => 'DD/MM/YYYY (26/04/2025)',
                                'm/d/Y' => 'MM/DD/YYYY (04/26/2025)',
                            ])
                            ->required(),
                        Select::make('time_format')
                            ->label('تنسيق الوقت')
                            ->options([
                                'H:i' => '24 ساعة (14:30)',
                                'h:i A' => '12 ساعة (02:30 PM)',
                            ])
                            ->required(),
                    ])->columns(2),

                Section::make('إعدادات النظام المتقدمة')
                    ->schema([
                        Toggle::make('enable_activity_log')
                            ->label('تفعيل سجل النشاطات'),
                    ]),

                Section::make('إعدادات النسخ الاحتياطي')
                    ->schema([
                        Toggle::make('backup_enabled')
                            ->label('تفعيل النسخ الاحتياطي التلقائي'),
                        Select::make('backup_frequency')
                            ->label('تكرار النسخ الاحتياطي')
                            ->options([
                                'daily' => 'يومي',
                                'weekly' => 'أسبوعي',
                                'monthly' => 'شهري',
                            ]),
                        TextInput::make('backup_retention')
                            ->label('مدة الاحتفاظ بالنسخ الاحتياطية (بالأيام)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365),
                        // Note: The "Download Backup Now" button is an action, not a form field.
                        // I can add it as an extra Action on the page header if needed, or keeping it strictly form-based for now.
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
            ->title('تم حفظ إعدادات النظام بنجاح')
            ->success()
            ->send();
    }
}
