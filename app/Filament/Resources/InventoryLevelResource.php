<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryLevelResource\Pages;
use App\Filament\Resources\InventoryLevelResource\RelationManagers;
use App\Models\InventoryLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryLevelResource extends Resource
{
    protected static ?string $model = InventoryLevel::class;

    protected static ?string $navigationGroup = 'إدارة المخزون';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'مستويات المخزون';
    protected static ?string $pluralLabel = 'مستويات المخزون';
    protected static ?string $modelLabel = 'مخزون';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المخزون')
                    ->schema([
                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->label('المخزن')
                            ->disabled(fn ($context) => $context === 'edit')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('variant_id')
                            ->relationship('variant', 'sku')
                            ->label('المنتج')
                            ->disabled(fn ($context) => $context === 'edit')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),
                        
                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية الحالية')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($context) => $context === 'edit') // Allow setting initial quantity on create
                            ->helperText(fn ($context) => $context === 'create' ? 'يمكنك تعيين رصيد افتتاحي هنا' : 'يتم تحديث الكمية آلياً عبر حركات المخزون'),
                        
                        Forms\Components\TextInput::make('shelf_location')
                            ->label('موقع الرف')
                            ->placeholder('A1-B3'),
                        
                        Forms\Components\DateTimePicker::make('last_counted_at')
                            ->label('آخر جرد')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('variant.product.name')
                    ->label('المنتج')
                    ->sortable()
                    ->searchable()
                    ->description(fn ($record) => $record->variant->color . ' - ' . $record->variant->size),
                
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->sortable()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('shelf_location')
                    ->label('موقع الرف')
                    ->searchable()
                    ->icon('heroicon-o-map-pin'),
                
                Tables\Columns\TextColumn::make('last_counted_at')
                    ->label('آخر جرد')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name')
                    ->label('تصفية حسب المخزن'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->poll('5s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryLevels::route('/'),
            'create' => Pages\CreateInventoryLevel::route('/create'),
            'edit' => Pages\EditInventoryLevel::route('/{record}/edit'),
        ];
    }
}
