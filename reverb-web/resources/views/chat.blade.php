<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Chat - Laravel Reverb</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .chat-container {
            width: 100%;
            max-width: 600px;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .chat-header {
            background: #6366f1;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            background: #4ade80;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        #messages {
            height: 400px;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .message {
            background: #334155;
            border-radius: 12px;
            padding: 10px 14px;
            max-width: 80%;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message.mine {
            background: #6366f1;
            align-self: flex-end;
        }
        .message .meta {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .message.mine .meta { color: #c7d2fe; }
        .message .text { font-size: 14px; }
        .chat-input {
            display: flex;
            gap: 8px;
            padding: 16px;
            background: #0f172a;
        }
        .chat-input input[type="text"] {
            flex: 1;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 10px 14px;
            color: #e2e8f0;
            font-size: 14px;
            outline: none;
        }
        .chat-input input:focus { border-color: #6366f1; }
        .chat-input button {
            background: #6366f1;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .chat-input button:hover { background: #4f46e5; }
        #username-area {
            padding: 16px;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }
        #username-area input {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 8px 12px;
            color: #e2e8f0;
            font-size: 13px;
            width: 100%;
            outline: none;
        }
        #username-area label {
            font-size: 12px;
            color: #94a3b8;
            display: block;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <div class="status-dot"></div>
            Laravel Reverb Live Chat
        </div>

        <div id="username-area">
            <label>Username kamu:</label>
            <input type="text" id="username" placeholder="Masukkan username..." value="User_{{ rand(100,999) }}">
        </div>

        <div id="messages"></div>

        <div class="chat-input">
            <input type="text" id="message-input" placeholder="Ketik pesan..." autocomplete="off">
            <button onclick="sendMessage()">Kirim</button>
        </div>
    </div>

    <script>
        function addMessage(username, text, time, isMine) {
            const box = document.getElementById('messages');
            const div = document.createElement('div');
            div.className = 'message ' + (isMine ? 'mine' : '');
            div.innerHTML = `
                <div class="meta">${username} • ${time}</div>
                <div class="text">${escapeHtml(text)}</div>
            `;
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        }

        function escapeHtml(text) {
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function sendMessage() {
            const input = document.getElementById('message-input');
            const username = document.getElementById('username').value.trim() || 'Anonim';
            const message = input.value.trim();
            if (!message) return;
            input.value = '';

            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ message, username }),
            });
        }

        document.getElementById('message-input').addEventListener('keydown', e => {
            if (e.key === 'Enter') sendMessage();
        });

        // Tunggu Echo siap dulu sebelum subscribe
        const waitEcho = setInterval(() => {
            if (window.Echo) {
                clearInterval(waitEcho);
                window.Echo.channel('chat')
                    .listen('.message.sent', (data) => {
                        const myUsername = document.getElementById('username').value.trim() || 'Anonim';
                        const isMine = data.username === myUsername;
                        addMessage(data.username, data.message, data.timestamp, isMine);
                    });
            }
        }, 100);
    </script>
</body>
</html>