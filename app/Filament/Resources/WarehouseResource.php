<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront'; // Keep icon for Hub
    protected static bool $shouldRegisterNavigation = true;

    // hidden from sidebar
    protected static ?string $navigationLabel = 'المستودعات';
    protected static ?string $navigationGroup = '📦 المنتجات والمخزون';
    protected static ?string $pluralLabel = 'المستودعات';
    protected static ?string $modelLabel = 'مستودع';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المخزن')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('اسم المخزن')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label('الموقع الجغرافي')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('contact_info')
                            ->label('بيانات التواصل')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('الإعدادات')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label('المخزن الافتراضي')
                            ->helperText('تعيين هذا المخزن كخيار افتراضي عند استلام الشحنات'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->label('#'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('اسم المخزن'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->label('الموقع'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('نشط'),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('افتراضي'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('تاريخ الإنشاء'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
