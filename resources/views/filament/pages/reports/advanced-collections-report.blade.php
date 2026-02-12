<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Filters Section --}}
        <x-filament::section icon="heroicon-o-funnel" collapsible>
            <x-slot name="heading">
                خيارات وتصفية التقرير
            </x-slot>

            <x-slot name="headerActions">
                <x-filament::button wire:click="resetFilters" color="gray" size="sm" icon="heroicon-o-x-mark">
                    إعادة تعيين
                </x-filament::button>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Date Range --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">الفترة الزمنية</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live.debounce.500ms="dateFrom" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <span class="self-center text-gray-500">-</span>
                        <input type="date" wire:model.live.debounce.500ms="dateTo" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                {{-- Company Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">شركة الشحن</label>
                    <select wire:model.live="shippingCompanyId" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                        <option value="">جميع الشركات</option>
                        @foreach($this->companies as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/10 flex justify-end gap-3">
                <x-filament::button wire:click="exportExcel" color="success" icon="heroicon-o-arrow-down-tray">
                    تصدير Excel
                </x-filament::button>
                <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-printer">
                    طباعة PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-success-50 dark:bg-success-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-banknotes" class="w-8 h-8 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي التحصيلات</p>
                        <h2 class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ number_format($this->kpis['totalCollections'], 2) }} <span class="text-xs font-normal text-gray-400 text-gray-950 dark:text-white">EGP</span></h2>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-calculator" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد العمليات</p>
                        <h2 class="text-2xl font-bold text-gray-950 dark:text-white mt-1">{{ number_format($this->kpis['count']) }}</h2>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-chart-bar" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">متوسط التحصيل</p>
                        <h2 class="text-2xl font-bold text-gray-950 dark:text-white mt-1">{{ number_format($this->kpis['averageAmount'], 2) }} <span class="text-xs font-normal text-gray-400">EGP</span></h2>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Chart Section --}}
            <div class="lg:col-span-3">
                <x-filament::section>
                    <x-slot name="heading">تحليل التحصيلات</x-slot>
                    <div id="collectionsChart" class="w-full h-[400px]" wire:ignore></div>
                </x-filament::section>
            </div>

            {{-- Table Section --}}
            <div class="lg:col-span-3">
                <x-filament::section>
                    <x-slot name="heading">سجل التحصيلات</x-slot>
                    
                    <div class="overflow-x-auto -mx-6">
                        <table class="w-full text-sm text-left rtl:text-right">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 font-medium">#</th>
                                    <th class="px-6 py-4 font-medium">التاريخ</th>
                                    <th class="px-6 py-4 font-medium">شركة الشحن</th>
                                    <th class="px-6 py-4 font-medium">المبلغ</th>
                                    <th class="px-6 py-4 font-medium">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($this->collections as $index => $collection)
                                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4">{{ $this->collections->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $collection->collection_date ? \Carbon\Carbon::parse($collection->collection_date)->format('Y-m-d') : '-' }}</td>
                                    <td class="px-6 py-4">{{ $collection->shippingCompany->name ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-success-600 dark:text-success-400">{{ number_format($collection->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ Str::limit($collection->notes, 50) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <x-filament::icon icon="heroicon-o-inbox" class="w-12 h-12 mb-4 text-gray-300" />
                                            <p class="text-lg">لا توجد تحصيلات</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($this->collections->hasPages())
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            {{ $this->collections->links() }}
                        </div>
                    @endif
                </x-filament::section>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .dark .apexcharts-menu {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #f3f4f6 !important;
        }
        .dark .apexcharts-menu-item:hover {
            background: #374151 !important;
        }
        .dark .apexcharts-toolbar svg {
            fill: #9ca3af !important;
        }
        .dark .apexcharts-tooltip {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #f3f4f6 !important;
        }
        .dark .apexcharts-tooltip-title {
            background: #374151 !important;
            border-bottom: 1px solid #4b5563 !important;
            font-family: inherit !important;
        }
        .dark .apexcharts-text {
            fill: #9ca3af !important;
        }
        .dark .apexcharts-gridline {
            stroke: #374151 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
    document.addEventListener('livewire:initialized', () => {
        renderChart();
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => setTimeout(renderChart, 100));
        });
    });

    function renderChart() {
        const labels = @json($this->chartData['labels'] ?? []);
        const values = @json($this->chartData['values'] ?? []);
        
        if (!document.querySelector("#collectionsChart")) return;
        document.querySelector("#collectionsChart").innerHTML = '';

        if (labels.length === 0) return;

        new ApexCharts(document.querySelector("#collectionsChart"), {
            chart: { 
                type: 'bar', // Using Bar chart for collections as it suits daily totals better
                height: 400, 
                fontFamily: 'inherit', 
                background: 'transparent', 
                toolbar: { 
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                zoom: {
                    enabled: true
                }
            },
            series: [{ name: 'التحصيلات', data: values }],
            xaxis: { categories: labels, labels: { style: { colors: '#9ca3af' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#9ca3af' }, formatter: (val) => val.toFixed(2) } },
            colors: ['#10b981'], // Success green for money
            fill: { opacity: 0.9 },
            dataLabels: { enabled: false },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4, xaxis: { lines: { show: false } } },
            tooltip: { theme: 'dark' }
        }).render();
    }
    </script>
    @endpush
</x-filament-panels::page>
