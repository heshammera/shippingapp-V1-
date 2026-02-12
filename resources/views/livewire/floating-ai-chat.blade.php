<div 
    x-data="{ 
        open: false,
        isTyping: false,
        toggle() { 
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },
        scrollToBottom() {
            const el = document.getElementById('chat-messages-container');
            if (el) el.scrollTop = el.scrollHeight;
        }
    }"
    style="position: fixed; bottom: 20px; left: 20px; z-index: 2147483647; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"
>
    <!-- Chat Window -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        style="
            width: 400px; 
            height: 600px; 
            max-width: 90vw; 
            max-height: 80vh; 
            background-color: white; 
            border-radius: 16px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            margin-bottom: 16px;
            border: 1px solid rgba(0,0,0,0.05);
        "
    >
        <!-- Header: Professional Support Agent Style -->
        <div class="bg-slate-900 p-5 flex items-center justify-between shadow-sm shrink-0">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-full border-2 border-white/20 overflow-hidden bg-slate-800">
                        <img src="https://ui-avatars.com/api/?name=Sarah+Support&background=3b82f6&color=fff" alt="Support" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg leading-tight">سارة من الدعم</h3>
                    <p class="text-slate-400 text-xs font-medium">نرد عادةً خلال لحظات</p>
                </div>
            </div>
            <button @click="toggle()" class="text-slate-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages Body -->
        <!-- Messages Body -->
        <div 
            id="chat-messages-container"
            class="flex-1 flex flex-col overflow-y-auto p-5 bg-slate-50 space-y-6 scroll-smooth"
        >
            <!-- Welcome Message (Empty State) -->
            @if(count($messages) == 0)
                <div class="flex flex-col items-center justify-center h-full text-center space-y-4 opacity-0 animate-[fadeIn_0.5s_ease-out_forwards] mt-auto mb-auto">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 mb-2">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-slate-900 font-bold text-lg">مرحباً بك! 👋</h4>
                        <p class="text-slate-500 text-sm max-w-[200px] mx-auto mt-1">أنا هنا لمساعدتك في تحليل البيانات وإدارة شحناتك.</p>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 mt-4">
                        <button wire:click="$set('question', 'ما هي أرباح اليوم؟'); sendMessage()" class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-full hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">💰 أرباح اليوم</button>
                        <button wire:click="$set('question', 'تقرير الشحنات'); sendMessage()" class="text-xs bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-full hover:border-blue-500 hover:text-blue-600 transition-colors shadow-sm">📊 تقرير الشحنات</button>
                    </div>
                </div>
            @endif

            <!-- Messages Loop -->
            @foreach($messages as $index => $msg)
                @php $isUser = $msg['type'] === 'user'; @endphp
                <div class="flex w-full {{ $isUser ? 'flex-row-reverse' : 'flex-row' }} gap-3 group {{ $loop->first ? 'mt-auto' : '' }}">
                    <!-- Avatar -->
                    <div class="flex-shrink-0 w-8 h-8 rounded-full overflow-hidden self-end mb-1 shadow-sm ring-1 ring-black/5 {{ $isUser ? 'bg-slate-200' : 'bg-blue-600' }}">
                         @if($isUser)
                            <svg class="w-full h-full p-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                         @else
                            <img src="https://ui-avatars.com/api/?name=Sarah+Support&background=3b82f6&color=fff" alt="AI" class="w-full h-full object-cover">
                         @endif
                    </div>

                    <!-- Bubble -->
                    <div class="flex flex-col max-w-[80%] {{ $isUser ? 'items-end' : 'items-start' }}">
                        <div class="
                            relative px-4 py-3 text-sm shadow-sm
                            {{ $isUser 
                                ? 'bg-blue-600 text-white rounded-2xl rounded-br-sm' 
                                : 'bg-white text-slate-800 rounded-2xl rounded-bl-sm border border-slate-100' 
                            }}
                        ">
                            @if(!$isUser)
                                <div class="prose prose-sm max-w-none prose-p:my-1 prose-headings:my-2 text-slate-700">
                                    {!! Str::markdown($msg['content']) !!}
                                </div>
                            @else
                                {{ $msg['content'] }}
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ \Carbon\Carbon::parse($msg['timestamp'])->format('h:i A') }}
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Loading Indicator (Simulated Typing) -->
            <div wire:loading wire:target="sendMessage" class="flex w-full flex-row gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 self-end mb-1 shadow-sm overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=S+S&background=3b82f6&color=fff" class="w-full h-full object-cover">
                </div>
                <div class="bg-white border border-slate-100 px-4 py-3 rounded-2xl rounded-bl-sm shadow-sm flex items-center gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-white border-t border-slate-100 shrink-0 relative z-20">
            <form wire:submit.prevent="sendMessage" class="relative flex items-center gap-2">
                <input 
                    type="text" 
                    wire:model="question" 
                    placeholder="اكتب رسالتك هنا..." 
                    class="w-full pl-4 pr-12 py-3.5 bg-slate-50 border-0 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-blue-100 focus:bg-white placeholder-slate-400 transition-all shadow-inner"
                    style="box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.02);"
                >
                <button 
                    type="submit"
                    class="absolute left-2 top-1.5 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed group"
                    wire:loading.attr="disabled"
                >
                    <svg wire:loading.remove wire:target="sendMessage" class="w-4 h-4 transform rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <svg wire:loading wire:target="sendMessage" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                 <p class="text-[10px] text-slate-300">مدعوم بواسطة الذكاء الاصطناعي</p>
            </div>
        </div>
    </div>

    <!-- Floating Trigger Button -->
    <button 
        @click="toggle()"
        style="
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease;
            color: white;
        "
        onmouseover="this.style.transform='scale(1.1)'"
        onmouseout="this.style.transform='scale(1)'"
    >
        <span x-show="!open" style="display: flex; align-items: center; justify-content: center;">
             <svg style="width: 28px; height: 28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        </span>
        <span x-show="open" x-cloak style="display: flex; align-items: center; justify-content: center;">
            <svg style="width: 28px; height: 28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </span>
    </button>
</div>
