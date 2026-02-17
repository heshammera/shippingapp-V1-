<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductVariantResource\Pages;
use App\Models\ProductVariant;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?string $navigationLabel = 'المخزون (Variants)';
    protected static ?string $pluralLabel = 'الأنواع';
    protected static ?string $modelLabel = 'نوع';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = '📦 المنتجات والمخزون';
    
    // Hide from main navigation - accessed via ProductResource RelationManager
    protected static bool $shouldRegisterNavigation = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات النوع')
                    ->schema([
                        Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU (رمز المنتج)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('AUTO'),
                        
                        Forms\Components\TextInput::make('color')
                            ->label('اللون')
                            ->maxLength(50),
                        
                        Forms\Components\TextInput::make('size')
                            ->label('المقاس')
                            ->maxLength(50),
                        
                        Forms\Components\TextInput::make('barcode')
                            ->label('الباركود')
                            ->maxLength(50),
                    ])->columns(2),
                
                Forms\Components\Section::make('إدارة المخزون')
                    ->schema([
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('الكمية في المخزن')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('reserved_quantity')
                            ->label('المحجوز')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->hint('يُحدث تلقائياً من الشحنات'),
                        
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->label('حد التنبيه')
                            ->required()
                            ->numeric()
                            ->default(5),
                        
                        Forms\Components\Toggle::make('is_unlimited')
                            ->label('مخزون غير محدود')
                            ->default(false)
                            ->helperText('إذا كان مفعّلاً، لن يتم حساب المخزون'),
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
                
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ SKU'),
                
                Tables\Columns\TextColumn::make('color')
                    ->label('اللون')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('size')
                    ->label('المقاس')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('المخزون')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->is_low_stock ? 'danger' : 'success')
                    ->icon(fn ($record) => $record->is_low_stock ? 'heroicon-o-exclamation-triangle' : null),
                
                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label('المحجوز')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('المتاح')
                    ->getStateUsing(fn ($record) => $record->available_quantity)
                    ->numeric()
                    ->badge()
                    ->color(fn ($record) => $record->available_quantity <= 0 ? 'danger' : 'success'),
                
                Tables\Columns\IconColumn::make('is_unlimited')
                    ->label('غير محدود')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('المنتج')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('low_stock')
                    ->label('مخزون منخفض')
                    ->query(fn ($query) => $query->lowStock()),
                
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('نفد من المخزون')
                    ->query(fn ($query) => $query->outOfStock()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjust_stock')
                    ->label('تعديل')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        Forms\Components\TextInput::make('quantity_change')
                            ->label('التغيير')
                            ->numeric()
                            ->required()
                            ->helperText('أدخل رقماً موجباً للإضافة أو سالباً للنقص'),
                        Forms\Components\Textarea::make('reason')
                            ->label('السبب')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(\App\Services\InventoryService::class)->adjust(
                            $record,
                            $data['quantity_change'],
                            $data['reason']
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم تعديل المخزون بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductVariants::route('/'),
        ];
    }
}
