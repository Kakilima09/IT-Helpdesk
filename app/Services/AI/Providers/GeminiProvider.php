<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProvider
{
    protected $key;
    protected $model;
    protected $baseUrl;
    protected $timeout;
    protected $maxTokens;
    protected $temperature;
    protected $retries;

    public function __construct(array $config)
    {
        $this->key = $config['key'] ?? null;
        $this->model = $config['model'] ?? 'gemini-3.8-flash';
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models', '/');
        $this->timeout = (int) ($config['timeout'] ?? 60);
        $this->maxTokens = (int) ($config['max_tokens'] ?? 4096);
        $this->temperature = (float) ($config['temperature'] ?? 0.4);
        $this->retries = (int) ($config['retries'] ?? 2);
    }

    public function name(): string
    {
        return 'gemini';
    }

    public function chat(array $messages): string
    {
        if (empty($this->key)) {
            throw new AIProviderException('API key untuk provider gemini belum diisi.');
        }

        $systemInstruction = null;
        $contents = [];

        foreach ($messages as $message) {
            $role = $message['role'] ?? 'user';
            $content = $message['content'] ?? '';

            if ($role === 'system') {
                $systemInstruction = ['parts' => [['text' => $content]]];
                continue;
            }

            $contents[] = [
                'role' => $role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $content]],
            ];
        }

        if (empty($contents)) {
            throw new AIProviderException('Tidak ada konten yang bisa dikirim ke gemini.');
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
                'responseMimeType' => 'application/json',
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = $systemInstruction;
        }

        $url = $this->baseUrl . '/' . $this->model . ':generateContent?key=' . urlencode($this->key);

        $response = null;

        // Gemini free tier sering membalas 503 (high demand), jadi coba ulang
        // dengan backoff sebelum menyerah dan pindah provider.
        $attempts = max(1, (int) ($this->retries + 1));

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout($this->timeout)
                ->post($url, $payload);

            if ($response->successful() || ! in_array($response->status(), [429, 500, 502, 503, 504], true)) {
                break;
            }

            if ($attempt < $attempts) {
                usleep(800000 * $attempt);
            }
        }

        if ($response->failed()) {
            $message = $response->json('error.message') ?: $response->body();

            throw new AIProviderException(
                'HTTP ' . $response->status() . ' dari gemini: ' . $message
            );
        }

        // Model reasoning bisa mengembalikan part lebih dari satu; ambil part
        // yang paling dekat dengan JSON.
        $parts = $response->json('candidates.0.content.parts', []);
        $text = '';

        foreach ($parts as $part) {
            if (! empty($part['text'])) {
                $text = $part['text'];
                break;
            }
        }

        if (blank($text)) {
            $blockReason = $response->json('promptFeedback.blockReason') ?: 'unknown';
            throw new AIProviderException("Respons kosong dari gemini (blockReason: {$blockReason}).");
        }

        // Output terpotong akan membuat JSON tidak valid, lebih baik pindah
        // provider daripada mengirim balasan setengah jadi ke pengguna.
        if ($response->json('candidates.0.finishReason') === 'MAX_TOKENS') {
            throw new AIProviderException('Respons gemini terpotong karena batas token, naikkan AI_MAX_TOKENS.');
        }

        return $text;
    }
}
