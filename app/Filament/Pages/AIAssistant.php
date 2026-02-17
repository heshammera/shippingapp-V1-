<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\GeminiService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class AIAssistant extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static string $view = 'filament.pages.ai-assistant';
    protected static ?string $title = 'المساعد الذكي';
    protected static ?string $navigationLabel = 'المساعد الذكي';
    protected static ?string $navigationGroup = '⚡ أدوات ذكية';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;
    
    public string $question = '';
    public string $answer = '';
    public bool $isLoading = false;
    public array $chatHistory = [];
    
    public function mount()
    {
        // تحميل آخر محادثات من الجلسة
        $this->chatHistory = session('ai_chat_history', []);
    }
    
    public function ask()
    {
        $this->validate([
            'question' => 'required|string|min:3'
        ]);
        
        $this->isLoading = true;
        
        try {
            $gemini = app(GeminiService::class);
            
            // جلب context من نظام الشحن
            $context = $this->getSystemContext();
            
            // إرسال للـ AI
            $this->answer = $gemini->chat($this->question, $context);
            
            // حفظ في ال history
            $this->chatHistory[] = [
                'question' => $this->question,
                'answer' => $this->answer,
                'timestamp' => now()->toDateTimeString()
            ];
            
            // حفظ في الجلسة (آخر 10 محادثات)
            session([
                'ai_chat_history' => array_slice($this->chatHistory, -10)
            ]);
            
            // إعادة تعيين السؤال
            $this->question = '';
            
        } catch (\Exception $e) {
            $this->answer = 'عذراً، حدث خطأ: ' . $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }
    
    public function clearHistory()
    {
        $this->chatHistory = [];
        session()->forget('ai_chat_history');
        $this->answer = '';
    }
    
    public function askQuickQuestion(string $question)
    {
        $this->question = $question;
        $this->ask();
    }
    
    private function getSystemContext(): array
    {
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
            'active_drivers' => \App\Models\User::where('type', 'delivery_agent')
                ->where('is_active', true)
                ->count(),
        ];
    }
}
