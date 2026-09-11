<?php

namespace App\Http\Controllers\User\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seosetting;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Pages;

class AIAssistantController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->route('login');
        }

        // Data untuk layout
        $seopage = Seosetting::first();
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $page = Pages::all();

        // Ambil history dari session (jika ada)
        $history = session('ai_chat_history', []);
        $lastConversation = null; // tidak digunakan, tapi kita kirim null

        return view('user.ai-assistant.index', compact(
            'customer', 'lastConversation', 'seopage', 'title', 'footertext', 'page'
        ));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->message;
        $conversationId = session('ai_conversation_id', null);

        // Jika belum ada conversation_id, buat baru (gunakan timestamp)
        if (!$conversationId) {
            $conversationId = time() . '_' . Auth::guard('customer')->id();
            session(['ai_conversation_id' => $conversationId]);
        }

        // Simpan history ke session (opsional)
        $history = session('ai_chat_history', []);
        $history[] = ['role' => 'user', 'message' => $userMessage];
        session(['ai_chat_history' => $history]);

        // Generate dummy response
        $dummyResponse = $this->generateDummyResponse($userMessage);

        // Simpan response ke history
        $history[] = ['role' => 'assistant', 'message' => $dummyResponse['message']];
        session(['ai_chat_history' => $history]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId,
            'response' => $dummyResponse,
        ]);
    }

    public function createTicket(Request $request)
    {
        // Validasi sederhana
        $conversationId = session('ai_conversation_id');
        if (!$conversationId) {
            return response()->json([
                'success' => false,
                'error' => 'No active conversation.'
            ]);
        }

        // Ambil draft dari session (kita simpan draft terakhir)
        $draft = session('ai_last_draft', null);
        if (!$draft) {
            return response()->json([
                'success' => false,
                'error' => 'Ticket draft not found.'
            ]);
        }

        try {
            // Buat ticket menggunakan model existing
            $ticket = \App\Models\Ticket\Ticket::create([
                'cust_id' => Auth::guard('customer')->id(),
                'subject' => $draft['subject'],
                'message' => $draft['description'],
                'category_id' => null,
                'status' => 'New',
                'priority' => $draft['priority'] ?? 'Medium',
            ]);

            // Set ticket_id
            $ticket->ticket_id = setting('CUSTOMER_TICKETID', 'TKT') . '-' . $ticket->id;
            $ticket->save();

            // Hapus session setelah ticket dibuat
            session()->forget(['ai_conversation_id', 'ai_chat_history', 'ai_last_draft']);

            return response()->json([
                'success' => true,
                'ticket_id' => $ticket->id,
                'redirect' => route('loadmore.load_data', $ticket->ticket_id),
            ]);

        } catch (\Exception $e) {
            \Log::error('Create Ticket Error (Session): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create ticket.'
            ]);
        }
    }

    // ---- Dummy Response Generator ----

    private function generateDummyResponse($userMessage)
    {
        $lower = strtolower($userMessage);
        $response = [];
        $draft = null;

        if (strpos($lower, 'wifi') !== false || strpos($lower, 'internet') !== false || strpos($lower, 'koneksi') !== false) {
            $response = [
                'status' => 'diagnosing',
                'message' => "Saya mendeteksi masalah WiFi. Coba langkah berikut:\n1. Restart router WiFi\n2. Nonaktifkan dan aktifkan kembali WiFi di perangkat Anda\n3. Periksa apakah perangkat lain bisa terhubung\n\nApakah masalah sudah selesai?",
                'resolved' => false,
                'create_ticket' => false,
            ];
            $draft = [
                'subject' => 'Masalah Koneksi WiFi',
                'category' => 'Network',
                'priority' => 'Medium',
                'description' => "Pengguna melaporkan masalah koneksi WiFi.\nPertanyaan: $userMessage",
                'recommendation' => 'Periksa konfigurasi network adapter dan reset WiFi.'
            ];
        } elseif (strpos($lower, 'printer') !== false) {
            $response = [
                'status' => 'diagnosing',
                'message' => "Masalah printer terdeteksi. Coba:\n1. Pastikan printer menyala\n2. Cek koneksi USB atau jaringan\n3. Restart printer\n4. Coba cetak halaman uji\n\nApakah masalah sudah selesai?",
                'resolved' => false,
                'create_ticket' => false,
            ];
            $draft = [
                'subject' => 'Masalah Printer',
                'category' => 'Hardware',
                'priority' => 'Medium',
                'description' => "Pengguna melaporkan masalah printer.\nPertanyaan: $userMessage",
                'recommendation' => 'Cek driver printer dan koneksi fisik.'
            ];
        } elseif (strpos($lower, 'login') !== false || strpos($lower, 'password') !== false) {
            $response = [
                'status' => 'diagnosing',
                'message' => "Masalah login. Coba:\n1. Reset password melalui lupa password\n2. Pastikan caps lock tidak aktif\n3. Coba di browser berbeda\n\nApakah masalah sudah selesai?",
                'resolved' => false,
                'create_ticket' => false,
            ];
            $draft = [
                'subject' => 'Masalah Login',
                'category' => 'Account',
                'priority' => 'High',
                'description' => "Pengguna mengalami masalah login.\nPertanyaan: $userMessage",
                'recommendation' => 'Reset password dan verifikasi akun.'
            ];
        } elseif (strpos($lower, 'email') !== false) {
            $response = [
                'status' => 'diagnosing',
                'message' => "Masalah email. Coba:\n1. Periksa koneksi internet\n2. Coba kirim email ke diri sendiri\n3. Cek folder spam\n\nApakah masalah sudah selesai?",
                'resolved' => false,
                'create_ticket' => false,
            ];
            $draft = [
                'subject' => 'Masalah Email',
                'category' => 'Email',
                'priority' => 'Medium',
                'description' => "Pengguna melaporkan masalah email.\nPertanyaan: $userMessage",
                'recommendation' => 'Periksa setting SMTP dan koneksi.'
            ];
        } else {
            $response = [
                'status' => 'diagnosing',
                'message' => "Saya akan mencoba membantu. Ceritakan lebih detail masalah IT Anda, misalnya:\n- \"Laptop saya tidak bisa menyala\"\n- \"Printer tidak merespon\"\n- \"Email tidak terkirim\"\n\nSaya akan mencoba memberikan solusi.",
                'resolved' => false,
                'create_ticket' => false,
            ];
            $draft = null;
        }

        // Cek apakah user mengatakan "tidak selesai" atau "masih bermasalah"
        if (strpos($lower, 'tidak') !== false && (strpos($lower, 'selesai') !== false || strpos($lower, 'berhasil') !== false)) {
            $response['create_ticket'] = true;
            $response['message'] = "Baik, saya akan membuat tiket IT untuk masalah Anda. Mohon tunggu...\n\n" . $response['message'];
            // Simpan draft ke session
            session(['ai_last_draft' => $draft]);
        }

        // Tambahkan ticket ke response jika create_ticket true
        if ($response['create_ticket'] && $draft) {
            $response['ticket'] = $draft;
        }

        return $response;
    }
}