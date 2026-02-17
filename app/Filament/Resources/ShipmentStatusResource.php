<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentStatusResource\Pages;
use App\Models\ShipmentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentStatusResource extends Resource
{
    protected static ?string $model = ShipmentStatus::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'حالات الشحنات';
    protected static ?string $pluralLabel = 'حالات الشحن';
    protected static ?string $modelLabel = 'حالة شحن';
    protected static ?string $navigationGroup = '📦 إدارة الشحنات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الحالة')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الحالة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('الكود البرمجي')
                            ->helperText('يستخدم للربط المحاسبي (مثال: delivered)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\Select::make('color')
                            ->label('لون التمييز')
                            ->options([
                                'success' => 'أخضر (للمكتمل)',
                                'danger' => 'أحمر (للمرتجع/الملغي)',
                                'warning' => 'برتقالي (للمعلق)',
                                'info' => 'أزرق (للجديد)',
                                'primary' => 'نيلي (للافتراضي)',
                                'gray' => 'رمادي (للمؤجل)',
                                'purple' => 'بنفسجي',
                                'pink' => 'زهري',
                                'rose' => 'وردي',
                                'amber' => 'كهرماني',
                                'lime' => 'ليموني',
                                'emerald' => 'زمردي',
                                'teal' => 'أزرق مخضر (Teal)',
                                'cyan' => 'سماوي (Cyan)',
                                'sky' => 'أزرق سماوي',
                                'violet' => 'بنفسجي غامق',
                                'fuchsia' => 'فوشيا',
                                'slate' => 'رمادي غامق',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_default')
                            ->label('حالة افتراضية')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الحالة')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state, ShipmentStatus $record): string => $record->color ?? 'gray'),
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('اللون')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => 'أخضر',
                        'danger' => 'أحمر',
                        'warning' => 'برتقالي',
                        'info' => 'أزرق',
                        'primary' => 'نيلي',
                        'purple' => 'بنفسجي',
                        'pink' => 'زهري',
                        'rose' => 'وردي',
                        'amber' => 'كهرماني',
                        'lime' => 'ليموني',
                        'emerald' => 'زمردي',
                        'teal' => 'Teal',
                        'cyan' => 'Cyan',
                        'sky' => 'أزرق سماوي',
                        'violet' => 'بنفسجي غامق',
                        'fuchsia' => 'فوشيا',
                        'slate' => 'رمادي غامق',
                        default => 'رمادي',
                    })
                    ->icon('heroicon-o-swatch')
                    ->iconColor(fn (string $state): string => $state),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('الترتيب')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('افتراضي')
                    ->boolean(),
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
            'index' => Pages\ListShipmentStatuses::route('/'),
            'create' => Pages\CreateShipmentStatus::route('/create'),
            'edit' => Pages\EditShipmentStatus::route('/{record}/edit'),
        ];
    }
}
