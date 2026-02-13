<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;

class FloatingAiChat extends Component
{
    public bool $isOpen = false;
    public string $question = '';
    public array $messages = [];
    public bool $isLoading = false;
    public bool $showWelcomeTooltip = true;
    
    protected $listeners = ['openChat' => 'openChatWindow'];
    
    public function mount()
    {
        // تنظيف الجلسة لإصلاح العرض وضمان بداية نظيفة
        // في المستقبل يمكن إزالة "|| true" لتمكين حفظ المحادثة
        if (request()->has('reset_chat') || true) { 
            session()->forget('floating_ai_messages');
            $this->messages = [];
        } else {
            $this->messages = session('floating_ai_messages', []);
        }
        
        // إخفاء tooltip بعد 10 ثواني
        $this->dispatch('hideTooltipAfterDelay');
    }
    
    public function openChatWindow()
    {
        $this->isOpen = true;
        $this->showWelcomeTooltip = false;
    }
    
    public function closeChat()
    {
        $this->isOpen = false;
    }
    
    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        $this->showWelcomeTooltip = false;
    }
    
    public function sendMessage()
    {
        if (empty(trim($this->question))) {
            return;
        }
        
        // زيادة وقت التنفيذ لهذا الطلب فقط
        set_time_limit(60);
        
        $this->isLoading = true;
        
        // تحديث واجهة المستخدم فوراً
        $this->dispatch('scroll-to-bottom');
        
        try {
            // إضافة سؤال المستخدم
            $this->messages[] = [
                'type' => 'user',
                'content' => $this->question,
                'timestamp' => now()->toDateTimeString()
            ];
            
            // التحقق من الـ Cache أولاً
            $cacheKey = 'ai_response_' . md5($this->question);
            $cachedResponse = cache()->get($cacheKey);
            
            if ($cachedResponse) {
                $answer = $cachedResponse;
            } else {
                $gemini = app(GeminiService::class);
                
                // سياق مبسط جداً للسرعة
                $context = $this->getSystemContext();
                
                // الحصول على الرد
                $answer = $gemini->chat($this->question, $context);
                
                // حفظ في الكاش لمدة ساعة
                cache()->put($cacheKey, $answer, now()->addHour());
            }
            
            // إضافة رد المساعد
            $this->messages[] = [
                'type' => 'assistant',
                'content' => $answer,
                'timestamp' => now()->toDateTimeString()
            ];
            
            // حفظ في الجلسة (آخر 15 رسالة فقط)
            session(['floating_ai_messages' => array_slice($this->messages, -15)]);
            
            // إعادة تعيين السؤال
            $this->question = '';
            
        } catch (\Exception $e) {
            \Log::error('AI Chat Error', [
                'message' => $e->getMessage(),
                'question' => $this->question
            ]);
            
            // عرض رسالة الخطأ الفعلية للمساعدة في التصحيح
            $errorMsg = app()->environment('local') ? $e->getMessage() : 'عذراً، حدث خطأ في الاتصال.';
            
            $this->messages[] = [
                'type' => 'assistant',
                'content' => "⚠️ **حدث خطأ:**\n" . $errorMsg,
                'timestamp' => now()->toDateTimeString()
            ];
        } finally {
            $this->isLoading = false;
        }
    }
    
    public function askQuickQuestion(string $question)
    {
        $this->question = $question;
        $this->sendMessage();
    }
    
    public function clearChat()
    {
        $this->messages = [];
        session()->forget('floating_ai_messages');
        cache()->forget('ai_response_*'); // مسح الكاش
        $this->addWelcomeMessage();
    }
    
    private function addWelcomeMessage()
    {
        // تم التعطيل - نعتمد على واجهة الـ Blade الفارغة لتجنب مشاكل العرض
    }
    
    private function getSystemContext(): array
    {
        try {
            return [
                'total_shipments_today' => \App\Models\Shipment::whereDate('created_at', today())->count(),
                'pending_shipments' => \App\Models\Shipment::where('status_id', 1)->count(),
                'delivered_today' => \App\Models\Shipment::whereDate('updated_at', today())
                    ->where('status_id', 5)
                    ->count(),
                'total_revenue_month' => \App\Models\Collection::whereMonth('collection_date', now()->month)
                    ->sum('amount'),
                'total_expenses_month' => \App\Models\Expense::whereMonth('expense_date', now()->month)
                    ->sum('amount'),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function render()
    {
        // Security: Hide completely if not logged in
        if (!auth()->check()) {
            return <<<'blade'
                <div></div>
            blade;
        }

        return view('livewire.floating-ai-chat');
    }
}
