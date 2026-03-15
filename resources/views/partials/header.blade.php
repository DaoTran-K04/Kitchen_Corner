{{-- Top Bar --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="bg-brand-green text-white/80 text-xs py-2 hidden md:block border-b border-white/10">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex gap-6">
            <a href="tel:19001234" class="hover:text-brand-accent cursor-pointer transition flex items-center">
                <i class="fas fa-phone-alt mr-2"></i> Hotline: 1900 1234
            </a>
            <a href="mailto:contact@gocsach.com"
                class="hover:text-brand-accent cursor-pointer transition flex items-center">
                <i class="fas fa-envelope mr-2"></i> contact@gocsach.com
            </a>
        </div>
        <div class="flex gap-4 items-center">
            <button onclick="openModal('helpModal')" class="hover:text-white transition focus:outline-none">Trợ
                giúp</button>
            <span class="text-white/20">|</span>
            <button onclick="openModal('rulesModal')" class="hover:text-white transition focus:outline-none">Quy tắc
                cộng đồng</button>

            <div class="flex gap-3 ml-4">
                <a href="https://www.facebook.com/profile.php?id=61585413759981" target="_blank" rel="noopener noreferrer" class="hover:text-brand-accent transition"><i class="fab fa-facebook-f"></i></a>
                <a href="https://youtu.be/mKptA96QMZ0" target="_blank" rel="noopener noreferrer" class="hover:text-brand-accent transition"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
</div>

{{-- Header --}}
<header id="main-header"
    class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm transition-all duration-300">
    <div class="container mx-auto px-4 py-3">
        <div class="flex flex-wrap justify-between items-center gap-4">

            {{-- Logo --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div
                        class="w-10 h-10 bg-brand-green text-white rounded-lg flex items-center justify-center shadow-md transform group-hover:rotate-6 transition-transform duration-300">
                        <i class="fas fa-book-reader text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold font-serif text-brand-green leading-none tracking-tight">GÓC
                            SÁCH</span>
                        <span class="text-[10px] text-gray-500 uppercase tracking-widest font-semibold">Review &
                            Share</span>
                    </div>
                </a>
            </div>

            {{-- Search Bar - Ẩn trên trang Tìm kiếm vì đã có form riêng --}}
            @if(!request()->routeIs('books.search'))
                <div class="hidden md:flex flex-1 max-w-2xl px-8 relative z-40">
                    <form action="{{ route('books.list') }}" method="GET" class="relative w-full flex items-center"
                        id="header-search-form">
                        
                        {{-- Hidden Input cho Category --}}
                        <input type="hidden" name="categories[]" id="header-category-input" value="">

                        {{-- Dropdown Danh Mục --}}
                        <div class="absolute left-0 pl-1 z-50 group pb-4 -mb-4">
                            <div
                                class="flex items-center cursor-pointer bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-full transition relative z-20">
                                <span id="header-category-label" class="text-gray-600 text-xs font-bold mr-1 max-w-[80px] truncate">Danh mục</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-500 transition-transform group-hover:rotate-180"></i>
                            </div>
                            <div
                                class="dropdown-menu dropdown-bridge absolute top-full left-0 mt-0 bg-white rounded-xl shadow-2xl border border-gray-100 p-4 min-w-[600px] max-w-[800px] z-10">
                                <div class="grid grid-rows-[repeat(10,minmax(0,1fr))] grid-flow-col gap-x-8 gap-y-2">
                                    <div onclick="selectHeaderCategory('', 'Danh mục')"
                                        class="text-sm text-gray-600 hover:text-brand-green hover:font-bold truncate flex items-center cursor-pointer py-0.5">
                                        <i class="fas fa-caret-right text-gray-300 mr-2 text-xs"></i> Tất cả
                                    </div>
                                    @if(isset($menuCategories))
                                        @foreach($menuCategories as $cat)
                                            <div onclick="selectHeaderCategory('{{ $cat->name }}', '{{ $cat->name }}')"
                                                class="text-sm text-gray-600 hover:text-brand-green hover:font-bold truncate block py-0.5 cursor-pointer">{{ $cat->name }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Input tìm kiếm --}}
                        <input type="text" id="header-search-input" name="keyword" value="{{ request('keyword') }}"
                            autocomplete="off" placeholder="Nhập tên sách, tác giả..."
                            class="w-full bg-gray-50 border border-gray-200 hover:border-brand-green/30 focus:border-brand-green/50 rounded-full py-2.5 pl-40 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-brand-green/10 transition-all text-gray-700 placeholder-gray-400 shadow-inner">

                        <button type="submit"
                            class="absolute right-2 top-1.5 w-8 h-8 bg-brand-green text-white rounded-full flex items-center justify-center hover:bg-brand-accent transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </form>
                    
                    <script>
                        function selectHeaderCategory(value, label) {
                            document.getElementById('header-category-input').value = value;
                            document.getElementById('header-category-label').innerText = label;
                        }
                    </script>

                    {{-- Kết quả Ajax (Đặt NGOÀI form để tránh form submit) --}}
                    <div id="header-search-results"
                        class="absolute top-full left-0 w-full bg-white shadow-xl rounded-xl mt-2 hidden z-[60] overflow-hidden border border-gray-100 max-h-[400px] overflow-y-auto">
                        {{-- JS sẽ render kết quả vào đây --}}
                    </div>
                </div>
            @endif

            {{-- User & Notification Actions --}}
            <div class="flex items-center gap-3 md:gap-5">
                @auth
                    {{-- Notification Bell - Click to Open --}}
                    <div class="relative" id="notification-dropdown-container">
                        <button type="button" id="notification-dropdown-trigger"
                            class="text-gray-500 transition relative p-2 focus:outline-none rounded-xl hover:bg-green-100 hover:text-brand-green hover:shadow-[0_0_15px_rgba(62,95,78,0.3)]">
                            <i class="far fa-bell text-xl"></i>
                            <span id="notification-badge"
                                class="{{ Auth::user()->unreadNotifications->count() > 0 ? '' : 'hidden' }} absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white animate-bounce">
                                <span id="notification-count">{{ Auth::user()->unreadNotifications->count() }}</span>
                            </span>
                        </button>
                        <div id="notification-dropdown-menu"
                            class="hidden fixed sm:absolute left-0 sm:left-auto right-0 top-16 sm:top-full sm:mt-2 w-full sm:w-80 bg-white rounded-none sm:rounded-xl shadow-xl border-t sm:border border-gray-100 overflow-hidden animate-fade-in z-[100] sm:origin-top-right">
                            <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-700">Thông báo</span>
                                <button type="button" onclick="markAllNotificationsAsRead()" id="mark-all-read-btn"
                                    class="{{ Auth::user()->unreadNotifications->count() > 0 ? '' : 'hidden' }} text-[10px] text-blue-500 hover:underline cursor-pointer">Đánh
                                    dấu đã đọc</button>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto">
                                @forelse(Auth::user()->notifications as $notification)
                                    @php
                                        // Check both Class Name (DB type column) and Data Type (json data)
                                        $dbType = $notification->type;
                                        $dataType = $notification->data['type'] ?? '';
                                        
                                        $systemClasses = [
                                            'App\Notifications\NewReportNotification',
                                            'App\Notifications\NewBookRequestNotification', 
                                            'App\Notifications\BookApprovedNotification',
                                            'App\Notifications\AdminNewPostNotification'
                                        ];
                                        
                                        $systemTypes = ['new_report', 'book_request', 'book_approved', 'admin_new_post'];
                                        
                                        $isSystemNotification = in_array($dbType, $systemClasses) || in_array($dataType, $systemTypes);
                                        
                                        // Map Icon & Color based on dataType (preferred) or infer from dbType
                                        $type = $dataType ?: match($dbType) {
                                            'App\Notifications\NewReportNotification' => 'new_report',
                                            'App\Notifications\NewBookRequestNotification' => 'book_request',
                                            'App\Notifications\BookApprovedNotification' => 'book_approved',
                                            'App\Notifications\AdminNewPostNotification' => 'admin_new_post',
                                            default => ''
                                        };

                                        $icon = 'fas fa-bell';
                                        $iconColor = 'text-yellow-600';
                                        $title = '';
                                        
                                        switch($type) {
                                            case 'new_report':
                                                $icon = 'fas fa-flag';
                                                $title = 'Báo cáo mới';
                                                break;
                                            case 'book_request':
                                                $icon = 'fas fa-book';
                                                $title = 'Gợi ý sách mới';
                                                break;
                                            case 'book_approved':
                                                $icon = 'fas fa-check-circle';
                                                $iconColor = 'text-green-600';
                                                $title = 'Sách được duyệt';
                                                break;
                                            case 'admin_new_post':
                                                $icon = 'fas fa-file-contract';
                                                $iconColor = 'text-red-600';
                                                $title = 'Bài đăng mới ';
                                                break;
                                            case 'new_book_follower':
                                                $icon = 'fas fa-book-open';
                                                $title = 'Sách mới từ người dùng';
                                                break;
                                        }
                                        
                                        $bgColor = str_contains($iconColor, 'red') ? 'bg-red-100' : 'bg-green-100';
                                    @endphp

                                    <a href="{{ route('notification.read', $notification->id) }}"
                                        class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 {{ $notification->read_at ? 'opacity-60 grayscale-[0.5]' : 'bg-blue-50/30' }}">
                                        {{-- DEBUG removed --}}
                                        <div class="flex-shrink-0 mt-1">
                                            @if($isSystemNotification)
                                                <div class="w-8 h-8 rounded-full {{ $bgColor }} flex items-center justify-center">
                                                    <i class="{{ $icon }} {{ $iconColor }} text-sm"></i>
                                                </div>
                                            @else
                                                <img src="{{ $notification->data['avatar'] ?? 'https://ui-avatars.com/api/?name=User' }}"
                                                    class="w-8 h-8 rounded-full border border-gray-100 object-cover">
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            @if($isSystemNotification)
                                                <p class="text-sm font-bold text-gray-800">{{ $title }}</p>
                                                <p class="text-xs text-gray-600 line-clamp-2 mt-0.5">
                                                    {{ $notification->data['message'] ?? '' }}</p>
                                            @else
                                                <p class="text-sm text-gray-700 line-clamp-2">
                                                    @php
                                                        $displayName = $notification->data['uploader_name'] ?? ($notification->data['user_name'] ?? '');
                                                    @endphp
                                                    
                                                    @if($displayName && $displayName !== 'Ai đó')
                                                        <span class="font-bold text-gray-900">{{ $displayName }}</span>
                                                    @endif
                                                    
                                                    {{ $notification->data['message'] ?? 'đã tương tác với bạn' }}
                                                    <span
                                                        class="font-bold block text-xs text-gray-500 italic mt-0.5">"{{ Str::limit($notification->data['post_title'] ?? ($notification->data['book_title'] ?? ''), 50) }}"</span>
                                                </p>
                                            @endif
                                            <p class="text-[10px] text-gray-400 mt-1 flex items-center"><i
                                                    class="far fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>

                                        @if(!$notification->read_at)
                                            <div class="w-2 h-2 bg-brand-green rounded-full mt-2 shrink-0"></div>
                                        @endif
                                    </a>
                                @empty
                                    <div class="text-center py-8 text-gray-400" id="empty-notification">
                                        <i class="far fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                                        <p class="text-xs">Không có thông báo mới</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const notifTrigger = document.getElementById('notification-dropdown-trigger');
                        const notifMenu = document.getElementById('notification-dropdown-menu');
                        const notifContainer = document.getElementById('notification-dropdown-container');

                        if (notifTrigger && notifMenu) {
                            notifTrigger.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isOpen = !notifMenu.classList.contains('hidden');
                                
                                // Close user dropdown if open
                                const userMenu = document.getElementById('user-dropdown-menu');
                                if (userMenu) userMenu.classList.add('hidden');
                                
                                if (isOpen) {
                                    notifMenu.classList.add('hidden');
                                } else {
                                    notifMenu.classList.remove('hidden');
                                }
                            });

                            document.addEventListener('click', function(e) {
                                if (!notifContainer.contains(e.target)) {
                                    notifMenu.classList.add('hidden');
                                }
                            });

                            document.addEventListener('keydown', function(e) {
                                if (e.key === 'Escape') {
                                    notifMenu.classList.add('hidden');
                                }
                            });
                        }
                    });
                    </script>

                    {{-- User Dropdown - Click to Open --}}
                    <div class="relative z-50" id="user-dropdown-container">
                        <button type="button" id="user-dropdown-trigger"
                            class="flex items-center gap-2 focus:outline-none py-1 cursor-pointer relative z-20 rounded-xl px-2 transition-all duration-300 hover:bg-green-100 hover:shadow-[0_0_15px_rgba(62,95,78,0.3)]">
                            @include('partials.user-avatar-with-frame', [
                                'user' => Auth::user(),
                                'size' => 'w-12 h-12',
                                'avatarSize' => 'w-9 h-9'
                            ])
                            <div class="hidden lg:flex flex-col items-start">
                                <span
                                    class="text-xs font-bold text-gray-700 truncate max-w-[80px]">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ Auth::user()->role == 'admin' ? 'Quản trị viên' : 'Thành viên' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 ml-1 transition-transform duration-300" id="user-dropdown-arrow"></i>
                        </button>

                        <div id="user-dropdown-menu"
                            class="hidden absolute right-0 top-full mt-2 w-60 bg-white rounded-xl shadow-xl border border-gray-100 py-2 animate-fade-in origin-top-right z-10">
                            <div class="px-4 py-3 border-b border-gray-50 bg-gray-50/50 rounded-t-xl mb-1">
                                <p class="text-xs text-gray-400 uppercase tracking-wider font-bold">Tài khoản</p>
                                <p class="text-sm font-bold text-brand-green truncate">{{ Auth::user()->email }}</p>
                            </div>

                            @if(Auth::user()->role == 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center px-4 py-2.5 text-red-600 bg-red-50 hover:bg-red-100 transition font-bold border-l-2 border-transparent hover:border-red-600 mx-2 rounded-md mb-1">
                                    <i class="fas fa-tachometer-alt w-5 mr-2"></i> Quản Trị Viên
                                </a>
                            @endif

                            <a href="{{ route('profile') }}"
                                class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-brand-green transition mx-2 rounded-md">
                                <i class="fas fa-user-circle w-5 mr-2 text-gray-400"></i> Hồ sơ cá nhân
                            </a>

                            <a href="{{ route('change.password') }}"
                                class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 hover:text-brand-green transition mx-2 rounded-md">
                                <i class="fas fa-key w-5 mr-2 text-gray-400"></i> Đổi mật khẩu
                            </a>

                            <div class="border-t border-gray-100 my-1 pt-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left flex items-center px-4 py-2.5 text-gray-500 hover:bg-red-50 hover:text-red-600 transition font-medium mx-2 rounded-md">
                                    <i class="fas fa-sign-out-alt w-5 mr-2"></i> Đăng Xuất
                                </button>
                            </form>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const trigger = document.getElementById('user-dropdown-trigger');
                        const menu = document.getElementById('user-dropdown-menu');
                        const arrow = document.getElementById('user-dropdown-arrow');
                        const container = document.getElementById('user-dropdown-container');

                        if (trigger && menu) {
                            // Toggle dropdown on click
                            trigger.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                const isOpen = !menu.classList.contains('hidden');
                                
                                // Close notification dropdown if open
                                const notifMenu = document.getElementById('notification-dropdown-menu');
                                if (notifMenu) notifMenu.classList.add('hidden');
                                
                                if (isOpen) {
                                    menu.classList.add('hidden');
                                    arrow.classList.remove('rotate-180');
                                } else {
                                    menu.classList.remove('hidden');
                                    arrow.classList.add('rotate-180');
                                }
                            });

                            // Close dropdown when clicking outside
                            document.addEventListener('click', function(e) {
                                if (!container.contains(e.target)) {
                                    menu.classList.add('hidden');
                                    arrow.classList.remove('rotate-180');
                                }
                            });

                            // Close on ESC key
                            document.addEventListener('keydown', function(e) {
                                if (e.key === 'Escape') {
                                    menu.classList.add('hidden');
                                    arrow.classList.remove('rotate-180');
                                }
                            });
                        }
                    });
                    </script>
                @else
                    {{-- Guest - Hide on very small screens, show in mobile menu instead --}}
                    <div class="hidden xs:flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('login') }}"
                            class="bg-brand-green text-white px-3 py-1.5 sm:px-5 sm:py-2.5 rounded-full hover:bg-[#16271f] transition font-bold shadow-md text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2">
                            <i class="fas fa-sign-in-alt text-xs"></i> <span class="hidden sm:inline">Đăng Nhập</span><span class="sm:hidden">Đăng nhập</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="hidden sm:flex items-center gap-2 text-gray-600 hover:text-brand-green font-bold text-sm px-3 py-2 rounded-lg border border-gray-200 hover:border-brand-green hover:bg-gray-50 transition">
                            <i class="fas fa-user-plus text-xs"></i> <span>Đăng Ký</span>
                        </a>
                    </div>
                @endauth

                {{-- Mobile Menu Button --}}
                <button id="mobile-menu-btn"
                    class="md:hidden p-2 text-gray-600 hover:text-brand-green hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-bars text-xl" id="mobile-menu-icon"></i>
                </button>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="hidden md:flex justify-center mt-2 border-t border-gray-100 pt-3">
            <nav class="flex items-center gap-8 text-sm font-semibold text-gray-500">
                <a href="{{ route('home') }}"
                    class="hover:text-brand-green hover:border-b-2 hover:border-brand-green pb-3 -mb-3.5 transition-all {{ request()->routeIs('home') ? 'text-brand-green border-b-2 border-brand-green' : '' }}">Trang
                    Chủ</a>
                <a href="{{ route('books.list') }}"
                    class="hover:text-brand-green hover:border-b-2 hover:border-brand-green pb-3 -mb-3.5 transition-all {{ request()->routeIs('books.list') ? 'text-brand-green border-b-2 border-brand-green' : '' }}">Danh
                    Sách</a>
                <a href="{{ route('books.search') }}"
                    class="hover:text-brand-green hover:border-b-2 hover:border-brand-green pb-3 -mb-3.5 transition-all {{ request()->routeIs('books.search') ? 'text-brand-green border-b-2 border-brand-green' : '' }}">Review
                    Hay</a>
                <a href="{{ route('authors.index') }}"
                    class="hover:text-brand-green hover:border-b-2 hover:border-brand-green pb-3 -mb-3.5 transition-all {{ request()->routeIs('authors.*') ? 'text-brand-green border-b-2 border-brand-green' : '' }}">Tác
                    Giả</a>
                <a href="{{ route('challenges.index') }}"
                    class="relative flex items-center gap-1.5 px-3 py-1.5 -my-1 rounded-full bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold text-sm shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 group {{ request()->routeIs('challenges.index') ? 'ring-2 ring-yellow-400 ring-offset-2' : '' }}">
                    <i class="fas fa-fire text-yellow-300 group-hover:animate-pulse"></i>
                    <span>Thử Thách</span>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-yellow-400 rounded-full animate-ping"></span>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-yellow-400 rounded-full"></span>
                </a>
            </nav>
        </div>
    </div>
