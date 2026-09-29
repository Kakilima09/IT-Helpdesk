<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AIProvider;
use App\Services\AI\Exceptions\AIProviderException;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\LocalFallbackProvider;
use App\Services\AI\Providers\OpenAICompatibleProvider;
use Illuminate\Support\Facades\Log;

class AIProviderManager
{
    /**
     * Berapa provider remote yang sudah dicoba sebelum jatuh ke fallback lokal.
     */
    protected $remoteAttempts = 0;

    /**
     * Daftar provider yang gagal pada request ini.
     */
    protected $failed = [];

    /**
     * Resolusi API key: env dulu, lalu database (settings table).
     */
    public function key(string $provider): ?string
    {
        $envKey = config('ai.providers.' . $provider . '.key');

        if (!empty($envKey)) {
            return $envKey;
        }

        try {
            $dbKey = setting('ai_' . $provider . '_key');
        } catch (\Throwable $e) {
            return null;
        }

        return !empty($dbKey) ? $dbKey : null;
    }

    public function dbModel(string $provider): ?string
    {
        try {
            $model = setting('ai_' . $provider . '_model');
        } catch (\Throwable $e) {
            return null;
        }

        return !empty($model) ? $model : null;
    }

    public function config(string $provider): array
    {
        $config = config('ai.providers.' . $provider, []);

        $key = $this->key($provider);
        $model = $this->dbModel($provider) ?: ($config['model'] ?? null);

        if ($key) {
            $config['key'] = $key;
        }

        if ($model) {
            $config['model'] = $model;
        }

        // env() selalu mengembalikan string, beberapa API menolak nilai non-integer.
        $config['timeout'] = (int) config('ai.timeout', 60);
        $config['max_tokens'] = (int) config('ai.max_tokens', 4096);
        $config['temperature'] = (float) config('ai.temperature', 0.4);
        $config['max_turns'] = (int) config('ai.max_turns', 6);
        $config['retries'] = (int) config('ai.retries', 2);

        return $config;
    }

    public function make(string $provider): AIProvider
    {
        $config = $this->config($provider);

        if ($provider === 'gemini') {
            return new GeminiProvider($config);
        }

        if ($provider === 'local') {
            return new LocalFallbackProvider($config);
        }

        $instance = new OpenAICompatibleProvider($provider, $config);

        if ($provider === 'openrouter') {
            $instance->setExtraHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ]);
        }

        return $instance;
    }

    /**
     * Urutan provider yang akan dicoba: primary lalu fallback order.
     * Primary didahulukan dari setelan admin (settings table) bila terisi,
     * lalu diikuti config/env, sehingga perubahan provider di halaman admin
     * langsung berefek tanpa perlu menyentuh .env.
     */
    public function candidateOrder(): array
    {
        $primary = config('ai.provider', 'groq');

        try {
            if (!empty(setting('ai_provider')) && in_array(setting('ai_provider'), array_keys(config('ai.providers', [])), true)) {
                $primary = setting('ai_provider');
            }
        } catch (\Throwable $e) {
            // setting() belum tersedia (mis. proses tanpa DB); pakai config.
        }

        $order = array_values(array_unique(array_merge([$primary], (array) config('ai.fallback_order', []))));

        return array_values(array_filter($order, function ($provider) {
            return array_key_exists($provider, config('ai.providers', []));
        }));
    }

    /**
     * Chat dengan percobaan berurutan antar provider.
     *
     * @param array  $messages
     * @param array  $knowledge  Knowledge base untuk provider lokal
     * @param string|null $forced  Paksa pakai provider tertentu
     * @return array{content: string, provider: string}
     */
    public function chat(array $messages, array $knowledge = [], ?string $forced = null): array
    {
        $order = $forced ? [$forced] : $this->candidateOrder();

        // "local" selalu dicoba terakhir sebagai jaring pengaman.
        $local = [];
        $remote = [];

        foreach ($order as $provider) {
            if ($provider === 'local') {
                $local[] = $provider;
            } else {
                $remote[] = $provider;
            }
        }

        foreach ($remote as $provider) {
            $result = $this->tryProvider($provider, $messages);

            if ($result) {
                return $result;
            }
        }

        if (!empty($local)) {
            try {
                $instance = $this->make($local[0]);

                if ($instance instanceof LocalFallbackProvider) {
                    $instance->setContext($knowledge, config('ai.max_turns', 6));
                }

                return ['content' => $instance->chat($messages), 'provider' => $instance->name()];
            } catch (\Throwable $e) {
                Log::error('AI local fallback gagal: ' . $e->getMessage());
                throw new AIProviderException('Semua provider AI gagal. ' . $e->getMessage(), 0, $e);
            }
        }

        throw new AIProviderException('Tidak ada provider AI yang tersedia. ' . implode('; ', $this->failed));
    }

    protected function tryProvider(string $provider, array $messages): ?array
    {
        $maxRemote = (int) config('ai.max_remote_attempts', 2);

        if ($this->remoteAttempts >= max(1, $maxRemote)) {
            return null;
        }

        if (empty($this->key($provider))) {
            $this->failed[] = "{$provider}: API key belum diisi";
            return null;
        }

        try {
            $instance = $this->make($provider);

            $this->remoteAttempts++;
            $content = $instance->chat($messages);

            return ['content' => $content, 'provider' => $instance->name()];
        } catch (\Throwable $e) {
            $this->failed[] = "{$provider}: " . $e->getMessage();
            Log::warning('AI provider ' . $provider . ' gagal: ' . $e->getMessage());

            return null;
        }
    }
}
