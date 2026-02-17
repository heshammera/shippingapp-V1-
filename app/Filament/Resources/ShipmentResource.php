<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Collection;
use App\Shipping\CarrierFactory;
use Filament\Notifications\Notification;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'الشحنات';
    protected static ?string $navigationGroup = '📦 إدارة الشحنات';
    protected static ?int $navigationSort = 1;
    protected static ?string $pluralLabel = 'الشحنات';
    protected static ?string $modelLabel = 'شحنة';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['status', 'shippingCompany', 'deliveryAgent', 'products']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // Step 1: Customer & Address
                    Forms\Components\Wizard\Step::make('بيانات العميل')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\TextInput::make('customer_name')
                                ->label('اسم العميل')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('customer_phone')
                                ->label('رقم الهاتف')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('alternate_phone')
                                ->label('رقم بديل')
                                ->tel()
                                ->maxLength(20),
                            Forms\Components\Select::make('governorate')
                                ->label('المحافظة')
                                ->options([
                                    'القاهرة' => 'القاهرة', 'الجيزة' => 'الجيزة', 'القليوبية' => 'القليوبية',
                                    'الإسكندرية' => 'الإسكندرية', 'الشرقية' => 'الشرقية', 'الدقهلية' => 'الدقهلية',
                                    'كفر الشيخ' => 'كفر الشيخ', 'المنوفية' => 'المنوفية', 'البحيرة' => 'البحيرة',
                                    'الغربية' => 'الغربية', 'بورسعيد' => 'بورسعيد', 'الإسماعيلية' => 'الإسماعيلية',
                                    'السويس' => 'السويس', 'مطروح' => 'مطروح', 'شمال سيناء' => 'شمال سيناء',
                                    'جنوب سيناء' => 'جنوب سيناء', 'بني سويف' => 'بني سويف', 'الفيوم' => 'الفيوم',
                                    'المنيا' => 'المنيا', 'أسيوط' => 'أسيوط', 'الوادي الجديد' => 'الوادي الجديد',
                                    'سوهاج' => 'سوهاج', 'قنا' => 'قنا', 'الأقصر' => 'الأقصر', 'أسوان' => 'أسوان', 'البحر الأحمر' => 'البحر الأحمر'
                                ])
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $shipping = 60; // Default logic
                                    $set('shipping_price', $shipping);
                                }),
                            Forms\Components\Textarea::make('customer_address')
                                ->label('العنوان بالكامل')
                                ->required()
                                ->columnSpanFull(),
                        ])->columns(2),

                    // Step 2: Products
                    Forms\Components\Wizard\Step::make('المنتجات')
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            Forms\Components\Repeater::make('products')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('المنتج')
                                        ->options(fn () => \App\Models\Product::pluck('name', 'id')->toArray())
                                        ->searchable()
                                        ->preload()
                                        ->live() // Using live instead of reactive for better UX
                                        ->required()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('price', $product->price);
                                            }
                                        })
                                        ->columnSpan(3),
                                    Forms\Components\Select::make('color')
                                        ->label('اللون')
                                        ->options(function (Forms\Get $get) {
                                            $productId = $get('product_id');
                                            if (! $productId) {
                                                return [];
                                            }
                                            $product = \App\Models\Product::find($productId);
                                            if (! $product) {
                                                return [];
                                            }
                                            $colors = $product->availableColors(); // Using the helper method from model
                                            return array_combine($colors, $colors);
                                        })
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\Select::make('size')
                                        ->label('المقاس')
                                        ->options(function (Forms\Get $get) {
                                            $productId = $get('product_id');
                                            if (! $productId) {
                                                return [];
                                            }
                                            $product = \App\Models\Product::find($productId);
                                            if (! $product) {
                                                return [];
                                            }
                                            $sizes = $product->availableSizes(); // Using the helper method from model
                                            return array_combine($sizes, $sizes);
                                        })
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('quantity')
                                        ->label('الكمية')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            self::updateTotal($get, $set);
                                        })
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('price')
                                        ->label('السعر')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function (callable $get, callable $set) {
                                            self::updateTotal($get, $set);
                                        })
                                        ->columnSpan(2),
                                ])
                                ->columns(11)
                                ->live()
                                ->afterStateUpdated(function (callable $get, callable $set) {
                                    self::updateTotal($get, $set);
                                }),
                        ]),

                    // Step 3: Shipping & Review
                    Forms\Components\Wizard\Step::make('الشحن والمراجعة')
                        ->icon('heroicon-o-truck')
                        ->schema([
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\TextInput::make('tracking_number')
                                        ->label('رقم التتبع')
                                        ->default(fn() => 'TRK-' . strtoupper(uniqid()))
                                        ->readOnly(),
                                    Forms\Components\Select::make('shipping_company_id')
                                        ->label('شركة الشحن')
                                        ->relationship('shippingCompany', 'name')
                                        ->default(6)
                                        ->live()
                                        ->afterStateUpdated(fn (Forms\Set $set) => $set('delivery_agent_id', null)),
                                    Forms\Components\Select::make('delivery_agent_id')
                                        ->label('المندوب')
                                        ->relationship('deliveryAgent', 'name', fn ($query, Forms\Get $get) => 
                                            $query->where('shipping_company_id', $get('shipping_company_id'))
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->disabled(fn (Forms\Get $get) => ! $get('shipping_company_id')),
                                    Forms\Components\TextInput::make('shipping_price')
                                        ->label('سعر الشحن')
                                        ->numeric()
                                        ->prefix('EGP')
                                        ->readOnly(),
                                    Forms\Components\TextInput::make('total_amount')
                                        ->label('الإجمالي الكلي')
                                        ->numeric()
                                        ->readOnly()
                                        ->prefix('EGP')
                                        ->dehydrated(),
                                    Forms\Components\Textarea::make('notes')
                                        ->label('ملاحظات إضافية')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    // Helper to calculate total
    public static function updateTotal(callable $get, callable $set)
    {
        $products = $get('products') ?? [];
        $total = 0;
        foreach ($products as $product) {
            $qty = (float) ($product['quantity'] ?? 0);
            $price = (float) ($product['price'] ?? 0);
            $total += $qty * $price;
        }
        $shipping = (float) $get('shipping_price');
        $set('total_amount', $total + $shipping);
    }

    public static function table(Table $table): Table
    {
        // Increase execution time for handling large shipment lists with barcodes
        set_time_limit(120);

        return $table
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
                    ->alignCenter(), // Center the cell content as well

                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('رقم التتبع')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state, Shipment $record) {
                        $code = $record->tracking_number;
                        try {
                            // Ensure Barcode Generator is available or use existing logic
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
                    ->html()
                    ->searchable(['customer_name', 'customer_phone', 'governorate']),
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

                            // Cleaner, Compact Design
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
                    ->html()
                    ->searchable(['products.name']),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('الإجمالي')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\SelectColumn::make('status_id')
                    ->label('الحالة')
                    ->options(\App\Models\ShipmentStatus::pluck('name', 'id')->toArray())
                    ->disablePlaceholderSelection()
                    ->sortable()
                    ->searchable()
                    ->afterStateUpdated(function ($state, Shipment $record, $livewire) {
                         $livewire->dispatch('refreshTable'); 
                    }),
                Tables\Columns\SelectColumn::make('shipping_company_id')
                    ->label('شركة الشحن')
                    ->options(\App\Models\ShippingCompany::pluck('name', 'id')->toArray())
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->afterStateUpdated(function ($state, Shipment $record, $livewire) {
                         $livewire->dispatch('refreshTable'); 
                    }), 
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
                    ->sortable()
                    ->getStateUsing(fn (Shipment $record) => $record->shipping_date ? \Carbon\Carbon::parse($record->shipping_date)->format('Y-m-d') : null)
                    ->rules(['date'])
                    ->toggleable(),
                Tables\Columns\TextInputColumn::make('delivery_date')
                    ->label('تاريخ التسليم')
                    ->type('date')
                    ->sortable()
                    ->getStateUsing(fn (Shipment $record) => $record->delivery_date ? \Carbon\Carbon::parse($record->delivery_date)->format('Y-m-d') : null)
                    ->rules(['date'])
                    ->toggleable(),
                Tables\Columns\TextInputColumn::make('return_date')
                    ->label('تاريخ الارجاع')
                    ->type('date')
                    ->sortable()
                    //->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('Y-m-d') : null)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordClasses(fn (Shipment $record) => match ($record->status->code ?? '') {
                'delivered' => 'tr-success', 
                'returned' => 'tr-danger', 
                'partial_return' => 'tr-warning', 
                'rescheduled' => 'tr-info', 
                default => 'tr-' . ($record->status->color ?? 'gray'),
            })

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('send_to_carrier')
                        ->label('إرسال لشركة الشحن')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Shipment $record) => 
                            $record->shippingCompany?->integration_enabled && 
                            empty($record->external_tracking_number)
                        )
                        ->action(function (Shipment $record) {
                            try {
                                $carrier = CarrierFactory::make($record->shippingCompany);
                                $result = $carrier->createShipment($record);

                                if ($result['success']) {
                                    $record->update([
                                        'external_tracking_number' => $result['tracking_number'],
                                        'external_reference' => $result['reference'],
                                    ]);

                                    Notification::make()
                                        ->title('تم إرسال الشحنة بنجاح')
                                        ->success()
                                        ->body("رقم التتبع الخارجي: {$result['tracking_number']}")
                                        ->send();
                                } else {
                                    throw new \Exception($result['error'] ?? 'خطأ غير معروف');
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('فشل الإرسال')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('track_carrier')
                        ->label('تتبع الشحنة')
                        ->icon('heroicon-o-magnifying-glass')
                        ->color('info')
                        ->visible(fn (Shipment $record) => !empty($record->external_tracking_number))
                        ->action(function (Shipment $record) {
                            try {
                                $carrier = CarrierFactory::make($record->shippingCompany);
                                $status = $carrier->trackShipment($record);

                                Notification::make()
                                    ->title('حالة الشحنة الحالية')
                                    ->info()
                                    ->body("الحالة: " . ($status['status'] ?? 'غير معروف'))
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('فشل التتبع')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('print_invoices')
                        ->label('طباعة فواتير')
                        ->icon('heroicon-o-printer')
                        ->action(function (\Illuminate\Support\Collection $records, $livewire) {
                             $url = route('shipments.print.invoices', ['ids' => $records->pluck('id')->implode(',')]);
                             $livewire->js("window.open('$url', '_blank')");
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('print_table')
                        ->label('طباعة جدول')
                        ->icon('heroicon-o-table-cells')
                        ->action(function (\Illuminate\Support\Collection $records, $livewire) {
                             $url = route('shipments.print.table', ['ids' => $records->pluck('id')->implode(',')]);
                             $livewire->js("window.open('$url', '_blank')");
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('export_excel')
                        ->label('تصدير Excel')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShipmentsExport($records), 'shipments.xlsx');
                        }),
                    Tables\Actions\BulkAction::make('print_thermal')
                        ->label('طباعة بوليصة (4x6)')
                        ->icon('heroicon-o-qr-code')
                        ->color('success')
                        ->action(function (\Illuminate\Support\Collection $records, $livewire) {
                             $url = route('shipments.print.thermal', ['ids' => $records->pluck('id')->implode(',')]);
                             $livewire->js("window.open('$url', '_blank')");
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('status_id')
                            ->label('الحالة')
                            ->options(\App\Models\ShipmentStatus::pluck('name', 'id'))
                            ->multiple(),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('shipping_company_id')
                            ->label('شركة الشحن')
                            ->options(\App\Models\ShippingCompany::pluck('name', 'id'))
                            ->multiple(),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('delivery_agent_id')
                            ->label('المندوب')
                            ->options(\App\Models\DeliveryAgent::pluck('name', 'id'))
                            ->multiple(),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('shipping_date')
                            ->label('تاريخ الشحن'),
                        Tables\Filters\QueryBuilder\Constraints\BooleanConstraint::make('is_printed')
                            ->label('حالة الطباعة'),
                        Tables\Filters\QueryBuilder\Constraints\SelectConstraint::make('product_id')
                            ->label('المنتج')
                            ->options(\App\Models\Product::pluck('name', 'id'))
                            ->relationship('products', 'name'),
                    ])
                    ->constraintPickerColumns(2),
            ], layout: FiltersLayout::AboveContentCollapsible);
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
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view' => Pages\ViewShipment::route('/{record}'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }

}
