<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\ChartOfAccount;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'المصروفات';
    protected static ?string $modelLabel = 'مصروف';
    protected static ?string $pluralModelLabel = 'المصروفات';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('description')
                            ->label('الوصف')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required()
                            ->prefix('EGP'),
                            
                        Forms\Components\DatePicker::make('expense_date')
                            ->label('طتاريخ المصروف')
                            ->default(now())
                            ->required(),
                            
                        Forms\Components\Select::make('expense_account_id')
                            ->label('حساب المصروف')
                            ->options(ChartOfAccount::where('type', 'expense')->pluck('name_ar', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\FileUpload::make('receipt_image')
                            ->label('صورة الإيصال')
                            ->image()
                            ->directory('expenses'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('حالة الدفع')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'submitted' => 'مقدم للمراجعة',
                                'approved' => 'معتمد',
                                'rejected' => 'مرفوض',
                                'paid' => 'مدفوع',
                            ])
                            ->default('draft')
                            ->required(),
                            
                        Forms\Components\Select::make('paid_via_account_id')
                            ->label('طريقة الدفع (الخزينة/البنك)')
                            ->options(ChartOfAccount::where('type', 'asset')->where(function($q) {
                                $q->where('code', 'like', '1101%')->orWhere('code', 'like', '1102%');
                            })->pluck('name_ar', 'id'))
                            ->searchable()
                            ->placeholder('اختر حساب الدفع إذا تم الدفع'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('رقم مرجعي')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('EGP')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('expenseAccount.name_ar')
                    ->label('الحساب'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'submitted',
                        'success' => 'approved', // or paid
                        'danger' => 'rejected',
                    ]),
                    
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('الموظف'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                     ->options([
                        'draft' => 'مسودة',
                        'submitted' => 'مقدم',
                        'approved' => 'معتمد',
                        'rejected' => 'مرفوض',
                        'paid' => 'مدفوع',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('اعتماد')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Expense $record) => in_array($record->status, ['draft', 'submitted']))
                    ->action(function (Expense $record) {
                        $record->approve(auth()->user());
                        // Trigger Accounting
                        app(\App\Services\AccountingService::class)->createExpenseEntry($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('تم اعتماد المصروف وترحيل القيد')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
