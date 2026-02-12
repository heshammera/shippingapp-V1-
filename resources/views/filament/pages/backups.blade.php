<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        
        {{-- Card Container --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            
            @if(count($backups) > 0)
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="py-3 px-6">اسم الملف</th>
                                <th scope="col" class="py-3 px-6">الحجم</th>
                                <th scope="col" class="py-3 px-6">تاريخ الإنشاء</th>
                                <th scope="col" class="py-3 px-6 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-document-currency-dollar class="w-5 h-5 text-gray-400"/>
                                            <span dir="ltr">{{ $backup['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        {{ $this->formatBytes($backup['size']) }}
                                    </td>
                                    <td class="py-4 px-6" dir="ltr">
                                        {{ date('Y-m-d H:i:s', $backup['date']) }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            
                                            {{-- Download --}}
                                            <button wire:click="downloadBackup('{{ $backup['name'] }}')" 
                                                    class="text-primary-600 cursor-pointer hover:text-primary-800 dark:text-primary-500 dark:hover:text-primary-400 font-medium text-sm px-2 py-1">
                                                تحميل
                                            </button>

                                            {{-- Restore --}}
                                            <button type="button"
                                                    wire:click="restoreBackup('{{ $backup['name'] }}')"
                                                    wire:confirm="هل أنت متأكد أنك تريد استعادة هذه النسخة؟ سيتم فقدان أي بيانات تم تغييرها بعد تاريخ هذه النسخة."
                                                    class="text-warning-600 cursor-pointer hover:text-warning-800 dark:text-warning-500 dark:hover:text-warning-400 font-medium text-sm px-2 py-1">
                                                استعادة
                                            </button>

                                            {{-- Delete --}}
                                            <button type="button"
                                                    wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                    wire:confirm="هل أنت متأكد من حذف هذه النسخة الاحتياطية نهائياً؟"
                                                    class="text-danger-600 cursor-pointer hover:text-danger-800 dark:text-danger-500 dark:hover:text-danger-400 font-medium text-sm px-2 py-1">
                                                حذف
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-archive-box-x-mark class="w-16 h-16 mb-4 opacity-50"/>
                    <p class="text-lg font-medium">لا توجد نسخ احتياطية حالياً</p>
                    <p class="text-sm">قم بإنشاء نسخة احتياطية جديدة باستخدام الزر في الأعلى.</p>
                </div>
            @endif

        </div>

    </div>
</x-filament-panels::page>
