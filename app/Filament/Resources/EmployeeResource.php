<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'الموظفين';
    protected static ?string $pluralLabel = 'الموظفين';
    protected static ?string $modelLabel = 'موظف';
    protected static ?string $navigationGroup = '👥 الموارد البشرية';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('البيانات الشخصية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_id')
                            ->label('الرقم القومي')
                            ->unique(ignoreRecord: true)
                            ->maxLength(14),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('بيانات الوظيفة والراتب')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('المسمى الوظيفي')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('basic_salary')
                            ->label('الراتب الأساسي')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                        Forms\Components\DatePicker::make('joined_at')
                            ->label('تاريخ التعيين'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('موظف نشط')
                            ->default(true)
                            ->required(),
                        Forms\Components\Select::make('account_id')
                            ->label('الحساب المالي المرتبط')
                            ->relationship('account', 'name_ar')
                            ->searchable()
                            ->helperText('لحساب مديونيات أو سلف الموظف'),
                    ])->columns(2),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('الوظيفة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('الراتب')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('التعيين')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('نشط')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة العمل')
                    ->boolean()
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
