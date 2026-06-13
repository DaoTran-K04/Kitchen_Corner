{{-- Bottom Navigation Bar for Mobile --}}
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-[90] pb-[env(safe-area-inset-bottom,0)] transition-transform duration-300" id="mobile-bottom-nav">
    <div class="flex items-center justify-around h-16 sm:h-18 px-2">
        
        {{-- Trang Chủ --}}
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('home') ? 'text-brand-green font-bold' : 'text-gray-400 hover:text-brand-green' }} transition-colors relative group">
            <i class="fas fa-home text-xl mb-1 group-hover:-translate-y-1 transition-transform"></i>
            <span class="text-[10px]">Trang chủ</span>
            @if(request()->routeIs('home'))
                <span class="absolute top-1 right-3 w-2 h-2 bg-brand-accent rounded-full border border-white"></span>
            @endif
        </a>

        {{-- Công Thức --}}
        <a href="{{ route('recipes.list') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('recipes.*') && !request()->routeIs('recipes.smart-search') ? 'text-brand-green font-bold' : 'text-gray-400 hover:text-brand-green' }} transition-colors relative group">
            <i class="fas fa-book-open text-xl mb-1 group-hover:-translate-y-1 transition-transform"></i>
            <span class="text-[10px]">Công thức</span>
        </a>

        {{-- Nút trung tâm (Thêm Công Thức / Tủ lạnh web) --}}
        <div class="relative -top-5 flex justify-center w-16">
            @auth
                <a href="{{ route('recipes.smart-search') }}" class="w-14 h-14 bg-gradient-to-tr from-brand-green to-brand-accent rounded-full flex items-center justify-center shadow-lg shadow-brand-green/30 text-white transform transition hover:scale-110 hover:rotate-12 border-4 border-white">
                    <i class="fas fa-magic text-xl"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="w-14 h-14 bg-gradient-to-tr from-gray-400 to-gray-500 rounded-full flex items-center justify-center shadow-lg text-white transform transition hover:scale-110 border-4 border-white" title="Đăng nhập để dùng tính năng AI">
                    <i class="fas fa-lock text-xl"></i>
                </a>
            @endauth
        </div>

        {{-- Tạp Chí --}}
        <a href="{{ route('articles.index') }}" class="flex flex-col items-center justify-center w-16 h-full {{ request()->routeIs('articles.*') ? 'text-brand-green font-bold' : 'text-gray-400 hover:text-brand-green' }} transition-colors relative group">
            <i class="fas fa-newspaper text-xl mb-1 group-hover:-translate-y-1 transition-transform"></i>
            <span class="text-[10px]">Tạp chí</span>
        </a>

        {{-- Cá nhân / Thêm (Mở mobile menu cũ) --}}
        <button type="button" id="bottom-nav-menu-btn" onclick="if(typeof openMobileMenu === 'function') openMobileMenu();" class="flex flex-col items-center justify-center w-16 h-full text-gray-400 hover:text-brand-green transition-colors relative group">
            <div class="relative pointer-events-none">
                @auth
                    @include('partials.user-avatar-with-frame', ['user' => Auth::user(), 'size' => 'w-6 h-6 mb-1', 'showNameplate' => false])
                @else
                    <i class="fas fa-bars text-xl mb-1 group-hover:-translate-y-1 transition-transform"></i>
                @endauth
            </div>
            <span class="text-[10px]">Thêm</span>
        </button>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ẩn Bottom Nav khi scroll xuống, hiện khi scroll lên
        let lastScrollY = window.scrollY;
        const bottomNav = document.getElementById('mobile-bottom-nav');

        window.addEventListener('scroll', () => {
            if (!bottomNav) return;
            const currentScrollY = window.scrollY;
            
            // Nếu scroll xuống > 100px và không ở top
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                bottomNav.style.transform = 'translateY(100%)';
            } else {
                bottomNav.style.transform = 'translateY(0)';
            }
            lastScrollY = currentScrollY;
        }, { passive: true });
    });
</script>
