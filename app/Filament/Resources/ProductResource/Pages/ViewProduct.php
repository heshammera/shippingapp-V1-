<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('تفاصيل المنتج')
                    ->schema([
                        TextEntry::make('name')
                            ->label('اسم المنتج')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                        
                        TextEntry::make('price')
                            ->label('سعر البيع')
                            ->money('EGP'),
                        
                        TextEntry::make('cost_price')
                            ->label('سعر التكلفة')
                            ->money('EGP')
                            ->visible(fn () => auth()->user()->role === 'admin'),

                        TextEntry::make('total_stock')
                            ->label('إجمالي المخزون')
                            ->badge()
                            ->color(fn (string $state): string => $state < 10 ? 'danger' : 'success'),
                            
                        TextEntry::make('created_at')
                            ->label('تاريخ الإضافة')
                            ->dateTime(),
                    ])->columns(2),

                Section::make('المخزون والأنواع (Variants)')
                    ->schema([
                        RepeatableEntry::make('variants')
                            ->label('')
                            ->schema([
                                TextEntry::make('sku')->label('SKU'),
                                TextEntry::make('color')->label('اللون'),
                                TextEntry::make('size')->label('المقاس'),
                                TextEntry::make('stock_quantity')
                                    ->label('الكمية المتاحة')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('reserved_quantity')
                                    ->label('المحجوز')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('barcode')->label('Barcode'),
                            ])
                            ->columns(6)
                    ])
            ]);
    }
}
