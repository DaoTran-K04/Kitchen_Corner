<div class="relative mt-12 sm:mt-20">
    <div class="absolute top-0 left-0 w-full overflow-hidden leading-none z-0 transform -translate-y-full pointer-events-none">
        <svg class="relative block w-full h-10 sm:h-12 md:h-16 text-[#2C3E36]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="currentColor"></path>
        </svg>
    </div>

    <footer class="bg-[#2C3E36] text-white pt-10 sm:pt-16 pb-6 sm:pb-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        
        <div class="absolute top-0 right-0 w-64 sm:w-96 h-64 sm:h-96 bg-brand-green-light/20 rounded-full blur-[100px] -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-brand-accent/10 rounded-full blur-[80px] -ml-10 -mb-10 pointer-events-none"></div>

        <div class="container mx-auto px-4 relative z-10">
            {{-- Main Grid - 1 col mobile, 2 cols tablet, 4 cols desktop --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 mb-8 sm:mb-12">
                
                {{-- Logo & Social - Full width on smallest screens --}}
                <div class="col-span-2 sm:col-span-2 md:col-span-1 space-y-4 text-center sm:text-left">
                    <div class="flex flex-col items-center sm:items-start">
                        <div class="mb-2"><i class="fas fa-book-open text-3xl sm:text-4xl text-[#E9EDC9]"></i></div>
                        <h3 class="font-bold text-base sm:text-lg leading-tight">Góc Sách Review</h3>
                    </div>
                    <p class="text-gray-300 text-xs sm:text-sm leading-relaxed max-w-xs mx-auto sm:mx-0">
                        Nơi kết nối những tâm hồn yêu sách. Chia sẻ cảm nhận, lan tỏa tri thức.
                    </p>
                    <div class="flex gap-3 pt-2 justify-center sm:justify-start">
                        <a href="https://www.facebook.com/profile.php?id=61585413759981" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-8 sm:h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D4A373] hover:text-white transition transform hover:-translate-y-1"><i class="fab fa-facebook-f text-sm sm:text-xs"></i></a>
                        <a href="https://youtu.be/mKptA96QMZ0" target="_blank" rel="noopener noreferrer" class="w-9 h-9 sm:w-8 sm:h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#D4A373] hover:text-white transition transform hover:-translate-y-1"><i class="fab fa-youtube text-sm sm:text-xs"></i></a>
                    </div>
                </div>

                {{-- Liên Kết Nhanh --}}
                <div>
                    <h4 class="font-bold mb-3 sm:mb-6 text-white text-sm sm:text-lg">Liên Kết</h4>
                    <ul class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-300">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-angle-right text-xs"></i> Trang chủ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.about') }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-angle-right text-xs"></i> Về chúng tôi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.terms') }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-angle-right text-xs"></i> Điều khoản
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.privacy') }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-angle-right text-xs"></i> Bảo mật
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.contact') }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-angle-right text-xs"></i> Liên hệ
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Thể Loại --}}
                <div>
                    <h4 class="font-bold mb-3 sm:mb-6 text-white text-sm sm:text-lg">Thể Loại</h4>
                    <ul class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-300">
                        <li>
                            <a href="{{ route('books.list', ['category' => 'tieu-thuyet']) }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-book text-xs opacity-50"></i> Tiểu thuyết
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.list', ['category' => 'kinh-te']) }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-book text-xs opacity-50"></i> Kinh tế
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.list', ['category' => 'tam-ly-ky-nang-song']) }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-book text-xs opacity-50"></i> Tâm lý
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.list', ['category' => 'van-hoc-nuoc-ngoai']) }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-book text-xs opacity-50"></i> Văn học
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('books.list', ['category' => 'thieu-nhi']) }}" class="hover:text-white transition flex items-center gap-1.5 sm:gap-2">
                                <i class="fas fa-book text-xs opacity-50"></i> Thiếu nhi
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-span-2 sm:col-span-2 md:col-span-1">
                    <h4 class="font-bold mb-3 sm:mb-6 text-white text-sm sm:text-lg text-center sm:text-left">Đăng Ký Nhận Tin</h4>
                    <p class="text-xs text-gray-300 mb-3 sm:mb-4 leading-relaxed text-center sm:text-left">Nhận thông báo sách mới, sự kiện và bài viết hay.</p>
                    <form id="newsletter-form" class="flex flex-col sm:flex-col gap-2 sm:gap-3">
                        @csrf
                        <input type="email" id="newsletter-email" name="email" placeholder="Email của bạn..." 
                            class="w-full px-4 py-2.5 sm:py-3 bg-white/10 border border-white/20 rounded-lg text-sm focus:outline-none focus:border-[#D4A373] text-white placeholder-gray-400 transition" required>
                        <button type="submit" id="newsletter-btn" class="bg-[#8C6B4B] hover:bg-[#6e5338] text-white font-bold px-4 py-2.5 sm:py-3 rounded-lg text-xs sm:text-sm transition shadow-lg flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane text-xs" id="newsletter-icon"></i>
                            <span id="newsletter-text">Đăng Ký</span>
                        </button>
                        <p id="newsletter-message" class="text-xs text-center sm:text-left hidden"></p>
                    </form>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-gray-600/50 pt-6 sm:pt-8 flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4 text-[10px] sm:text-xs text-gray-400">
                <p class="text-center sm:text-left">© {{ date('Y') }} Góc Sách Review. All rights reserved.</p>
                <div class="flex gap-4 sm:gap-6 flex-wrap justify-center">
                    <a href="{{ route('page.privacy') }}" class="hover:text-white transition">Privacy</a>
                    <a href="{{ route('page.terms') }}" class="hover:text-white transition">Terms</a>
                    <a href="#" class="hover:text-white transition">Cookies</a>
                </div>
            </div>
        </div>
    </footer>
</div>

<script>
document.getElementById('newsletter-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('newsletter-email').value;
    const btn = document.getElementById('newsletter-btn');
    const icon = document.getElementById('newsletter-icon');
    const text = document.getElementById('newsletter-text');
    const message = document.getElementById('newsletter-message');
    const csrfToken = document.querySelector('#newsletter-form input[name="_token"]').value;
    
    // Disable button and show loading
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin text-xs';
    text.textContent = 'Đang xử lý...';
    message.classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        message.classList.remove('hidden');
        if (data.success) {
            message.className = 'text-xs text-center sm:text-left text-green-400';
            message.textContent = data.message;
            document.getElementById('newsletter-email').value = '';
        } else {
            message.className = 'text-xs text-center sm:text-left text-red-400';
            message.textContent = data.message;
        }
    } catch (error) {
        message.classList.remove('hidden');
        message.className = 'text-xs text-center sm:text-left text-red-400';
        message.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
    } finally {
        // Reset button
        btn.disabled = false;
        icon.className = 'fas fa-paper-plane text-xs';
        text.textContent = 'Đăng Ký';
    }
});
</script>
