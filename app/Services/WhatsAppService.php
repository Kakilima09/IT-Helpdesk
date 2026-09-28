<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppService
{
    protected $token;
    protected $baseUrl;
    protected $countryCode;

    public function __construct()
    {
        $this->token = config('whatsapp.fonnte_token', '');
        $this->baseUrl = rtrim(config('whatsapp.fonnte_base_url', 'https://api.fonnte.com'), '/');
        $this->countryCode = config('whatsapp.country_code', '62');
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Kirim pesan teks WhatsApp via Fonnte.
     */
    public function sendText($target, $message)
    {
        return $this->request([
            'target' => $this->normalizeNumber($target),
            'message' => $message,
            'type' => 'text',
        ]);
    }

    /**
     * Kirim dokumen PDF WhatsApp via Fonnte.
     * $document dapat berupa base64 string atau URL publik.
     */
    public function sendDocument($target, $document, $filename, $caption = null)
    {
        return $this->request([
            'target' => $this->normalizeNumber($target),
            'type' => 'document',
            'document' => $document,
            'filename' => $filename,
            'message' => $caption ?: 'Dokumen GA Request',
        ]);
    }

    /**
     * Normalisasi nomor: buang non-digit, ganti awalan 0 dengan kode negara.
     */
    public function normalizeNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', (string) $number);
        if (Str::startsWith($number, '0')) {
            $number = $this->countryCode . substr($number, 1);
        }
        return $number;
    }

    protected function request(array $payload)
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsApp gateway belum dikonfigurasi (FONNTE_TOKEN kosong).');
            return false;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($this->baseUrl . '/send', [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'form_params' => $payload,
                'timeout' => 30,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            Log::info('Fonnte response: ' . json_encode($body));
            return $body;
        } catch (\Exception $e) {
            Log::error('Fonnte request gagal: ' . $e->getMessage());
            return false;
        }
    }
}