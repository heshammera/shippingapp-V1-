<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'أنواع المنتج (المخزون)';
    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('color')
                    ->label('اللون')
                    ->options(function (Forms\Get $get) {
                        $productId = $get('product_id') ?? $this->getOwnerRecord()->id;
                        $product = \App\Models\Product::find($productId);
                        
                        if (!$product || !$product->colors) {
                            return [];
                        }
                        
                        // Convert array of colors to key-value pairs
                        return array_combine($product->colors, $product->colors);
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->helperText('الألوان المتاحة من المنتج الأساسي'),
                
                Forms\Components\Select::make('size')
                    ->label('المقاس')
                    ->options(function (Forms\Get $get) {
                        $productId = $get('product_id') ?? $this->getOwnerRecord()->id;
                        $product = \App\Models\Product::find($productId);
                        
                        if (!$product || !$product->sizes) {
                            return [];
                        }
                        
                        // Convert array of sizes to key-value pairs
                        return array_combine($product->sizes, $product->sizes);
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->helperText('المقاسات المتاحة من المنتج الأساسي'),
                
                Forms\Components\TextInput::make('sku')
                    ->label('SKU (رمز المنتج)')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('سيتم التوليد تلقائياً')
                    ->helperText('اتركه فارغاً للتوليد التلقائي')
                    ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                        // Auto-generate SKU if empty
                        if (empty($state)) {
                            $productId = $get('product_id') ?? $this->getOwnerRecord()->id;
                            $color = $get('color') ?? '';
                            $size = $get('size') ?? '';
                            
                            return $this->generateSKU($productId, $color, $size);
                        }
                        return $state;
                    }),
                
                Forms\Components\TextInput::make('stock_quantity')
                    ->label('الكمية في المخزن')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                
                Forms\Components\TextInput::make('low_stock_threshold')
                    ->label('حد التنبيه')
                    ->required()
                    ->numeric()
                    ->default(5),
                
                Forms\Components\TextInput::make('barcode')
                    ->label('الباركود')
                    ->maxLength(50)
                    ->placeholder('سيتم التوليد تلقائياً')
                    ->helperText('اتركه فارغاً للتوليد التلقائي')
                    ->dehydrateStateUsing(function ($state) {
                        // Auto-generate barcode if empty
                        if (empty($state)) {
                            return 'BC' . time() . rand(1000, 9999);
                        }
                        return $state;
                    }),
                
                Forms\Components\Toggle::make('is_unlimited')
                    ->label('مخزون غير محدود')
                    ->default(false),
            ])->columns(2);
    }
    
    /**
     * Generate SKU for variant
     */
    private function generateSKU($productId, $color, $size): string
    {
        $product = \App\Models\Product::find($productId);
        $productName = $product ? $product->name : 'PROD';
        
        // Clean and shorten product name
        $productPart = substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 4);
        if (empty($productPart)) {
            $productPart = 'P' . $productId;
        }
        
        // Clean color and size
        $colorPart = substr(preg_replace('/[^A-Za-z0-9]/', '', $color), 0, 3);
        $sizePart = substr(preg_replace('/[^A-Za-z0-9]/', '', $size), 0, 3);
        
        // Fallback to hash if Arabic
        if (empty($colorPart)) {
            $colorPart = 'C' . abs(crc32($color) % 100);
        }
        if (empty($sizePart)) {
            $sizePart = 'S' . abs(crc32($size) % 100);
        }
        
        return strtoupper($productPart . '-' . $colorPart . '-' . $sizePart . '-' . substr(uniqid(), -4));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                
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
                    ->color(fn ($record) => $record->is_low_stock ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->label('المحجوز')
                    ->numeric()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('المتاح')
                    ->getStateUsing(fn ($record) => $record->available_quantity)
                    ->badge()
                    ->color(fn ($record) => $record->available_quantity <= 0 ? 'danger' : 'success'),
                
                Tables\Columns\IconColumn::make('is_unlimited')
                    ->label('غير محدود')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('مخزون منخفض')
                    ->query(fn ($query) => $query->lowStock()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة نوع'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjust_stock')
                    ->label('تعديل المخزون')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
