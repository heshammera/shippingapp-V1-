<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Quick Questions --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-bolt" class="w-5 h-5" />
                    أسئلة سريعة
                </div>
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <x-filament::button 
                    wire:click="askQuickQuestion('كم شحنة تم تسليمها اليوم؟')"
                    color="gray"
                    size="sm"
                >
                    📦 شحنات اليوم
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="askQuickQuestion('ما هو صافي الربح هذا الشهر؟')"
                    color="gray"
                    size="sm"
                >
                    💰 الربح الشهري
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="askQuickQuestion('ما هي الشحنات المعلقة؟')"
                    color="gray"
                    size="sm"
                >
                    ⏳ الشحنات المعلقة
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="askQuickQuestion('أعطني ملخص أداء هذا الأسبوع')"
                    color="gray"
                    size="sm"
                >
                    📊 ملخص الأسبوع
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="askQuickQuestion('ما هي التوصيات لتحسين الأداء؟')"
                    color="gray"
                    size="sm"
                >
                    💡 توصيات
                </x-filament::button>
                
                <x-filament::button 
                    wire:click="askQuickQuestion('من هو أفضل مندوب هذا الشهر؟')"
                    color="gray"
                    size="sm"
                >
                    🏆 أفضل مندوب
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Main Chat Interface --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-5 h-5" />
                    محادثة مع المساعد الذكي
                </div>
            </x-slot>
            
            <x-slot name="headerActions">
                @if(count($chatHistory) > 0)
                    <x-filament::button 
                        wire:click="clearHistory" 
                        color="danger" 
                        size="sm"
                        icon="heroicon-o-trash"
                    >
                        مسح المحادثات
                    </x-filament::button>
                @endif
            </x-slot>
            
            <form wire:submit="ask" class="space-y-4">
                <div class="relative">
                    <x-filament::input.wrapper>
                        <textarea
                            wire:model="question"
                            placeholder="اسأل المساعد الذكي أي شيء... مثال: كم شحنة تم تسليمها اليوم؟"
                            rows="3"
                            class="block w-full rounded-lg border-0 bg-white dark:bg-white/5 py-3 px-4 text-gray-950 dark:text-white shadow-sm ring-1 ring-inset ring-gray-950/10 dark:ring-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
                            @if($isLoading) disabled @endif
                        ></textarea>
                    </x-filament::input.wrapper>
                </div>
                
                <div class="flex justify-end">
                    <x-filament::button 
                        type="submit"
                        icon="heroicon-o-paper-airplane"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>إرسال</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري التفكير...
                        </span>
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Chat History --}}
        @if(count($chatHistory) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    المحادثات السابقة
                </x-slot>
                
                <div class="space-y-4">
                    @foreach(array_reverse($chatHistory) as $chat)
                        <div class="space-y-3">
                            {{-- User Question --}}
                            <div class="flex justify-end">
                                <div class="max-w-3xl bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-primary-600 dark:text-primary-400 mb-1">أنت</p>
                                            <p class="text-gray-950 dark:text-white">{{ $chat['question'] }}</p>
                                        </div>
                                        <x-filament::icon icon="heroicon-o-user-circle" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($chat['timestamp'])->diffForHumans() }}</p>
                                </div>
                            </div>
                            
                            {{-- AI Answer --}}
                            <div class="flex justify-start">
                                <div class="max-w-3xl bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                    <div class="flex items-start gap-3">
                                        <x-filament::icon icon="heroicon-o-sparkles" class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-warning-600 dark:text-warning-400 mb-1">المساعد الذكي</p>
                                            <div class="prose dark:prose-invert prose-sm max-w-none">
                                                {!! \Illuminate\Support\Str::markdown($chat['answer']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="border-gray-200 dark:border-gray-700">
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- Help Section --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-question-mark-circle" class="w-5 h-5" />
                    كيف أستخدم المساعد الذكي؟
                </div>
            </x-slot>
            
            <div class="prose dark:prose-invert prose-sm max-w-none">
                <p>يمكنك سؤال المساعد الذكي عن أي شيء يتعلق بنظامك:</p>
                
                <ul>
                    <li><strong>إحصائيات:</strong> "كم شحنة تم تسليمها اليوم؟"</li>
                    <li><strong>تحليلات:</strong> "حلل أداء هذا الشهر"</li>
                    <li><strong>مقارنات:</strong> "قارن أداء الشهر الحالي بالشهر الماضي"</li>
                    <li><strong>توصيات:</strong> "كيف أحسن معدل التسليم؟"</li>
                    <li><strong>بيانات محددة:</strong> "من هو أفضل مندوب؟"</li>
                </ul>
                
                <div class="mt-4 p-4 bg-info-50 dark:bg-info-900/20 rounded-lg">
                    <p class="text-info-600 dark:text-info-400 font-semibold mb-2">💡 نصيحة:</p>
                    <p class="text-info-600 dark:text-info-400">كلما كان سؤالك أكثر تحديداً، كلما كانت الإجابة أكثر دقة!</p>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