</header>

{{-- Mobile Menu Overlay --}}
<div id="mobile-menu" class="fixed inset-0 z-[60] hidden">
    {{-- Backdrop --}}
    <div id="mobile-menu-backdrop" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Menu Panel --}}
    <div id="mobile-menu-panel"
        class="absolute top-0 right-0 w-80 max-w-[85vw] h-full bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out">
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-100 bg-brand-green text-white">
            <div class="flex items-center gap-2">
                <i class="fas fa-book-reader"></i>
                <span class="font-bold">GÓC SÁCH</span>
            </div>
            <button id="close-mobile-menu" class="p-2 hover:bg-white/10 rounded-lg transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Search --}}
        <div class="p-4 border-b border-gray-100">
            <form action="{{ route('books.search') }}" method="GET" class="relative">
                <input type="text" name="keyword" placeholder="Tìm kiếm sách..."
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-brand-green">
                <button type="submit"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-brand-green text-white rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-xs"></i>
                </button>
            </form>
        </div>

        {{-- Navigation Links --}}
        <nav class="p-4 space-y-1">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition {{ request()->routeIs('home') ? 'bg-brand-green/10 text-brand-green font-bold' : 'text-gray-700' }}">
                <i class="fas fa-home w-5"></i> Trang Chủ
            </a>
            <a href="{{ route('books.list') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition {{ request()->routeIs('books.list') ? 'bg-brand-green/10 text-brand-green font-bold' : 'text-gray-700' }}">
                <i class="fas fa-book w-5"></i> Danh Sách Sách
            </a>
            <a href="{{ route('books.search') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition {{ request()->routeIs('books.search') ? 'bg-brand-green/10 text-brand-green font-bold' : 'text-gray-700' }}">
                <i class="fas fa-star w-5"></i> Review Hay
            </a>
            <a href="{{ route('authors.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 transition {{ request()->routeIs('authors.*') ? 'bg-brand-green/10 text-brand-green font-bold' : 'text-gray-700' }}">
                <i class="fas fa-user-edit w-5"></i> Tác Giả
            </a>
            <a href="{{ route('challenges.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold">
                <i class="fas fa-fire w-5"></i> Thử Thách Đọc Sách
            </a>
        </nav>

        {{-- User Section --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100 bg-gray-50">
                @auth
                <div class="flex items-center gap-3 mb-3">
                    @include('partials.user-avatar-with-frame', [
                        'user' => Auth::user(),
                        'size' => 'w-12 h-12',
                        'avatarSize' => 'w-10 h-10'
                    ])
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('profile') }}"
                        class="flex-1 text-center px-3 py-2 bg-brand-green text-white rounded-lg text-sm font-bold">Hồ
                        sơ</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-300 transition">Đăng
                            xuất</button>
                    </form>
                </div>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                        class="block text-center px-4 py-2.5 bg-brand-green text-white rounded-lg font-bold hover:bg-[#16271f] transition shadow-md">
                        <i class="fas fa-sign-in-alt mr-2"></i>Đăng Nhập</a>
                    <a href="{{ route('register') }}"
                        class="block text-center px-4 py-2.5 border-2 border-gray-200 text-gray-600 rounded-lg font-bold hover:border-brand-green hover:text-brand-green transition">
                        <i class="fas fa-user-plus mr-2"></i>Đăng Ký Miễn Phí</a>
                </div>
            @endauth
        </div>
    </div>
