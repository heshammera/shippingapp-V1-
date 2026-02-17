<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\ChartOfAccount;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'القيود اليومية';
    protected static ?string $modelLabel = 'قيد يومية';
    protected static ?string $pluralModelLabel = 'القيود اليومية';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل القيد')
                    ->schema([
                        Forms\Components\TextInput::make('entry_number')
                            ->label('رقم القيد')
                            ->default('AUTO')
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\DatePicker::make('entry_date')
                            ->label('التاريخ')
                            ->default(now())
                            ->required(),
                            
                        Forms\Components\TextInput::make('description')
                            ->label('البيان')
                            ->required()
                            ->columnSpanFull(),
                            
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'posted' => 'مرحل',
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('أطراف القيد')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->label('الأسطر')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('account_id')
                                    ->label('الحساب')
                                    ->options(fn () => \App\Models\ChartOfAccount::where('level', '>=', 3)->pluck('name_ar', 'id')) // Simplified query
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),
                                    
                                Forms\Components\TextInput::make('debit')
                                    ->label('مدين')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        if ($state > 0) $set('credit', 0);
                                    }),
                                    
                                Forms\Components\TextInput::make('credit')
                                    ->label('دائن')
                                    ->numeric()
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        if ($state > 0) $set('debit', 0);
                                    }),
                                    
                                Forms\Components\TextInput::make('description')
                                    ->label('ملاحظات')
                                    ->columnSpan(2),
                            ])
                            ->columns(6)
                            ->defaultItems(2)
                            ->columnSpanFull()
                            ->live(),
                            
                        Forms\Components\Placeholder::make('total_debit')
                            ->label('إجمالي المدين')
                            ->content(fn (Forms\Get $get) => collect($get('lines'))->sum('debit')),
                            
                        Forms\Components\Placeholder::make('total_credit')
                            ->label('إجمالي الدائن')
                            ->content(fn (Forms\Get $get) => collect($get('lines'))->sum('credit')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_number')
                    ->label('رقم القيد')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('entry_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('البيان')
                    ->searchable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->colors([
                        'primary' => 'manual',
                        'success' => 'automatic',
                        'warning' => 'opening_balance',
                    ]),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'posted',
                    ]),
                    
                Tables\Columns\TextColumn::make('lines_sum_debit')
                    ->label('الإجمالي')
                    ->sum('lines', 'debit')
                    ->money('EGP'),
                    
                Tables\Columns\TextColumn::make('created_by')
                    ->label('بواسطة')
                    ->formatStateUsing(fn ($record) => $record->creator->name ?? 'System'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'مسودة',
                        'posted' => 'مرحل',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'manual' => 'يدوي',
                        'automatic' => 'تلقائي',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('entry_date', 'desc');
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
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'view' => Pages\ViewJournalEntry::route('/{record}'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
