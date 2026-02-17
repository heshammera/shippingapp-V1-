<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentSettlementResource\Pages;
use App\Filament\Resources\AgentSettlementResource\RelationManagers;
use App\Models\AgentSettlement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentSettlementResource extends Resource
{
    protected static ?string $model = AgentSettlement::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'تسويات المناديب';
    protected static ?string $pluralLabel = 'تسويات المناديب';
    protected static ?string $modelLabel = 'تسوية';
    protected static ?string $navigationGroup = 'الشؤون المالية';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('رقم التسوية')
                            ->readOnly()
                            ->placeholder('سيتم التوليد تلقائياً'),
                        Forms\Components\Select::make('agent_id')
                            ->label('المندوب')
                            ->relationship('agent', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ المحصل')
                            ->numeric()
                            ->required()
                            ->prefix('EGP'),
                        Forms\Components\DatePicker::make('settlement_date')
                            ->label('تاريخ التوريد')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('receiving_account_id')
                            ->label('حساب الاستلام')
                            ->options(\App\Models\ChartOfAccount::whereIn('code', ['1101', '1102'])->pluck('name_ar', 'id'))
                            ->required()
                            ->searchable()
                            ->helperText('الخزينة أو البنك'),
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('الرقم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('المندوب')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('settlement_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('receivingAccount.name_ar')
                    ->label('الحساب')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('agent_id')
                    ->label('المندوب')
                    ->relationship('agent', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'تم التأكيد',
                        'cancelled' => 'ملغي',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (AgentSettlement $record) => $record->status === 'pending'),
                
                Tables\Actions\Action::make('confirm')
                    ->label('تأكيد الترحيل')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (AgentSettlement $record) => $record->status === 'pending')
                    ->action(function (AgentSettlement $record) {
                        app(\App\Services\AccountingService::class)->createSettlementEntry($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم تأكيد التسوية وترحيل القيد المحاسبي')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListAgentSettlements::route('/'),
            'create' => Pages\CreateAgentSettlement::route('/create'),
            'edit' => Pages\EditAgentSettlement::route('/{record}/edit'),
        ];
    }
}
