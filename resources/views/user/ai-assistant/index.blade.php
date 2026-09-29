@extends('layouts.usermaster')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($existingTicket)
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong>Tiket {{ $existingTicket->ticket_id }}</strong> sudah dibuat dari percakapan ini.
                        <a href="{{ route('loadmore.load_data', $existingTicket->ticket_id) }}">Lihat tiket</a>
                    </div>
                    <a href="{{ route('customer.ai-assistant') }}" class="btn btn-sm btn-outline-primary">Mulai percakapan baru</a>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="mr-2">🤖</span> IT Assistant
                    <span class="ml-auto text-muted">
                        Halo, {{ Auth::guard('customer')->user()->firstname }} {{ Auth::guard('customer')->user()->lastname }} 👋
                    </span>
                </div>
                <div class="card-body">
                    <div id="chat-container" style="height: 420px; overflow-y: auto; border: 1px solid #dee2e6; padding: 12px; border-radius: 5px; background: #f8f9fa;">
                        <div id="chat-welcome" class="text-center text-muted small {{ ($conversation && $conversation->messages->isNotEmpty()) ? 'd-none' : '' }}">
                            <p class="mb-1"><strong>Jelaskan masalah IT Anda, saya akan mencoba menyelesaikannya.</strong></p>
                            <p class="mb-0">Jika tidak berhasil, saya akan membuatkan tiket ke tim IT untuk Anda.</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text" id="user-message" class="form-control" placeholder="Contoh: WiFi di laptop saya sudah lama tidak terhubung..." maxlength="2000" autocomplete="off">
                            <div class="input-group-append">
                                <button id="send-btn" type="button" class="btn btn-primary">Kirim</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Tekan Enter untuk mengirim.</small>
                    </div>

                    <div class="mt-2" id="quick-replies" style="display:none;">
                        <span class="mr-1 text-muted small">Balasan cepat:</span>
                        <button type="button" class="btn btn-sm btn-outline-success mr-1 quick-reply" data-text="sudah, masalahnya sudah selesai. Terima kasih!">Sudah selesai</button>
                        <button type="button" class="btn btn-sm btn-outline-danger mr-1 quick-reply" data-text="belum, cara tadi belum berhasil dan masih bermasalah">Belum berhasil</button>
                    </div>

                    <div class="mt-3">
                        <p class="mb-1 small text-muted">Masalah yang sering diajukan:</p>
                        @foreach(['Monitor', 'Wifi', 'Laptop', 'Lan', 'Printer', 'Office', 'Backup Data', 'PC', 'Loker', 'Windows'] as $problem)
                            <span class="badge badge-secondary p-2 mr-1 problem-chip" style="cursor:pointer;">{{ $problem }}</span>
                        @endforeach
                    </div>

                    <div id="loading" class="text-center mt-2" style="display:none;">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        <span class="ml-1 small text-muted">AI sedang menganalisa...</span>
                    </div>

                    <div id="status-area" class="mt-3"></div>

                    <div id="error-area" class="mt-3" style="display:none;">
                        <div class="alert alert-danger">
                            <span id="error-message"></span>
                            <button type="button" id="retry-btn" class="btn btn-sm btn-warning ml-2">Coba lagi</button>
                            <a href="{{ route('client.ticket') }}" class="btn btn-sm btn-secondary ml-1">Buat tiket manual</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chat-container');
    const userInput = document.getElementById('user-message');
    const sendBtn = document.getElementById('send-btn');
    const loading = document.getElementById('loading');
    const statusArea = document.getElementById('status-area');
    const errorArea = document.getElementById('error-area');
    const errorMessage = document.getElementById('error-message');
    const retryBtn = document.getElementById('retry-btn');
    const quickReplies = document.getElementById('quick-replies');
    const welcome = document.getElementById('chat-welcome');

    const chatUrl = @json(route('customer.ai-assistant.chat'));
    const createUrl = @json(route('customer.ai-assistant.create-ticket'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());

    let conversationId = @json($conversation->id ?? null);
    let conversationStatus = @json($conversation->status ?? 'ongoing');
    let isProcessing = false;
    let lastMessage = '';

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function renderMarkdown(text) {
        return escapeHtml(text)
            .replace(/```([\s\S]*?)```/g, '<pre class="bg-light p-2 rounded"><code>$1</code></pre>')
            .replace(/`([^`\n]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>')
            .replace(/(^|\n)\s*[-•]\s+/g, '$1• ')
            .replace(/(^|\n)\s*(\d+)\.\s+/g, '$1$2. ')
            .replace(/\n/g, '<br>');
    }

    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function appendMessage(role, message) {
        welcome.classList.add('d-none');

        const msgDiv = document.createElement('div');
        msgDiv.className = 'message ' + (role === 'user' ? 'text-right' : '');

        const bubble = document.createElement('div');
        bubble.className = 'bubble ' + (role === 'user' ? 'bg-primary text-white' : 'bg-white border');
        bubble.style.display = 'inline-block';
        bubble.style.padding = '8px 12px';
        bubble.style.borderRadius = '18px';
        bubble.style.margin = '5px 0';
        bubble.style.maxWidth = '85%';
        bubble.style.textAlign = 'left';
        bubble.style.boxShadow = '0 1px 2px rgba(0,0,0,.08)';
        bubble.innerHTML = role === 'user' ? escapeHtml(message) : renderMarkdown(message);

        msgDiv.appendChild(bubble);
        chatContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function showLoading(show) {
        loading.style.display = show ? 'block' : 'none';
        sendBtn.disabled = show;
        userInput.disabled = show;
        isProcessing = show;
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorArea.style.display = 'block';
    }

    function hideError() {
        errorArea.style.display = 'none';
    }

    function sendMessage(text) {
        if (isProcessing) return;

        const message = (text !== undefined ? text : userInput.value).trim();

        if (!message) return;

        if (conversationStatus !== 'ongoing') {
            showError('Percakapan ini sudah selesai. Silakan mulai percakapan baru.');
            userInput.disabled = true;
            sendBtn.disabled = true;
            return;
        }

        lastMessage = message;
        hideError();
        appendMessage('user', message);
        userInput.value = '';
        showLoading(true);
        statusArea.innerHTML = '';

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                conversation_id: conversationId
            })
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data: data })))
        .then(({ ok, data }) => {
            showLoading(false);

            if (!ok || !data.success) {
                appendMessage('assistant', '❌ ' + (data.error || 'Terjadi kesalahan. Silakan coba lagi.'));
                showError(data.error || 'Unknown error');
                return;
            }

            conversationId = data.conversation_id;
            handleAIResponse(data);
        })
        .catch(error => {
            showLoading(false);
            appendMessage('assistant', '❌ Gagal terhubung ke server. Coba lagi sebentar.');
            showError('Connection error: ' + error.message);
        });
    }

    function handleAIResponse(data) {
        const response = data.response || {};

        appendMessage('assistant', response.message || '');

        if (data.degraded) {
            quickReplies.style.display = 'none';
            statusArea.innerHTML = '<div class="alert alert-warning small">Mode cadangan aktif. Saya memakai knowledge base lokal karena layanan AI sedang tidak tersedia.</div>';
            return;
        }

        if (response.resolved) {
            conversationStatus = 'resolved';
            quickReplies.style.display = 'none';
            statusArea.innerHTML = '<div class="alert alert-success">✅ Masalah Anda sudah ditandai selesai. Terima kasih sudah menggunakan AI Assistant.</div>'
                + '<a href="' + @json(route('client.dashboard')) + '" class="btn btn-sm btn-secondary">Kembali ke Dashboard</a>';
            userInput.disabled = true;
            sendBtn.disabled = true;
            return;
        }

        if (response.create_ticket) {
            conversationStatus = 'escalated';
            quickReplies.style.display = 'none';
            renderTicketPreview(response);
            return;
        }

        quickReplies.style.display = 'block';
        statusArea.innerHTML = '';
    }

    function renderTicketPreview(response) {
        const ticket = response.ticket || {};

        statusArea.innerHTML = ''
            + '<div class="alert alert-warning">'
            + '<strong>AI tidak berhasil menyelesaikan masalah ini secara otomatis.</strong> '
            + 'Berikut ringkasan yang akan dikirim ke tim IT:'
            + '<hr>'
            + '<p class="mb-1"><strong>Subjek:</strong> ' + escapeHtml(ticket.subject || '-') + '</p>'
            + '<p class="mb-1"><strong>Kategori:</strong> ' + escapeHtml(ticket.category || '-') + '</p>'
            + '<p class="mb-1"><strong>Prioritas:</strong> ' + escapeHtml(ticket.priority || 'Medium') + '</p>'
            + '<p class="mb-1"><strong>Diagnosis AI:</strong> ' + escapeHtml(response.diagnosis || '-') + '</p>'
            + '<p class="mb-2"><strong>Deskripsi:</strong><br>' + escapeHtml(ticket.description || '-') + '</p>'
            + '<button type="button" id="create-ticket-btn" class="btn btn-primary btn-sm">Buat Tiket IT Sekarang</button>'
            + '</div>';

        document.getElementById('create-ticket-btn').addEventListener('click', createTicket);
    }

    function createTicket() {
        if (isProcessing) return;

        showLoading(true);
        hideError();

        fetch(createUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ conversation_id: conversationId })
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data: data })))
        .then(({ ok, data }) => {
            showLoading(false);

            if (!ok || !data.success) {
                showError(data.error || 'Gagal membuat tiket.');
                return;
            }

            statusArea.innerHTML = '<div class="alert alert-success">✅ Tiket <strong>' + escapeHtml(data.ticket_id) + '</strong> berhasil dibuat. Mengalihkan ke halaman tiket...</div>';
            setTimeout(() => window.location.href = data.redirect, 2000);
        })
        .catch(error => {
            showLoading(false);
            showError('Gagal membuat tiket: ' + error.message);
        });
    }

    function loadHistory(messages) {
        messages.forEach(msg => {
            if (msg.message) {
                appendMessage(msg.role, msg.message);
            }
        });
        scrollToBottom();
    }

    sendBtn.addEventListener('click', () => sendMessage());

    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    retryBtn.addEventListener('click', function() {
        if (lastMessage) sendMessage(lastMessage);
    });

    document.querySelectorAll('.quick-reply').forEach(button => {
        button.addEventListener('click', function() {
            sendMessage(this.dataset.text);
        });
    });

    document.querySelectorAll('.problem-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            userInput.value = this.textContent.trim() + ' saya bermasalah, tolong bantu diagnosa.';
            userInput.focus();
        });
    });

    @if($conversation && $conversation->messages->isNotEmpty())
        loadHistory(@json($conversation->messages->map(function($m) {
            return ['role' => $m->role, 'message' => $m->message];
        })));

        @if($conversation->status === 'resolved')
            statusArea.innerHTML = '<div class="alert alert-success">✅ Percakapan ini sudah selesai.</div>';
            userInput.disabled = true;
            sendBtn.disabled = true;
        @endif
    @endif

    @if($existingTicket)
        userInput.disabled = true;
        sendBtn.disabled = true;
    @endif

    scrollToBottom();
});
</script>
@endsection
