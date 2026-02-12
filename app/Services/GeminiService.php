<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    // Groq API Endpoint (OpenAI Compatible)
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        // استخدام مفتاح Groq بدلاً من Gemini
        $this->apiKey = env('GROQ_API_KEY') ?: config('gemini.api_key');
        // استخدام موديل Llama 3 السريع والذكي
        $this->model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
        
        if (empty($this->apiKey)) {
            Log::warning('Groq API Key is missing');
        }
    }

    /**
     * Send a chat message to Groq
     */
    public function chat(string $prompt, array $context = []): string
    {
        // 1. تحديد هوية المستخدم (Role-Based Context)
        $user = auth()->user();
        $userRole = $user ? ($user->role ?? 'User') : 'Guest';
        $userName = $user ? $user->name : 'المستخدم';

        // 2. تجهيز الـ System Message (السياق الصارم)
        $systemMessage = <<<PROMPT
أنت "سارة"، مساعد ذكي متخصص ومحترف لنظام إدارة الشحنات.
مهمتك الوحيدة هي مساعدة "{$userName}" (الصفة: {$userRole}) في إدارة أعمال الشحن.

القواعد الصارمة (Strict Rules):
1. ⛔ يمنع منعاً باتاً الإجابة عن أي أسئلة خارج نطاق العمل (مثل الطقس، الفضاء، الرياضة، النكت، السياسة).
2. إذا سأل المستخدم عن شيء غير الشحن أو النظام، اعتذر بأسلوب مهني وقل: "أنا متخصص فقط في مساعدتك في إدارة الشحنات."
3. تحدث بلهجة شركات (Professional Corporate Tone) ومختصرة جداً.
4. استخدم الأرقام والبيانات المتاحة في الـ Context بدقة. لا تؤلف أرقاماً.

تعليمات الأمان وخصوصية البيانات:
- أنت تتحدث مع: {$userRole}.
- إذا كان "Admin": أعطه كل التفاصيل المالية والأرباح.
- إذا كان "Mandoub" (مندوب) أو "Driver": أعطه فقط معلومات شحناته والعملاء، ولا تظهر له أرباح الشركة الكلية أبداً.

PROMPT;

        if (!empty($context)) {
            $systemMessage .= "\n\n[بيانات السياق الحالية من النظام]:\n" . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        // 3. إرسال الطلب
        return $this->sendRequest([
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $prompt]
        ]);
    }

    /**
     * Send request to Groq API (OpenAI Compatible Format)
     */
    public function sendRequest(array $messages): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('API Key غير موجود. يرجى إضافة GROQ_API_KEY في ملف .env');
        }

        $url = "{$this->baseUrl}/chat/completions";
        
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];
        
        // إعدادات التجاوز لـ Localhost كما طلبنا سابقاً
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])
        ->timeout(30)
        ->withOptions(['verify' => false])
        ->post($url, $payload);
            
        if (!$response->successful()) {
            $error = $response->json()['error']['message'] ?? $response->body();
            throw new \Exception('Groq Error: ' . $error);
        }
        
        return $response->json()['choices'][0]['message']['content'] ?? 'لا توجد إجابة.';
    }

    /**
     * Analyze Business Data
     */
    public function analyzeBusinessData(array $data, string $question = null): string
    {
        $prompt = $question ?? "حلل هذه البيانات التجارية وأعطني نقاط القوة والضعف وتوصيات.";
        return $this->chat($prompt, ['data' => $data]);
    }
    
    // Legacy methods kept for interface compatibility but pointing to simple chat
    public function composeEmail(string $purpose, array $data = []): string
    {
        return $this->chat("اكتب بريد إلكتروني لـ: $purpose", $data);
    }
}
