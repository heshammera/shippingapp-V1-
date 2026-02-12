<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-bold mb-2">أهلاً بك، {{ auth()->user()->name }} 👋</h2>
            <p class="text-sm text-gray-500">لديك اليوم مهام بانتظار التوصيل. ابدأ بمتابعة شحناتك من الجدول أدناه.</p>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
