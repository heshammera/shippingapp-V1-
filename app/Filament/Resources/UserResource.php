<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'المستخدمين';
    protected static ?string $pluralLabel = 'المستخدمين';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $navigationGroup = '👥 المستخدمين والشركاء';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->minLength(6)
                            ->maxLength(255)
                            ->revealable(),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('الدور والصلاحيات')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('الدور')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('اختر دور أو أكثر للمستخدم'),
                        
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('صلاحيات إضافية (اختياري)')
                            ->relationship('permissions', 'name')
                            ->saveRelationshipsUsing(function ($component, $state) {
                                $component->getModelInstance()->permissions()->sync(array_unique($state));
                            })
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->helperText('يمكنك إضافة صلاحيات إضافية بجانب صلاحيات الدور')
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('إعدادات الحساب')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('تفعيل/تعطيل حساب المستخدم'),
                        
                        Forms\Components\Toggle::make('lifetime')
                            ->label('مدى الحياة')
                            ->default(false)
                            ->live()
                            ->helperText('إذا تم التفعيل، لن ينتهي حساب المستخدم'),
                        
                        Forms\Components\TextInput::make('expires_days')
                            ->label('مدة الصلاحية (بالأيام)')
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->hidden(fn (Forms\Get $get) => $get('lifetime'))
                            ->helperText('عدد الأيام حتى انتهاء صلاحية الحساب'),
                        
                        Forms\Components\DatePicker::make('expires_at')
                            ->label('تاريخ انتهاء الصلاحية')
                            ->displayFormat('Y-m-d')
                            ->disabled()
                            ->dehydrated(false)
                            ->hidden(fn (Forms\Get $get, string $context) => $context === 'create' || $get('lifetime')),
                    ])->columns(3),

                Forms\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Forms\Components\Select::make('shipping_company_id')
                            ->label('شركة الشحن')
                            ->relationship('shippingCompany', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('للمندوبين ومستخدمي شركات الشحن فقط'),
                        
                        Forms\Components\DateTimePicker::make('last_login_at')
                            ->label('آخر تسجيل دخول')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user')
                    ->iconColor('primary'),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم النسخ!')
                    ->copyMessageDuration(1500),
                
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('الأدوار')
                    ->badge()
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_status')
                    ->label('حالة الاشتراك')
                    ->badge()
                    ->getStateUsing(fn (User $record) => $record->expiry_status['text'])
                    ->color(fn (User $record) => $record->expiry_status['color']),
                
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('آخر تسجيل دخول')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('الدور')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->placeholder('الكل')
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
                
                Tables\Filters\Filter::make('expired')
                    ->label('منتهية الصلاحية')
                    ->query(fn ($query) => $query->where('expires_at', '<', now())->whereNotNull('expires_at')),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('قريبة من الانتهاء (7 أيام)')
                    ->query(fn ($query) => $query->whereBetween('expires_at', [now(), now()->addDays(7)])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('extend')
                    ->label('تمديد')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('days')
                            ->label('عدد الأيام')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->minValue(1),
                    ])
                    ->action(function (User $record, array $data) {
                        if ($record->expires_at) {
                            $record->expires_at = $record->expires_at->addDays($data['days']);
                        } else {
                            $record->expires_at = now()->addDays($data['days']);
                        }
                        $record->save();
                    })
                    ->successNotificationTitle('تم تمديد الصلاحية'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('تعطيل')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