</div>

{{-- Modals --}}
<div id="rulesModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 modal-overlay transition-opacity" onclick="closeModal('rulesModal')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg animate-scale-up">
                <div class="bg-brand-green px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white" id="modal-title"><i
                            class="fas fa-gavel mr-2"></i> Quy Tắc Cộng Đồng</h3>
                    <button onclick="closeModal('rulesModal')" class="text-white/70 hover:text-white transition"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <div class="px-4 py-5 sm:p-6 text-sm text-gray-600 space-y-3 max-h-[400px] overflow-y-auto">
                    <p class="font-bold text-gray-800">1. Tôn trọng lẫn nhau:</p>
                    <p>Không sử dụng ngôn từ đả kích, xúc phạm hoặc phân biệt đối xử với các thành viên khác.</p>
                    <p class="font-bold text-gray-800 mt-2">2. Không Spam:</p>
                    <p>Không đăng tải các nội dung quảng cáo, tin rác hoặc bình luận trùng lặp nhiều lần.</p>
                    <p class="font-bold text-gray-800 mt-2">3. Bản quyền nội dung:</p>
                    <p>Chỉ chia sẻ những nội dung bạn có quyền sở hữu hoặc trích dẫn nguồn rõ ràng. Không đăng tải sách
                        lậu.</p>
                    <p class="font-bold text-gray-800 mt-2">4. Review trung thực:</p>
                    <p>Đánh giá sách dựa trên trải nghiệm thực tế, không thiên vị hoặc cố tình dìm hàng.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                        class="inline-flex w-full justify-center rounded-md bg-brand-green px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-green/90 sm:ml-3 sm:w-auto"
                        onclick="closeModal('rulesModal')">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="helpModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 modal-overlay transition-opacity" onclick="closeModal('helpModal')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg animate-scale-up">
                <div class="bg-blue-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white"><i class="fas fa-question-circle mr-2"></i> Trung
                        Tâm Trợ Giúp</h3>
                    <button onclick="closeModal('helpModal')" class="text-white/70 hover:text-white transition"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <div class="px-4 py-5 sm:p-6 text-sm text-gray-600 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-2 rounded-full text-blue-600"><i class="fas fa-user-plus"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-800">Làm sao để đăng ký?</h4>
                            <p>Nhấn vào nút "Đăng Ký" ở góc phải màn hình và điền thông tin email của bạn.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-2 rounded-full text-blue-600"><i class="fas fa-star"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-800">Cách viết Review?</h4>
                            <p>Tìm cuốn sách bạn muốn, vào trang chi tiết và kéo xuống phần "Viết đánh giá của bạn".</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-100 p-2 rounded-full text-blue-600"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h4 class="font-bold text-gray-800">Liên hệ hỗ trợ?</h4>
                            <p>Email: <a href="mailto:support@gocsach.com"
                                    class="text-blue-600 hover:underline">support@gocsach.com</a><br>Hotline: 1900 1234
                                (8h-17h)</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                        class="inline-flex w-full justify-center rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300 sm:ml-3 sm:w-auto"
                        onclick="closeModal('helpModal')">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === "Escape") {
            document.querySelectorAll('[id$="Modal"]').forEach(el => el.classList.add('hidden'));
        }
    });
