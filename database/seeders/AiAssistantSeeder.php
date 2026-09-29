<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiAssistantSeeder extends Seeder
{
    /**
     * Baris settings untuk AI Assistant. Aman dijalankan berulang karena
     * memakai firstOrCreate.
     */
    public function run()
    {
        $defaults = [
            'ai_enabled' => 'off',
            'ai_provider' => 'gemini',
            'ai_gemini_key' => '',
            'ai_gemini_model' => 'gemini-2.0-flash',
            'ai_groq_key' => '',
            'ai_groq_model' => 'openai/gpt-oss-120b',
            'ai_openrouter_key' => '',
            'ai_openrouter_model' => 'meta-llama/llama-3.3-70b-instruct:free',
            'ai_openai_key' => '',
            'ai_openai_model' => 'gpt-4o-mini',
        ];

        foreach ($defaults as $key => $value) {
            if (DB::table('settings')->where('key', $key)->exists()) {
                continue;
            }

            DB::table('settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
