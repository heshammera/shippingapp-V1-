<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الفاتورة')
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label('رقم الفاتورة'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'draft' => 'مسودة',
                                'issued' => 'صادرة',
                                'paid' => 'مدفوعة',
                                'cancelled' => 'ملغية',
                                'overdue' => 'متأخرة',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('issue_date')
                            ->label('تاريخ الإصدار')
                            ->date(),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label('تاريخ الاستحقاق')
                            ->date(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('QR Code')
                    ->schema([
                        Infolists\Components\ViewEntry::make('qr_code_image')
                            ->label('QR Code')
                            ->view('filament.infolists.entries.qr-code')
                            ->state(fn (Invoice $record) => $record->qr_code_image),
                    ])
                    ->collapsible(),

                Infolists\Components\Section::make('معلومات العميل')
                    ->schema([
                        Infolists\Components\TextEntry::make('customer_name')
                            ->label('اسم العميل'),
                        Infolists\Components\TextEntry::make('customer_email')
                            ->label('البريد الإلكتروني'),
                        Infolists\Components\TextEntry::make('customer_phone')
                            ->label('الهاتف'),
                        Infolists\Components\TextEntry::make('customer_address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('البنود')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label('الوصف'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('الكمية'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('سعر الوحدة')
                                    ->money('EGP'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label('المجموع')
                                    ->money('EGP'),
                            ])
                            ->columns(4),
                    ]),

                Infolists\Components\Section::make('المبالغ')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('الإجمالي قبل الضريبة')
                            ->money('EGP'),
                        Infolists\Components\TextEntry::make('tax_rate')
                            ->label('نسبة الضريبة')
                            ->suffix('%'),
                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label('قيمة الضريبة')
                            ->money('EGP'),
                        Infolists\Components\TextEntry::make('discount_amount')
                            ->label('الخصم')
                            ->money('EGP'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('الإجمالي النهائي')
                            ->money('EGP')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('ملاحظات')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('ملاحظات'),
                        Infolists\Components\TextEntry::make('terms')
                            ->label('الشروط والأحكام'),
                    ])
                    ->collapsible(),
            ]);
    }
}
