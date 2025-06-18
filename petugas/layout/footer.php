<?php
// File: petugas/layout/footer.php
?>
</div> <button id="chat-toggle-button" class="fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors z-40">
    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
</button>
<div id="chat-window" class="hidden fixed bottom-24 right-6 w-full max-w-sm h-full max-h-[600px] bg-slate-800 rounded-lg shadow-2xl border border-slate-700 flex flex-col z-40">
    <div class="flex justify-between items-center p-4 bg-slate-700 rounded-t-lg">
        <h3 class="font-semibold text-white">Chatbot Kepegawaian</h3>
        <button id="chat-close-button" class="text-gray-400 hover:text-white">&times;</button>
    </div>
    <div id="chat-messages" class="flex-1 p-4 space-y-4 overflow-y-auto">
        <div class="flex"><div class="bg-slate-700 p-3 rounded-lg max-w-xs"><p class="text-sm">Selamat datang! Ada yang bisa saya bantu terkait data kepegawaian?</p></div></div>
    </div>
    <div class="p-4 bg-slate-900/50 border-t border-slate-700">
        <form id="chat-form" class="flex items-center gap-2">
            <input type="text" id="chat-input" placeholder="Ketik pertanyaan Anda..." class="flex-1 bg-slate-700 border-slate-600 rounded-full py-2 px-4 text-white focus:ring-blue-500 focus:border-blue-500" autocomplete="off">
            <button type="submit" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg></button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ... (Script untuk sidebar dan user menu tetap sama) ...
    
    // --- SCRIPT CHATBOT DENGAN PENANGANAN ERROR YANG LEBIH BAIK ---
    const chatToggleButton = document.getElementById('chat-toggle-button');
    const chatWindow = document.getElementById('chat-window');
    const chatCloseButton = document.getElementById('chat-close-button');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    chatToggleButton.addEventListener('click', () => chatWindow.classList.toggle('hidden'));
    chatCloseButton.addEventListener('click', () => chatWindow.classList.add('hidden'));

    function addMessage(text, sender) {
        // ... (fungsi addMessage tetap sama) ...
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
        const messageBubble = document.createElement('div');
        messageBubble.className = `p-3 rounded-lg max-w-xs text-sm ${sender === 'user' ? 'bg-blue-600 text-white' : 'bg-slate-700'}`;
        messageBubble.innerHTML = text;
        messageDiv.appendChild(messageBubble);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;

        addMessage(message, 'user');
        chatInput.value = '';

        // Tampilkan "sedang mengetik..."
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'flex justify-start';
        typingIndicator.innerHTML = `<div class="p-3 rounded-lg max-w-xs text-sm bg-slate-700 italic">Bot sedang mengetik...</div>`;
        chatMessages.appendChild(typingIndicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        try {
            const response = await fetch('../api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });

            // Hapus "sedang mengetik..."
            typingIndicator.remove();
            
            if (!response.ok) {
                // Jika server merespon dengan error (spt 404, 500), tampilkan statusnya
                throw new Error(`Gagal menghubungi server. Status: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();
            
            if (data.error) {
                // Jika API kita mengembalikan pesan error yang sudah kita buat
                throw new Error(data.error);
            }
            
            const formattedReply = data.reply.replace(/\n/g, '<br>');
            addMessage(formattedReply, 'bot');

        } catch (error) {
            // Tangkap semua jenis error dan tampilkan pesannya
            if(typingIndicator.parentNode) {
                typingIndicator.remove();
            }
            addMessage(`Maaf, terjadi masalah: <br><em class="text-xs text-red-400/80">${error.message}</em>`, 'bot');
        }
    });
});
</script>
</body>
</html>