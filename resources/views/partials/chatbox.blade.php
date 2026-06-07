{{-- AI Chatbox Component --}}
<div id="chatbox-container" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9998]">

    {{-- Chat Window - Responsive: compact on mobile, larger on desktop --}}
    <div id="chatbox-window"
        class="hidden absolute bottom-16 right-0 w-[92vw] sm:w-[460px] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300 origin-bottom-right scale-95 opacity-0 flex flex-col"
        style="max-height: calc(100vh - 120px); height: clamp(400px, 70vh, 680px);">

        {{-- Header --}}
        <div
            class="bg-gradient-to-r from-brand-green to-emerald-600 text-white p-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-robot text-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm">Trợ lý Góc Bếp</h4>
                    <p class="text-[10px] text-white/80 flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> Đang hoạt động
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="clearChatHistory()" title="Xóa lịch sử"
                    class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
                <button onclick="toggleChatbox()"
                    class="w-10 h-10 sm:w-8 sm:h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition">
                    <i class="fas fa-times text-lg sm:text-base"></i>
                </button>
            </div>
        </div>

        {{-- Messages Area - Flexible height --}}
        <div id="chatbox-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 min-h-0">
            {{-- Welcome Message --}}
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-none px-4 py-3 shadow-sm max-w-[85%] sm:max-w-[80%]">
                    <p class="text-sm text-gray-700">Xin chào! Tôi là trợ lý AI của Góc Bếp. Tôi có thể giúp bạn tìm món ngon, gợi ý công thức theo nguyên liệu hoặc tư vấn dinh dưỡng. Bạn cần gì nào?</p>
                </div>
            </div>

            {{-- Quick Replies - Gợi ý nhanh --}}
            <div id="quick-replies" class="flex flex-wrap gap-2 mt-2">
                <button
                    class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200"
                    data-message="Gợi ý món ngon">
                    Gợi ý món ngon
                </button>
                <button
                    class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200"
                    data-message="Thống kê Góc Bếp">
                    Thống kê
                </button>
                <button
                    class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200"
                    data-message="Cách đăng công thức">
                    Cách đăng công thức
                </button>
                <button
                    class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200"
                    data-message="Tủ Lạnh Web là gì">
                    Tủ Lạnh Web
                </button>
            </div>
        </div>

        {{-- Typing Indicator (hidden by default) --}}
        <div id="typing-indicator" class="hidden px-4 py-2 bg-gray-50 flex-shrink-0">
            <div class="flex gap-3 items-center">
                <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div class="flex gap-1">
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white border-t border-gray-100 flex-shrink-0 safe-area-bottom">
            <form id="chatbox-form" class="flex gap-2">
                @csrf
                <input type="text" id="chatbox-input" placeholder="Nhập tin nhắn..." autocomplete="off"
                    class="flex-1 px-4 py-3 bg-gray-100 rounded-xl text-base sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/30 transition">
                <button type="submit" id="chatbox-send"
                    class="w-12 h-12 bg-brand-green hover:bg-emerald-700 text-white rounded-xl flex items-center justify-center transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 text-center mt-2">Powered by Gemini AI</p>
        </div>

    </div>

    {{-- Floating Buttons Container --}}
    <div id="floating-buttons-container" class="flex flex-col gap-2 sm:gap-3 items-center">
        {{-- Back to Top Button --}}
        <button id="back-to-top-btn"
            class="w-9 h-9 sm:w-11 sm:h-11 bg-brand-green text-white rounded-full shadow-lg opacity-0 invisible transform translate-y-4 transition-all duration-300 hover:bg-brand-accent hover:scale-110 hover:shadow-xl flex items-center justify-center group"
            aria-label="Back to top">
            <i class="fas fa-chevron-up text-xs sm:text-sm group-hover:animate-bounce"></i>
        </button>

        {{-- Mobile Toggle Button (chỉ hiện trên mobile) --}}
        <button id="mobile-float-toggle"
            class="md:hidden w-9 h-9 sm:w-11 sm:h-11 bg-gradient-to-br from-gray-600 to-gray-800 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300"
            onclick="toggleMobileFloatMenu()" aria-label="Toggle menu">
            <i id="mobile-float-icon" class="fas fa-ellipsis-v text-base sm:text-lg"></i>
        </button>

        {{-- Các nút social (ẩn trên mobile, hiện khi toggle) --}}
        <div id="mobile-float-buttons"
            class="flex flex-col gap-2 sm:gap-3 md:flex md:opacity-100 hidden opacity-0 transition-all duration-300">
            {{-- Facebook Messenger Button --}}
            <a href="https://www.facebook.com/groups/2047512272494576?locale=vi_VN" target="_blank" rel="noopener noreferrer"
                class="floating-btn w-9 h-9 sm:w-11 sm:h-11 bg-gradient-to-br from-[#00b2ff] to-[#006aff] text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-all duration-300 relative group"
                style="animation-delay: 0s; --glow-color: rgba(0, 132, 255, 0.5);"
                title="Di chuyển tới Cộng đồng Góc Bếp trên Facebook">
                <i class="fab fa-facebook text-lg sm:text-xl relative z-10"></i>
            </a>

            {{-- Zalo Button --}}
            <a href="https://zalo.me/g/onlvhshfpg452kfyzkgc" target="_blank" rel="noopener noreferrer"
                class="floating-btn w-9 h-9 sm:w-11 sm:h-11 rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-all duration-300 relative group overflow-hidden"
                style="animation-delay: 0.3s; --glow-color: rgba(0, 104, 255, 0.5);"
                title="Di chuyển tới Cộng đồng Góc Bếp trên Zalo">
                <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Icon_of_Zalo.svg/120px-Icon_of_Zalo.svg.png" alt="Zalo"
                    class="w-full h-full object-cover rounded-full relative z-10">
            </a>

            {{-- AI Chatbox Button --}}
            <button onclick="toggleChatbox()" id="chatbox-toggle"
                class="floating-btn w-9 h-9 sm:w-11 sm:h-11 bg-gradient-to-br from-brand-green to-emerald-600 text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-all duration-300 relative"
                style="animation-delay: 0.6s; --glow-color: rgba(34, 197, 94, 0.5);" title="Trò chuyện với Chatbot">
                <i id="chatbox-icon-open" class="fas fa-comments text-sm sm:text-base relative z-10"></i>
                <i id="chatbox-icon-close" class="fas fa-times text-sm sm:text-base hidden relative z-10"></i>
            </button>
        </div>
    </div>
