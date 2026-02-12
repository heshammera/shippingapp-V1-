<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTransferResource\Pages;
use App\Models\StockTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'تحويلات مخزنية';
    protected static ?string $navigationGroup = '📦 المخزون';
    protected static ?string $pluralLabel = 'تحويلات المخزون';
    protected static ?string $modelLabel = 'تحويل مخزني';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل النقل')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('رقم المرجع')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('سيتم التوليد تلقائياً'),
                        
                        Forms\Components\DatePicker::make('transfer_date')
                            ->label('تاريخ النقل')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('from_warehouse_id')
                            ->label('من مخزن')
                            ->relationship('fromWarehouse', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),

                        Forms\Components\Select::make('to_warehouse_id')
                            ->label('إلى مخزن')
                            ->relationship('toWarehouse', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->different('from_warehouse_id')
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),
                        
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'pending' => 'قيد الانتظار',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                            ])
                            ->default('draft')
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status === 'completed'), // Cannot change if completed

                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('المنتجات')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('variant_id')
                                    ->label('المنتج')
                                    ->relationship('variant', 'sku')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->disabled(fn ($record) => $record && $record->status === 'completed'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('المرجع')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fromWarehouse.name')
                    ->label('من')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('toWarehouse.name')
                    ->label('إلى')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'pending' => 'قيد الانتظار',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('items_count')
                    ->label('عدد العناصر')
                    ->counts('items'),

                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('بواسطة')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('complete')
                    ->label('إتمام النقل')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد نقل المخزون')
                    ->modalDescription('هل أنت متأكد من إتمام عملية النقل؟ سيتم خصم المخزون من المصدر وإضافته للوجهة فوراً.')
                    ->visible(fn (StockTransfer $record) => $record->status !== 'completed' && $record->status !== 'cancelled')
                    ->action(function (StockTransfer $record) {
                        try {
                            if ($record->items->isEmpty()) {
                                Notification::make()->title('لا توجد عناصر للنقل')->danger()->send();
                                return;
                            }

                            $inventoryService = app(InventoryService::class);
                            
                            \DB::transaction(function () use ($record, $inventoryService) {
                                foreach ($record->items as $item) {
                                    $inventoryService->transfer(
                                        $item->variant,
                                        $record->fromWarehouse,
                                        $record->toWarehouse,
                                        $item->quantity,
                                        "Stock Transfer #{$record->reference_number}",
                                        $record->reference_number
                                    );
                                }
                                
                                $record->update(['status' => 'completed']);
                            });

                            Notification::make()->title('تم النقل بنجاح')->success()->send();

                        } catch (\Exception $e) {
                            Notification::make()->title('فشل النقل')->body($e->getMessage())->danger()->send();
                        }
                    }),
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
            'index' => Pages\ListStockTransfers::route('/'),
            'create' => Pages\CreateStockTransfer::route('/create'),
            'edit' => Pages\EditStockTransfer::route('/{record}/edit'),
        ];
    }
}
