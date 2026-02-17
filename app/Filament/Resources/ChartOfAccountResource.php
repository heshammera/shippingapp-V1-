<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChartOfAccountResource\Pages;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'دليل الحسابات (COA)';
    protected static ?string $pluralLabel = 'الحسابات';
    protected static ?string $modelLabel = 'حساب';
    protected static ?string $navigationGroup = '💰 المالية';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('كود الحساب')
                    ->required()
                    ->unique(ignoreRecord: true),
                
                Forms\Components\TextInput::make('name_ar')
                    ->label('الاسم (عربي)')
                    ->required(),
                    
                Forms\Components\Select::make('parent_id')
                    ->label('الحساب الرئيسي')
                    ->relationship('parent', 'name_ar')
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('type')
                    ->label('النوع')
                    ->options([
                        'asset' => 'أصول',
                        'liability' => 'خصوم',
                        'equity' => 'حقوق ملكية',
                        'revenue' => 'إيرادات',
                        'expense' => 'مصروفات',
                    ])
                    ->required(),
                    
                Forms\Components\Select::make('nature')
                    ->label('الطبيعة')
                    ->options([
                        'debit' => 'مدين',
                        'credit' => 'دائن',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('الكود')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name_ar')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('parent.name_ar')->label('الحساب الرئيسي'),
                Tables\Columns\TextColumn::make('type')->label('النوع')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                        'asset' => 'أصول',
                        'liability' => 'خصوم',
                        'equity' => 'حقوق ملكية',
                        'revenue' => 'إيرادات',
                        'expense' => 'مصروفات',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChartOfAccounts::route('/'),
            'create' => Pages\CreateChartOfAccount::route('/create'),
            'edit' => Pages\EditChartOfAccount::route('/{record}/edit'),
        ];
    }
}