</div>

<script>
    // Mobile floating buttons toggle
    let mobileFloatOpen = false;

    function toggleMobileFloatMenu() {
        const buttons = document.getElementById('mobile-float-buttons');
        const icon = document.getElementById('mobile-float-icon');
        const toggle = document.getElementById('mobile-float-toggle');

        mobileFloatOpen = !mobileFloatOpen;

        if (mobileFloatOpen) {
            buttons.classList.remove('hidden', 'opacity-0');
            buttons.classList.add('opacity-100');
            icon.classList.remove('fa-ellipsis-v');
            icon.classList.add('fa-times');
            toggle.classList.remove('from-gray-600', 'to-gray-800');
            toggle.classList.add('from-rose-500', 'to-pink-600');
        } else {
            buttons.classList.add('opacity-0');
            buttons.classList.remove('opacity-100');
            setTimeout(() => buttons.classList.add('hidden'), 300);
            icon.classList.add('fa-ellipsis-v');
            icon.classList.remove('fa-times');
            toggle.classList.add('from-gray-600', 'to-gray-800');
            toggle.classList.remove('from-rose-500', 'to-pink-600');
        }
    }
</script>

<style>
    /* Floating animation với glow effect */
    .floating-btn {
        animation: floatGlow 3s ease-in-out infinite;
        box-shadow: 0 4px 15px var(--glow-color, rgba(0, 0, 0, 0.2));
        transition: opacity 0.3s, transform 0.3s;
    }

    .floating-btn:hover {
        animation-play-state: paused;
        box-shadow: 0 8px 25px var(--glow-color, rgba(0, 0, 0, 0.3));
    }

    @keyframes floatGlow {

        0%,
        100% {
            transform: translateY(0);
            box-shadow: 0 4px 15px var(--glow-color, rgba(0, 0, 0, 0.2));
        }

        50% {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px var(--glow-color, rgba(0, 0, 0, 0.4));
        }
    }

    /* Back to top button transition */
    #back-to-top-btn {
        transition: opacity 0.3s, transform 0.3s, visibility 0.3s;
    }
