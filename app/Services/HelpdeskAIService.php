<?php

namespace App\Services;

use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Category;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpdeskAIService
{
    protected $openaiKey;
    protected $model;
    protected $maxTokens;
    protected $timeout;

    public function __construct()
    {
        $this->openaiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->maxTokens = config('services.openai.max_tokens', 1200);
        $this->timeout = config('services.openai.timeout', 45);
    }

    /**
     * Proses percakapan user dan kembalikan response AI.
     */
    public function chat($customerId, $userMessage, $conversationId = null)
    {
        Log::info('Service chat() dipanggil', ['message' => $userMessage]);
        // 1. Dapatkan atau buat conversation
        $conversation = $this->getOrCreateConversation($customerId, $conversationId);

        // 2. Simpan pesan user
        $this->saveMessage($conversation->id, 'user', $userMessage);

        // 3. Cari Knowledge Base & similar tickets (opsional)
        $knowledge = $this->searchKnowledge($userMessage);
        $similarTickets = $this->findSimilarTickets($userMessage);

        // 4. Bangun context untuk AI
        $context = $this->buildContext($conversation, $knowledge, $similarTickets);

        // 5. Panggil OpenAI atau fallback
        $aiResponse = $this->getAIResponse($context, $userMessage);
        $parsed = $this->parseAIResponse($aiResponse);

        // 6. Simpan pesan assistant
        $this->saveMessage($conversation->id, 'assistant', $parsed['message'] ?? $aiResponse, $parsed);

        // 7. Update status conversation
        if (isset($parsed['resolved']) && $parsed['resolved']) {
            $conversation->status = 'resolved';
            $conversation->save();
        } elseif (isset($parsed['create_ticket']) && $parsed['create_ticket']) {
            $conversation->status = 'escalated';
            $conversation->save();
        }

        return [
            'conversation_id' => $conversation->id,
            'response' => $parsed,
        ];
    }

    /**
     * Dapatkan response AI dari OpenAI atau fallback dummy.
     */
    protected function getAIResponse($messages, $userMessage)
    {
        // Cek apakah OpenAI key tersedia
        if (empty($this->openaiKey)) {
            Log::warning('OpenAI key is not set. Using fallback response.');
            return $this->getDummyResponse($userMessage);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiKey,
                'Content-Type' => 'application/json',
            ])->timeout($this->timeout)
              ->post('https://api.openai.com/v1/chat/completions', [
                  'model' => $this->model,
                  'messages' => $messages,
                  'max_tokens' => $this->maxTokens,
                  'temperature' => 0.5,
                  'response_format' => ['type' => 'json_object'],
              ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('OpenAI call failed: ' . $e->getMessage());
        }

        // Fallback jika OpenAI gagal
        return $this->getDummyResponse($userMessage);
    }

    /**
     * Response dummy untuk testing.
     */
    protected function getDummyResponse($userMessage)
    {
        // Deteksi kata kunci sederhana untuk memberikan solusi
        $lower = strtolower($userMessage);
        if (strpos($lower, 'wifi') !== false || strpos($lower, 'internet') !== false) {
            return json_encode([
                'status' => 'diagnosing',
                'message' => 'Saya mendeteksi masalah WiFi. Coba restart router Anda dan periksa apakah perangkat lain bisa terhubung. Apakah masalah sudah selesai?',
                'resolved' => false,
                'create_ticket' => false,
            ]);
        } elseif (strpos($lower, 'printer') !== false) {
            return json_encode([
                'status' => 'diagnosing',
                'message' => 'Masalah printer. Pastikan printer menyala dan terhubung ke jaringan. Coba cetak halaman uji. Apakah masalah sudah selesai?',
                'resolved' => false,
                'create_ticket' => false,
            ]);
        } elseif (strpos($lower, 'login') !== false || strpos($lower, 'password') !== false) {
            return json_encode([
                'status' => 'diagnosing',
                'message' => 'Masalah login. Coba reset password Anda melalui lupa password. Jika masih gagal, hubungi tim IT.',
                'resolved' => false,
                'create_ticket' => false,
            ]);
        } else {
            return json_encode([
                'status' => 'diagnosing',
                'message' => 'Saya akan mencoba membantu. Bisa ceritakan lebih detail masalah IT Anda? Misalnya: "Laptop saya tidak bisa menyala" atau "Email tidak bisa terkirim".',
                'resolved' => false,
                'create_ticket' => false,
            ]);
        }
    }

    /**
     * Buat ticket dari hasil diagnosis AI.
     */
    public function createTicketFromAI($conversationId, $customerId)
    {
        $conversation = AIConversation::with('messages')->findOrFail($conversationId);

        $lastAssistant = $conversation->messages()
            ->where('role', 'assistant')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastAssistant || empty($lastAssistant->metadata['ticket'])) {
            throw new \Exception('Ticket draft not found in AI response.');
        }

        $draft = $lastAssistant->metadata['ticket'];

        // Cari category berdasarkan nama (fallback ke ID 1)
        $category = null;
        if (!empty($draft['category'])) {
            $category = Category::where('name', $draft['category'])->first();
        }
        if (!$category) {
            $category = Category::first(); // default
        }

        // Buat ticket sesuai struktur existing
        $ticket = Ticket::create([
            'cust_id' => $customerId,
            'subject' => $draft['subject'],
            'message' => $draft['description'],
            'category_id' => $category ? $category->id : null,
            'status' => 'New',
            'priority' => $category ? $category->priority : 'Medium',
        ]);

        // Set ticket_id
        $ticket->ticket_id = setting('CUSTOMER_TICKETID', 'TKT') . '-' . $ticket->id;
        $ticket->save();

        // Simpan relasi
        $conversation->ticket_id = $ticket->id;
        $conversation->save();

        return $ticket;
    }

    // ---- Helper methods ----

    protected function getOrCreateConversation($customerId, $conversationId)
    {
        if ($conversationId) {
            return AIConversation::where('customer_id', $customerId)
                ->where('id', $conversationId)
                ->firstOrFail();
        }
        return AIConversation::create([
            'customer_id' => $customerId,
            'status' => 'ongoing',
        ]);
    }

    protected function saveMessage($conversationId, $role, $message, $metadata = null)
    {
        return AIMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }

    protected function searchKnowledge($query)
    {
        // Sesuaikan dengan model KnowledgeBase Anda
        return collect(); // dummy
    }

    protected function findSimilarTickets($query)
    {
        // Sesuaikan dengan model Ticket
        return collect(); // dummy
    }

    protected function buildContext($conversation, $knowledge, $similarTickets)
    {
        $history = $conversation->messages()->orderBy('id')->get()->map(function ($msg) {
            return ['role' => $msg->role, 'content' => $msg->message];
        })->toArray();

        $systemPrompt = "You are an AI IT Assistant. Respond in JSON format: status, message, resolved, create_ticket, ticket (if create_ticket true).";
        return array_merge([['role' => 'system', 'content' => $systemPrompt]], $history);
    }

    protected function parseAIResponse($response)
    {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'diagnosing',
                'message' => $response,
                'resolved' => false,
                'create_ticket' => false,
            ];
        }
        return array_merge([
            'status' => 'diagnosing',
            'message' => 'OK',
            'resolved' => false,
            'create_ticket' => false,
            'ticket' => null,
        ], $data);
    }
}