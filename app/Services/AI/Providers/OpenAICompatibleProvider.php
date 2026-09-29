<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\Exceptions\AIProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAICompatibleProvider implements AIProvider
{
    protected $name;
    protected $key;
    protected $model;
    protected $baseUrl;
    protected $timeout;
    protected $maxTokens;
    protected $temperature;
    protected $extraHeaders = [];

    public function __construct($name, array $config)
    {
        $this->name = $name;
        $this->key = $config['key'] ?? null;
        $this->model = $config['model'] ?? 'gpt-4o-mini';
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->timeout = (int) ($config['timeout'] ?? 60);
        $this->maxTokens = (int) ($config['max_tokens'] ?? 4096);
        $this->temperature = (float) ($config['temperature'] ?? 0.4);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function chat(array $messages): string
    {
        if (empty($this->key)) {
            throw new AIProviderException("API key untuk provider {$this->name} belum diisi.");
        }

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];

        $response = Http::withHeaders(array_merge([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
        ], $this->extraHeaders))
            ->timeout($this->timeout)
            ->post($this->baseUrl . '/chat/completions', $payload);

        if ($response->failed()) {
            throw new AIProviderException(
                "HTTP {$response->status()} dari {$this->name}: " . $response->body()
            );
        }

        $content = $response->json('choices.0.message.content');

        if (blank($content)) {
            throw new AIProviderException("Respons kosong dari {$this->name}.");
        }

        return $content;
    }

    public function setExtraHeaders(array $headers)
    {
        $this->extraHeaders = $headers;

        return $this;
    }
}