</style>

<script>
    let chatHistory = [];
    window.isChatboxOpen = false;
    let historyLoaded = false;
    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    // Tải lịch sử chat từ database
    async function loadChatHistory() {
        if (!isLoggedIn || historyLoaded) return;

        try {
            const response = await fetch('{{ route("chatbot.history") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await response.json();

            if (data.success && data.messages.length > 0) {
                // Ẩn quick replies nếu có lịch sử
                document.getElementById('quick-replies').style.display = 'none';

                // Hiển thị các tin nhắn cũ
                data.messages.forEach(msg => {
                    addMessage(msg.content, msg.role === 'user', msg.created_at);
                    chatHistory.push({ role: msg.role, content: msg.content });
                });
            }
            historyLoaded = true;
        } catch (error) {
            console.error('Error loading chat history:', error);
        }
    }

    // Xác nhận xóa lịch sử
    async function clearChatHistory() {
        const result = await SwalConfirm('Xóa lịch sử?', 'Bạn có chắc muốn xóa toàn bộ lịch sử trò chuyện?', 'warning', 'Xóa ngay');
        if (!result.isConfirmed) return;

        try {
            if (isLoggedIn) {
                const response = await fetch('{{ route("chatbot.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                if (!data.success) throw new Error('Failed to clear server history');
            }

            // Reset UI for everyone
            chatHistory = [];
            historyLoaded = false;
            const container = document.getElementById('chatbox-messages');
            container.innerHTML = `
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-xs"></i>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tl-none px-4 py-3 shadow-sm max-w-[85%] sm:max-w-[80%]">
                        <p class="text-sm text-gray-700">Xin chào! Tôi là trợ lý AI của Góc Bếp. Tôi có thể giúp bạn tìm món ngon, gợi ý công thức theo nguyên liệu hoặc tư vấn dinh dưỡng. Bạn cần gì nào?</p>
                    </div>
                </div>
                <div id="quick-replies" class="flex flex-wrap gap-2 mt-2">
                    <button class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200" data-message="Gợi ý món ngon">Gợi ý món ngon</button>
                    <button class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200" data-message="Thống kê Góc Bếp">Thống kê</button>
                    <button class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200" data-message="Cách đăng công thức">Cách đăng công thức</button>
                    <button class="quick-reply-btn px-3 py-1.5 bg-white border border-brand-green text-brand-green rounded-full text-xs hover:bg-brand-green hover:text-white transition-all duration-200" data-message="Tủ Lạnh Web là gì">Tủ Lạnh Web</button>
                </div>
            `;
            // Re-attach quick reply listeners
            attachQuickReplyListeners();
        } catch (error) {
            console.error('Error clearing chat history:', error);
        }
    }

    function toggleChatbox() {
        const windowChat = document.getElementById('chatbox-window');
        const toggle = document.getElementById('chatbox-toggle');
        const iconOpen = document.getElementById('chatbox-icon-open');
        const iconClose = document.getElementById('chatbox-icon-close');
        const floatingBtns = document.getElementById('floating-buttons-container');
        window.isChatboxOpen = !window.isChatboxOpen;

        if (window.isChatboxOpen) {
            // Close header dropdowns if they exist
            if (typeof window.closeAllHeaderDropdowns === 'function') {
                window.closeAllHeaderDropdowns();
            }

            windowChat.classList.remove('hidden');
            setTimeout(() => {
                windowChat.classList.remove('scale-95', 'opacity-0');
                windowChat.classList.add('scale-100', 'opacity-100');
            }, 10);
            document.getElementById('chatbox-input').focus();
            // Switch to X icon
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
            // Hide other floating buttons (Messenger, Zalo, Back to top)
            if (floatingBtns) {
                floatingBtns.querySelectorAll('a, #back-to-top-btn').forEach(btn => {
                    btn.classList.add('opacity-0', 'pointer-events-none', 'scale-0');
                });
            }
            // Load lịch sử chat khi mở
            loadChatHistory();
        } else {
            windowChat.classList.remove('scale-100', 'opacity-100');
            windowChat.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                windowChat.classList.add('hidden');
            }, 300);
            // Switch back to comments icon
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
            // Show other floating buttons again
            if (floatingBtns) {
                floatingBtns.querySelectorAll('a').forEach(btn => {
                    btn.classList.remove('opacity-0', 'pointer-events-none', 'scale-0');
                });
                // Back to top button has its own visibility logic, just remove pointer-events
                const backBtn = document.getElementById('back-to-top-btn');
                if (backBtn) {
                    backBtn.classList.remove('pointer-events-none', 'scale-0');
                }
            }
        }
    }

    function addMessage(content, isUser = false, timestamp = null) {
        const container = document.getElementById('chatbox-messages');
        const timeStr = formatTime(timestamp);
        
        let textContent = content;
        let recipes = [];
        let sourceInfo = '';
        
        if (typeof content === 'object' && content !== null) {
            textContent = content.message || '';
            recipes = content.suggested_recipes || [];
            
            // Debugging or showing AI label
            if (!isUser && content.called_ai) {
                sourceInfo = '<span class="text-[10px] bg-brand-green/10 text-brand-green px-1.5 py-0.5 rounded ml-2">AI</span>';
            }
        }

        let recipeCardsHtml = '';
        if (recipes.length > 0) {
            recipeCardsHtml = '<div class="mt-3 flex flex-col gap-2">';
            recipes.forEach(r => {
                const timeStr = r.cooking_time ? `<span class="text-xs text-gray-500"><i class="far fa-clock mr-1"></i>${r.cooking_time}p</span>` : '';
                const diffStr = r.difficulty === 'easy' ? 'Dễ' : (r.difficulty === 'hard' ? 'Khó' : 'TB');
                const diffHtml = `<span class="text-xs text-brand-green"><i class="fas fa-layer-group mr-1"></i>${diffStr}</span>`;
                const imgUrl = r.thumbnail ? r.thumbnail : '/images/default-recipe.jpg';
                
                recipeCardsHtml += `
                    <div class="flex items-center gap-3 p-2 border border-gray-100 rounded-lg hover:bg-gray-50 transition cursor-pointer" onclick="window.location.href='/recipes/${r.slug}'">
                        <img src="${imgUrl}" alt="${escapeHtml(r.title)}" class="w-12 h-12 rounded object-cover flex-shrink-0" onerror="this.src='/images/default-recipe.jpg'">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-sm font-semibold text-gray-800 truncate">${escapeHtml(r.title)}</h5>
                            <div class="flex items-center gap-2 mt-1">
                                ${timeStr} ${diffHtml}
                            </div>
                        </div>
                        <div class="w-6 h-6 bg-brand-green/10 text-brand-green rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </div>
                    </div>
                `;
            });
            recipeCardsHtml += '</div>';
        }

        const messageHtml = isUser
            ? `<div class="flex gap-3 justify-end">
                    <div class="max-w-[80%]">
                        <div class="bg-brand-green text-white rounded-2xl rounded-tr-none px-4 py-3 shadow-sm">
                            <p class="text-sm">${escapeHtml(textContent)}</p>
                        </div>
                        <p class="text-[10px] text-gray-400 text-right mt-1">${timeStr}</p>
                    </div>
               </div>`
            : `<div class="flex gap-3">
                    <div class="w-8 h-8 bg-brand-green rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-xs"></i>
                    </div>
                    <div class="max-w-[80%]">
                        <div class="bg-white rounded-2xl rounded-tl-none px-4 py-3 shadow-sm border border-gray-100">
                            <div class="text-sm text-gray-700 space-y-2">${formatMessage(textContent)}</div>
                            ${recipeCardsHtml}
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-[10px] text-gray-400">${timeStr}${sourceInfo}</p>
                        </div>
                    </div>
               </div>`;

        container.insertAdjacentHTML('beforeend', messageHtml);
        container.scrollTop = container.scrollHeight;
    }

    // Format thời gian hiển thị
    function formatTime(timestamp) {
        if (!timestamp) {
            // Tin nhắn mới - lấy thời gian hiện tại
            const now = new Date();
            return now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        }
        // Tin nhắn từ lịch sử
        const date = new Date(timestamp);
        const now = new Date();
        const isToday = date.toDateString() === now.toDateString();

        if (isToday) {
            return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        } else {
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }) + ' ' +
                date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatMessage(text) {
        if (!text) return '';
        // Convert markdown-like formatting
        let formatted = escapeHtml(text)
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
            
        // Auto-link URLs
        const urlRegex = /(https?:\/\/[^\s<]+)/g;
        return formatted.replace(urlRegex, '<a href="$1" class="text-blue-500 hover:text-blue-700 underline font-medium">$1</a>');
    }

    function showTyping() {
        document.getElementById('typing-indicator').classList.remove('hidden');
        document.getElementById('chatbox-messages').scrollTop = document.getElementById('chatbox-messages').scrollHeight;
    }

    function hideTyping() {
        document.getElementById('typing-indicator').classList.add('hidden');
    }

    document.getElementById('chatbox-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const input = document.getElementById('chatbox-input');
        const sendBtn = document.getElementById('chatbox-send');
        const message = input.value.trim();

        if (!message) return;

        // Display user message
        addMessage(message, true);
        chatHistory.push({ role: 'user', content: message });

        // Clear input and disable
        input.value = '';
        sendBtn.disabled = true;
        showTyping();

        try {
            const response = await fetch('{{ route("chatbot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory.slice(-10) // Send last 10 messages for context
                })
            });

            const data = await response.json();
            hideTyping();

            if (data.success) {
                addMessage(data.reply, false);
                chatHistory.push({ role: 'assistant', content: data.reply });
            } else {
                addMessage(data.reply || 'Xin lỗi, có lỗi xảy ra!', false);
            }
        } catch (error) {
            hideTyping();
            addMessage('Không thể kết nối. Vui lòng thử lại! 🙏', false);
            console.error('Chatbot error:', error);
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    });

    // Quick Reply buttons - wrapped in a function for re-attachment
    function attachQuickReplyListeners() {
        document.querySelectorAll('.quick-reply-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const message = this.getAttribute('data-message');
                document.getElementById('chatbox-input').value = message;
                document.getElementById('chatbox-form').dispatchEvent(new Event('submit'));
                // Ẩn quick replies sau khi click
                const quickReplies = document.getElementById('quick-replies');
                if (quickReplies) quickReplies.style.display = 'none';
            });
        });
    }
    // Initial attachment
    attachQuickReplyListeners();

    // Close on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && window.isChatboxOpen) {
            toggleChatbox();
        }
    });

    // Back to Top Button
    const backToTopBtn = document.getElementById('back-to-top-btn');
    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.remove('opacity-0', 'invisible', 'translate-y-4');
                backToTopBtn.classList.add('opacity-100', 'visible', 'translate-y-0');
            } else {
                backToTopBtn.classList.add('opacity-0', 'invisible', 'translate-y-4');
                backToTopBtn.classList.remove('opacity-100', 'visible', 'translate-y-0');
            }
        });

        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
</script>

<style>
    #chatbox-window.scale-100 {
        transform: scale(1);
    }

    #chatbox-window.scale-95 {
        transform: scale(0.95);
    }

    #chatbox-window.opacity-100 {
        opacity: 1;
    }

    #chatbox-window.opacity-0 {
        opacity: 0;
    }
</style>

