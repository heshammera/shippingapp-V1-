<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'المخزون الحالي';
    protected static ?string $navigationGroup = '📦 المنتجات والمخزون';
    protected static ?string $pluralLabel = 'المخزون';
    protected static ?string $modelLabel = 'عنصر مخزون';
    protected static ?int $navigationSort = 2;
    
    // Hidden - replaced by ProductVariant system
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المخزون')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('color')
                                    ->label('اللون')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('size')
                                    ->label('المقاس')
                                    ->maxLength(50),
                            ]),
                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية الحالية')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('low_stock_alert')
                            ->label('تنبيه انخفاض المخزون عند')
                            ->numeric()
                            ->default(5)
                            ->required(),
                        Forms\Components\Toggle::make('is_unlimited')
                            ->label('كمية غير محدودة')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('المنتج')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('اللون')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('المقاس'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->sortable()
                    ->color(fn (Inventory $record): string => $record->quantity <= $record->low_stock_alert ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_unlimited')
                    ->label('غير محدود')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('المنتج')
                    ->relationship('product', 'name'),
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
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
