<?php

namespace App\Http\Controllers\User\AI;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use App\Models\Apptitle;
use App\Models\Footertext;
use App\Models\Pages;
use App\Models\Seosetting;
use App\Services\HelpdeskAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AIAssistantController extends Controller
{
    protected $ai;

    public function __construct(HelpdeskAIService $ai)
    {
        $this->ai = $ai;
    }

    public function index()
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return redirect()->route('login');
        }

        $seopage = Seosetting::first();
        $title = Apptitle::first();
        $footertext = Footertext::first();
        $page = Pages::all();

        $conversation = AIConversation::with('messages')
            ->where('cust_id', $customer->id)
            ->where('status', 'ongoing')
            ->latest('id')
            ->first();

        $existingTicket = $conversation && $conversation->ticket_id
            ? $conversation->ticket()
            : null;

        return view('user.ai-assistant.index', compact(
            'customer', 'seopage', 'title', 'footertext', 'page', 'conversation', 'existingTicket'
        ));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['success' => false, 'error' => 'Sesi Anda sudah berakhir, silakan login kembali.'], 401);
        }

        if (config('ai.enabled') === false || setting('ai_enabled') !== 'on') {
            return response()->json([
                'success' => false,
                'error' => 'AI Assistant sedang dinonaktifkan oleh administrator. Silakan buat tiket manual.',
            ], 503);
        }

        try {
            $result = $this->ai->chat(
                $customer,
                $request->input('message'),
                $request->input('conversation_id')
            );
        } catch (Throwable $e) {
            Log::error('AI Assistant chat error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'AI Assistant sedang mengalami gangguan. Silakan coba lagi atau buat tiket manual.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $result['conversation_id'],
            'response' => $result['response'],
            'provider' => $result['provider'] ?? null,
            'degraded' => $result['degraded'] ?? false,
        ]);
    }

    public function createTicket(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['success' => false, 'error' => 'Sesi Anda sudah berakhir, silakan login kembali.'], 401);
        }

        $conversationId = $request->input('conversation_id') ?: session('ai_conversation_id');

        if (! $conversationId) {
            return response()->json(['success' => false, 'error' => 'Tidak ada percakapan aktif untuk dijadikan tiket.'], 422);
        }

        try {
            $ticket = $this->ai->createTicketFromAI($customer, $conversationId);
        } catch (Throwable $e) {
            Log::error('AI Assistant create ticket error: ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => 'Gagal membuat tiket. Silakan coba lagi.'], 500);
        }

        session()->forget(['ai_conversation_id', 'ai_chat_history', 'ai_last_draft']);

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->ticket_id,
            'redirect' => route('loadmore.load_data', $ticket->ticket_id),
        ]);
    }
}
