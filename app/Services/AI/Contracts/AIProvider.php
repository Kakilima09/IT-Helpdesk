<?php

namespace App\Services\AI\Contracts;

interface AIProvider
{
    /**
     * Kirim percakapan ke provider dan kembalikan teks balasan mentah.
     *
     * @param array $messages Array of ['role' => 'system|user|assistant', 'content' => string]
     * @return string
     *
     * @throws \App\Services\AI\Exceptions\AIProviderException
     */
    public function chat(array $messages): string;

    /**
     * Nama provider untuk keperluan logging.
     *
     * @return string
     */
    public function name(): string;
}
