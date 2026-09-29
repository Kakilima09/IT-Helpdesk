<?php

namespace App\Services;

use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\Articles\Article;
use App\Models\FAQ;
use App\Models\Ticket\Category;
use App\Models\Ticket\Ticket;
use App\Models\Ticketnote;
use App\Notifications\TicketCreateNotifications;
use App\Models\User;
use App\Services\AI\AIProviderManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HelpdeskAIService
{
    protected $manager;

    public function __construct(AIProviderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Proses satu putaran percakapan: simpan pesan user, panggil AI, simpan
     * balasan, dan perbarui status conversation.
     *
     * @param \App\Models\Customer $customer
     * @param string               $userMessage
     * @param int|null             $conversationId
     * @return array
     */
    public function chat($customer, $userMessage, $conversationId = null)
    {
        $conversation = $this->getOrCreateConversation($customer, $conversationId);

        $this->saveMessage($conversation->id, 'user', $userMessage);

        $knowledge = $this->searchKnowledge($userMessage);
        $context = $this->buildContext($conversation, $knowledge);

        $provider = null;

        try {
            $result = $this->manager->chat($context, $knowledge);
            $raw = $result['content'];
            $provider = $result['provider'];
        } catch (\Throwable $e) {
            Log::error('HelpdeskAIService: semua provider gagal - ' . $e->getMessage());

            return [
                'conversation_id' => $conversation->id,
                'response' => $this->offlineResponse($userMessage, $knowledge),
                'provider' => 'offline',
                'degraded' => true,
            ];
        }

        $parsed = $this->parseAIResponse($raw);

        $this->saveMessage($conversation->id, 'assistant', $parsed['message'], [
            'status' => $parsed['status'],
            'resolved' => $parsed['resolved'],
            'create_ticket' => $parsed['create_ticket'],
            'diagnosis' => $parsed['diagnosis'],
            'ticket' => $parsed['ticket'],
            'provider' => $provider,
        ]);

        if ($parsed['resolved']) {
            $conversation->update(['status' => 'resolved']);
        } elseif ($parsed['create_ticket']) {
            $conversation->update(['status' => 'escalated']);
        }

        return [
            'conversation_id' => $conversation->id,
            'response' => $parsed,
            'provider' => $provider,
        ];
    }

    /**
     * Buat ticket dari draft escalation percakapan.
     *
     * @param \App\Models\Customer $customer
     * @param int                  $conversationId
     * @return Ticket
     *
     * @throws \RuntimeException
     */
    public function createTicketFromAI($customer, $conversationId)
    {
        $conversation = AIConversation::where('cust_id', $customer->id)
            ->where('id', $conversationId)
            ->first();

        if (!$conversation) {
            throw new \RuntimeException('Percakapan tidak ditemukan.');
        }

        // Kalau sudah ada tiket, jangan buat duplikat.
        if ($conversation->ticket_id && ($existing = Ticket::find($conversation->ticket_id))) {
            return $existing;
        }

        $draft = $this->draftFromConversation($conversation);

        $category = $this->resolveCategory($draft['category'] ?? null);
        $customerName = trim(($customer->firstname ?? '') . ' ' . ($customer->lastname ?? ''));
        $customerName = $customerName ?: ($customer->username ?? 'Pelanggan');

        $ticket = Ticket::create([
            'cust_id' => $customer->id,
            'subject' => $draft['subject'],
            'message' => $draft['description'],
            'category_id' => $category ? $category->id : null,
            'status' => 'New',
            'priority' => $draft['priority'] ?: ($category->priority ?? 'Medium'),
            // Halaman detail tiket selalu memanggil decrypt() pada kolom ini
            // tanpa cek null, jadi harus selalu terisi. String 'undefined'
            // adalah konvensi app untuk tiket tanpa purchase code Envato.
            'purchasecode' => encrypt('undefined'),
        ]);

        $ticket->ticket_id = setting('CUSTOMER_TICKETID', 'TKT') . '-' . $ticket->id;
        $ticket->save();

        $conversation->update([
            'ticket_id' => $ticket->id,
            'status' => 'escalated',
        ]);

        $this->saveMessage($conversation->id, 'assistant', null, [
            'system' => true,
            'event' => 'ticket_created',
            'ticket_id' => $ticket->id,
            'ticket_ref' => $ticket->ticket_id,
        ]);

        $this->attachTicketNotes($ticket, $draft, $conversation);
        $this->notifyAdmins($ticket);

        Log::info('AI Assistant membuat ticket', [
            'ticket_id' => $ticket->ticket_id,
            'conversation_id' => $conversation->id,
            'customer' => $customerName,
        ]);

        return $ticket;
    }

    // ---- Conversation helpers ----

    protected function getOrCreateConversation($customer, $conversationId = null)
    {
        if ($conversationId) {
            $existing = AIConversation::where('id', $conversationId)
                ->where('cust_id', $customer->id)
                ->first();

            // Percakapan escalated masih boleh dilanjutkan: user bisa tetap
            // mengerjakan troubleshooting atau menyatakan masalahnya selesai
            // sebelum sempat membuat tiket.
            if ($existing && in_array($existing->status, ['ongoing', 'escalated'], true)) {
                return $existing;
            }
        }

        return AIConversation::create([
            'cust_id' => $customer->id,
            'status' => 'ongoing',
        ]);
    }

    protected function saveMessage($conversationId, $role, $message, $metadata = null)
    {
        return AIMessage::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'message' => $message ?? '',
            'metadata' => $metadata,
        ]);
    }

    /**
     * Susun draft tiket dari percakapan: metadata AI terakhir, digabung dengan
     * ringkasan seluruh pesan user.
     */
    protected function draftFromConversation(AIConversation $conversation): array
    {
        $assistantMessage = $conversation->messages()
            ->where('role', 'assistant')
            ->whereNotNull('metadata')
            ->orderBy('id', 'desc')
            ->get()
            ->first(function ($msg) {
                $meta = $msg->metadata ?: [];
                return !empty($meta['ticket']['subject']);
            });

        $draft = ($assistantMessage->metadata['ticket'] ?? []) ?: [];

        $userMessages = $conversation->messages()
            ->where('role', 'user')
            ->orderBy('id')
            ->pluck('message')
            ->filter()
            ->values()
            ->all();

        $firstUserMessage = $userMessages[0] ?? 'Tanpa keterangan';

        $subject = $draft['subject'] ?? Str::limit(trim(preg_replace('/\s+/', ' ', $firstUserMessage)), 90);

        $description = $draft['description'] ?? ($userMessages ? implode("\n", array_map(function ($m) {
            return '- ' . $m;
        }, $userMessages)) : $firstUserMessage);

        // Tempelkan transkrip hanya bila AI tidak memberi deskripsi sendiri.
        if (!empty($draft['description']) && count($userMessages) > 1) {
            $description .= "\n\n---\nRiwayat percakapan dengan AI Assistant:\n"
                . implode("\n", array_map(function ($m) {
                    return '- ' . $m;
                }, $userMessages));
        }

        $priority = $draft['priority'] ?? null;
        $allowedPriorities = ['Low', 'Medium', 'High', 'Urgent', 'Critical'];

        if (!in_array($priority, $allowedPriorities, true)) {
            $priority = 'Medium';
        }

        return [
            'subject' => $subject,
            'category' => $draft['category'] ?? null,
            'priority' => $priority,
            'description' => $description,
            'recommendation' => $draft['recommendation'] ?? null,
            'diagnosis' => $draft['diagnosis'] ?? null,
        ];
    }

    protected function resolveCategory($name)
    {
        if (!empty($name)) {
            $category = Category::where('name', $name)->first();

            if ($category) {
                return $category;
            }
        }

        return Category::where('display', 1)->orderBy('id')->first() ?: Category::orderBy('id')->first();
    }

    /**
     * Simpan diagnosis AI sebagai note internal pada tiket.
     */
    protected function attachTicketNotes(Ticket $ticket, array $draft, AIConversation $conversation)
    {
        $lines = [];

        if (!empty($draft['diagnosis'])) {
            $lines[] = 'Diagnosis AI: ' . $draft['diagnosis'];
        }

        if (!empty($draft['recommendation'])) {
            $lines[] = 'Rekomendasi AI: ' . $draft['recommendation'];
        }

        $lines[] = 'Sumber: AI Assistant (percakapan #' . $conversation->id . ')';

        if (empty($lines)) {
            return;
        }

        try {
            Ticketnote::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'ticketnotes' => "Catatan AI Assistant\n" . implode("\n", $lines),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan note AI pada ticket ' . $ticket->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke admin, mengikuti alur TicketController@store.
     */
    protected function notifyAdmins(Ticket $ticket)
    {
        try {
            $userIds = [];

            foreach ($ticket->category->groupscategoryc ?? [] as $group) {
                foreach ($group->groupsc->groupsuser ?? [] as $member) {
                    $userIds[] = $member->users_id;
                }
            }

            $userIds = array_filter(array_unique($userIds));

            $admins = User::leftJoin('groups_users', 'groups_users.users_id', 'users.id')
                ->whereNull('groups_users.groups_id')
                ->whereNull('groups_users.users_id')
                ->get();

            if (!empty($userIds)) {
                $agents = User::whereIn('id', $userIds)->get();
                foreach ($agents as $agent) {
                    $agent->notify(new TicketCreateNotifications($ticket));
                }

                foreach ($admins as $admin) {
                    if ($admin->getRoleNames()->contains('superadmin')) {
                        $admin->notify(new TicketCreateNotifications($ticket));
                    }
                }
            } else {
                foreach ($admins as $admin) {
                    $admin->notify(new TicketCreateNotifications($ticket));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirim notifikasi admin untuk ticket AI: ' . $e->getMessage());
        }
    }

    // ---- Response parsing ----

    /**
     * Parse balasan AI menjadi struktur yang dipakai controller dan view.
     * Menangani JSON bertembol ```json, teks biasa, dan field yang hilang.
     *
     * @param string $raw
     * @return array
     */
    public function parseAIResponse($raw)
    {
        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            $decoded = json_decode($this->extractJsonBlock($raw), true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                // AI menjawab free text. Perlakukan sebagai respons diagnosa biasa.
                return $this->normalize([
                    'status' => 'diagnosing',
                    'message' => trim($raw),
                    'resolved' => false,
                    'create_ticket' => false,
                ]);
            }
        }

        return $this->normalize($decoded);
    }

    protected function extractJsonBlock($raw)
    {
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $raw, $matches)) {
            return $matches[1];
        }

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');

        if ($start !== false && $end !== false && $end > $start) {
            return substr($raw, $start, $end - $start + 1);
        }

        return $raw;
    }

    protected function normalize(array $data): array
    {
        $resolved = !empty($data['resolved']);
        $createTicket = !empty($data['create_ticket']) && !$resolved;

        $status = $data['status'] ?? null;
        $allowedStatus = ['diagnosing', 'resolved', 'needs_ticket'];

        if (!in_array($status, $allowedStatus, true)) {
            $status = $resolved ? 'resolved' : ($createTicket ? 'needs_ticket' : 'diagnosing');
        }

        if ($resolved) {
            $status = 'resolved';
        } elseif ($createTicket) {
            $status = 'needs_ticket';
        }

        $message = trim((string) ($data['message'] ?? ''));

        if ($message === '') {
            $message = $createTicket
                ? 'Maaf, saya belum berhasil menyelesaikan masalah Anda. Silakan buat tiket IT agar teknisi dapat membantu.'
                : 'Bisa ceritakan detail masalah Anda?';
        }

        $ticket = $data['ticket'] ?? null;

        if (!$createTicket) {
            $ticket = null;
        } else {
            $ticket = $this->normalizeTicket(is_array($ticket) ? $ticket : []);
        }

        return [
            'status' => $status,
            'message' => $message,
            'resolved' => $resolved,
            'create_ticket' => $createTicket,
            'diagnosis' => trim((string) ($data['diagnosis'] ?? '')) ?: null,
            'ticket' => $ticket,
        ];
    }

    protected function normalizeTicket(array $ticket): array
    {
        $categories = Category::orderBy('id')->pluck('name')->filter()->values();
        $category = $ticket['category'] ?? null;

        if ($category && ! $categories->contains($category)) {
            $category = null;
        }

        if (! $category) {
            $category = $categories->first();
        }

        $subject = trim((string) ($ticket['subject'] ?? ''));

        if ($subject === '') {
            $subject = 'Permintaan bantuan IT dari AI Assistant';
        }

        if (Str::length($subject) > 255) {
            $subject = Str::limit($subject, 252, '...');
        }

        $priority = $ticket['priority'] ?? 'Medium';

        if (!in_array($priority, ['Low', 'Medium', 'High', 'Urgent', 'Critical'], true)) {
            $priority = 'Medium';
        }

        $description = trim((string) ($ticket['description'] ?? ''));

        if ($description === '') {
            $description = $subject;
        }

        return [
            'subject' => $subject,
            'category' => $category,
            'priority' => $priority,
            'description' => $description,
            'recommendation' => trim((string) ($ticket['recommendation'] ?? '')) ?: null,
        ];
    }

    /**
     * Balasan cadangan ketika seluruh provider remote gagal.
     */
    protected function offlineResponse($userMessage, array $knowledge): array
    {
        $instance = new \App\Services\AI\Providers\LocalFallbackProvider([
            'max_turns' => config('ai.max_turns', 6),
        ]);

        $instance->setContext($knowledge, config('ai.max_turns', 6));

        $history = [
            ['role' => 'system', 'content' => 'Kamu adalah AI IT Assistant helpdesk.'],
            ['role' => 'user', 'content' => $userMessage],
        ];

        return $this->parseAIResponse($instance->chat($history));
    }

    // ---- Knowledge base ----

    /**
     * Cari FAQ dan artikel relevan berdasarkan keyword pesan user.
     */
    public function searchKnowledge($query)
    {
        $limit = config('ai.knowledge_limit', 5);
        $keywords = $this->extractKeywords($query);

        if (empty($keywords)) {
            return [];
        }

        $results = [];

        // Pencarian di SQL dibuat longgar untuk recall, lalu relevansi
        // difilter ulang lewat score() dengan batas kata.
        $faqs = FAQ::whereIn('status', [1, '1', 'Published'])
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('question', 'like', '%' . $keyword . '%');
                }
            })
            ->limit($limit * 3)
            ->get();

        foreach ($faqs as $faq) {
            $results[] = [
                'type' => 'faq',
                'title' => $faq->question,
                'content' => $faq->answer,
                'score' => $this->score($faq->question, $faq->answer, $keywords),
            ];
        }

        $articles = Article::where('status', 'Published')
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('title', 'like', '%' . $keyword . '%')
                      ->orWhere('message', 'like', '%' . $keyword . '%');
                }
            })
            ->limit($limit * 3)
            ->get();

        foreach ($articles as $article) {
            $results[] = [
                'type' => 'article',
                'title' => $article->title,
                'content' => Str::limit(strip_tags($article->message), 1200),
                'score' => $this->score($article->title, strip_tags($article->message), $keywords),
            ];
        }

        // Buang hasil dengan skor 0 supaya tidak ada konten tak relevan di prompt.
        $results = array_filter($results, function ($item) {
            return $item['score'] > 0;
        });

        usort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($results, 0, $limit);
    }

    /**
     * Batas kata dengan spasi sentinel, supaya LIKE tidak menganggap "mau"
     * sebagai bagian dari kata "mauris".
     */
    protected function padKeyword($keyword)
    {
        return ' ' . $keyword . ' ';
    }

    /**
     * Skor relevansi sederhana: keyword di judul diberi bobot lebih tinggi.
     */
    protected function score($title, $body, array $keywords)
    {
        $title = ' ' . Str::lower(strip_tags((string) $title)) . ' ';
        $body = ' ' . Str::lower(strip_tags((string) $body)) . ' ';
        $score = 0;

        foreach ($keywords as $keyword) {
            $needle = $this->padKeyword($keyword);

            if (Str::contains($title, $needle)) {
                $score += 3;
            }

            if (Str::contains($body, $needle)) {
                $score += 1;
            }
        }

        return $score;
    }

    protected function extractKeywords($query)
    {
        $stopWords = [
            'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan', 'saya', 'aku',
            'tidak', 'bisa', 'tolong', 'mohon', 'gimana', 'bagaimana', 'kok', 'sudah',
            'belum', 'ini', 'itu', 'ada', 'adalah', 'the', 'and', 'for', 'with', 'not',
            'please', 'help', 'my', 'is', 'are', 'to', 'of',
        ];

        $words = preg_split('/[^\p{L}\p{N}]+/u', Str::lower($query), -1, PREG_SPLIT_NO_EMPTY);
        $words = array_values(array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) >= 3 && !in_array($word, $stopWords, true);
        }));

        return array_slice(array_values(array_unique($words)), 0, 8);
    }

    // ---- Prompt & parsing ----

    protected function buildContext(AIConversation $conversation, array $knowledge): array
    {
        $messages = [['role' => 'system', 'content' => $this->systemPrompt($knowledge)]];

        $limit = config('ai.history_limit', 10);

        $history = $conversation->messages()
            ->orderBy('id')
            ->get()
            ->filter(function ($msg) {
                return in_array($msg->role, ['user', 'assistant'], true) && $msg->message !== '';
            })
            ->take(-$limit);

        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->message,
            ];
        }

        return $messages;
    }

    protected function systemPrompt(array $knowledge): string
    {
        $categoryNames = Category::pluck('name')->filter()->values()->implode(', ');

        $knowledgeBlock = 'Tidak ada artikel knowledge base yang cocok.';

        if (!empty($knowledge)) {
            $lines = [];
            foreach ($knowledge as $item) {
                $lines[] = '- [' . strtoupper($item['type']) . '] ' . $item['title'] . ': '
                    . trim(preg_replace('/\s+/', ' ', strip_tags($item['content'])));
            }
            $knowledgeBlock = implode("\n", $lines);
        }

        return <<<PROMPT
Kamu adalah AI IT Assistant untuk sebuah helpdesk perusahaan. Tugasmu mendiagnosis dan mencoba menyelesaikan masalah IT pengguna secara mandiri, sebelum masalah diteruskan ke teknisi.

Kategori tiket yang tersedia: {$categoryNames}

Knowledge base internal (gunakan jika relevan, jangan mengarang di luar ini):
{$knowledgeBlock}

Aturan:
1. Selalu balas dalam Bahasa Indonesia yang sopan, ringkas, dan teknis.
2. Berikan langkah troubleshooting konkret dan bernomor, maksimal 6 langkah per balasan.
3. Jaga "message" tetap ringkas (maksimal ±1200 karakter) dan "diagnosis" singkat (±300 karakter); jangan menulis pendahuluan berlebihan.
4. Gaya markdown: **tebal** untuk penekanan, dan bullet/nomor untuk langkah.
5. Jangan pernah mengarang nomor tiket, URL, atau kebijakan perusahaan.
6. Jangan meminta data sensitif seperti password, nomor kartu, atau PIN.
7. Jika informasi masih kurang, ajukan SATU pertanyaan klarifikasi yang relevan sebelum memberi langkah lanjutan.
8. Balas HANYA dengan JSON valid tanpa teks tambahan, dengan struktur:
{
  "status": "diagnosing" | "resolved" | "needs_ticket",
  "message": "balasan untuk pengguna dalam markdown",
  "resolved": true hanya bila masalah sudah benar-benar selesai,
  "create_ticket": true hanya bila troubleshooting gagal atau di luar kemampuanmu,
  "diagnosis": "ringkasan singkat hasil diagnosa untuk teknisi",
  "ticket": {
    "subject": "judul tiket maksimal 90 karakter",
    "category": "salah satu kategori di atas",
    "priority": "Low" | "Medium" | "High" | "Urgent" | "Critical",
    "description": "deskripsi masalah lengkap, lengkapkan dengan pesan asli pengguna dan langkah yang sudah dicoba",
    "recommendation": "rekomendasi tindakan untuk teknisi"
  }
}
9. Aturan "ticket": null (tanpa objek) ketika create_ticket bernilai false.
10. Set resolved=true dan create_ticket=false bila pengguna mengonfirmasi masalahnya selesai.
11. Buat create_ticket=true bila pengguna menyatakan cara sebelumnya gagal, atau setelah 5 percakapan troubleshooting tanpa penyelesaian.
12. Prioritas Urgent atau Critical hanya untuk kehilangan data, sistem gagal total, atau perusahaan tidak bisa beroperasi.
PROMPT;
    }
}
