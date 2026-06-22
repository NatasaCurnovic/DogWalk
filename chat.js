"use strict";

document.addEventListener('DOMContentLoaded', function () {

    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput  = document.getElementById('messageInput');

    const requestId = chatBox.dataset.requestId;
    const userId = parseInt(chatBox.dataset.userId, 10);

    let lastMessageCount = -1; // forces first render

    function formatTime(dateStr) {
        const d = new Date(dateStr.replace(' ', 'T'));
        const hh = String(d.getHours()).padStart(2, '0');
        const mm = String(d.getMinutes()).padStart(2, '0');
        return hh + ':' + mm;
    }

    function escHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function renderMessages(messages) {
        if (messages.length === lastMessageCount) return; // nothing new, skip re-render
        lastMessageCount = messages.length;

        if (messages.length === 0) {
            chatBox.innerHTML = '<div class="text-center text-muted small py-3">Još nema poruka. Pošaljite prvu!</div>';
            return;
        }

        const html = messages.map(function (m) {
            const isMine = parseInt(m.sender_id, 10) === userId;
            const cls= isMine ? 'mine' : 'theirs';
            return '<div class="msg-bubble ' + cls + '">'
                 + escHtml(m.body)
                 + '<span class="msg-time">' + formatTime(m.created_at) + '</span>'
                 + '</div>';
        }).join('');

        chatBox.innerHTML = html;
        chatBox.scrollTop = chatBox.scrollHeight; // auto-scroll to bottom
    }

    function loadMessages() {
        fetch('get_messages.php?request_id=' + encodeURIComponent(requestId))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.messages) {
                    renderMessages(data.messages);
                }
            })
            .catch(function () {
            });
    }

    function sendMessage(text) {
        fetch('send_message.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ request_id: requestId, body: text })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                loadMessages();
            } else {
                alert('Poruka nije poslata. Pokušajte ponovo.');
            }
        })
        .catch(function () {
            alert('Greška pri slanju poruke.');
        });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const text = messageInput.value.trim();
        if (!text) return;

        messageInput.value = '';
        sendMessage(text);
    });

    loadMessages();
    setInterval(loadMessages, 3000);
});
