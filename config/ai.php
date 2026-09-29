<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Assistant
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk AI IT Assistant. Mendukung beberapa provider sekaligus
    | dengan fallback berurutan. Semua provider punya free tier, ambil API key
    | gratis dari:
    |
    |   gemini     -> https://aistudio.google.com/app/apikey
    |   groq       -> https://console.groq.com/keys
    |   openrouter -> https://openrouter.ai/keys
    |   openai     -> https://platform.openai.com/api-keys
    |
    | Nilai key bisa diisi lewat .env ATAU lewat database (settings table)
    | melalui halaman Admin > Settings > AI Assistant.
    |
    */

    'enabled' => env('AI_ENABLED', true),

    /*
     * Provider yang dipakai pertama. Nilai: gemini | groq | openrouter | openai | local
     */
    'provider' => env('AI_PROVIDER', 'gemini'),

    /*
     * Urutan fallback ketika provider utama gagal / tidak punya key.
     */
    'fallback_order' => [
        'gemini',
        'groq',
        'openrouter',
        'local',
    ],

    'timeout' => env('AI_TIMEOUT', 60),
    // Cukup besar karena beberapa model (mis. Gemini 3.x) memakai sebagian
    // kuota untuk token reasoning sebelum menulis JSON.
    'max_tokens' => env('AI_MAX_TOKENS', 4096),
    'temperature' => env('AI_TEMPERATURE', 0.4),

    /*
     * Berapa pesan terakhir yang dikirim sebagai konteks ke AI.
     */
    'history_limit' => 10,

    /*
     * Berapa kali provider dicoba ulang pada error transient (429/5xx) sebelum
     * pindah ke provider berikutnya.
     */
    'retries' => env('AI_RETRIES', 2),

    /*
     * Batas jumlah percakapan sebelum AI otomatis menawarkan escalation.
     */
    'max_turns' => 6,

    /*
     * Jumlah baris knowledge base (FAQ + Article) yang di-inject sebagai konteks.
     */
    'knowledge_limit' => 5,

    'providers' => [

        'gemini' => [
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
        ],

        'groq' => [
            'key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
            'base_url' => 'https://api.groq.com/openai/v1',
        ],

        'openrouter' => [
            'key' => env('OPENROUTER_API_KEY'),
            'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.3-70b-instruct:free'),
            'base_url' => 'https://openrouter.ai/api/v1',
        ],

        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => 'https://api.openai.com/v1',
        ],

        /*
         * Fallback tanpa API key. Memakai knowledge base + keyword lokal
         * sehingga AI Assistant tetap bisa digunakan tanpa koneksi internet.
         */
        'local' => [
            'key' => null,
            'model' => 'local-knowledge-base',
            'base_url' => null,
        ],

    ],

];
