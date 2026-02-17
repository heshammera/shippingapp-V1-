<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'الفواتير';
    protected static ?string $modelLabel = 'فاتورة';
    protected static ?string $pluralModelLabel = 'الفواتير';
    protected static ?string $navigationGroup = '💰 الإدارة المالية';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات العميل')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('العميل')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $customer = \App\Models\User::find($state);
                                    if ($customer) {
                                        $set('customer_name', $customer->name);
                                        $set('customer_email', $customer->email);
                                        $set('customer_phone', $customer->phone);
                                        $set('customer_address', $customer->address);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('اسم العميل')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('الهاتف')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('customer_address')
                            ->label('العنوان')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تفاصيل الفاتورة')
                    ->schema([
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('تاريخ الإصدار')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->default(now()->addDays(30))
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'draft' => 'مسودة',
                                'issued' => 'صادرة',
                                'paid' => 'مدفوعة',
                                'cancelled' => 'ملغية',
                                'overdue' => 'متأخرة',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('tax_rate')
                            ->label('نسبة الضريبة (%)')
                            ->numeric()
                            ->default(14)
                            ->suffix('%')
                            ->required(),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('الخصم')
                            ->numeric()
                            ->default(0)
                            ->prefix('EGP'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('البنود')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('description', $product->name);
                                                $set('unit_price', $product->price);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('description')
                                    ->label('الوصف')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('سعر الوحدة')
                                    ->numeric()
                                    ->required()
                                    ->prefix('EGP')
                                    ->live(),

                                Forms\Components\Placeholder::make('amount')
                                    ->label('المجموع')
                                    ->content(function (Forms\Get $get) {
                                        $qty = $get('quantity') ?? 0;
                                        $price = $get('unit_price') ?? 0;
                                        return number_format($qty * $price, 2) . ' EGP';
                                    }),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->addActionLabel('إضافة بند')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('ملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3),

                        Forms\Components\Textarea::make('terms')
                            ->label('الشروط والأحكام')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('issue_date')
                    ->label('تاريخ الإصدار')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('تاريخ الاستحقاق')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('المبلغ الإجمالي')
                    ->money('EGP')
                    ->sortable()
                    ->weight('bold')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('EGP')),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'مسودة',
                        'issued' => 'صادرة',
                        'paid' => 'مدفوعة',
                        'cancelled' => 'ملغية',
                        'overdue' => 'متأخرة',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'issued' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'overdue' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_overdue')
                    ->label('متأخرة؟')
                    ->boolean()
                    ->getStateUsing(fn (Invoice $record) => $record->is_overdue)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'draft' => 'مسودة',
                        'issued' => 'صادرة',
                        'paid' => 'مدفوعة',
                        'cancelled' => 'ملغية',
                        'overdue' => 'متأخرة',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->label('متأخرة فقط')
                    ->query(fn ($query) => $query->overdue()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('تحديد كمدفوعة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Invoice $record) => $record->status !== 'paid')
                    ->action(fn (Invoice $record) => $record->markAsPaid())
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('cancel')
                    ->label('إلغاء')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Invoice $record) => !in_array($record->status, ['paid', 'cancelled']))
                    ->action(fn (Invoice $record) => $record->cancel())
                    ->requiresConfirmation(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
