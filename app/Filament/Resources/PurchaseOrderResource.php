<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = '📦 المخزون';
    protected static ?string $navigationLabel = 'أوامر الشراء';
    protected static ?string $pluralLabel = 'أوامر الشراء';
    protected static ?string $modelLabel = 'أمر شراء';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->label('المورد')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->label('المخزن المستلم')
                            ->required(),

                        Forms\Components\DatePicker::make('order_date')
                            ->label('تاريخ الطلب')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->label('تاريخ التوصيل المتوقع'),
                        
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'ordered' => 'تم الطلب',
                                'received' => 'تم الاستلام',
                                'cancelled' => 'ملغي',
                            ])
                            ->required()
                            ->default('draft')
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('الأصناف')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->label('المنتجات المطلوبة')
                            ->schema([
                                Forms\Components\Select::make('variant_id')
                                    ->label('المنتج')
                                    ->relationship('variant', 'sku')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('quantity_ordered')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                                
                                Forms\Components\TextInput::make('unit_cost')
                                    ->label('التكلفة الوحدوية')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('المورد')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('المخزن')
                    ->sortable(),
                
                Tables\Columns\SelectColumn::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'ordered' => 'تم الطلب',
                        'received' => 'تم الاستلام',
                        'cancelled' => 'ملغي',
                    ])
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('موعد الوصول')
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->status === 'received') return 'success';
                        if ($record->status === 'cancelled') return 'gray';
                        
                        $date = \Carbon\Carbon::parse($state);
                        
                        if ($date->isToday()) return 'warning';
                        if ($date->isPast()) return 'danger';
                        
                        return 'info';
                    })
                    ->description(function ($state, $record) {
                        if ($record->status === 'received') return 'تم الاستلام';
                        if ($record->status === 'cancelled') return null;
                        
                        $date = \Carbon\Carbon::parse($state);
                        
                        if ($date->isToday()) return 'يصل اليوم';
                        if ($date->isPast()) return 'تأخر ' . $date->diffForHumans();
                        
                        return 'يصل ' . $date->diffForHumans();
                    }),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'ordered' => 'تم الطلب',
                        'received' => 'تم الاستلام',
                        'cancelled' => 'ملغي',
                    ]),
                
                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->label('المورد'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
