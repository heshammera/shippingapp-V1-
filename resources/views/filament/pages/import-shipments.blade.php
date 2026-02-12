<x-filament::page>
    <div class="space-y-6">
        
        <!-- Instructions Section -->
        <x-filament::section>
            <x-slot name="heading">
                إرشادات هامة قبل الاستيراد
            </x-slot>
            <x-slot name="description">
                يرجى التأكد من مطابقة ملف Excel للمعايير التالية لضمان نجاح عملية الاستيراد.
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Steps -->
                <div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">1</div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">صيغة الملف</p>
                            <p>يجب أن يكون الملف بصيغة <code>.xlsx</code> أو <code>.csv</code> فقط.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">2</div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">عناوين الأعمدة</p>
                            <p>يجب أن يحتوي الصف الأول في الملف على أسماء الأعمدة باللغة الإنجليزية كما هو موضح في الجدول أدناه.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs">3</div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">البيانات الإلزامية</p>
                            <p>حقل <code>tracking_number</code> هو الحقل الوحيد الإلزامي لإنشاء الشحنة. باقي الحقول اختيارية.</p>
                        </div>
                    </div>
                </div>

                <!-- Template Download (Placeholder/Button) -->
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                    <x-heroicon-o-document-text class="w-10 h-10 text-gray-400 mb-2" />
                    <div class="mt-2">
                         <x-filament::button color="gray" size="sm" tag="a" href="{{ route('shipments.import.template') }}" target="_blank">
                            تحميل النموذج
                        </x-filament::button>
                    </div>
                </div>
            </div>

            <!-- Columns Table -->
            <div class="mt-6 overflow-x-auto border rounded-lg dark:border-gray-700">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 uppercase font-bold">
                        <tr>
                            <th class="px-4 py-3 border-b dark:border-gray-700">اسم العمود (Header)</th>
                            <th class="px-4 py-3 border-b dark:border-gray-700">الوصف</th>
                            <th class="px-4 py-3 border-b dark:border-gray-700">مثال</th>
                            <th class="px-4 py-3 border-b dark:border-gray-700">إلزامي؟</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">tracking_number</td>
                            <td class="px-4 py-3">رقم البوليصة / التتبع</td>
                            <td class="px-4 py-3 text-gray-500">TRK-123456</td>
                            <td class="px-4 py-3 text-success-600 font-bold">نعم</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">customer_name</td>
                            <td class="px-4 py-3">اسم العميل</td>
                            <td class="px-4 py-3 text-gray-500">أحمد محمد</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">customer_phone</td>
                            <td class="px-4 py-3">رقم الهاتف</td>
                            <td class="px-4 py-3 text-gray-500">01012345678</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">customer_address</td>
                            <td class="px-4 py-3">العنوان بالتفصيل</td>
                            <td class="px-4 py-3 text-gray-500">القاهرة - مدينة نصر</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                         <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">product_name</td>
                            <td class="px-4 py-3">اسم المنتج</td>
                            <td class="px-4 py-3 text-gray-500">تيشيرت صيفي</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">quantity</td>
                            <td class="px-4 py-3">الكمية</td>
                            <td class="px-4 py-3 text-gray-500">2</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-mono text-primary-600">selling_price</td>
                            <td class="px-4 py-3">سعر البيع للقطعة</td>
                            <td class="px-4 py-3 text-gray-500">150</td>
                            <td class="px-4 py-3 text-gray-400">لا</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Use Form Component -->
        <x-filament::section>
            <x-slot name="heading">
                رفع الملف
            </x-slot>
            
            <form wire:submit.prevent="import">
                {{ $this->form }}
                
                <div class="mt-4 flex justify-end">
                    <x-filament::button type="submit" icon="heroicon-o-arrow-up-tray">
                        بدء استيراد الشحنات
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

    </div>
</x-filament::page>
