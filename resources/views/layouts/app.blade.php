<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Góc Bếp - Chia Sẻ Công Thức & Dinh Dưỡng')</title>
    <meta name="description" content="Khám phá hàng nghìn công thức nấu ăn ngon, mẹo vặt nhà bếp và kiến thức dinh dưỡng tại Góc Bếp. Nơi kết nối những người yêu ẩm thực.">
    <meta name="keywords" content="công thức nấu ăn, món ngon mỗi ngày, nấu ăn ngon, thực đơn dinh dưỡng, ẩm thực việt nam, góc bếp">
    <meta name="author" content="Trần Hoàng Đạo">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">

    {{-- Preconnect for faster CDN & Image handshake --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Non-blocking Font Awesome: load as print then swap to all --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"></noscript>

    {{-- Non-blocking Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300&family=Nunito+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300&family=Nunito+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet"></noscript>

    @php
        $siteTheme = \Illuminate\Support\Facades\Cache::rememberForever('active_theme', function () {
            $setting = \App\Models\Setting::where('key', 'active_theme')->first();
            return $setting ? $setting->value : 'auto';
        });
        if ($siteTheme === 'auto') {
            $month = now()->month; $day = now()->day;
            if ($month == 12 && $day >= 20 && $day <= 26) $siteTheme = 'christmas';
            elseif (($month == 1 && $day >= 15) || ($month == 2 && $day <= 15)) $siteTheme = 'tet';
            elseif ($month == 2 && $day >= 12 && $day <= 30) $siteTheme = 'valentine';
            elseif (($month == 10 && $day >= 25) || ($month == 11 && $day <= 2)) $siteTheme = 'halloween';
            else $siteTheme = 'default';
        }
        $colors = [
            'default' => ['primary' => '#9B2226', 'light' => '#AE2012', 'accent' => '#E9D8A6', 'bg1' => 'rgba(254, 243, 220, 0.60)', 'bg2' => 'rgba(250, 232, 200, 0.55)', 'url' => '/images/admin-bg.png'],
            'valentine' => ['primary' => '#D81B60', 'light' => '#E91E63', 'accent' => '#F48FB1', 'bg1' => 'rgba(252, 228, 236, 0.85)', 'bg2' => 'rgba(248, 187, 208, 0.80)', 'url' => 'https://www.transparenttextures.com/patterns/hearts.png'],
            'tet' => ['primary' => '#B71C1C', 'light' => '#D32F2F', 'accent' => '#FFC107', 'bg1' => 'rgba(255, 248, 225, 0.90)', 'bg2' => 'rgba(255, 236, 179, 0.85)', 'url' => '/images/admin-bg.png'],
            'halloween' => ['primary' => '#E65100', 'light' => '#EF6C00', 'accent' => '#6A1B9A', 'bg1' => 'rgba(255, 243, 224, 0.90)', 'bg2' => 'rgba(255, 224, 178, 0.85)', 'url' => 'https://www.transparenttextures.com/patterns/cobweb.png'],
            'christmas' => ['primary' => '#1B5E20', 'light' => '#2E7D32', 'accent' => '#C62828', 'bg1' => 'rgba(232, 245, 233, 0.90)', 'bg2' => 'rgba(200, 230, 201, 0.85)', 'url' => 'https://www.transparenttextures.com/patterns/stardust.png'],
        ];
        $cColors = $colors[$siteTheme] ?? $colors['default'];
    @endphp

    <script>
        tailwind.config = {
            theme: {
                screens: {
                    'xs': '400px', 'sm': '640px', 'md': '768px', 'lg': '1024px', 'xl': '1280px', '2xl': '1536px',
                },
                extend: {
                    colors: {
                        'brand-green':       '{{ $cColors["primary"] }}',
                        'brand-green-light': '{{ $cColors["light"] }}',
                        'brand-accent':      '{{ $cColors["accent"] }}',
                        'brand-cream':       '#FAFAF8',
                        'brand-beige':       '#FDF0D5',
                        'brand-brown':       '#CA6702',
                        'brand-text':        '#1A1A1A',
                        'brand-muted':       '#6B7280',
                    },
                    fontFamily: {
                        sans:    ['Nunito Sans', 'sans-serif'],
                        serif:   ['Playfair Display', 'Merriweather', 'serif'],
                    },
                    boxShadow: {
                        'soft':   '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card':   '0 10px 30px rgba(0, 0, 0, 0.06)',
                        'deep':   '0 25px 50px rgba(0, 0, 0, 0.15)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Tối ưu hóa render cuộn trang cực nhanh cho thiết bị di động/máy tính */
        .content-lazy {
            content-visibility: auto;
            contain-intrinsic-size: 1px 1000px;
        }

        /* Prevent horizontal scroll on mobile */
        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
            scroll-behavior: smooth;
        }

        body {
            /* Dynamic active theme background mapping */
            background-image:
                linear-gradient(135deg,
                    {{ $cColors["bg1"] }} 0%,
                    {{ $cColors["bg2"] }} 50%,
                    {{ $cColors["bg1"] }} 100%),
                url('{{ $cColors["url"] }}');
            background-size: cover, auto;
            background-position: center, center;
            background-attachment: fixed, fixed;
            color: #1A1A1A;
        }

        /* Typography Đặc biệt */
        .font-serif { font-family: 'Playfair Display', 'Merriweather', serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        /* Line Clamp */
        .line-clamp-1 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 1; }
        .line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .line-clamp-3 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }

        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px) saturate(200%);
            -webkit-backdrop-filter: blur(16px) saturate(200%);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .glass-dark {
            background: rgba(26, 51, 42, 0.85);
            backdrop-filter: blur(12px) saturate(160%);
            -webkit-backdrop-filter: blur(12px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
        }

        /* Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Entry Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(42, 72, 58, 0.1);
            border-radius: 20px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #2A483A;
        }

        .hero-slider-wrapper {
            transition: transform 0.8s cubic-bezier(0.65, 0, 0.35, 1);
        }

        /* Safe area for iOS */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* ===== PERFORMANCE OPTIMIZATIONS ===== */

        /* Ảnh lazy load: fade-in mượt khi load xong */
        img[loading="lazy"] {
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        img[loading="lazy"].loaded {
            opacity: 1;
        }

        /* Section dưới fold: trình duyệt không render cho đến khi gần viewport */
        .lazy-section {
            content-visibility: auto;
            contain-intrinsic-size: 1px 600px;
        }

        /* Card hover optimization: dùng GPU layer riêng */
        .recipe-card-item {
            will-change: transform;
            transform: translateZ(0);
        }

        /* Tránh layout shift khi ảnh load */
        .img-aspect-4-3 {
            aspect-ratio: 4 / 3;
            background: #f3f4f6;
        }

        /* Page transition: trang mới fade-in */
        @keyframes page-enter {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-content {
            animation: page-enter 0.3s ease-out;
        }
    </style>

    {{-- SweetAlert2: defer to not block initial render --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script>
        // Override sau khi Swal load xong
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal === 'undefined') return;

            window.alert = function(message) {
                Swal.fire({
                    title: 'Góc Bếp thông báo',
                    text: message,
                    icon: 'info',
                    confirmButtonColor: '#9B2226',
                    confirmButtonText: 'Đã hiểu',
                    customClass: {
                        container: 'z-[9999]',
                        title: 'font-serif text-2xl text-red-900',
                        popup: 'rounded-[2rem] shadow-2xl border-0',
                        confirmButton: 'rounded-full px-8 py-2.5 font-bold shadow hover:shadow-lg transition-all'
                    }
                });
            };

            window.requireLogin = function(customMessage = 'Bạn cần đăng nhập để sử dụng tính năng này!') {
                Swal.fire({
                    title: 'Vui lòng đăng nhập',
                    text: customMessage,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#9B2226',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Đăng nhập',
                    cancelButtonText: 'Để sau',
                    customClass: {
                        container: 'z-[9999]',
                        title: 'font-serif text-2xl text-red-900',
                        popup: 'rounded-[2rem] shadow-2xl border-0',
                        confirmButton: 'rounded-full px-6 py-2.5 font-bold shadow hover:shadow-lg transition-all',
                        cancelButton: 'rounded-full px-6 py-2.5 font-bold shadow transition-all'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            };

            window.SwalConfirm = function(title, text, icon = 'warning', confirmButtonText = 'Đồng ý', cancelButtonText = 'Hủy') {
                return Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#9B2226',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: cancelButtonText,
                    reverseButtons: true,
                    customClass: {
                        container: 'z-[9999]',
                        title: 'font-serif text-2xl text-red-900',
                        popup: 'rounded-[2rem] shadow-2xl border-0',
                        confirmButton: 'rounded-full px-6 py-2.5 font-bold shadow hover:shadow-lg transition-all',
                        cancelButton: 'rounded-full px-6 py-2.5 font-bold shadow transition-all'
                    }
                });
            };
        });
    </script>
</head>

<body class="font-sans antialiased flex flex-col min-h-screen selection:bg-brand-green selection:text-white">

    @include('partials.header')

    <div class="flex-grow pb-20 lg:pb-0 {{ (Request::is('/') || Request::is('cong-thuc*') || Request::is('tac-gia*') || Request::is('tim-kiem-nguyen-lieu*') || Request::is('mon-an*') || Request::is('login') || Request::is('register') || Request::is('tap-chi*') || Request::is('thu-thach*')) ? '' : 'pt-20 lg:pt-24' }}">



        @yield('content')
    </div>

    <div class="content-lazy lazy-section">
        @include('partials.footer')
    </div>

    {{-- Report Modal (Available on all pages) --}}
    @include('partials.report-modal')

    @stack('scripts')

    {{-- Seasonal Decorations --}}
    @include('partials.seasonal-decoration')

    {{-- AI Chatbox --}}
    @include('partials.chatbox')

    {{-- Bottom Navigation (Mobile Only) --}}
    @include('partials.bottom-nav')

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuPanel = document.getElementById('mobile-menu-panel');
        const closeMobileMenuBtn = document.getElementById('close-mobile-menu');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');

        function openMobileMenu() {
            if (mobileMenu && mobileMenuPanel) {
                mobileMenu.classList.remove('hidden');
                // Trigger animation
                setTimeout(() => {
                    mobileMenuPanel.classList.remove('translate-x-full');
                }, 10);
                // Change icon to X
                if (mobileMenuIcon) {
                    mobileMenuIcon.classList.remove('fa-bars');
                    mobileMenuIcon.classList.add('fa-times');
                }
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileMenu() {
            if (mobileMenu && mobileMenuPanel) {
                mobileMenuPanel.classList.add('translate-x-full');
                // Wait for animation to complete before hiding
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
                // Change icon back to bars
                if (mobileMenuIcon) {
                    mobileMenuIcon.classList.remove('fa-times');
                    mobileMenuIcon.classList.add('fa-bars');
                }
                // Restore body scroll
                document.body.style.overflow = '';
            }
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openMobileMenu);
        }

        if (closeMobileMenuBtn) {
            closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
        }

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', closeMobileMenu);
        }

        // ===== REVEAL ON SCROLL (dùng IntersectionObserver, hiệu quả hơn scroll event) =====
        (function() {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        revealObserver.unobserve(entry.target); // chỉ trigger 1 lần
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -80px 0px' });

            document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
        })();

        // ===== LAZY IMAGE FADE-IN =====
        // Tự động thêm class 'loaded' khi ảnh lazy load xong → fade-in mượt
        (function() {
            const imgObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    const img = entry.target;
                    if (img.complete) {
                        img.classList.add('loaded');
                    } else {
                        img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
                        img.addEventListener('error', () => img.classList.add('loaded'), { once: true });
                    }
                    obs.unobserve(img);
                });
            }, { rootMargin: '200px' });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => imgObserver.observe(img));
        })();

        // ===== PAGE TRANSITION =====
        document.addEventListener('DOMContentLoaded', () => {
            const main = document.querySelector('.flex-grow');
            if (main) main.classList.add('page-content');
        });
    </script>

    {{-- Instant.page: Pre-fetch on hover for instant navigation --}}
    <script src="https://instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipfGQRjygabvOoY28BmgrrU946y1DUFTD7D54D7nV1yuHDU9zoEwUJve" crossorigin="anonymous"></script>
</body>

</html>