</script>

<style>
    .dropdown-menu {
        display: none;
    }

    .group:hover .dropdown-menu {
        display: block;
    }

    /* Fix: Disable hover khi trang mới load để tránh dropdown tự hiện */
    .page-loading .dropdown-menu {
        display: none !important;
        pointer-events: none;
    }

    .page-loading .group:hover .dropdown-menu {
        display: none !important;
    }

    .dropdown-bridge::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 0;
        width: 100%;
        height: 10px;
        background: transparent;
    }

    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .animate-fade-in {
        animation: fadeIn 0.2s ease-in-out;
    }

    .animate-scale-up {
        animation: scaleUp 0.3s ease-out;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes scaleUp {
        0% {
            opacity: 0;
            transform: scale(0.95);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Header Shrink */
    .header-scrolled {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }

    .header-scrolled .logo-text {
        font-size: 1rem !important;
    }

    .header-scrolled .logo-icon {
        width: 2rem !important;
        height: 2rem !important;
    }

    .header-scrolled .nav-section {
        margin-top: 0.25rem !important;
        padding-top: 0.5rem !important;
    }
</style>

{{-- Fix dropdown tự hiện khi chuyển trang --}}
<script>
    // Thêm class page-loading ngay khi script chạy
    document.body.classList.add('page-loading');

    // Xóa class sau 500ms để cho phép hover hoạt động
    window.addEventListener('load', function () {
        setTimeout(function () {
            document.body.classList.remove('page-loading');
        }, 500);
    });
</script>

{{-- Live Search Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('header-search-input');
        const resultsBox = document.getElementById('header-search-results');
        let timeout = null;

        if (searchInput && resultsBox) {
            // 1. Khi người dùng gõ phím
            searchInput.addEventListener('input', function () {
                const keyword = this.value.trim();
                clearTimeout(timeout);

                if (keyword.length < 2) {
                    resultsBox.classList.add('hidden');
                    resultsBox.innerHTML = '';
                    return;
                }

                // Hiển thị loading
                resultsBox.innerHTML = '<div class="p-4 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Đang tìm kiếm...</div>';
                resultsBox.classList.remove('hidden');

                // Debounce 300ms
                timeout = setTimeout(() => {
                    fetchResults(keyword);
                }, 300);
            });

            // 2. Gửi Ajax lên Server
            function fetchResults(keyword) {
                fetch(`/ajax-search?keyword=${encodeURIComponent(keyword)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderResults(data, keyword);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultsBox.innerHTML = '<div class="p-4 text-center text-red-400">Có lỗi xảy ra</div>';
                    });
            }

            // 3. Hiển thị kết quả
            function renderResults(books, keyword) {
                if (books.length > 0) {
                    let html = '<ul class="divide-y divide-gray-100">';

                    books.forEach(book => {
                        // Xử lý ảnh bìa
                        let imgUrl = book.cover_image
                            ? (book.cover_image.startsWith('http') ? book.cover_image : '/storage/' + book.cover_image)
                            : 'https://placehold.co/50x70?text=No+Image';

                        // URL chi tiết sách (dùng slug hoặc ID nếu slug không có)
                        let detailUrl = `/chi-tiet/${book.slug || book.id}`;

                        // Highlight từ khóa trong title
                        let highlightedTitle = book.title.replace(
                            new RegExp(`(${keyword})`, 'gi'),
                            '<span class="bg-yellow-200 text-gray-900 font-bold">$1</span>'
                        );

                        html += `
                        <li>
                            <a href="${detailUrl}" class="search-result-link flex items-center gap-3 p-3 hover:bg-gray-50 transition cursor-pointer">
                                <img src="${imgUrl}" class="w-10 h-14 object-cover rounded shadow-sm border border-gray-200 flex-shrink-0" onerror="this.src='https://placehold.co/50x70?text=No+Image'">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-1">${highlightedTitle}</h4>
                                    <p class="text-xs text-gray-500">${book.author_name || 'Đang cập nhật'}</p>
                                    ${book.avg_rating ? `<div class="flex items-center gap-1 mt-0.5"><i class="fas fa-star text-yellow-400 text-[10px]"></i><span class="text-[10px] text-gray-400">${parseFloat(book.avg_rating).toFixed(1)}</span></div>` : ''}
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                            </a>
                        </li>
                    `;
                    });

                    html += '</ul>';
                    resultsBox.innerHTML = html;
                } else {
                    resultsBox.innerHTML = `
                    <div class="p-6 text-center">
                        <i class="far fa-frown text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-400">Không tìm thấy sách nào với từ khóa "<strong>${keyword}</strong>"</p>
                        <a href="/danh-sach-sach" class="text-xs text-brand-green hover:underline mt-2 inline-block">Xem tất cả sách →</a>
                    </div>
                `;
                }
                resultsBox.classList.remove('hidden');
            }

            // 4. Click ra ngoài thì ẩn dropdown (nhưng cho phép click vào link bên trong)
            document.addEventListener('click', function (e) {
                // Nếu click vào link bên trong resultsBox thì cho phép navigation
                if (e.target.closest('#header-search-results a')) {
                    return; // Không làm gì, để link hoạt động bình thường
                }

                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.classList.add('hidden');
                }
            });

            // 5. Nhấn ESC để đóng dropdown
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    resultsBox.classList.add('hidden');
                }
            });

            // 6. Focus lại input thì hiện dropdown nếu có kết quả
            searchInput.addEventListener('focus', function () {
                if (resultsBox.innerHTML.trim() !== '' && this.value.length >= 2) {
                    resultsBox.classList.remove('hidden');
                }
            });
        }
    });
</script>

{{-- Notification Polling Script --}}
@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastUnreadCount = {{ Auth::user()->unreadNotifications->count() }};
    
    // Hàm fetch thông báo mới
    function fetchNotifications() {
        fetch('/api/notifications', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateNotificationUI(data);
        })
        .catch(error => console.log('Notification fetch error:', error));
    }
    
    // Hàm cập nhật giao diện
    function updateNotificationUI(data) {
        const badge = document.getElementById('notification-badge');
        const countSpan = document.getElementById('notification-count');
        const markAllBtn = document.getElementById('mark-all-read-btn');
        const listContainer = document.getElementById('notification-list');
        
        if (!badge || !countSpan) return;
        
        // Cập nhật badge số lượng
        if (data.unread_count > 0) {
            badge.classList.remove('hidden');
            countSpan.innerText = data.unread_count;
            if (markAllBtn) markAllBtn.classList.remove('hidden');
            
            // Nếu có thông báo mới, hiệu ứng rung chuông
            if (data.unread_count > lastUnreadCount) {
                badge.classList.add('animate-ping');
                setTimeout(() => badge.classList.remove('animate-ping'), 1000);
                
                // Phát âm thanh thông báo (optional)
                // playNotificationSound();
            }
        } else {
            badge.classList.add('hidden');
            if (markAllBtn) markAllBtn.classList.add('hidden');
        }
        
        lastUnreadCount = data.unread_count;
        
        // Cập nhật danh sách thông báo
        if (listContainer && data.notifications.length > 0) {
            let html = '';
            data.notifications.forEach(n => {
                const isRead = n.read_at !== null;
                const bgClass = isRead ? 'opacity-60 grayscale-[0.5]' : 'bg-blue-50/30';
                // Xác định màu nền icon động (đỏ cho từ chối, xanh cho duyệt)
                const iconBgColor = n.color && n.color.includes('red') ? 'bg-red-100' : 'bg-green-100';
                
                html += `
                    <a href="${n.link}" class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 ${bgClass}">
                        <div class="flex-shrink-0 mt-1">
                            ${n.is_system 
                                ? `<div class="w-8 h-8 rounded-full ${iconBgColor} flex items-center justify-center"><i class="${n.icon} ${n.color} text-sm"></i></div>`
                                : `<img src="${n.user_avatar}" class="w-8 h-8 rounded-full border border-gray-100 object-cover">`
                            }
                        </div>
                        <div class="flex-1">
                            ${n.is_system 
                                ? `<p class="text-sm font-bold text-gray-800">${n.title || 'Thông báo hệ thống'}</p><p class="text-xs text-gray-600 line-clamp-2 mt-0.5">${n.message}</p>`
                                : `<p class="text-sm text-gray-700 line-clamp-2"><span class="font-bold text-gray-900">${n.user_name}</span> ${n.message}<span class="font-bold block text-xs text-gray-500 italic mt-0.5">"${n.post_title}"</span></p>`
                            }
                            <p class="text-[10px] text-gray-400 mt-1 flex items-center"><i class="far fa-clock mr-1"></i> ${n.time}</p>
                        </div>
                        ${!isRead ? '<div class="w-2 h-2 bg-brand-green rounded-full mt-2 shrink-0"></div>' : ''}
                    </a>
                `;
            });
            listContainer.innerHTML = html;
        } else if (listContainer && data.notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="far fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                    <p class="text-xs">Không có thông báo mới</p>
                </div>
            `;
        }
    }
    
    // Polling mỗi 10 giây
    setInterval(fetchNotifications, 10000);
    
    // Fetch ngay khi trang load (sau 2 giây để tránh lag)
    setTimeout(fetchNotifications, 2000);
    
    // ====== NOTIFICATION BELL CLICK HANDLER ======
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    const wrapper = document.getElementById('notification-wrapper');
    
    if (bellBtn && dropdown) {
        // Khi click vào chuông thông báo
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Toggle dropdown
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            
            // Nếu mở dropdown và có thông báo chưa đọc -> chỉ ẩn badge số, KHÔNG đánh dấu đã đọc
            // Người dùng vẫn thấy các thông báo chưa đọc với chấm xanh
            if (isHidden && badge && !badge.classList.contains('hidden')) {
                // Chỉ ẩn badge số trên chuông, không gọi API đánh dấu đã đọc
                badge.classList.add('hidden');
            }
        });
        
        // ====== MARK ALL AS READ (AJAX) ======
        window.markAllNotificationsAsRead = function() {
            const markAllBtn = document.getElementById('mark-all-read-btn');
            const listContainer = document.getElementById('notification-list');
            
            // Disable button và hiện loading
            if (markAllBtn) {
                markAllBtn.innerText = 'Đang xử lý...';
                markAllBtn.disabled = true;
            }
            
            fetch('{{ route("notification.readAll") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ẩn nút "Đánh dấu đã đọc"
                    if (markAllBtn) markAllBtn.classList.add('hidden');
                    
                    // Cập nhật UI: xóa chấm xanh và thêm class đã đọc cho tất cả thông báo
                    if (listContainer) {
                        // Xóa tất cả chấm xanh (unread indicator)
                        listContainer.querySelectorAll('.bg-brand-green.rounded-full').forEach(dot => dot.remove());
                        
                        // Thêm class đã đọc cho tất cả thông báo
                        listContainer.querySelectorAll('a').forEach(item => {
                            item.classList.remove('bg-blue-50/30');
                            item.classList.add('opacity-60', 'grayscale-[0.5]');
                        });
                    }
                    
                    // Ẩn badge số trên chuông (nếu chưa ẩn)
                    const badge = document.getElementById('notification-badge');
                    if (badge) badge.classList.add('hidden');
                }
            })
            .catch(err => {
                console.log('Mark all read error:', err);
                // Restore button nếu lỗi
                if (markAllBtn) {
                    markAllBtn.innerText = 'Đánh dấu đã đọc';
                    markAllBtn.disabled = false;
                }
            });
        };
        
        // Click ra ngoài thì đóng dropdown
        document.addEventListener('click', function(e) {
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Nhấn ESC để đóng dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdown.classList.add('hidden');
            }
        });
    }
});
</script>
@endauth
