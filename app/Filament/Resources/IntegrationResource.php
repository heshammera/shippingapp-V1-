<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntegrationResource\Pages;
use App\Filament\Resources\IntegrationResource\RelationManagers;
use App\Models\Integration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IntegrationResource extends Resource
{
    protected static ?string $model = Integration::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'الربط الخارجي';
    protected static ?string $pluralLabel = 'عمليات الربط';
    protected static ?string $modelLabel = 'ربط';
    protected static ?string $navigationGroup = '⚙️ الإعدادات والربط';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الربط')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المتجر / المنصة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('platform')
                            ->label('المنصة')
                            ->options([
                                'woocommerce' => 'WooCommerce',
                                'shopify' => 'Shopify (قريباً)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('رابط الموقع (URL)')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('بيانات الاعتماد (API Credentials)')
                    ->schema([
                        Forms\Components\KeyValue::make('credentials')
                            ->label('المفاتيح')
                            ->helperText('WooCommerce: consumer_key, consumer_secret')
                                ->required(),
                    ]),

                Forms\Components\Section::make('إعدادات Webhook (الأتمتة اللحظية)')
                    ->schema([
                        Forms\Components\TextInput::make('webhook_url')
                            ->label('رابط الـ Webhook')
                            ->helperText('انسخ هذا الرابط وضعه في إعدادات Webhooks في WooCommerce (حدث: Order Created)')
                            ->default(fn ($record) => $record ? url("/api/webhooks/woocommerce/{$record->id}") : 'سيظهر الرابط بعد الحفظ')
                            ->readOnly()
                            ->columnSpanFull()
                            ->visible(fn ($get) => $get('platform') === 'woocommerce'),
                    ])->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('platform')
                    ->label('المنصة')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('url')
                    ->label('الرابط')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_sync_at')
                    ->label('آخر مزامنة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('sync')
                    ->label('مزامنة الطلبات')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Integration $record) {
                        $service = new \App\Services\ECommerceService();
                        $count = $service->syncOrders($record);

                        \Filament\Notifications\Notification::make()
                            ->title('تمت المزامنة بنجاح')
                            ->body("تم استيراد {$count} طلبات جديدة من {$record->name}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIntegrations::route('/'),
            'create' => Pages\CreateIntegration::route('/create'),
            'edit' => Pages\EditIntegration::route('/{record}/edit'),
        ];
    }
}
