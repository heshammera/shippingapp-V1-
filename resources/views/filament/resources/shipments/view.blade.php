<x-filament::page>
    @php
        $record = $this->record;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left Column: Shipment Info -->
        <div class="col-span-2 space-y-6">
            
            <!-- Shipment Info Card -->
            <x-filament::section>
                <x-slot name="heading">معلومات الشحنة</x-slot>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right">
                        <tbody>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500 w-1/4">رقم التتبع</th>
                                <td class="py-2">
                                    <div class="flex flex-col items-center">
                                        @php
                                            $code = $record->tracking_number;
                                            try {
                                                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                                                $svg = $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 40);
                                                $svg = str_replace('<svg', '<svg preserveAspectRatio="none"', $svg);
                                                $base64 = base64_encode($svg);
                                                $barcodeSrc = "data:image/svg+xml;base64,{$base64}";
                                            } catch (\Throwable $e) { $barcodeSrc = ""; }
                                        @endphp
                                        <img src="{{ $barcodeSrc }}" style="width: 200px; height: 40px;" class="mb-1">
                                        <span class="font-mono font-bold">{{ $code }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500">العميل</th>
                                <td class="py-2 font-bold">{{ $record->customer_name }}</td>
                            </tr>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500">الهاتف</th>
                                <td class="py-2 font-mono">{{ $record->customer_phone }}</td>
                            </tr>
                            @if($record->alternate_phone)
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500">هاتف بديل</th>
                                <td class="py-2 font-mono">{{ $record->alternate_phone }}</td>
                            </tr>
                            @endif
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500">العنوان</th>
                                <td class="py-2">{{ $record->customer_address }}</td>
                            </tr>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 font-medium text-gray-500">المحافظة</th>
                                <td class="py-2">{{ $record->governorate }}</td>
                            </tr>
                            <tr>
                                <th class="py-2 font-medium text-gray-500">شركة الشحن</th>
                                <td class="py-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $record->shippingCompany->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            <!-- Products Details -->
            @if($record->products->count())
            <x-filament::section>
                <x-slot name="heading">تفاصيل المنتجات</x-slot>

                <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                    <table class="w-full text-sm text-center">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-2">PRD</th>
                                <th class="px-4 py-2">ل</th>
                                <th class="px-4 py-2">م</th>
                                <th class="px-4 py-2">ك</th>
                                <th class="px-4 py-2">TOT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($record->products as $product)
                            <tr>
                                <td class="px-4 py-2">{{ $product->name }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $colorMap = [
                                            'بيج' => '#f5f5dc', 'أسود' => '#000000', 'اسود' => '#000000', 'ابيض' => '#ffffff',
                                            'ازرق بيبسي' => '#004b9a', 'بترولي' => '#004b9a', 'نبيتي' => '#722f37',
                                            'زيتي' => '#708238', 'موف' => '#4b0082', 'منت جرين' => '#98ff98',
                                            'رصاصي' => '#808080', 'فوشيا' => '#ff00ff', 'بينك' => '#ffc0cb', 'بلو' => '#0000ff',
                                        ];
                                        $rawColor = trim(mb_strtolower($product->pivot->color));
                                        $boxColor = '#6c757d'; 
                                        foreach ($colorMap as $name => $hex) {
                                            if (mb_strpos($rawColor, mb_strtolower($name)) !== false) {
                                                $boxColor = $hex;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="w-4 h-4 rounded border border-gray-300 shadow-sm" style="background-color: {{ $boxColor }};"></span>
                                        <span>{{ $product->pivot->color }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2">{{ $product->pivot->size }}</td>
                                <td class="px-4 py-2 font-bold">{{ $product->pivot->quantity }}</td>
                                <td class="px-4 py-2">{{ number_format($product->pivot->price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded border dark:border-gray-700 flex justify-between items-center">
                        <span class="font-bold">سعر الشحن:</span>
                        <span class="text-primary-600 font-bold text-lg">{{ number_format($record->shipping_price) }} ج.م</span>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded border border-green-200 dark:border-green-800 flex justify-between items-center">
                        <span class="font-bold">الإجمالي الكلي:</span>
                        <span class="text-success-600 font-bold text-xl">{{ number_format($record->total_amount) }} ج.م</span>
                    </div>
                </div>
            </x-filament::section>
            @endif

        </div>

        <!-- Right Column: Timeline & Notes -->
        <div class="space-y-6">
            
            <!-- Dates -->
            <x-filament::section>
                <x-slot name="heading">التواريخ</x-slot>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-500">تاريخ الشحن</span>
                        <span class="font-mono font-bold">{{ $record->shipping_date ? date('Y-m-d', strtotime($record->shipping_date)) : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-500">تاريخ التسليم</span>
                        <span class="font-mono font-bold">{{ $record->delivery_date ? date('Y-m-d', strtotime($record->delivery_date)) : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">تاريخ الإرجاع</span>
                        <span class="font-mono font-bold">{{ $record->return_date ? date('Y-m-d', strtotime($record->return_date)) : '-' }}</span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Notes -->
            <x-filament::section>
                <x-slot name="heading">الملاحظات</x-slot>
                <div class="space-y-4 text-sm">
                    <div>
                        <h5 class="font-bold mb-1 text-gray-700 dark:text-gray-300">ملاحظات عامة</h5>
                        <p class="bg-gray-50 dark:bg-gray-800 p-2 rounded">{{ $record->notes ?? 'لا توجد ملاحظات' }}</p>
                    </div>
                    <div>
                        <h5 class="font-bold mb-1 text-gray-700 dark:text-gray-300">ملاحظات المندوب</h5>
                        <p class="bg-gray-50 dark:bg-gray-800 p-2 rounded">{{ $record->agent_notes ?? 'لا توجد ملاحظات' }}</p>
                    </div>
                </div>
            </x-filament::section>

            <!-- Timeline -->
            <x-filament::section>
                <x-slot name="heading">سجل التغييرات</x-slot>
                <ol class="relative border-r border-gray-200 dark:border-gray-700 mr-2">                  
                    <li class="mb-6 ml-4">
                        <div class="absolute w-3 h-3 bg-primary-600 rounded-full mt-1.5 -mr-1.5 border border-white dark:border-gray-900"></div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ $record->created_at->format('Y-m-d H:i') }}</time>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">إنشاء الشحنة</h3>
                    </li>
                    @if($record->shipping_date)
                    <li class="mb-6 ml-4">
                        <div class="absolute w-3 h-3 bg-blue-500 rounded-full mt-1.5 -mr-1.5 border border-white dark:border-gray-900"></div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ date('Y-m-d', strtotime($record->shipping_date)) }}</time>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">تم الشحن</h3>
                    </li>
                    @endif
                    @if($record->delivery_date)
                    <li class="mb-6 ml-4">
                        <div class="absolute w-3 h-3 bg-green-500 rounded-full mt-1.5 -mr-1.5 border border-white dark:border-gray-900"></div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ date('Y-m-d', strtotime($record->delivery_date)) }}</time>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">تم التسليم</h3>
                    </li>
                    @endif
                     <li class="mb-6 ml-4">
                        <div class="absolute w-3 h-3 bg-gray-400 rounded-full mt-1.5 -mr-1.5 border border-white dark:border-gray-900"></div>
                        <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ $record->updated_at->format('Y-m-d H:i') }}</time>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">آخر تحديث</h3>
                    </li>
                </ol>
            </x-filament::section>

        </div>
    </div>
</x-filament::page>
