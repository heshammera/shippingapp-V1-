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

class NotificationSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'إعدادات التنبيهات';
    protected static ?string $navigationGroup = '⚙️ النظام';
    protected static ?int $navigationSort = 6;
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
            'enable_sms_notifications' => (boolean) Setting::getValue('enable_sms_notifications', '0'),
            'enable_email_notifications' => (boolean) Setting::getValue('enable_email_notifications', '0'),
            'sms_api_key' => Setting::getValue('sms_api_key', ''),
            'sms_sender_id' => Setting::getValue('sms_sender_id', ''),
            'smtp_host' => Setting::getValue('smtp_host', ''),
            'smtp_port' => Setting::getValue('smtp_port', '587'),
            'smtp_username' => Setting::getValue('smtp_username', ''),
            'smtp_password' => Setting::getValue('smtp_password', ''),
            'smtp_encryption' => Setting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address' => Setting::getValue('smtp_from_address', ''),
            'smtp_from_name' => Setting::getValue('smtp_from_name', ''),
            'notification_status_change' => (boolean) Setting::getValue('notification_status_change', '1'),
            'notification_new_shipment' => (boolean) Setting::getValue('notification_new_shipment', '1'),
            'notification_delivery_date' => (boolean) Setting::getValue('notification_delivery_date', '1'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('إعدادات الإشعارات')
                    ->schema([
                        Toggle::make('enable_sms_notifications')
                            ->label('تفعيل إشعارات الرسائل القصيرة'),
                        Toggle::make('enable_email_notifications')
                            ->label('تفعيل إشعارات البريد الإلكتروني'),
                    ])->columns(2),

                Section::make('إعدادات الرسائل القصيرة (SMS)')
                    ->schema([
                        TextInput::make('sms_api_key')
                            ->label('SMS API Key')
                            ->password() // Hide key if needed, or text
                            ->revealable(),
                        TextInput::make('sms_sender_id')
                            ->label('معرف المرسل (Sender ID)'),
                    ])->columns(2),

                Section::make('إعدادات البريد الإلكتروني (SMTP)')
                    ->schema([
                        TextInput::make('smtp_host')->label('SMTP Host'),
                        TextInput::make('smtp_port')->label('SMTP Port')->numeric(),
                        TextInput::make('smtp_username')->label('SMTP Username'),
                        TextInput::make('smtp_password')->label('SMTP Password')->password()->revealable(),
                        Select::make('smtp_encryption')
                            ->label('تشفير SMTP')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                'none' => 'None',
                            ]),
                        TextInput::make('smtp_from_address')->label('عنوان البريد (From Address)')->email(),
                        TextInput::make('smtp_from_name')->label('اسم المرسل (From Name)'),
                    ])->columns(2),

                Section::make('أنواع الإشعارات')
                    ->schema([
                        Toggle::make('notification_status_change')
                            ->label('إشعار عند تغيير حالة الشحنة'),
                        Toggle::make('notification_new_shipment')
                            ->label('إشعار عند إضافة شحنة جديدة'),
                        Toggle::make('notification_delivery_date')
                            ->label('إشعار عند تحديد موعد التسليم'),
                    ])->columns(3),
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
            ->title('تم حفظ إعدادات الإشعارات بنجاح')
            ->success()
            ->send();
    }
}
