@if(setting('ai_enabled') == 'on' && Auth::guard('customer')->check())
<style>
    .aiw-root { position: fixed; right: 20px; bottom: 20px; z-index: 1050; font-family: inherit; }
    .aiw-launcher { width: 58px; height: 58px; border-radius: 50%; border: 0; background: linear-gradient(135deg, #4f46e5, #2563eb); color: #fff; font-size: 26px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(37, 99, 235, .35); cursor: pointer; transition: transform .2s ease, opacity .2s ease; }
    .aiw-launcher:hover { transform: scale(1.05); }
    .aiw-launcher.aiw-launcher-hidden { opacity: 0; pointer-events: none; transform: scale(.8); }
    .aiw-panel { position: fixed; right: 20px; bottom: 88px; width: 370px; max-width: calc(100vw - 24px); height: 540px; max-height: calc(100vh - 110px); background: #fff; border-radius: 16px; box-shadow: 0 18px 50px rgba(15, 23, 42, .25); display: flex; flex-direction: column; overflow: hidden; opacity: 0; visibility: hidden; transform: translateY(12px) scale(.98); transition: opacity .2s ease, transform .2s ease, visibility .2s ease; }
    .aiw-panel.aiw-open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
    .aiw-header { background: linear-gradient(135deg, #4f46e5, #2563eb); color: #fff; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
    .aiw-header-info { display: flex; align-items: center; gap: 10px; }
    .aiw-avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 255, 255, .2); display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .aiw-title { font-weight: 600; font-size: 15px; line-height: 1.1; }
    .aiw-subtitle { font-size: 11px; opacity: .85; }
    .aiw-close { background: transparent; border: 0; color: #fff; font-size: 24px; line-height: 1; cursor: pointer; padding: 0 4px; }
    .aiw-messages { flex: 1; overflow-y: auto; padding: 14px; background: #f1f5f9; }
    .aiw-welcome { text-align: center; color: #64748b; font-size: 13px; padding: 10px 4px; }
    .aiw-welcome-icon { font-size: 34px; margin-bottom: 6px; }
    .aiw-msg { display: flex; margin: 6px 0; }
    .aiw-msg-user { justify-content: flex-end; }
    .aiw-msg-ai { justify-content: flex-start; }
    .aiw-bubble { max-width: 85%; padding: 8px 12px; border-radius: 16px; font-size: 13px; line-height: 1.45; box-shadow: 0 1px 2px rgba(15, 23, 42, .08); word-wrap: break-word; }
    .aiw-msg-user .aiw-bubble { background: #2563eb; color: #fff; border-bottom-right-radius: 4px; }
    .aiw-msg-ai .aiw-bubble { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; }
    .aiw-bubble pre { background: #0f172a; color: #e2e8f0; padding: 8px; border-radius: 8px; font-size: 12px; overflow-x: auto; }
    .aiw-bubble code { background: rgba(37, 99, 235, .1); color: #1d4ed8; padding: 1px 4px; border-radius: 4px; font-size: 12px; }
    .aiw-bubble pre code { background: transparent; color: inherit; padding: 0; }
    .aiw-quick { display: flex; gap: 6px; flex-wrap: wrap; padding: 8px 12px 0; background: #f1f5f9; }
    .aiw-chip { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 999px; padding: 4px 10px; font-size: 12px; cursor: pointer; }
    .aiw-chip:hover { background: #e2e8f0; }
    .aiw-status { padding: 0 12px; background: #f1f5f9; }
    .aiw-note { margin: 8px 0; padding: 8px 10px; border-radius: 10px; font-size: 12px; }
    .aiw-note-success { background: #dcfce7; color: #166534; }
    .aiw-note-warning { background: #fef3c7; color: #92400e; }
    .aiw-note-danger { background: #fee2e2; color: #991b1b; }
    .aiw-ticket { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; margin: 8px 0; font-size: 12px; }
    .aiw-ticket-title { font-weight: 600; margin-bottom: 6px; color: #0f172a; }
    .aiw-ticket-row { display: flex; justify-content: space-between; gap: 8px; padding: 2px 0; color: #475569; }
    .aiw-ticket-row strong { color: #0f172a; text-align: right; }
    .aiw-btn-primary { width: 100%; margin-top: 8px; border: 0; background: #2563eb; color: #fff; border-radius: 8px; padding: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .aiw-btn-primary:hover { background: #1d4ed8; }
    .aiw-btn-secondary { width: 100%; margin-top: 6px; border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 8px; padding: 7px; font-size: 12px; cursor: pointer; }
    .aiw-btn-secondary:hover { background: #f1f5f9; }
    .aiw-loading { display: flex; align-items: center; gap: 6px; padding: 6px 14px; background: #f1f5f9; }
    .aiw-typing { display: inline-flex; gap: 3px; }
    .aiw-typing span { width: 6px; height: 6px; border-radius: 50%; background: #94a3b8; animation: aiw-bounce 1.2s infinite; }
    .aiw-typing span:nth-child(2) { animation-delay: .15s; }
    .aiw-typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes aiw-bounce { 0%, 60%, 100% { transform: translateY(0); opacity: .5; } 30% { transform: translateY(-4px); opacity: 1; } }
    .aiw-input-area { display: flex; gap: 8px; padding: 10px 12px; border-top: 1px solid #e2e8f0; background: #fff; }
    .aiw-input { flex: 1; border: 1px solid #cbd5e1; border-radius: 999px; padding: 8px 14px; font-size: 13px; outline: none; }
    .aiw-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
    .aiw-send { width: 38px; height: 38px; border-radius: 50%; border: 0; background: #2563eb; color: #fff; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .aiw-send:disabled, .aiw-input:disabled { opacity: .55; cursor: not-allowed; }
    @media (max-width: 575.98px) {
        .aiw-root { right: 14px; bottom: 14px; }
        .aiw-panel { right: 10px; left: 10px; bottom: 80px; width: auto; height: calc(100vh - 100px); max-height: none; }
    }
</style>

<div id="aiw-root" class="aiw-root">
    <button type="button" id="aiw-launcher" class="aiw-launcher" aria-label="Buka IT Assistant">🤖</button>

    <div id="aiw-panel" class="aiw-panel" role="dialog" aria-label="IT Assistant" aria-hidden="true">
        <div class="aiw-header">
            <div class="aiw-header-info">
                <span class="aiw-avatar">🤖</span>
                <div>
                    <div class="aiw-title">IT Assistant</div>
                    <div class="aiw-subtitle">Online &bull; siap membantu</div>
                </div>
            </div>
            <button type="button" id="aiw-close" class="aiw-close" aria-label="Tutup">&times;</button>
        </div>

        <div id="aiw-messages" class="aiw-messages">
            <div id="aiw-welcome" class="aiw-welcome">
                <div class="aiw-welcome-icon">🤖</div>
                <p class="mb-1"><strong>Halo! Ada masalah IT yang bisa saya bantu?</strong></p>
                <p class="mb-0">Jelaskan keluhan Anda. Jika tidak bisa diselesaikan, saya buatkan tiket ke tim IT.</p>
            </div>
        </div>

        <div id="aiw-quick" class="aiw-quick" style="display:none;">
            <button type="button" class="aiw-chip aiw-quick-reply" data-text="sudah, masalahnya sudah selesai. Terima kasih!">Sudah selesai</button>
            <button type="button" class="aiw-chip aiw-quick-reply" data-text="belum, cara tadi belum berhasil dan masih bermasalah">Belum berhasil</button>
        </div>

        <div id="aiw-status" class="aiw-status"></div>

        <div id="aiw-loading" class="aiw-loading" style="display:none;">
            <span class="aiw-typing"><span></span><span></span><span></span></span>
        </div>

        <div class="aiw-input-area">
            <input type="text" id="aiw-input" class="aiw-input" placeholder="Tulis pesan..." maxlength="2000" autocomplete="off">
            <button type="button" id="aiw-send" class="aiw-send" aria-label="Kirim">&#10148;</button>
        </div>
    </div>
</div>

<script>
(function () {
    const root = document.getElementById('aiw-root');
    if (!root) return;

    const launcher = document.getElementById('aiw-launcher');
    const panel = document.getElementById('aiw-panel');
    const closeBtn = document.getElementById('aiw-close');
    const messagesEl = document.getElementById('aiw-messages');
    const welcomeEl = document.getElementById('aiw-welcome');
    const quickEl = document.getElementById('aiw-quick');
    const statusEl = document.getElementById('aiw-status');
    const loadingEl = document.getElementById('aiw-loading');
    const inputEl = document.getElementById('aiw-input');
    const sendBtn = document.getElementById('aiw-send');

    const chatUrl = @json(route('customer.ai-assistant.chat'));
    const createUrl = @json(route('customer.ai-assistant.create-ticket'));
    const historyUrl = @json(route('customer.ai-assistant.history'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());

    let conversationId = null;
    let isProcessing = false;
    let historyLoaded = false;
    let hasMessages = false;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function renderMarkdown(text) {
        return escapeHtml(text)
            .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
            .replace(/`([^`\n]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>')
            .replace(/(^|\n)\s*[-•]\s+/g, '$1• ')
            .replace(/(^|\n)\s*(\d+)\.\s+/g, '$1$2. ')
            .replace(/\n/g, '<br>');
    }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendMessage(role, message) {
        if (welcomeEl) welcomeEl.style.display = 'none';
        hasMessages = true;

        const wrap = document.createElement('div');
        wrap.className = 'aiw-msg ' + (role === 'user' ? 'aiw-msg-user' : 'aiw-msg-ai');

        const bubble = document.createElement('div');
        bubble.className = 'aiw-bubble';
        bubble.innerHTML = role === 'user' ? escapeHtml(message) : renderMarkdown(message);

        wrap.appendChild(bubble);
        messagesEl.appendChild(wrap);
        scrollToBottom();
    }

    function appendSystem(text) {
        const wrap = document.createElement('div');
        wrap.className = 'aiw-msg aiw-msg-ai';
        wrap.innerHTML = '<div class="aiw-note aiw-note-success w-100 text-center">' + escapeHtml(text) + '</div>';
        messagesEl.appendChild(wrap);
        scrollToBottom();
    }

    function setLoading(show) {
        loadingEl.style.display = show ? 'flex' : 'none';
        isProcessing = show;
        inputEl.disabled = show;
        sendBtn.disabled = show;
    }

    function setComposerEnabled(enabled) {
        inputEl.disabled = !enabled;
        sendBtn.disabled = !enabled;
    }

    function showStatus(html, type) {
        statusEl.innerHTML = '<div class="aiw-note aiw-note-' + (type || 'warning') + '">' + html + '</div>';
    }

    function openPanel() {
        panel.classList.add('aiw-open');
        panel.setAttribute('aria-hidden', 'false');
        launcher.classList.add('aiw-launcher-hidden');
        if (!historyLoaded) {
            loadHistory();
        }
        setTimeout(function () { inputEl.focus(); }, 150);
    }

    function closePanel() {
        panel.classList.remove('aiw-open');
        panel.setAttribute('aria-hidden', 'true');
        launcher.classList.remove('aiw-launcher-hidden');
    }

    function startNewConversation() {
        conversationId = null;
        messagesEl.querySelectorAll('.aiw-msg').forEach(function (el) { el.remove(); });
        statusEl.innerHTML = '';
        quickEl.style.display = 'none';
        if (welcomeEl) welcomeEl.style.display = 'block';
        hasMessages = false;
        setComposerEnabled(true);
        inputEl.value = '';
        inputEl.focus();
    }

    function bindNewChat() {
        const btn = document.getElementById('aiw-new-chat');
        if (btn) {
            btn.addEventListener('click', startNewConversation);
        }
    }

    function showTicketCreated(ticket) {
        showStatus(
            'Tiket <strong>' + escapeHtml(ticket.ticket_id) + '</strong> sudah dibuat. ' +
            '<a href="' + escapeHtml(ticket.url) + '">Lihat tiket</a>' +
            '<button type="button" class="aiw-btn-secondary" id="aiw-new-chat">Mulai percakapan baru</button>',
            'success'
        );
        setComposerEnabled(false);
        bindNewChat();
    }

    function showResolved() {
        showStatus(
            'Masalah Anda sudah ditandai selesai. Terima kasih!' +
            '<button type="button" class="aiw-btn-secondary" id="aiw-new-chat">Mulai percakapan baru</button>',
            'success'
        );
        setComposerEnabled(false);
        bindNewChat();
    }

    function renderTicketPreview(response) {
        const ticket = response.ticket || {};

        statusEl.innerHTML = ''
            + '<div class="aiw-ticket">'
            + '<div class="aiw-ticket-title">AI belum bisa menyelesaikan. Ringkasan tiket:</div>'
            + '<div class="aiw-ticket-row"><span>Subjek</span><strong>' + escapeHtml(ticket.subject || '-') + '</strong></div>'
            + '<div class="aiw-ticket-row"><span>Kategori</span><strong>' + escapeHtml(ticket.category || '-') + '</strong></div>'
            + '<div class="aiw-ticket-row"><span>Prioritas</span><strong>' + escapeHtml(ticket.priority || 'Medium') + '</strong></div>'
            + '<div class="aiw-ticket-row"><span>Diagnosis</span><strong>' + escapeHtml(response.diagnosis || '-') + '</strong></div>'
            + '<button type="button" id="aiw-create-ticket" class="aiw-btn-primary">Buat Tiket IT</button>'
            + '</div>';

        document.getElementById('aiw-create-ticket').addEventListener('click', createTicket);
    }

    function loadHistory() {
        historyLoaded = true;

        fetch(historyUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success || !data.conversation) {
                    return;
                }

                const conv = data.conversation;
                conversationId = conv.id;

                let lastAssistant = null;

                (conv.messages || []).forEach(function (msg) {
                    if (msg.message) {
                        appendMessage(msg.role, msg.message);
                        if (msg.role === 'assistant') {
                            lastAssistant = msg;
                        }
                    } else if (msg.metadata && msg.metadata.event === 'ticket_created') {
                        appendSystem('Tiket ' + (msg.metadata.ticket_ref || '') + ' dibuat dari percakapan ini.');
                    }
                });

                if (conv.ticket) {
                    showTicketCreated(conv.ticket);
                } else if (conv.status === 'resolved') {
                    showResolved();
                } else if (lastAssistant && lastAssistant.metadata && lastAssistant.metadata.create_ticket) {
                    renderTicketPreview({
                        ticket: lastAssistant.metadata.ticket || {},
                        diagnosis: lastAssistant.metadata.diagnosis || ''
                    });
                } else {
                    quickEl.style.display = hasMessages ? 'flex' : 'none';
                }
            })
            .catch(function () {
                historyLoaded = false;
            });
    }

    function sendMessage(text) {
        if (isProcessing) return;

        const message = (text !== undefined ? text : inputEl.value).trim();
        if (!message) return;

        appendMessage('user', message);
        inputEl.value = '';
        quickEl.style.display = 'none';
        statusEl.innerHTML = '';
        setLoading(true);

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
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                setLoading(false);

                if (!result.ok || !result.data.success) {
                    appendMessage('assistant', '❌ ' + (result.data.error || 'Terjadi kesalahan. Silakan coba lagi.'));
                    showStatus(result.data.error || 'Terjadi kesalahan.', 'danger');
                    return;
                }

                conversationId = result.data.conversation_id;
                handleAIResponse(result.data);
            })
            .catch(function () {
                setLoading(false);
                appendMessage('assistant', '❌ Gagal terhubung ke server. Coba lagi sebentar.');
                showStatus('Gagal terhubung ke server.', 'danger');
            });
    }

    function handleAIResponse(data) {
        const response = data.response || {};

        appendMessage('assistant', response.message || '');

        if (data.degraded) {
            quickEl.style.display = 'none';
            showStatus('Mode cadangan aktif. Saya memakai knowledge base lokal karena layanan AI sedang tidak tersedia.', 'warning');
            return;
        }

        if (response.resolved) {
            showResolved();
            return;
        }

        if (response.create_ticket) {
            quickEl.style.display = 'none';
            renderTicketPreview(response);
            return;
        }

        quickEl.style.display = 'flex';
        statusEl.innerHTML = '';
    }

    function createTicket() {
        if (isProcessing) return;

        setLoading(true);

        fetch(createUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ conversation_id: conversationId })
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                setLoading(false);

                if (!result.ok || !result.data.success) {
                    showStatus(result.data.error || 'Gagal membuat tiket.', 'danger');
                    return;
                }

                showStatus('Tiket <strong>' + escapeHtml(result.data.ticket_id) + '</strong> berhasil dibuat. Mengalihkan ke halaman tiket...', 'success');
                setComposerEnabled(false);
                setTimeout(function () { window.location.href = result.data.redirect; }, 1500);
            })
            .catch(function () {
                setLoading(false);
                showStatus('Gagal membuat tiket. Silakan coba lagi.', 'danger');
            });
    }

    launcher.addEventListener('click', function () {
        if (panel.classList.contains('aiw-open')) {
            closePanel();
        } else {
            openPanel();
        }
    });

    closeBtn.addEventListener('click', closePanel);
    sendBtn.addEventListener('click', function () { sendMessage(); });

    inputEl.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && panel.classList.contains('aiw-open')) {
            closePanel();
        }
    });

    quickEl.querySelectorAll('.aiw-quick-reply').forEach(function (button) {
        button.addEventListener('click', function () {
            sendMessage(button.dataset.text);
        });
    });
})();
</script>
@endif
