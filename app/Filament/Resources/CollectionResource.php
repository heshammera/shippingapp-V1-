<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'التحصيلات';
    protected static ?string $pluralLabel = 'التحصيلات';
    protected static ?string $modelLabel = 'تحصيل';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات التحصيل')
                    ->schema([
                        Forms\Components\Select::make('shipping_company_id')
                            ->label('شركة الشحن')
                            ->relationship('shippingCompany', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('delivery_agent_id')
                            ->label('المندوب')
                            ->relationship('deliveryAgent', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('EGP'),
                        Forms\Components\DatePicker::make('collection_date')
                            ->label('تاريخ التحصيل')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shippingCompany.name')
                    ->label('شركة الشحن')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deliveryAgent.name')
                    ->label('المندوب')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collection_date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تم الإنشاء في')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shipping_company_id')
                    ->label('شركة الشحن')
                    ->relationship('shippingCompany', 'name'),
                Tables\Filters\Filter::make('collection_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('collection_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('collection_date', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('collection_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
