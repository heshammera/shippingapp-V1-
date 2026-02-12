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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Date Range --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">الفترة الزمنية</label>
                    <div class="flex gap-2">
                        <div class="relative w-full">
                            <input type="date" wire:model.live.debounce.500ms="dateFrom" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <div wire:loading wire:target="dateFrom" class="absolute inset-y-0 left-2 flex items-center">
                                <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="self-center text-gray-500">-</span>
                        <div class="relative w-full">
                            <input type="date" wire:model.live.debounce.500ms="dateTo" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <div wire:loading wire:target="dateTo" class="absolute inset-y-0 left-2 flex items-center">
                                <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Company Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">شركة الشحن</label>
                    <div class="relative">
                        <select wire:model.live="shippingCompanyId" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="">جميع الشركات</option>
                            @foreach($this->companies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="shippingCompanyId" class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                            <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">حالة الشحنة</label>
                    <div class="relative">
                        <select wire:model.live="statusId" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="">جميع الحالات</option>
                            @foreach($this->statuses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="statusId" class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                            <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Agent Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-6 text-gray-950 dark:text-white">المندوب</label>
                    <div class="relative">
                        <select wire:model.live="deliveryAgentId" class="block w-full rounded-lg border-0 bg-white dark:bg-gray-900 py-1.5 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6">
                            <option value="">جميع المناديب</option>
                            @foreach($this->agents as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <div wire:loading wire:target="deliveryAgentId" class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                            <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Global Loading Indicator --}}
            <div wire:loading.delay wire:target="dateFrom,dateTo,shippingCompanyId,statusId,deliveryAgentId" class="mt-4 p-3 bg-primary-50 dark:bg-primary-900/10 rounded-lg flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-primary-600 dark:text-primary-400">جاري تحديث البيانات...</span>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-white/10 flex justify-end gap-3">
                <x-filament::button wire:click="exportExcel" color="success" icon="heroicon-o-arrow-down-tray" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="exportExcel">تصدير Excel</span>
                    <span wire:loading wire:target="exportExcel" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري التصدير...
                    </span>
                </x-filament::button>
                <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-printer" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="exportPdf">طباعة PDF</span>
                    <span wire:loading wire:target="exportPdf" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.3730 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري الإنشاء...
                    </span>
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" wire:loading.class="opacity-50 pointer-events-none" wire:target="dateFrom,dateTo,shippingCompanyId,statusId,deliveryAgentId">
            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-cube" class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الشحنات</p>
                        <h2 class="text-2xl font-bold text-gray-950 dark:text-white mt-1">{{ number_format($this->kpis['totalShipments']) }}</h2>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-banknotes" class="w-8 h-8 text-danger-600 dark:text-danger-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">التكلفة الكلية</p>
                        <h2 class="text-2xl font-bold text-gray-950 dark:text-white mt-1">{{ number_format($this->kpis['totalCost']) }} <span class="text-xs font-normal text-gray-400">EGP</span></h2>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-warning-50 dark:bg-warning-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-currency-dollar" class="w-8 h-8 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المبيعات</p>
                        <h2 class="text-2xl font-bold text-gray-950 dark:text-white mt-1">{{ number_format($this->kpis['totalSelling']) }} <span class="text-xs font-normal text-gray-400">EGP</span></h2>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="p-6">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-success-50 dark:bg-success-900/10 rounded-xl">
                        <x-filament::icon icon="heroicon-o-arrow-trending-up" class="w-8 h-8 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي الأرباح</p>
                        <h2 class="text-2xl font-bold text-success-600 dark:text-success-400 mt-1">{{ number_format($this->kpis['netProfit']) }} <span class="text-xs font-normal text-gray-400 text-gray-950 dark:text-white">EGP</span></h2>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Chart Section --}}
            <div class="lg:col-span-3">
                <x-filament::section>
                    <x-slot name="heading">تحليل نمو الشحنات</x-slot>
                    <div id="shipmentsChart" class="w-full h-[400px]" wire:ignore></div>
                </x-filament::section>
            </div>

            {{-- Table Section --}}
            <div class="lg:col-span-3">
                <x-filament::section>
                    <x-slot name="heading">سجل العمليات التفصيلي</x-slot>
                    
                    <div class="overflow-x-auto -mx-6">
                        <table class="w-full text-sm text-left rtl:text-right">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 font-medium">#</th>
                                    <th class="px-6 py-4 font-medium">رقم التتبع</th>
                                    <th class="px-6 py-4 font-medium">العميل</th>
                                    <th class="px-6 py-4 font-medium">شركة الشحن</th>
                                    <th class="px-6 py-4 font-medium">الحالة</th>
                                    <th class="px-6 py-4 font-medium">التكلفة</th>
                                    <th class="px-6 py-4 font-medium">البيع</th>
                                    <th class="px-6 py-4 font-medium">الربح</th>
                                    <th class="px-6 py-4 font-medium">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($this->shipments as $index => $shipment)
                                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4">{{ $this->shipments->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 class="font-medium text-primary-600">{{ $shipment->tracking_number }}</td>
                                    <td class="px-6 py-4">{{ $shipment->customer_name }}</td>
                                    <td class="px-6 py-4">{{ $shipment->shippingCompany->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if($shipment->status)
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                 style="background-color: {{ $shipment->status->color }}20; color: {{ $shipment->status->color }}">
                                                {{ $shipment->status->name }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ number_format($shipment->cost_price, 2) }}</td>
                                    <td class="px-6 py-4">{{ number_format($shipment->selling_price, 2) }}</td>
                                    <td class="px-6 py-4 font-medium {{ ($shipment->selling_price - $shipment->cost_price) >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                        {{ number_format($shipment->selling_price - $shipment->cost_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $shipment->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <x-filament::icon icon="heroicon-o-inbox" class="w-12 h-12 mb-4 text-gray-300" />
                                            <p class="text-lg">لا توجد بيانات للعرض</p>
                                            <p class="text-sm">جرب تغيير خيارات التصفية أعلاه</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($this->shipments->hasPages())
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            {{ $this->shipments->links() }}
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
        
        if (!document.querySelector("#shipmentsChart")) return;
        document.querySelector("#shipmentsChart").innerHTML = '';

        if (labels.length === 0) return;

        new ApexCharts(document.querySelector("#shipmentsChart"), {
            chart: { 
                type: 'area', 
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
                    enabled: true,
                    type: 'x',  
                    autoScaleYaxis: true
                }
            },
            series: [{ name: 'الشحنات', data: values }],
            xaxis: { categories: labels, labels: { style: { colors: '#9ca3af' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#9ca3af' }, formatter: (val) => Math.round(val) } },
            colors: ['#3b82f6'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4, xaxis: { lines: { show: false } } },
            tooltip: { theme: 'dark' }
        }).render();
    }
    </script>
    @endpush
</x-filament-panels::page>
