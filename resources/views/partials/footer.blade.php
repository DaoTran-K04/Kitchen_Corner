<div class="relative mt-10 sm:mt-16">
    <footer class="bg-brand-green text-white pt-16 sm:pt-24 pb-8 sm:pb-10 relative overflow-hidden rounded-t-[40px] md:rounded-t-[80px] shadow-[0_-10px_40px_rgba(155,34,38,0.15)]">
        {{-- Beautiful Background Image with soft dark red overlay --}}
        <div class="absolute inset-0 z-0">
            <img loading="lazy" src="{{ asset('images/auth/kitchen_1.png') }}" class="w-full h-full object-cover opacity-50 mix-blend-overlay" alt="Footer Background">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-brand-green/90 to-brand-green/95"></div>
        </div>

        <div class="container mx-auto px-6 lg:px-12 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 mb-16">
                
                {{-- Column 1: Brand Info --}}
                <div class="space-y-6 text-center md:text-left flex flex-col items-center md:items-start group">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-xl mb-2 transform group-hover:scale-105 group-hover:rotate-3 transition-all duration-500">
                        <span class="text-3xl filter drop-shadow-md">🍳</span>
                    </div>
                    <h3 class="font-serif font-bold text-3xl tracking-wide">Góc Bếp</h3>
                    <p class="text-brand-cream/80 text-sm leading-relaxed max-w-xs font-light">
                        Nơi giao thoa giữa niềm đam mê ẩm thực và sự ấm áp của gia đình. Cùng nhau khám phá những công thức tuyệt hảo mỗi ngày.
                    </p>
                    <div class="flex gap-4 pt-4">
                        <a href="https://www.facebook.com/profile.php?id=61585413759981" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center hover:bg-brand-accent hover:border-brand-accent hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://youtu.be/mKptA96QMZ0" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center hover:bg-brand-accent hover:border-brand-accent hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center hover:bg-brand-accent hover:border-brand-accent hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-tiktok text-sm"></i>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div class="md:pl-8">
                    <h4 class="font-serif font-bold text-xl mb-8 relative inline-block">
                        Khám Phá
                        <span class="absolute -bottom-3 left-0 w-1/2 h-1 bg-brand-accent rounded-full"></span>
                    </h4>
                    <ul class="space-y-4 text-sm text-brand-cream/80 font-medium">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group/link">
                                <i class="fas fa-chevron-right text-[10px] text-brand-accent opacity-0 group-hover/link:opacity-100 transition-opacity"></i> Trang chủ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.about') }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group/link">
                                <i class="fas fa-chevron-right text-[10px] text-brand-accent opacity-0 group-hover/link:opacity-100 transition-opacity"></i> Về chúng tôi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.terms') }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group/link">
                                <i class="fas fa-chevron-right text-[10px] text-brand-accent opacity-0 group-hover/link:opacity-100 transition-opacity"></i> Điều khoản dịch vụ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.privacy') }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group/link">
                                <i class="fas fa-chevron-right text-[10px] text-brand-accent opacity-0 group-hover/link:opacity-100 transition-opacity"></i> Chính sách bảo mật
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('page.contact') }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group/link">
                                <i class="fas fa-chevron-right text-[10px] text-brand-accent opacity-0 group-hover/link:opacity-100 transition-opacity"></i> Liên hệ
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Column 3: Categories --}}
                <div>
                    <h4 class="font-serif font-bold text-xl mb-8 relative inline-block">
                        Chủ Đề Yêu Thích
                        <span class="absolute -bottom-3 left-0 w-1/2 h-1 bg-brand-accent rounded-full"></span>
                    </h4>
                    <ul class="space-y-4 text-sm text-brand-cream/80 font-medium">
                        <li>
                            <a href="{{ route('recipes.list', ['category' => 'mon-chinh']) }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/5"><i class="fas fa-utensils text-[11px] text-amber-200"></i></div> Món chính
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recipes.list', ['category' => 'mon-an-chay']) }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/5"><i class="fas fa-leaf text-[11px] text-green-300"></i></div> Món ăn chay
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recipes.list', ['category' => 'mon-trang-mieng']) }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/5"><i class="fas fa-cake-candles text-[11px] text-pink-300"></i></div> Món tráng miệng
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recipes.list', ['category' => 'mi-bun-pho']) }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/5"><i class="fas fa-bowl-food text-[11px] text-orange-200"></i></div> Mì - Bún - Phở
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('recipes.list', ['category' => 'mon-khai-vi']) }}" class="hover:text-white hover:translate-x-2 transition-all duration-300 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center border border-white/5"><i class="fas fa-pepper-hot text-[11px] text-red-300"></i></div> Món khai vị
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Column 4: Newsletter Box --}}
                <div>
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-7 border border-white/20 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-brand-accent/40 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700 pointer-events-none"></div>
                        <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-brand-green/60 rounded-full blur-2xl pointer-events-none"></div>
                        
                        <h4 class="font-serif font-bold text-xl mb-2 relative z-10 text-white">Bảng Tin Góc Bếp</h4>
                        <p class="text-[13px] text-brand-cream/90 mb-6 leading-relaxed relative z-10 font-light">
                            Đăng ký ngay để nhận những công thức độc quyền và mẹo nhà bếp hữu ích mỗi tuần từ cộng đồng của chúng tôi.
                        </p>
                        
                        <form id="newsletter-form" class="relative z-10 flex flex-col gap-3">
                            @csrf
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-white/50">
                                    <i class="fas fa-envelope text-sm"></i>
                                </span>
                                <input type="email" id="newsletter-email" name="email" placeholder="Email của bạn..." 
                                    class="w-full pl-11 pr-4 py-3 bg-black/20 border border-white/10 rounded-xl text-sm focus:outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent text-white placeholder-white/40 transition-all font-light" required>
                            </div>
                            <button type="submit" id="newsletter-btn" class="w-full bg-brand-accent hover:bg-[#d05003] text-white font-bold py-3.5 rounded-xl text-sm transition-all shadow-[0_4px_14px_0_rgba(232,93,4,0.39)] hover:shadow-[0_6px_20px_rgba(232,93,4,0.23)] flex justify-center items-center gap-2 group/btn transform hover:-translate-y-0.5">
                                <span id="newsletter-text">Nhận Công Thức Ngay</span>
                                <i class="fas fa-paper-plane text-xs transform group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform" id="newsletter-icon"></i>
                            </button>
                            <p id="newsletter-message" class="text-xs text-center mt-2 hidden font-medium"></p>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Bottom Footer Bar --}}
            <div class="border-t border-white/10 pt-8 mt-4 flex flex-col md:flex-row justify-between items-center gap-4 text-[13px] font-light text-white/60">
                <p class="text-center md:text-left flex items-center justify-center gap-1.5 flex-wrap">
                    © {{ date('Y') }} Góc Bếp. Thiết kế với <i class="fas fa-heart text-brand-accent animate-pulse mx-0.5"></i> dành cho người yêu nấu nướng.
                </p>
                <div class="flex gap-6 justify-center">
                    <a href="{{ route('page.privacy') }}" class="hover:text-white hover:underline transition">Privacy Policy</a>
                    <a href="{{ route('page.terms') }}" class="hover:text-white hover:underline transition">Terms of Service</a>
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
    const originalIcon = icon.className;
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
            message.className = 'text-xs text-center text-green-400 mt-2 font-medium';
            message.textContent = data.message;
            document.getElementById('newsletter-email').value = '';
        } else {
            message.className = 'text-xs text-center text-red-400 mt-2 font-medium';
            message.textContent = data.message;
        }
    } catch (error) {
        message.classList.remove('hidden');
        message.className = 'text-xs text-center text-red-400 mt-2 font-medium';
        message.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
    } finally {
        // Reset button
        btn.disabled = false;
        icon.className = originalIcon;
        text.textContent = 'Nhận Công Thức Ngay';
    }
});
</script>

