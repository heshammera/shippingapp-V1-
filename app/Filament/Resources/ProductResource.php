<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'المنتجات';
    protected static ?string $pluralLabel = 'المنتجات';
    protected static ?string $modelLabel = 'منتج';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = '📦 المنتجات والمخزون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المنتج')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المنتج')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                            ->label('سعر البيع')
                            ->required()
                            ->numeric()
                            ->prefix('EGP'),
                        Forms\Components\TextInput::make('cost_price')
                            ->label('سعر التكلفة')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->visible(fn () => auth()->user()->role === 'admin'),
                        Forms\Components\TextInput::make('stock')
                            ->label('المخزون')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TagsInput::make('colors')
                            ->label('الألوان المتاحة')
                            ->placeholder('أضف لون'),
                        Forms\Components\TagsInput::make('sizes')
                            ->label('المقاسات المتاحة')
                            ->placeholder('أضف مقاس'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withSum('variants', 'stock_quantity')->withSum('variants', 'reserved_quantity'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('سعر البيع')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_sum_stock_quantity')
                    ->label('الفعلي')
                    ->numeric()
                    ->sortable()
                    ->default(0)
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('variants_sum_reserved_quantity')
                    ->label('المحجوز')
                    ->numeric()
                    ->sortable()
                    ->default(0)
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('available_stock')
                    ->label('المتاح للبيع')
                    ->state(fn ($record) => ($record->variants_sum_stock_quantity ?? 0) - ($record->variants_sum_reserved_quantity ?? 0))
                    ->numeric()
                    ->badge()
                    ->color(fn (string $state): string => $state < 5 ? 'danger' : 'success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label('التكلفة')
                    ->money('EGP')
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Add View Action
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('print_barcodes')
                        ->label('طباعة الباركود')
                        ->icon('heroicon-o-qr-code')
                        ->action(function (\Illuminate\Support\Collection $records, $livewire) {
                            $url = route('products.print.barcodes', ['ids' => $records->pluck('id')->implode(',')]);
                            $livewire->js("window.open('$url', '_blank')");
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'), // Add View Route
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
