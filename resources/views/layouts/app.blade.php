<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Góc Sách - Mạng Xã Hội Đọc Sách')</title>

    {{-- [MỚI] THÊM FAVICON --}}
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300&family=Nunito+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                screens: {
                    'xs': '400px',
                    'sm': '640px',
                    'md': '768px',
                    'lg': '1024px',
                    'xl': '1280px',
                    '2xl': '1536px',
                },
                extend: {
                    colors: {
                        'brand-green': '#2A483A',
                        'brand-green-light': '#3E5F4E',
                        'brand-cream': '#FDFBF7',
                        'brand-beige': '#F2E8DC',
                        'brand-brown': '#8C6B4B',
                        'brand-accent': '#D4A373',
                    },
                    fontFamily: {
                        sans: ['Nunito Sans', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Prevent horizontal scroll on mobile */
        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        body {
            background-color: #FAF9F6;
            color: #333;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #E5E7EB;
            border-radius: 20px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #3E5F4E;
        }

        .hero-slider-wrapper {
            transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Safe area for iOS */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }
    </style>
</head>

<body class="font-sans antialiased flex flex-col min-h-screen selection:bg-brand-green selection:text-white">

    @include('partials.header')

    <div class="flex-grow">
        @yield('content')
    </div>

    @include('partials.footer')

    {{-- Report Modal (Available on all pages) --}}
    @include('partials.report-modal')

    @stack('scripts')

    {{-- Seasonal Decorations (Giáng sinh, Tết, Valentine, Halloween) --}}
    @include('partials.seasonal-decoration')

    {{-- AI Chatbox --}}
    @include('partials.chatbox')

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

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                closeMobileMenu();
            }
        });
    </script>
</body>

</html>