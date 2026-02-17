<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\Shipment;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class BarcodeScanner extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = '📦 إدارة الشحنات';
    protected static ?string $title = 'ماسح الباركود';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.barcode-scanner';

    public $barcode = '';
    public array $scannedShipmentIds = [];

    public function scan()
    {
        $this->barcode = trim($this->barcode);
        $this->validate(['barcode' => 'required']);

        // Check Variants first
        $variant = \App\Models\ProductVariant::where('barcode', $this->barcode)->first();
        if ($variant) {
            $this->reset('barcode');
            
            Notification::make()
                ->title('تم العثور على المنتج (Variant)')
                ->body($variant->full_name . " - الكمية: " . $variant->stock_quantity)
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view_inventory')
                        ->label('عرض المخزون')
                        ->url(route('filament.admin.resources.inventory-levels.index', ['tableFilters[search][value]' => $variant->sku])),
                ])
                ->send();
            return;
        }

        // Check Products
        $product = \App\Models\Product::where('barcode', $this->barcode)->first();
        if ($product) {
            $this->reset('barcode');
             Notification::make()
                ->title('تم العثور على المنتج')
                ->body($product->name)
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('edit')
                        ->label('تعديل المنتج')
                        ->url(route('filament.admin.resources.products.edit', $product))
                ])
                ->send();
            return;
        }

        // Check Shipments
        $shipment = Shipment::where('tracking_number', $this->barcode)
            ->orWhere('barcode', $this->barcode)
            ->first();

        if ($shipment) {
            $this->reset('barcode');
            
            // Add to scanned list (prepend to keep latest at top conceptually, though table sorting handles display)
            if (!in_array($shipment->id, $this->scannedShipmentIds)) {
                array_unshift($this->scannedShipmentIds, $shipment->id);
            }
            
            Notification::make()
                ->title('تم العثور على الشحنة')
                ->body("رقم التتبع: {$shipment->tracking_number}")
                ->success()
                ->send();
            return;
        }

        Notification::make()
            ->title('غير موجود')
            ->body("لم يتم العثور على منتج أو شحنة بهذا الباركود: {$this->barcode}")
            ->danger()
            ->send();
        
        $this->reset('barcode');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => 
                Shipment::query()
                    ->whereIn('id', $this->scannedShipmentIds)
                    ->with(['status', 'shippingCompany', 'deliveryAgent', 'products'])
            )
            ->columns([
                Tables\Columns\IconColumn::make('is_printed')
                    ->label(new \Illuminate\Support\HtmlString('
                        <div class="flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                        </div>
                    '))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle') // Checkmark
                    ->falseIcon('heroicon-o-x-circle')     // X Mark
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('رقم التتبع')
                    ->searchable()
                    ->formatStateUsing(function (string $state, Shipment $record) {
                        $code = $record->tracking_number;
                        try {
                            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                            $svg = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 80);
                            $svg = str_replace('<svg', '<svg preserveAspectRatio="none"', $svg);
                            $base64 = base64_encode($svg);
                            $barcodeSrc = "data:image/svg+xml;base64,{$base64}";
                        } catch (\Throwable $e) { $barcodeSrc = ""; }
                        
                        return new \Illuminate\Support\HtmlString("
                            " . ($record->is_printed && $record->print_date ? "
                                    <span class='text-[10px] text-gray-500 dark:text-gray-400 font-mono font-bold opacity-80'>
                                        " . $record->print_date->format(\App\Models\Setting::getValue('date_format', 'Y-m-d') . ' ' . \App\Models\Setting::getValue('time_format', 'H:i')) . "
                                    </span>
                                </div>
                            " : "") . "
                            <div class='flex flex-col items-center justify-center space-y-1 w-full' style='min-width: 90px;'>
                                <div class='mt-2'>
                                    <img src='{$barcodeSrc}' style='width: 100px; height: 50px; opacity: 0.8;' class='mix-blend-multiply invert-on-dark'>
                                </div>
                                <span class='font-mono text-[10px] font-bold tracking-wider text-gray-600 dark:text-gray-300 block bg-gray-50 dark:bg-gray-800 px-1 rounded'>{$code}</span>
                                " . ($record->is_printed ? "<span class='text-[9px] text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/50 px-1 rounded-full'>تمت الطباعة</span>" : "") . "
                            </div>
                        ");
                    })
                    ->html(),
                    
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('بيانات العميل')
                    ->state(fn (Shipment $record) => "
                        <div class='flex flex-col'>
                            <span class='font-bold text-sm text-gray-900 dark:text-white'>{$record->customer_name}</span>
                            <span class='text-xs text-gray-500 dark:text-gray-400'>{$record->customer_phone}</span>
                            " . ($record->governorate ? "<span class='text-[10px] text-gray-400 dark:text-gray-500'>{$record->governorate}</span>" : "") . "
                        </div>
                    ")
                    ->html(),

                Tables\Columns\TextColumn::make('products_summary')
                    ->label('المنتجات')
                    ->state(function (Shipment $record) {
                        return $record->products->map(function ($product) {
                            $color = $product->pivot->color ?? '';
                            $size = $product->pivot->size ?? '';
                            $colorHex = '#eee'; 
                            $colors = [
                                'أسود' => '#000', 'black' => '#000',
                                'أبيض' => '#fff', 'white' => '#fff',
                                'أحمر' => '#dc2626', 'red' => '#dc2626',
                                'أزرق' => '#2563eb', 'blue' => '#2563eb',
                                'بيج' => '#f5f5dc',
                                'رصاصي' => '#808080',
                                'بترولي' => '#004b9a',
                                'نبيتي' => '#722f37',
                                'زيتي' => '#708238',
                                'فوشيا' => '#ff00ff',
                            ];
                            foreach($colors as $key => $val) {
                                if(str_contains($color, $key)) $colorHex = $val;
                            }
                            
                            $textColor = (in_array($colorHex, ['#fff', '#ffffff', '#f5f5dc', '#eee', '#f3f4f6'])) ? '#000' : '#fff';
                            $border = (in_array($colorHex, ['#fff', '#ffffff', '#f5f5dc', '#eee', '#f3f4f6'])) ? 'border: 1px solid #ddd;' : '';

                            return "
                            <div class='flex flex-col items-start justify-center py-1 mb-1 border-b border-gray-100 last:border-0 dark:border-gray-700 w-full'>
                                <div class='flex justify-between items-center w-full'>
                                    <span class='font-bold text-xs text-gray-800 dark:text-white truncate' style='max-width: 80%;'>{$product->name}</span>
                                    <span class='text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'>x{$product->pivot->quantity}</span>
                                </div>
                                <div class='flex items-center gap-2 mt-1 flex-wrap'>
                                    " . ($color ? "<span class='px-1.5 py-0.5 text-[9px] rounded-sm shadow-sm' style='background-color: {$colorHex}; color: {$textColor}; {$border}'>{$color}</span>" : "") . "
                                    " . ($size ? "<span class='text-[10px] text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 px-1 rounded'>{$size}</span>" : "") . "
                                    <span class='text-[10px] text-gray-400 dark:text-gray-500'>|</span>
                                    <span class='text-[10px] font-medium text-gray-600 dark:text-gray-300'>
                                       " . number_format($product->pivot->price) . " ج.م
                                    </span>
                                </div>
                            </div>";
                        })->implode('');
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->weight('bold'),

                Tables\Columns\SelectColumn::make('status_id')
                    ->label('الحالة')
                    ->options(\App\Models\ShipmentStatus::pluck('name', 'id')->toArray())
                    ->disablePlaceholderSelection()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\SelectColumn::make('shipping_company_id')
                    ->label('شركة الشحن')
                    ->options(\App\Models\ShippingCompany::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\SelectColumn::make('delivery_agent_id')
                    ->label('المندوب')
                    ->options(\App\Models\DeliveryAgent::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable()
                    ->placeholder('اختيار')
                    ->toggleable()
                    ->disabled(fn (Shipment $record) => $record->shipping_company_id != 7),

                Tables\Columns\TextInputColumn::make('shipping_date')
                    ->label('تاريخ الشحن')
                    ->type('date')
                    ->rules(['date'])
                    ->toggleable(),

                Tables\Columns\TextInputColumn::make('delivery_date')
                    ->label('تاريخ التسليم')
                    ->type('date')
                    ->rules(['date'])
                    ->toggleable(),

                Tables\Columns\TextInputColumn::make('return_date')
                    ->label('تاريخ الارجاع')
                    ->type('date')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (Shipment $record) => match ($record->status->code ?? '') {
                'delivered' => 'tr-success', 
                'returned' => 'tr-danger', 
                'partial_return' => 'tr-warning', 
                'rescheduled' => 'tr-info', 
                default => 'tr-' . ($record->status->color ?? 'gray'),
            })
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Shipment $record) => route('filament.admin.resources.shipments.view', $record)),
                Tables\Actions\EditAction::make()
                    ->url(fn (Shipment $record) => route('filament.admin.resources.shipments.edit', $record)),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear')
                    ->label('مسح القائمة')
                    ->color('danger')
                    ->action(fn () => $this->scannedShipmentIds = [])
                    ->requiresConfirmation()
                    ->visible(fn () => !empty($this->scannedShipmentIds)),
            ]);
    }
}
