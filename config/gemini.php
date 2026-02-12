<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google AI (Gemini) Configuration
    |--------------------------------------------------------------------------
    */

    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
    'temperature' => env('GEMINI_TEMPERATURE', 0.7),
    'max_output_tokens' => env('GEMINI_MAX_TOKENS', 2048),
];
