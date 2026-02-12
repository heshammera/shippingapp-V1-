<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'الأدوار والصلاحيات';
    protected static ?string $pluralLabel = 'الأدوار';
    protected static ?string $modelLabel = 'دور';
    protected static ?string $navigationGroup = '⚙️ النظام';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الدور')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الدور')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('مثال: مدير المبيعات')
                            ->helperText('اسم فريد للدور'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->maxLength(500)
                            ->placeholder('وصف مختصر لهذا الدور...')
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('الصلاحيات')
                    ->schema([
                        Forms\Components\Tabs::make('Permissions')
                            ->tabs(static::getPermissionTabs())
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الدور')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-shield-check')
                    ->iconColor('primary'),
                
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('عدد الصلاحيات')
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('users_count')
                    ->label('عدد المستخدمين')
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'view' => Pages\ViewRole::route('/{record}'),
        ];
    }

    /**
     * Get permission tabs organized by category
     */
    protected static function getPermissionTabs(): array
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        $categories = [
            'shipments' => ['label' => 'الشحنات', 'icon' => 'heroicon-o-truck'],
            'products' => ['label' => 'المنتجات', 'icon' => 'heroicon-o-shopping-bag'],
            'users' => ['label' => 'المستخدمين', 'icon' => 'heroicon-o-users'],
            'roles' => ['label' => 'الأدوار', 'icon' => 'heroicon-o-shield-check'],
            'shipping_companies' => ['label' => 'شركات الشحن', 'icon' => 'heroicon-o-building-office'],
            'delivery_agents' => ['label' => 'المندوبين', 'icon' => 'heroicon-o-user-circle'],
            'collections' => ['label' => 'التحصيلات', 'icon' => 'heroicon-o-banknotes'],
            'expenses' => ['label' => 'المصروفات', 'icon' => 'heroicon-o-receipt-percent'],
            'statuses' => ['label' => 'حالات الشحن', 'icon' => 'heroicon-o-flag'],
            'inventory' => ['label' => 'المخزون', 'icon' => 'heroicon-o-archive-box'],
            'stock_movements' => ['label' => 'حركات المخزون', 'icon' => 'heroicon-o-arrows-right-left'],
            'reports' => ['label' => 'التقارير', 'icon' => 'heroicon-o-chart-bar'],
            'settings' => ['label' => 'الإعدادات', 'icon' => 'heroicon-o-cog-6-tooth'],
            'dashboard' => ['label' => 'لوحة التحكم', 'icon' => 'heroicon-o-home'],
            'activity_log' => ['label' => 'سجل النشاط', 'icon' => 'heroicon-o-clock'],
            'search' => ['label' => 'البحث', 'icon' => 'heroicon-o-magnifying-glass'],
            'notifications' => ['label' => 'الإشعارات', 'icon' => 'heroicon-o-bell'],
            'media' => ['label' => 'الملفات', 'icon' => 'heroicon-o-folder'],
            'permissions' => ['label' => 'الصلاحيات', 'icon' => 'heroicon-o-key'],
        ];

        $tabs = [];

        foreach ($permissions as $category => $categoryPermissions) {
            $categoryInfo = $categories[$category] ?? ['label' => ucfirst($category), 'icon' => 'heroicon-o-rectangle-stack'];
            
            $tabs[] = Forms\Components\Tabs\Tab::make($categoryInfo['label'])
                ->icon($categoryInfo['icon'])
                ->badge(count($categoryPermissions))
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
                        ->label('اختر الصلاحيات')
                        ->options($categoryPermissions->pluck('name')->mapWithKeys(function ($permission) {
                            return [$permission => static::formatPermissionLabel($permission)];
                        }))
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->gridDirection('row'),
                ]);
        }

        return $tabs;
    }

    /**
     * Format permission name to Arabic label
     */
    protected static function formatPermissionLabel(string $permission): string
    {
        $labels = [
            // Shipments
            'view_any' => 'عرض القائمة',
            'view' => 'عرض',
            'create' => 'إنشاء',
            'update' => 'تعديل',
            'delete' => 'حذف',
            'restore' => 'استعادة',
            'force_delete' => 'حذف نهائي',
            'bulk_delete' => 'حذف متعدد',
            'bulk_update_status' => 'تحديث حالة متعدد',
            'bulk_assign_agent' => 'تعيين مندوب متعدد',
            'export_excel' => 'تصدير Excel',
            'export_pdf' => 'تصدير PDF',
            'print_invoices' => 'طباعة فواتير',
            'print_table' => 'طباعة جدول',
            'print_thermal' => 'طباعة حرارية',
            'import' => 'استيراد',
            'download_template' => 'تحميل القالب',
            'update_status' => 'تحديث الحالة',
            'mark_delivered' => 'تعليم كمستلمة',
            'mark_returned' => 'تعليم كمرتجعة',
            'mark_partial_return' => 'إرجاع جزئي',
            'reschedule' => 'إعادة جدولة',
            'assign_agent' => 'تعيين مندوب',
            'change_company' => 'تغيير الشركة',
            'add_notes' => 'إضافة ملاحظات',
            'view_activity_log' => 'عرض سجل النشاط',
            'view_print_history' => 'عرض سجل الطباعة',
            'generate_barcode' => 'توليد باركود',
            'update_tracking_number' => 'تحديث رقم التتبع',
            // Add more translations as needed
        ];

        $parts = explode('.', $permission);
        $action = end($parts);
        
        return $labels[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }
}
