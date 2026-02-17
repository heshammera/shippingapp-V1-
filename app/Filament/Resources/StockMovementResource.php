<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'حركات المخزون';
    protected static ?string $pluralLabel = 'أرشيف الحركات';
    protected static ?string $modelLabel = 'حركة';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = '📦 المخزون';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only form for viewing details
                Forms\Components\Section::make('تفاصيل الحركة')
                    ->schema([
                        Forms\Components\TextInput::make('variant.full_name')
                            ->label('المنتج')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('movement_type')
                            ->label('نوع الحركة')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'purchase' => 'شراء',
                                'adjustment' => 'تعديل',
                                'reserve' => 'حجز',
                                'release' => 'فك حجز',
                                'deduct' => 'خصم',
                                'return' => 'إرجاع',
                                'transfer' => 'نقل',
                                default => $state,
                            })
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('quantity_change')
                            ->label('التغيير')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('quantity_before')
                            ->label('الكمية قبل')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('quantity_after')
                            ->label('الكمية بعد')
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('السبب')
                            ->disabled()
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('user.name')
                            ->label('المستخدم')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('created_at')
                            ->label('التاريخ')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('variant.full_name')
                    ->label('المنتج')
                    ->searchable(['product.name', 'color', 'size'])
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('movement_type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'purchase' => 'شراء',
                        'adjustment' => 'تعديل',
                        'reserve' => 'حجز',
                        'release' => 'فك حجز',
                        'deduct' => 'خصم',
                        'return' => 'إرجاع',
                        'transfer' => 'نقل',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'purchase', 'return' => 'success',
                        'deduct' => 'danger',
                        'reserve' => 'warning',
                        'release' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('quantity_change')
                    ->label('التغيير')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => ($state >= 0 ? '+' : '') . $state)
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('قبل')
                    ->numeric()
                    ->description(fn ($record) => match($record->movement_type) {
                        'reserve', 'release' => 'محجوز',
                        default => 'مخزون فعلي',
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('بعد')
                    ->numeric()
                    ->description(fn ($record) => match($record->movement_type) {
                        'reserve', 'release' => 'محجوز',
                        default => 'مخزون فعلي',
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('reason')
                    ->label('السبب')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->reason)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('shipment.tracking_number')
                    ->label('رقم الشحنة')
                    ->toggleable()
                    ->url(fn ($record) => $record->shipment_id 
                        ? route('filament.admin.resources.shipments.edit', ['record' => $record->shipment_id]) 
                        : null),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('movement_type')
                    ->label('نوع الحركة')
                    ->options([
                        'purchase' => 'شراء',
                        'adjustment' => 'تعديل',
                        'reserve' => 'حجز',
                        'release' => 'فك حجز',
                        'deduct' => 'خصم',
                        'return' => 'إرجاع',
                        'transfer' => 'نقل',
                    ]),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // No manual creation, only system-generated
    }
}
