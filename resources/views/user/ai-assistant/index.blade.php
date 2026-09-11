@extends('layouts.usermaster')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="mr-2">🤖</span> IT Assistant
                    <span class="ml-auto text-muted">Hello, {{ Auth::guard('customer')->user()->firstname }} {{ Auth::guard('customer')->user()->lastname }} 👋</span>
                </div>
                <div class="card-body">
                    <!-- Chat Container -->
                    <div id="chat-container" style="height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9;">
                        @if(isset($lastConversation))
                            @foreach($lastConversation->messages as $msg)
                                <div class="message {{ $msg->role == 'user' ? 'text-right' : '' }}">
                                    <div class="bubble {{ $msg->role == 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="display:inline-block; padding:8px 12px; border-radius:18px; margin:5px 0; max-width:80%;">
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Input Area -->
                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text" id="user-message" class="form-control" placeholder="Describe your IT problem..." autofocus>
                            <div class="input-group-append">
                                <button id="send-btn" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Press Enter to send</small>
                    </div>

                    <!-- Popular Problems -->
                    <div class="mt-3">
                        <p class="mb-1">Popular problems:</p>
                        @foreach(['Monitor', 'Wifi', 'Laptop', 'Lan', 'Printer', 'Office', 'Backup Data', 'PC', 'Loker', 'Windows'] as $problem)
                            <span class="badge badge-secondary p-2 mr-1 problem-chip" style="cursor:pointer;">{{ $problem }}</span>
                        @endforeach
                    </div>

                    <!-- Loading & Status -->
                    <div id="loading" class="text-center mt-2" style="display:none;">
                        <span class="spinner-border spinner-border-sm" role="status"></span> AI is thinking...
                    </div>
                    <div id="status-area" class="mt-3"></div>
                    <div id="error-area" class="mt-3" style="display:none;">
                        <div class="alert alert-danger">
                            <span id="error-message"></span>
                            <button id="retry-btn" class="btn btn-sm btn-warning ml-2">Retry</button>
                            <a href="{{ route('client.ticket') }}" class="btn btn-sm btn-secondary ml-1">Create Ticket</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM loaded');

    // DOM Elements
    const chatContainer = document.getElementById('chat-container');
    const userInput = document.getElementById('user-message');
    const sendBtn = document.getElementById('send-btn');
    const loading = document.getElementById('loading');
    const statusArea = document.getElementById('status-area');
    const errorArea = document.getElementById('error-area');
    const errorMessage = document.getElementById('error-message');
    const retryBtn = document.getElementById('retry-btn');

    // State
    let conversationId = {{ $lastConversation->id ?? 'null' }};
    let resolved = false;
    let lastMessage = '';
    let isProcessing = false;

    // Routes
    const chatUrl = '{{ route("customer.ai-assistant.chat") }}';
    const createUrl = '{{ route("customer.ai-assistant.create-ticket") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    console.log('Chat URL:', chatUrl);
    console.log('Create URL:', createUrl);
    console.log('Conversation ID:', conversationId);

    // Scroll to bottom
    function scrollToBottom() {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Append message
    function appendMessage(role, message) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${role === 'user' ? 'text-right' : ''}`;
        const bubble = document.createElement('div');
        bubble.className = `bubble ${role === 'user' ? 'bg-primary text-white' : 'bg-light'}`;
        bubble.style.display = 'inline-block';
        bubble.style.padding = '8px 12px';
        bubble.style.borderRadius = '18px';
        bubble.style.margin = '5px 0';
        bubble.style.maxWidth = '80%';
        bubble.innerHTML = message.replace(/\n/g, '<br>');
        msgDiv.appendChild(bubble);
        chatContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    // Show/Hide loading
    function showLoading(show) {
        loading.style.display = show ? 'block' : 'none';
        sendBtn.disabled = show;
        userInput.disabled = show;
        isProcessing = show;
    }

    // Show/Hide error
    function showError(message) {
        errorMessage.textContent = message;
        errorArea.style.display = 'block';
    }

    function hideError() {
        errorArea.style.display = 'none';
    }

    // Send message
    function sendMessage() {
        if (isProcessing) return;

        const message = userInput.value.trim();
        console.log('Send clicked, message:', message);

        if (!message || resolved) {
            console.log('Message empty or resolved, returning');
            return;
        }

        lastMessage = message;
        hideError();
        appendMessage('user', message);
        userInput.value = '';
        showLoading(true);

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                message: message,
                conversation_id: conversationId
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            showLoading(false);
            console.log('Response data:', data);
            if (data.success) {
                conversationId = data.conversation_id;
                handleAIResponse(data);
            } else {
                appendMessage('assistant', data.error || 'Terjadi kesalahan. Silakan buat tiket manual.');
                showError(data.error || 'Unknown error');
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Fetch error:', error);
            appendMessage('assistant', '❌ Gagal terhubung ke AI. Coba lagi nanti.');
            showError('Connection error: ' + error.message);
        });
    }

    // Handle AI response
    function handleAIResponse(data) {
        const response = data.response;
        appendMessage('assistant', response.message);

        if (response.resolved) {
            statusArea.innerHTML = `
                <div class="alert alert-success">
                    ✅ Problem Solved!<br>
                    No ticket created.
                </div>
                <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            `;
            resolved = true;
        } else if (response.create_ticket && response.ticket) {
            statusArea.innerHTML = `
                <div class="alert alert-warning">
                    <h5>Ticket Preview</h5>
                    <hr>
                    <p><strong>Subject:</strong> ${response.ticket.subject}</p>
                    <p><strong>Category:</strong> ${response.ticket.category || 'N/A'}</p>
                    <p><strong>Priority:</strong> ${response.ticket.priority || 'Medium'}</p>
                    <p><strong>Description:</strong><br>${response.ticket.description.replace(/\n/g, '<br>')}</p>
                    <p><strong>Diagnosis:</strong> ${response.diagnosis || 'N/A'}</p>
                    <p><strong>Recommendation:</strong> ${response.ticket.recommendation || 'N/A'}</p>
                    <hr>
                    <button id="create-ticket-btn" class="btn btn-primary">Create IT Ticket</button>
                    <button id="continue-troubleshooting-btn" class="btn btn-secondary">Continue Troubleshooting</button>
                </div>
            `;
            document.getElementById('create-ticket-btn')?.addEventListener('click', createTicket);
            document.getElementById('continue-troubleshooting-btn')?.addEventListener('click', function() {
                statusArea.innerHTML = '';
                resolved = false;
            });
        } else {
            statusArea.innerHTML = '';
        }
    }

    // Create ticket
    function createTicket() {
        fetch(createUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ conversation_id: conversationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusArea.innerHTML = `<div class="alert alert-info">✅ Ticket #${data.ticket_id} created. Redirecting...</div>`;
                setTimeout(() => window.location.href = data.redirect, 2000);
            } else {
                alert('Gagal membuat tiket.');
                showError('Create ticket error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Create ticket error:', err);
            alert('Error: ' + err.message);
            showError('Create ticket error: ' + err.message);
        });
    }

    // Retry last message
    retryBtn.addEventListener('click', function() {
        if (lastMessage) {
            userInput.value = lastMessage;
            sendMessage();
        }
    });

    // Event listeners
    sendBtn.addEventListener('click', function(e) {
        console.log('Tombol Send diklik');
        sendMessage();
    });

    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            console.log('Enter ditekan');
            sendMessage();
        }
    });

    // Popular problem chips
    document.querySelectorAll('.problem-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            userInput.value = this.textContent.trim();
            sendMessage();
        });
    });

    // Auto-scroll to bottom on load
    scrollToBottom();

    console.log('✅ Event listeners terpasang');
});
</script>
@endsection