<x-filament-panels::page>
    @if (method_exists($this, 'filters'))
        <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            {{ $this->filters }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Stats Overview (Full Width) --}}
        <div class="col-span-1 lg:col-span-3">
            @livewire(\App\Filament\Widgets\FinanceStatsWidget::class)
        </div>

        {{-- Financial Chart (2/3 Width) --}}
        <div class="col-span-1 lg:col-span-2">
            <div class="h-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-2">
                @livewire(\App\Filament\Widgets\FinancialChart::class)
            </div>
        </div>

        {{-- Shipping Balances (1/3 Width) --}}
        <div class="col-span-1 lg:col-span-1">
            <div class="h-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @livewire(\App\Filament\Widgets\ShippingBalancesWidget::class)
            </div>
        </div>

        {{-- Recent Collections and Expenses (Split Half/Half) --}}
        <div class="col-span-1 lg:col-span-3 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @livewire(\App\Filament\Widgets\LatestCollectionsWidget::class)
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @livewire(\App\Filament\Widgets\RecentExpensesWidget::class)
            </div>
        </div>
    </div>
</x-filament-panels::page>
