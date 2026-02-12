<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Filament\Resources\PayrollResource\RelationManagers;
use App\Models\Payroll;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'الرواتب';
    protected static ?string $pluralLabel = 'مسيرات الرواتب';
    protected static ?string $modelLabel = 'مسير راتب';
    protected static ?string $navigationGroup = '👥 الموارد البشرية';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المسير')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('الرقم المرجعي')
                            ->readOnly()
                            ->placeholder('سيتم التوليد تلقائياً'),
                        Forms\Components\Select::make('month')
                            ->label('الشهر')
                            ->options([
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
                            ])
                            ->required()
                            ->default(now()->month),
                        Forms\Components\TextInput::make('year')
                            ->label('السنة')
                            ->numeric()
                            ->required()
                            ->default(now()->year),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('إجمالي الرواتب')
                            ->numeric()
                            ->readOnly()
                            ->prefix('EGP')
                            ->helperText('يتم حسابه تلقائياً من البنود'),
                    ])->columns(4),

                Forms\Components\Section::make('بنود الرواتب')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('الموظف')
                                    ->relationship('employee', 'name', fn ($query) => $query->where('is_active', true))
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, $set) => $set('basic_salary', \App\Models\Employee::find($state)?->basic_salary ?? 0)),
                                Forms\Components\TextInput::make('basic_salary')
                                    ->label('الراتب الأساسي')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('bonuses')
                                    ->label('الحوافز')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('deductions')
                                    ->label('الاستقطاعات')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('notes')
                                    ->label('ملاحظات'),
                            ])
                            ->columns(5)
                            ->itemLabel(fn (array $state): ?string => \App\Models\Employee::find($state['employee_id'] ?? null)?->name ?? 'موظف')
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $items = $get('items');
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += ($item['basic_salary'] ?? 0) + ($item['bonuses'] ?? 0) - ($item['deductions'] ?? 0);
                                }
                                $set('total_amount', $total);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('الرقم المرجعي')
                    ->searchable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('الشهر')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
                        default => $state
                    }),
                Tables\Columns\TextColumn::make('year')
                    ->label('السنة'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'confirmed' => 'info',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('السنة')
                    ->options(fn () => Payroll::distinct()->pluck('year', 'year')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Payroll $record) => $record->status === 'draft'),
                
                Tables\Actions\Action::make('post')
                    ->label('اعتماد وصرف')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Payroll $record) => $record->status === 'draft')
                    ->action(function (Payroll $record) {
                        app(\App\Services\AccountingService::class)->createPayrollEntry($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم اعتماد مسير الرواتب وترحيله للمحاسبة بنجاح')
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
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
