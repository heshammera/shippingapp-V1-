<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button wire:click="generateReports">
                تحديث التقارير
            </x-filament::button>
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Income Statement -->
        <x-filament::section>
            <x-slot name="heading">
                قائمة الدخل (Income Statement)
            </x-slot>
            <x-slot name="description">
                عن الفترة من {{ $incomeStatement['date_range']['start'] }} إلى {{ $incomeStatement['date_range']['end'] }}
            </x-slot>

            <div class="space-y-4">
                <!-- Revenue -->
                <div>
                    <h3 class="font-bold text-lg text-success-600 border-b pb-1">الإيرادات</h3>
                    @foreach($incomeStatement['revenues'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 font-bold border-t mt-1">
                        <span>إجمالي الإيرادات</span>
                        <span>{{ number_format($incomeStatement['total_revenue'], 2) }}</span>
                    </div>
                </div>

                <!-- COGS -->
                <div>
                    <h3 class="font-bold text-lg text-danger-600 border-b pb-1">تكلفة المبيعات</h3>
                    @foreach($incomeStatement['cogs'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 font-bold border-t mt-1">
                        <span>إجمالي التكلفة</span>
                        <span class="text-danger-600">({{ number_format($incomeStatement['total_cogs'], 2) }})</span>
                    </div>
                </div>

                <!-- Gross Profit -->
                <div class="flex justify-between py-3 font-extrabold text-xl bg-gray-50 dark:bg-gray-800 px-2 rounded">
                    <span>مجمل الربح</span>
                    <span>{{ number_format($incomeStatement['gross_profit'], 2) }}</span>
                </div>

                <!-- Expenses -->
                <div>
                    <h3 class="font-bold text-lg text-warning-600 border-b pb-1">المصروفات التشغيلية</h3>
                    @foreach($incomeStatement['expenses'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 font-bold border-t mt-1">
                        <span>إجمالي المصروفات</span>
                        <span class="text-danger-600">({{ number_format($incomeStatement['total_expenses'], 2) }})</span>
                    </div>
                </div>

                <!-- Net Income -->
                <div class="flex justify-between py-4 font-extrabold text-2xl bg-primary-50 dark:bg-primary-900/20 px-4 rounded-lg border border-primary-100">
                    <span>صافي الدخل</span>
                    <span class="{{ $incomeStatement['net_income'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                        {{ number_format($incomeStatement['net_income'], 2) }}
                    </span>
                </div>
            </div>
        </x-filament::section>

        <!-- Balance Sheet -->
        <x-filament::section>
            <x-slot name="heading">
                الميزانية العمومية (Balance Sheet)
            </x-slot>
            <x-slot name="description">
                كما في {{ $balanceSheet['date'] }}
            </x-slot>

            <div class="space-y-6">
                <!-- Assets -->
                <div>
                    <h3 class="font-bold text-lg text-primary-600 border-b pb-1 mb-2">الأصول (Assets)</h3>
                    @foreach($balanceSheet['assets'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 font-bold border-t mt-1 bg-gray-50 dark:bg-gray-800 px-2">
                        <span>إجمالي الأصول</span>
                        <span>{{ number_format($balanceSheet['total_assets'], 2) }}</span>
                    </div>
                </div>

                <!-- Liabilities -->
                <div>
                    <h3 class="font-bold text-lg text-danger-600 border-b pb-1 mb-2">الخصوم (Liabilities)</h3>
                    @foreach($balanceSheet['liabilities'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between py-2 font-bold border-t mt-1">
                        <span>إجمالي الخصوم</span>
                        <span>{{ number_format($balanceSheet['total_liabilities'], 2) }}</span>
                    </div>
                </div>

                <!-- Equity -->
                <div>
                    <h3 class="font-bold text-lg text-info-600 border-b pb-1 mb-2">حقوق الملكية (Equity)</h3>
                    @foreach($balanceSheet['equity'] as $account)
                        <div class="flex justify-between py-1 text-sm">
                            <span>{{ $account['name'] }}</span>
                            <span>{{ number_format($account['balance'], 2) }}</span>
                        </div>
                    @endforeach
                    
                    <!-- Net Income for Period -->
                    <div class="flex justify-between py-1 text-sm font-medium text-gray-600 italic">
                        <span>صافي دخل الفترة الحالية</span>
                        <span>{{ number_format($balanceSheet['current_net_income'], 2) }}</span>
                    </div>

                    <div class="flex justify-between py-2 font-bold border-t mt-1">
                        <span>إجمالي حقوق الملكية</span>
                        <span>{{ number_format($balanceSheet['total_equity'] + $balanceSheet['current_net_income'], 2) }}</span>
                    </div>
                </div>

                <!-- Total Equities & Liabilities -->
                <div class="flex justify-between py-3 font-extrabold text-xl bg-gray-50 dark:bg-gray-800 px-2 rounded border-t-2 border-gray-200">
                    <span>إجمالي الخصوم وحقوق الملكية</span>
                    <span>{{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }}</span>
                </div>

                <!-- Balance Check -->
                @if(!$balanceSheet['is_balanced'])
                    <div class="p-4 bg-danger-50 text-danger-700 rounded-lg flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6"/>
                        <span class="font-bold">تنبيه: الميزانية غير متوازنة! الفرق: {{ number_format($balanceSheet['total_assets'] - $balanceSheet['total_liabilities_and_equity'], 2) }}</span>
                    </div>
                @else
                    <div class="p-4 bg-success-50 text-success-700 rounded-lg flex items-center justify-center gap-2">
                         <x-heroicon-o-check-circle class="w-6 h-6"/>
                         <span class="font-bold">الميزانية متوازنة ✅</span>
                    </div>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
