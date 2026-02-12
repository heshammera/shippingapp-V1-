<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- 📦 Inventory & Products Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cube class="w-5 h-5 text-primary-600" />
                    <span>إعدادات المخزون والمنتجات</span>
                </div>
            </x-slot>

            <div class="grid gap-2">
                <a href="{{ \App\Filament\Resources\WarehouseResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20">
                        <x-heroicon-o-building-storefront class="w-5 h-5 text-gray-500 group-hover:text-primary-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">المستودعات</h3>
                        <p class="text-xs text-gray-500">إدارة أماكن التخزين والفروع</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\SupplierResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20">
                        <x-heroicon-o-user-group class="w-5 h-5 text-gray-500 group-hover:text-primary-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">الموردين</h3>
                        <p class="text-xs text-gray-500">قائمة الموردين وبياناتهم</p>
                    </div>
                </a>
                
                <a href="{{ \App\Filament\Resources\StockMovementResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 group-hover:text-primary-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">أرشيف الحركات</h3>
                        <p class="text-xs text-gray-500">سجل حركات المخزون بالتفصيل</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\StockTransferResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20">
                        <x-heroicon-o-arrows-right-left class="w-5 h-5 text-gray-500 group-hover:text-primary-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">تحويلات مخزنية</h3>
                        <p class="text-xs text-gray-500">نقل البضائع بين المستودعات</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\PurchaseOrderResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20">
                        <x-heroicon-o-shopping-cart class="w-5 h-5 text-gray-500 group-hover:text-primary-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">أوامر الشراء</h3>
                        <p class="text-xs text-gray-500">إدارة طلبات الشراء من الموردين</p>
                    </div>
                </a>
            </div>
        </x-filament::section>

        {{-- 🚚 Partners Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-truck class="w-5 h-5 text-info-600" />
                    <span>الشركاء والمناديب</span>
                </div>
            </x-slot>

            <div class="grid gap-2">
                <a href="{{ \App\Filament\Resources\DeliveryAgentResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-info-50 dark:group-hover:bg-info-900/20">
                        <x-heroicon-o-users class="w-5 h-5 text-gray-500 group-hover:text-info-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">المناديب</h3>
                        <p class="text-xs text-gray-500">إدارة مناديب التوصيل</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\ShippingCompanyResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-info-50 dark:group-hover:bg-info-900/20">
                        <x-heroicon-o-building-office-2 class="w-5 h-5 text-gray-500 group-hover:text-info-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">شركات الشحن</h3>
                        <p class="text-xs text-gray-500">شركات الشحن الخارجية وتكاملها</p>
                    </div>
                </a>
            </div>
        </x-filament::section>

        {{-- 💰 Financial Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-5 h-5 text-success-600" />
                    <span>الإعدادات المالية والمحاسبية</span>
                </div>
            </x-slot>

            <div class="grid gap-2">
                <a href="{{ \App\Filament\Resources\ChartOfAccountResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-success-50 dark:group-hover:bg-success-900/20">
                        <x-heroicon-o-list-bullet class="w-5 h-5 text-gray-500 group-hover:text-success-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">دليل الحسابات (COA)</h3>
                        <p class="text-xs text-gray-500">الشجرة المحاسبية</p>
                    </div>
                </a>
                
                 <a href="{{ \App\Filament\Resources\JournalEntryResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-success-50 dark:group-hover:bg-success-900/20">
                        <x-heroicon-o-book-open class="w-5 h-5 text-gray-500 group-hover:text-success-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">القيود اليومية</h3>
                        <p class="text-xs text-gray-500">إدارة القيود اليدوية</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\AgentSettlementResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-success-50 dark:group-hover:bg-success-900/20">
                        <x-heroicon-o-calculator class="w-5 h-5 text-gray-500 group-hover:text-success-600" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">تصفيات المناديب</h3>
                        <p class="text-xs text-gray-500">تسوية عهد المناديب</p>
                    </div>
                </a>
            </div>
        </x-filament::section>

        {{-- ⚙️ System Settings --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-gray-600" />
                    <span>إعدادات النظام</span>
                </div>
            </x-slot>

            <div class="grid gap-2">
                 <a href="{{ \App\Filament\Pages\SystemSettings::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-adjustments-horizontal class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">الإعدادات العامة</h3>
                        <p class="text-xs text-gray-500">اسم المتجر، الشعار، والضرائب</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\UserResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-users class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">المستخدمين</h3>
                        <p class="text-xs text-gray-500">إدارة المستخدمين والصلاحيات</p>
                    </div>
                </a>
                
                <a href="{{ \App\Filament\Resources\RoleResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-shield-check class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">الأدوار</h3>
                        <p class="text-xs text-gray-500">توزيع صلاحيات الموظفين</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\IntegrationResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-link class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">الربط والتكامل</h3>
                        <p class="text-xs text-gray-500">WooCommerce, Google Sheets, etc.</p>
                    </div>
                </a>

                <a href="{{ \App\Filament\Resources\ShipmentStatusResource::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-tag class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">حالات الشحن</h3>
                        <p class="text-xs text-gray-500">تخصيص مسميات وألوان الحالات</p>
                    </div>
                </a>
                
                 <a href="{{ \App\Filament\Pages\Backups::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">النسخ الاحتياطي</h3>
                        <p class="text-xs text-gray-500">إدارة قاعدة البيانات</p>
                    </div>
                </a>
                
                 <a href="{{ \App\Filament\Pages\NotificationSettings::getUrl() }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full group-hover:bg-gray-200 dark:group-hover:bg-gray-700">
                        <x-heroicon-o-bell class="w-5 h-5 text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white" />
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-950 dark:text-white">التنبيهات</h3>
                        <p class="text-xs text-gray-500">تخصيص الرسائل والتنبيهات</p>
                    </div>
                </a>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
