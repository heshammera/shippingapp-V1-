<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryAgentResource\Pages;
use App\Models\DeliveryAgent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeliveryAgentResource extends Resource
{
    protected static ?string $model = DeliveryAgent::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'المناديب';
    protected static ?string $navigationGroup = '⚙️ النظام';
    protected static ?string $pluralLabel = 'المناديب';
    protected static ?string $modelLabel = 'مندوب';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المندوب')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('معلومات العمل')
                    ->schema([
                        Forms\Components\Select::make('shipping_company_id')
                            ->label('تابع لشركة')
                            ->relationship('shippingCompany', 'name')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('حساب المستخدم المرتبط')
                            ->relationship('user', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('account_id')
                            ->label('الحساب المالي')
                            ->relationship('account', 'name_ar')
                            ->searchable()
                            ->preload()
                            ->helperText('الحساب الذي سيتم تحميل المبالغ المحصلة عليه'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->required(),
                        Forms\Components\TextInput::make('max_edit_count')
                            ->label('الحد الأقصى للتعديلات')
                            ->numeric()
                            ->default(1),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('المندوب')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shippingCompany.name')
                    ->label('الشركة')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shipping_company_id')
                    ->label('الشركة')
                    ->relationship('shippingCompany', 'name'),
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
            'index' => Pages\ListDeliveryAgents::route('/'),
            'create' => Pages\CreateDeliveryAgent::route('/create'),
            'edit' => Pages\EditDeliveryAgent::route('/{record}/edit'),
        ];
    }
}
