<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingCompanyResource\Pages;
use App\Models\ShippingCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShippingCompanyResource extends Resource
{
    protected static ?string $model = ShippingCompany::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'شركات الشحن';
    protected static ?string $navigationGroup = '⚙️ النظام';
    protected static ?string $pluralLabel = 'شركات الشحن';
    protected static ?string $modelLabel = 'شركة شحن';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الشركة')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الشركة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_person')
                            ->label('مسؤول التواصل')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('الإعدادات العامة')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->required(),
                        Forms\Components\Toggle::make('affects_inventory')
                            ->label('يؤثر على المخزون')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('إعدادات الربط الخارجي (API)')
                    ->schema([
                        Forms\Components\Select::make('integration_type')
                            ->label('نوع الربط')
                            ->options([
                                'internal' => 'داخلي (بدون ربط)',
                                'aramex' => 'Aramex (SOAP)',
                                'dhl' => 'DHL (REST)',
                            ])
                            ->reactive()
                            ->required(),
                        Forms\Components\Toggle::make('integration_enabled')
                            ->label('تفعيل الربط')
                            ->default(false),
                        
                        Forms\Components\KeyValue::make('api_settings')
                            ->label('إعدادات API')
                            ->helperText('أدخل مفاتيح الربط المطلوبة (مثال: account_number, password, pin)')
                            ->visible(fn ($get) => $get('integration_type') !== 'internal')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الشركة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('المسؤول')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingCompanies::route('/'),
            'create' => Pages\CreateShippingCompany::route('/create'),
            'edit' => Pages\EditShippingCompany::route('/{record}/edit'),
        ];
    }
}
