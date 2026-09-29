<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProvider;
use Illuminate\Support\Str;

/**
 * Fallback provider yang tidak membutuhkan API key.
 *
 * Menggunakan knowledge base (FAQ + Article + playbook troubleshooting bawaan)
 * dan pencocokan keyword. Dipakai ketika semua provider remote gagal atau
 * belum dikonfigurasi, sehingga AI Assistant tetap berfungsi.
 */
class LocalFallbackProvider implements AIProvider
{
    protected $knowledge = [];
    protected $maxTurns = 6;

    public function __construct(array $config = [])
    {
        $this->maxTurns = (int) ($config['max_turns'] ?? 6);
    }

    public function name(): string
    {
        return 'local';
    }

    public function setContext(array $knowledge, $maxTurns = null)
    {
        $this->knowledge = $knowledge;
        $this->maxTurns = $maxTurns ?: $this->maxTurns;

        return $this;
    }

    public function chat(array $messages): string
    {
        $lastUserMessage = '';
        $userTurns = [];

        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'user') {
                $lastUserMessage = $message['content'] ?? '';
                $userTurns[] = $lastUserMessage;
            }
        }

        // Subjek tiket memakai keluhan awal, bukan balasan konfirmasi.
        $subjectSource = $userTurns[0] ?? $lastUserMessage;

        $turns = max(0, count($userTurns) - 1);

        // Topik diambil dari pesan terbaru, lalu mundur ke pesan sebelumnya
        // kalau pesan itu hanya balasan singkat (mis. "gagal terus") yang tidak
        // menyebut kategori. Ini menjaga konteks saat user ganti topik.
        $topic = null;
        for ($i = count($userTurns) - 1; $i >= 0; $i--) {
            $topic = $this->detectTopic($userTurns[$i]);

            if ($topic) {
                break;
            }
        }

        if ($this->looksResolved($lastUserMessage)) {
            return $this->json([
                'status' => 'resolved',
                'message' => 'Senang mendengar masalah Anda sudah teratasi. Jika nanti ada kendala IT lagi, silakan hubungi kami kembali kapan saja. Semoga aktivitas Anda lancar!',
                'resolved' => true,
                'create_ticket' => false,
                'diagnosis' => 'Pengguna mengonfirmasi masalah sudah solved.',
                'ticket' => null,
            ]);
        }

        $steps = $this->stepsFor($topic);
        $contextNote = $this->knowledgeContext($topic);

        if ($this->looksFailed($lastUserMessage) || $turns >= $this->maxTurns) {
            return $this->json([
                'status' => 'needs_ticket',
                'message' => "Maaf, troubleshooting yang saya sarankan belum menyelesaikan masalah Anda.\n\n"
                    . "Saya akan menyiapkan ringkasan masalah untuk diteruskan ke tim IT sebagai tiket. "
                    . "Klik tombol **Buat Tiket IT Sekarang** agar teknisi kami bisa membantu lebih lanjut.",
                'resolved' => false,
                'create_ticket' => true,
                'diagnosis' => 'Solusi standar belum berhasil, perlu eskalasi ke teknisi.',
                'ticket' => [
                    'subject' => $this->buildSubject($topic, $subjectSource),
                    'category' => $topic,
                    'priority' => $this->priorityFor($topic),
                    'description' => $this->buildDescription($subjectSource, $lastUserMessage, $steps, $turns),
                    'recommendation' => 'Periksa perangkat, koneksi jaringan, dan driver terkait oleh teknisi.',
                ],
            ]);
        }

        $message = 'Saya mendeteksi ini sebagai masalah **' . ($topic ?: 'IT umum') . "**. Coba langkah berikut:\n\n"
            . $steps
            . "\n\nSudah berhasil? Jawab **sudah** jika masalah sudah teratasi, atau **belum** jika masih bermasalah.";

        if ($contextNote) {
            $message .= "\n\nBerdasarkan knowledge base internal:\n" . $contextNote;
        }

        return $this->json([
            'status' => 'diagnosing',
            'message' => $message,
            'resolved' => false,
            'create_ticket' => false,
            'diagnosis' => $topic ? "Mendeteksi kategori {$topic}." : 'Kategori masalah belum dapat ditentukan.',
            'ticket' => null,
        ]);
    }

    protected function json(array $payload)
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function looksResolved($message)
    {
        $message = Str::lower($message);

        $negative = ['belum', 'tidak', 'masih', 'gagal', 'nggak', 'enggak', 'no ', 'never'];

        foreach (['sudah', 'selesai', 'berhasil', 'fixed', 'solved', 'solve', 'jalan lagi', 'nyambung lagi', 'terima kasih', 'mantap', 'oke'] as $word) {
            if (Str::contains($message, $word)) {
                foreach ($negative as $neg) {
                    if (Str::contains($message, $neg)) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    protected function looksFailed($message)
    {
        $message = Str::lower($message);

        $phrases = [
            'tidak berhasil', 'belum berhasil', 'masih bermasalah', 'gagal',
            'nggak berhasil', 'enggak berhasil', 'sudah coba semua', 'tetap sama',
            'tidak ada perubahan', 'tidak works', 'tidak work', 'masalahnya tetap',
        ];

        foreach ($phrases as $phrase) {
            if (Str::contains($message, $phrase)) {
                return true;
            }
        }

        return false;
    }

    protected function detectTopic($message)
    {
        $message = Str::lower($message);

        $map = [
            'Backup Data' => ['backup', 'data hilang', 'recovery', 'data corrupt', 'file hilang'],
            'Wifi' => ['wifi', 'wi-fi', 'wireless', 'nirkabel', 'hotspot', 'tethering'],
            'Lan' => ['lan', 'ethernet', 'kabel jaringan', 'kabel lan'],
            'Printer' => ['printer', 'cetak', 'print'],
            'Monitor' => ['monitor', 'layar', 'display', 'blank', 'hitam'],
            'Laptop' => ['laptop', 'notebook', 'macbook', 'baterai', 'battery', 'panas', 'overheat', 'charger'],
            'PC' => ['pc', 'desktop', 'komputer', 'tower', 'hang', 'nyala', 'restart'],
            'Windows' => ['windows', 'update', 'bsod', 'blue screen', 'aplikasi', 'lambat', 'slow'],
            'Office' => ['office', 'word', 'excel', 'ppt', 'powerpoint', 'outlook'],
            'PDF' => ['pdf', 'dokumen', 'document'],
        ];

        foreach ($map as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if ($this->containsWord($message, $keyword)) {
                    return $topic;
                }
            }
        }

        return null;
    }

    /**
     * Pencocokan berbasis batas kata, supaya "lantai" tidak dianggap "lan".
     */
    protected function containsWord($haystack, $needle)
    {
        $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($needle, '/') . '(?![\p{L}\p{N}])/ui';

        return (bool) preg_match($pattern, $haystack);
    }

    protected function stepsFor($topic)
    {
        $playbooks = [
            'Wifi' => "1. Matikan lalu nyalakan kembali WiFi di perangkat Anda.\n"
                . "2. Pastikan mode airplane dan data seluler dimatikan.\n"
                . "3. Lupakan (forget) jaringan WiFi, lalu hubungkan ulang dengan password.\n"
                . "4. Restart router: matikan 10 detik, nyalakan, tunggu 2 menit.\n"
                . "5. Cek apakah perangkat lain di area yang sama bisa terhubung.",

            'Lan' => "1. Pastikan kabel LAN terpasang rapat di kedua ujung.\n"
                . "2. Cek lampu indikator port, mati berarti tidak ada koneksi.\n"
                . "3. Jalankan ipconfig /release lalu ipconfig /renew di Command Prompt.\n"
                . "4. Nonaktifkan lalu aktifkan adapter Ethernet.\n"
                . "5. Uji koneksi ke gateway dengan ping.",

            'Printer' => "1. Pastikan printer menyala dan tidak menunjukkan lampu error.\n"
                . "2. Cek koneksi USB atau jaringan, ganti kabel bila perlu.\n"
                . "3. Kosongkan antrian cetak dari Control Panel.\n"
                . "4. Cetak halaman uji dari printer atau dari Printer Properties.\n"
                . "5. Restart service Print Spooler dari Services.msc.",

            'Monitor' => "1. Cek kabel power dan video, pastikan terpasang penuh.\n"
                . "2. Tekan tombol power 3 detik untuk discharge, lalu nyalakan.\n"
                . "3. Ganti input HDMI, DisplayPort, atau VGA memakai tombol Source.\n"
                . "4. Uji dengan kabel dan port lain.\n"
                . "5. Sambungkan ke laptop lain untuk memastikan sumber masalah.",

            'Laptop' => "1. Colokkan charger, lalu coba nyalakan saat terhubung listrik.\n"
                . "2. Tutup aplikasi berat, hentikan proses antivirus yang sedang scan.\n"
                . "3. Bersihkan ventilasi, pastikan lubang udara tidak tertutup benda.\n"
                . "4. Cabut semua periferal (mouse, USB) lalu coba lagi.\n"
                . "5. Periksa baterai lewat powercfg /batteryreport.",

            'PC' => "1. Tekan tombol power 5 detik untuk force shutdown, lalu nyalakan.\n"
                . "2. Cabut semua USB lalu nyalakan tanpa periferal.\n"
                . "3. Bersihkan debu pada heatsink dan kipas.\n"
                . "4. Cek PSU, kipas tidak berputar berarti ganti power supply.\n"
                . "5. Uji RAM satu per satu.\n"
                . "6. Reset BIOS dengan melepas baterai CMOS 30 detik.",

            'Windows' => "1. Restart perangkat, banyak error hilang setelah restart.\n"
                . "2. Jalankan DISM /Online /Cleanup-Image /RestoreHealth sebagai admin.\n"
                . "3. Jalankan sfc /scannow untuk memperbaiki file sistem.\n"
                . "4. Periksa Windows Update, jeda update yang sedang berjalan bila perlu.\n"
                . "5. Uninstall program yang baru dipasang sebelum masalah muncul.\n"
                . "6. Masuk Safe Mode lewat Shift+Restart saat booting.",

            'Office' => "1. Tutup paksa aplikasi lewat Task Manager (Ctrl+Shift+Esc).\n"
                . "2. Buka aplikasi sebagai Run as administrator.\n"
                . "3. Nonaktifkan add-in lewat File > Options > Add-ins.\n"
                . "4. Cek folder dokumen, pastikan bukan OneDrive yang sedang offline.\n"
                . "5. Repair lewat Control Panel > Programs > Microsoft Office > Change.",

            'PDF' => "1. Pastikan file tidak rusak, buka ulang atau unduh ulang dari sumber.\n"
                . "2. Update aplikasi PDF reader ke versi terbaru.\n"
                . "3. Cek pengaturan Protected View di File > Options > Security.\n"
                . "4. Coba buka di browser Chrome atau Edge sebagai pembanding.\n"
                . "5. Gunakan Print to PDF untuk memastikan file bisa dibaca.",

            'Backup Data' => "1. Jangan format atau install ulang apa pun, data masih bisa diselamatkan.\n"
                . "2. Cek backup di OneDrive, Google Drive, atau folder cadangan server.\n"
                . "3. Jangan menulis data baru ke disk yang bermasalah.\n"
                . "4. Screenshot kondisi error dan catat kapan terakhir data terlihat.\n"
                . "5. Hubungi tim IT segera agar proses recovery bisa dimulai.",
        ];

        if ($topic && isset($playbooks[$topic])) {
            return $playbooks[$topic];
        }

        return "1. Ceritakan lebih detail gejalanya: kapan terjadi, pesan error apa yang muncul, dan apakah ada perubahan sebelum masalah ini.\n"
            . "2. Coba restart perangkat, lalu cek koneksi internet.\n"
            . "3. Catat pesan error persis seperti yang tampil agar teknisi lebih mudah mendiagnosis.\n"
            . "4. Screenshot error bila bisa, sertakan juga langkah yang sudah Anda coba.";
    }

    protected function knowledgeContext($topic)
    {
        if (empty($this->knowledge)) {
            return '';
        }

        $filtered = array_filter($this->knowledge, function ($item) use ($topic) {
            if (!$topic) {
                return true;
            }

            return Str::contains(Str::lower($item['title'] . ' ' . $item['content']), Str::lower($topic));
        });

        if (empty($filtered)) {
            $filtered = array_slice($this->knowledge, 0, 2);
        }

        $lines = [];

        foreach (array_slice($filtered, 0, 3) as $item) {
            $lines[] = '- ' . $item['title'] . ': ' . Str::limit(strip_tags($item['content']), 200);
        }

        return implode("\n", $lines);
    }

    protected function priorityFor($topic)
    {
        if (in_array($topic, ['Wifi', 'Lan', 'PC', 'Windows', 'Backup Data'], true)) {
            return 'High';
        }

        if (in_array($topic, ['Printer', 'Monitor', 'Laptop', 'Office', 'PDF'], true)) {
            return 'Medium';
        }

        return 'Low';
    }

    protected function buildSubject($topic, $message)
    {
        $clean = trim(preg_replace('/\s+/', ' ', $message));

        if (Str::length($clean) > 90) {
            $clean = Str::limit($clean, 87) . '...';
        }

        return $topic ? "[{$topic}] " . $clean : $clean;
    }

    protected function buildDescription($originalMessage, $lastMessage, $steps, $turns)
    {
        $description = "Keluhan pelanggan:\n" . $originalMessage . "\n\n"
            . "Langkah troubleshooting yang sudah dicoba:\n" . $steps . "\n\n";

        if ($lastMessage !== $originalMessage) {
            $description .= "Balasan terakhir pelanggan:\n" . $lastMessage . "\n\n";
        }

        return $description
            . "Jumlah percakapan: " . $turns . ". Solusi standar belum berhasil, mohon tiket langsung ke teknisi.";
    }
}
