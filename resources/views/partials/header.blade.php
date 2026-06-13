<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Majestic Inverted Footer Header --}}
<header id="main-header" class="fixed top-0 left-0 right-0 z-[100] w-full transition-all duration-500 group/hdr">
    {{-- Shadow & Background container (Separate so overflow-hidden doesn't clip absolute dropdowns) --}}
    <div id="header-bg-container" class="absolute inset-0 z-0 bg-brand-green rounded-b-[40px] shadow-[0_15px_50px_rgba(155,34,38,0.2)] lg:rounded-b-[80px] overflow-hidden transition-all duration-500 origin-top opacity-100 scale-y-100">
        {{-- Beautiful Background Image with soft dark red overlay --}}
        <img loading="lazy" src="{{ asset('images/auth/kitchen_1.png') }}" class="w-full h-full object-cover opacity-50 mix-blend-overlay" alt="Header Background">
        <div class="absolute inset-0 bg-gradient-to-b from-brand-green/95 via-brand-green/90 to-brand-green/95"></div>
    </div>

    {{-- Main Container --}}
    <div id="header-container" class="relative z-10 flex items-center justify-between px-4 sm:px-6 md:px-10 py-3 sm:py-5 transition-all duration-300 gap-4 xl:gap-8 max-w-[1600px] mx-auto">



        
        {{-- 1. Logo (Left) --}}
        <div class="flex items-center justify-start shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 sm:gap-3 group">
                <div class="w-10 h-10 sm:w-11 sm:h-11 bg-white/10 backdrop-blur-md border border-white/20 text-white rounded-full flex items-center justify-center shadow-lg transform group-hover:rotate-12 group-hover:scale-105 transition-all duration-300">
                    <span class="text-xl drop-shadow-md">🍳</span>
                </div>
                <div class="flex flex-col hidden sm:flex drop-shadow-md">
                    <span class="text-xl md:text-2xl font-black font-serif text-white leading-none tracking-tight">GÓC BẾP</span>
                    <span class="text-[8px] md:text-[9px] text-brand-accent uppercase tracking-[0.25em] font-extrabold mt-0.5">Cook & Share</span>
                </div>
            </a>
        </div>

        {{-- 2. Navigation (Center - Desktop) --}}
        <nav class="hidden lg:flex flex-1 items-center justify-center gap-4 xl:gap-6 text-[13px] xl:text-[14px] font-bold text-white/90 whitespace-nowrap px-4 drop-shadow-sm">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-brand-accent' : 'hover:text-white' }} transition-colors relative group py-2">
                Trang Chủ
                <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left {{ request()->routeIs('home') ? 'scale-x-100' : '' }}"></span>
            </a>
            <a href="{{ route('recipes.list') }}" class="{{ request()->routeIs('recipes.list', 'recipes.show', 'recipes.search') ? 'text-brand-accent' : 'hover:text-white' }} transition-colors relative group py-2">
                Công Thức
                <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left {{ request()->routeIs('recipes.list', 'recipes.show', 'recipes.search') ? 'scale-x-100' : '' }}"></span>
            </a>
            @auth
                <a href="{{ route('recipes.smart-search') }}" class="{{ request()->routeIs('recipes.smart-search') ? 'text-brand-accent' : 'hover:text-white' }} transition-colors relative group py-2" title="Gợi ý món từ nguyên liệu bạn có">
                    Tủ Lạnh Web
                    <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left {{ request()->routeIs('recipes.smart-search') ? 'scale-x-100' : '' }}"></span>
                </a>
            @else
                <a href="{{ route('login') }}" class="hover:text-white transition-colors relative group py-2 opacity-70" title="Đăng nhập để dùng Tủ Lạnh Web AI">
                    Tủ Lạnh Web <i class="fas fa-lock text-[10px] text-brand-accent ml-1 animate-pulse"></i>
                    <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></span>
                </a>
            @endauth
            <a href="{{ route('authors.index') }}" class="{{ request()->routeIs('authors.index') ? 'text-brand-accent' : 'hover:text-white' }} transition-colors relative group py-2">
                Tác Giả
                <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left {{ request()->routeIs('authors.index') ? 'scale-x-100' : '' }}"></span>
            </a>
            <a href="{{ route('articles.index') }}" class="{{ request()->routeIs('articles.index', 'articles.show') ? 'text-brand-accent' : 'hover:text-white' }} transition-colors relative group py-2">
                Tạp Chí
                <span class="absolute bottom-0 left-0 w-full h-[3px] bg-brand-accent rounded-full transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left {{ request()->routeIs('articles.index', 'articles.show') ? 'scale-x-100' : '' }}"></span>
            </a>
            <a href="{{ route('challenges.index') }}" class="relative inline-flex items-center gap-1.5 text-brand-green hover:text-brand-green transition-colors group px-4 py-1.5 bg-brand-accent rounded-full shadow-md hover:shadow-lg hover:bg-amber-100 hover:scale-105">
                <i class="fas fa-fire text-[#ff5500] group-hover:animate-bounce"></i> Thử Thách
            </a>
        </nav>

        {{-- 3. Right Side Actions --}}
        <div class="flex items-center justify-end gap-2 xl:gap-4 shrink-0">
            {{-- Search Toggle Button --}}
            <button id="search-toggle-btn" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-brand-green flex items-center justify-center transition-all shadow-lg hover:shadow-xl [&.active]:bg-white [&.active]:text-brand-green [&.active]:shadow-xl [&.active]:border-white transform hover:-translate-y-0.5">
                <i class="fas fa-search text-base sm:text-lg"></i>
            </button>

            <div class="w-[1px] h-6 bg-white/20 hidden sm:block mx-1"></div>

            @auth
                {{-- Notification Bell --}}
                <div class="relative" id="notification-dropdown-container">
                    <button type="button" id="notification-dropdown-trigger"
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white hover:text-brand-green transition-all relative group shadow-lg">
                        <i class="far fa-bell text-lg sm:text-xl group-hover:animate-swing"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-brand-accent border-2 border-brand-green rounded-full animate-pulse"></span>
                        @endif
                    </button>
                    {{-- Dropdown Notification --}}
                    <div id="notification-dropdown-menu"
                        class="hidden absolute right-0 top-full mt-5 w-[320px] sm:w-[380px] bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in z-[100] origin-top-right">
                        <div class="px-5 py-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                            <span class="text-sm font-bold text-gray-800"><i class="fas fa-inbox mr-2 text-brand-green"></i>Thông báo</span>
                            <button type="button" onclick="markAllNotificationsAsRead()" id="mark-all-read-btn"
                                class="{{ Auth::user()->unreadNotifications->count() > 0 ? '' : 'hidden' }} text-[11px] font-bold text-brand-accent hover:underline cursor-pointer">
                                Đánh dấu đã đọc
                            </button>
                        </div>
                        <div id="notification-list" class="max-h-[350px] overflow-y-auto custom-scrollbar">
                            @forelse(Auth::user()->notifications as $notification)
                                @php
                                    $dbType = $notification->type;
                                    $dataType = $notification->data['type'] ?? '';
                                    $systemClasses = ['App\Notifications\NewReportNotification', 'App\Notifications\NewBookRequestNotification', 'App\Notifications\BookApprovedNotification', 'App\Notifications\AdminNewPostNotification'];
                                    $systemTypes = ['new_report', 'book_request', 'book_approved', 'admin_new_post'];
                                    $isSystemNotification = in_array($dbType, $systemClasses) || in_array($dataType, $systemTypes);
                                    
                                    $type = $dataType ?: match($dbType) {
                                        'App\Notifications\NewReportNotification' => 'new_report',
                                        'App\Notifications\NewBookRequestNotification' => 'book_request',
                                        'App\Notifications\BookApprovedNotification' => 'book_approved',
                                        'App\Notifications\AdminNewPostNotification' => 'admin_new_post',
                                        default => ''
                                    };

                                    $icon = 'fas fa-bell'; $iconColor = 'text-amber-600'; $title = ''; $bgColor = 'bg-amber-100';
                                    
                                    switch($type) {
                                        case 'new_report': $icon = 'fas fa-flag'; $iconColor = 'text-red-600'; $title = 'Báo cáo mới'; $bgColor = 'bg-red-100'; break;
                                        case 'book_request': $icon = 'fas fa-book'; $iconColor = 'text-amber-600'; $title = 'Gợi ý công thức mới'; $bgColor = 'bg-amber-100'; break;
                                        case 'recipe_approved': $icon = 'fas fa-check-circle'; $iconColor = 'text-green-600'; $title = 'Công thức được duyệt'; $bgColor = 'bg-green-100'; break;
                                        case 'admin_new_post': $icon = 'fas fa-file-contract'; $iconColor = 'text-brand-green'; $title = 'Bài đăng mới'; $bgColor = 'bg-brand-green/10'; break;
                                    }
                                @endphp

                                <a href="{{ route('notification.read', $notification->id) }}"
                                    class="flex gap-4 px-5 py-4 hover:bg-red-50/30 transition border-b border-gray-50 {{ $notification->read_at ? 'opacity-60' : 'bg-red-50/10' }}">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($isSystemNotification)
                                            <div class="w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center shadow-sm">
                                                <i class="{{ $icon }} {{ $iconColor }} text-sm"></i>
                                            </div>
                                        @else
                                            <img loading="lazy" src="{{ $notification->data['avatar'] ?? 'https://api.dicebear.com/7.x/initials/svg?seed=User' }}"
                                                class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        @if($isSystemNotification)
                                            <p class="text-[13px] font-bold text-gray-800">{{ $title }}</p>
                                            <p class="text-xs text-gray-600 line-clamp-2 mt-1 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                        @else
                                            <p class="text-xs text-gray-700 leading-relaxed">
                                                @php $displayName = $notification->data['uploader_name'] ?? ($notification->data['user_name'] ?? ''); @endphp
                                                @if($displayName && $displayName !== 'Ai đó') <span class="font-bold text-gray-900">{{ $displayName }}</span> @endif
                                                {{ $notification->data['message'] ?? 'đã tương tác với bạn' }}
                                                <span class="font-bold block text-brand-green italic mt-1">"{{ Str::limit($notification->data['post_title'] ?? ($notification->data['book_title'] ?? ''), 50) }}"</span>
                                            </p>
                                        @endif
                                        <p class="text-[10px] font-bold text-gray-400 mt-2 flex items-center uppercase tracking-wider"><i class="far fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <div class="w-2 h-2 bg-brand-accent rounded-full mt-2 shrink-0"></div>
                                    @endif
                                </a>
                            @empty
                                <div class="text-center py-10 text-gray-400">
                                    <i class="far fa-bell-slash text-3xl mb-3 text-gray-200"></i>
                                    <p class="text-xs font-medium">Bạn chưa có thông báo nào mới</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User Avatar Dropdown --}}
                <div class="relative z-50" id="user-dropdown-container">
                    <button type="button" id="user-dropdown-trigger" class="flex items-center focus:outline-none cursor-pointer relative z-20 rounded-full transition-transform hover:scale-105 shadow-lg border border-white/20 bg-white/10 backdrop-blur-md hover:border-white">
                    @include('partials.user-avatar-with-frame', [
                            'user' => Auth::user(),
                            'size' => 'w-9 sm:w-10 h-9 sm:h-10',
                            'showNameplate' => false
                        ])
                    </button>
                    {{-- Dropdown User Menu --}}
                    <div id="user-dropdown-menu"
                        class="hidden absolute right-0 top-full mt-6 w-64 bg-white rounded-3xl shadow-2xl border border-gray-100 py-2 animate-fade-in origin-top-right z-10">
                        <div class="px-5 py-4 border-b border-gray-50 bg-gray-50/50 rounded-t-3xl mb-2 flex flex-col items-center gap-2">
                            @include('partials.user-avatar-with-frame', [
                                'user' => Auth::user(),
                                'size' => 'w-14 h-14',
                                'namePosition' => 'bottom',
                                'nameClass' => 'text-sm font-bold text-brand-green'
                            ])
                            <p class="text-[10px] text-gray-400 font-medium">{{ Auth::user()->name }}</p>
                        </div>
                        @if(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-5 py-3 text-brand-green bg-red-50/50 hover:bg-red-50 transition font-bold mx-2 rounded-xl mb-1">
                                <i class="fas fa-crown w-5 mr-3 text-brand-accent"></i> Trang Quản Trị
                            </a>
                        @endif
                        <a href="{{ route('profile') }}" class="flex items-center px-5 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-green transition mx-2 rounded-xl">
                            <i class="fas fa-user w-5 mr-3 text-gray-400"></i> Hồ sơ cá nhân
                        </a>
                        <a href="{{ route('change.password') }}" class="flex items-center px-5 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-green transition mx-2 rounded-xl">
                            <i class="fas fa-lock w-5 mr-3 text-gray-400"></i> Đổi mật khẩu
                        </a>
                        
                        <div class="border-t border-gray-100 my-2 pt-2"></div>
                        
                        <div class="px-2 cursor-pointer" onclick="openModal('rulesModal')">
                            <span class="flex items-center px-5 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-accent transition rounded-xl text-xs"><i class="fas fa-gavel w-5 mr-3 text-gray-400"></i> Quy tắc cộng đồng</span>
                        </div>
                        <div class="px-2 cursor-pointer" onclick="openModal('helpModal')">
                            <span class="flex items-center px-5 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-accent transition rounded-xl text-xs"><i class="fas fa-question-circle w-5 mr-3 text-gray-400"></i> Trợ giúp</span>
                        </div>
                        
                        <div class="border-t border-gray-100 my-2 pt-2"></div>

                        <form method="POST" action="{{ route('logout') }}" class="px-2 pb-2">
                            @csrf
                            <button type="submit" class="w-full flex justify-center items-center px-5 py-3 text-white bg-gray-900 hover:bg-black transition font-bold rounded-xl shadow-lg">
                                Đăng Xuất <i class="fas fa-sign-out-alt ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Guest Buttons --}}
                <a href="{{ route('login') }}" class="hidden sm:flex text-[13px] xl:text-[14px] font-bold text-white/90 hover:text-white transition px-3 xl:px-4 py-2 hover:bg-white/10 rounded-xl whitespace-nowrap">
                    Đăng Nhập
                </a>
                <a href="{{ route('register') }}" class="hidden sm:flex items-center justify-center bg-brand-accent text-brand-green text-[13px] xl:text-[14px] font-bold px-5 xl:px-7 py-2.5 rounded-full hover:bg-amber-100 transition-all shadow-lg hover:-translate-y-0.5 whitespace-nowrap">
                    Đăng Ký
                </a>
            @endauth

            {{-- Mobile Menu Button (Hidden now because we use Bottom Nav) --}}
            <button id="mobile-menu-btn" class="hidden w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white/20 flex items-center justify-center transition ml-1 shadow-md">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- Dropdown Expandable Live Search Area (Attached closely to Pill) --}}
    <div id="expandable-search-pane" class="absolute top-[80px] sm:top-[100px] left-0 right-0 mx-2 sm:mx-10 bg-white/95 backdrop-blur-2xl rounded-[2rem] shadow-[0_40px_80px_-20px_rgba(155,34,38,0.2)] border border-white overflow-hidden transform origin-top scale-y-0 opacity-0 transition-all duration-300 pointer-events-none z-40">
        <div class="absolute inset-0 bg-brand-green/5 pointer-events-none"></div>
        <form action="{{ route('recipes.search') }}" method="GET" class="relative p-4 sm:p-6 pb-2" id="header-search-form">
            <h4 class="text-brand-green text-sm font-serif font-bold mb-3 px-2 flex items-center"><i class="fas fa-utensils mr-2"></i> Hôm nay bạn muốn nấu món gì?</h4>
            <div class="relative flex items-center bg-white rounded-full h-14 sm:h-16 px-6 border-2 border-brand-green/20 focus-within:border-brand-accent focus-within:shadow-[0_0_20px_rgba(232,93,4,0.15)] transition-all">
                <i class="fas fa-search text-brand-accent text-lg mr-4"></i>
                <input type="text" id="header-search-input" name="keyword" value="{{ request('keyword') }}"
                    autocomplete="off" placeholder="Gõ tên món ăn, nguyên liệu..."
                    class="flex-1 bg-transparent border-none p-0 text-gray-800 placeholder-gray-400 font-medium text-base sm:text-lg focus:outline-none focus:ring-0">
                <button type="submit" class="bg-brand-green text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#7a1a1e] transition shadow-[0_4px_15px_rgba(155,34,38,0.3)] whitespace-nowrap hidden sm:block">
                    Tìm Kiếm
                </button>
            </div>
            
            {{-- Quick Categories / Tags for Search --}}
            <div class="mt-4 px-2 flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                <span class="px-4 py-1.5 rounded-full bg-red-50 text-brand-green text-xs font-bold cursor-pointer hover:bg-red-100 transition whitespace-nowrap"><i class="fas fa-heart text-red-400 mr-1"></i> Món ăn nhanh</span>
                <span class="px-4 py-1.5 rounded-full bg-green-50 text-green-700 text-xs font-bold cursor-pointer hover:bg-green-100 transition whitespace-nowrap"><i class="fas fa-leaf text-green-500 mr-1"></i> Ăn Chay</span>
                <span class="px-4 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs font-bold cursor-pointer hover:bg-amber-100 transition whitespace-nowrap"><i class="fas fa-bread-slice text-amber-500 mr-1"></i> Ăn Sáng</span>
            </div>

            {{-- Search Results Pane --}}
            <div id="header-search-results" class="mt-2 hidden max-h-[300px] overflow-y-auto w-full custom-scrollbar text-sm px-2 pb-4">
                {{-- JS renders here --}}
            </div>
        </form>
    </div>
</header>

{{-- Mobile Menu Overlay --}}
<div id="mobile-menu" class="fixed inset-0 z-[120] hidden">
    <div id="mobile-menu-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-md transition-opacity"></div>
    <div id="mobile-menu-panel" class="absolute top-0 right-0 w-80 max-w-[85vw] h-full bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col">
        {{-- Header --}}
        <div class="flex items-center justify-between p-5 bg-white border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-green to-brand-accent flex justify-center items-center text-white"><i class="fas fa-utensils text-xs"></i></div>
                <span class="font-serif font-black text-brand-green text-lg tracking-tight">GÓC BẾP</span>
            </div>
            <button id="close-mobile-menu" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Mobile Nav --}}
        <nav class="p-5 flex-1 overflow-y-auto space-y-2">
            <a href="{{ route('home') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition {{ request()->routeIs('home') ? 'bg-red-50 text-brand-green font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="w-8 flex justify-center"><i class="fas fa-home"></i></div> Trang Chủ
            </a>
            <a href="{{ route('recipes.list') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition {{ request()->routeIs('recipes.*') && !request()->routeIs('recipes.smart-search') ? 'bg-red-50 text-brand-green font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="w-8 flex justify-center"><i class="fas fa-book-open"></i></div> Công Thức
            </a>
            @auth
                <a href="{{ route('recipes.smart-search') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition {{ request()->routeIs('recipes.smart-search') ? 'bg-red-50 text-brand-green font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                    <div class="w-8 flex justify-center"><i class="fas fa-shopping-basket"></i></div> Tủ Lạnh Web
                </a>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition text-gray-400 hover:bg-gray-50 relative">
                    <div class="w-8 flex justify-center"><i class="fas fa-shopping-basket"></i></div>
                    Tủ Lạnh Web
                    <span class="ml-auto text-[10px] bg-brand-accent/20 text-brand-accent font-bold px-2 py-0.5 rounded-full flex items-center gap-1"><i class="fas fa-lock text-[9px]"></i> Đăng nhập</span>
                </a>
            @endauth
            <a href="{{ route('articles.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl transition {{ request()->routeIs('articles.*') ? 'bg-red-50 text-brand-green font-bold' : 'text-gray-700 hover:bg-gray-50' }}">
                <div class="w-8 flex justify-center"><i class="fas fa-newspaper"></i></div> Tạp Chí
            </a>
            <a href="{{ route('challenges.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl bg-gradient-to-r from-brand-accent to-red-500 text-white font-bold shadow-md shadow-amber-500/20 mt-4">
                <div class="w-8 flex justify-center"><i class="fas fa-fire animate-pulse text-yellow-300"></i></div> Mùa Thử Thách
            </a>

            <div class="border-t border-gray-100 my-4 pt-4"></div>
            <a onclick="openModal('rulesModal')" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 text-sm font-medium cursor-pointer">
                <div class="w-8 flex justify-center"><i class="fas fa-gavel text-gray-400"></i></div> Quy tắc cộng đồng
            </a>
            <a onclick="openModal('helpModal')" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 text-sm font-medium cursor-pointer">
                <div class="w-8 flex justify-center"><i class="fas fa-question-circle text-gray-400"></i></div> Trợ giúp
            </a>
        </nav>

        {{-- Mobile User Bottom --}}
        <div class="p-5 border-t border-gray-100 bg-gray-50/50">
            @auth
                <div class="flex items-center gap-4 mb-4">
                    @include('partials.user-avatar-with-frame', ['user' => Auth::user(), 'size' => 'w-10 h-10', 'showNameplate' => false])
                    <div>
                        <p class="text-gray-500 text-sm mb-3 font-medium text-brand-green">@<span>{{ Str::slug(Auth::user()->name, '') }}</span></p>
                        <p class="text-[11px] text-gray-500 font-medium">{{ Auth::user()->name }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('profile') }}" class="flex-1 text-center py-2.5 bg-brand-green text-white rounded-xl text-sm font-bold shadow-md">Hồ sơ</a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-gray-200 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-300 transition">Đăng xuất</button>
                    </form>
                </div>
            @else
                <div class="space-y-3">
                    <a href="{{ route('login') }}" class="block text-center py-3 bg-brand-green text-white rounded-xl font-bold hover:bg-[#7a1a1e] transition shadow-md">Đăng Nhập</a>
                    <a href="{{ route('register') }}" class="block text-center py-3 border-2 border-brand-green/20 text-brand-green rounded-xl font-bold hover:border-brand-green transition bg-white">Đăng Ký Tài Khoản</a>
                </div>
            @endauth
        </div>
    </div>
</div>

{{-- Script for Toggles (Dropdowns & Mobile Menu & Search Pane) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const header = document.getElementById('main-header');
        const bgContainer = document.getElementById('header-bg-container');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('py-0');
                if (bgContainer) {
                    bgContainer.classList.remove('rounded-b-[40px]', 'lg:rounded-b-[80px]');
                    bgContainer.classList.add('opacity-95', 'shadow-xl');
                }
            } else {
                header.classList.remove('py-0');
                if (bgContainer) {
                    bgContainer.classList.add('rounded-b-[40px]', 'lg:rounded-b-[80px]');
                    bgContainer.classList.remove('opacity-95', 'shadow-xl');
                }
            }
        });

        // Toggle Search Pane
        const searchBtn = document.getElementById('search-toggle-btn');
        const searchPane = document.getElementById('expandable-search-pane');
        const searchInput = document.getElementById('header-search-input');
        
        if (searchBtn && searchPane) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                // Close others
                if (typeof toggleChatbox === 'function' && window.isChatboxOpen) {
                    toggleChatbox();
                }
                closeDropdown('notification-dropdown-menu');
                closeDropdown('user-dropdown-menu');

                const isClosed = searchPane.classList.contains('scale-y-0');
                if (isClosed) {
                    searchPane.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none');
                    searchBtn.classList.add('active');
                    setTimeout(() => searchInput.focus(), 300);
                } else {
                    closeSearchPane();
                }
            });
        }

        function closeSearchPane() {
            if (searchPane) {
                searchPane.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none');
                searchBtn.classList.remove('active');
            }
        }

        // Toggle Generic Dropdowns
        function setupDropdown(triggerId, menuId, containerId) {
            const trigger = document.getElementById(triggerId);
            const menu = document.getElementById(menuId);
            const container = document.getElementById(containerId);

            if (trigger && menu) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isOpen = !menu.classList.contains('hidden');
                    
                    // Close others
                    if (typeof toggleChatbox === 'function' && window.isChatboxOpen) {
                        toggleChatbox();
                    }
                    closeSearchPane();
                    if (menuId !== 'notification-dropdown-menu') closeDropdown('notification-dropdown-menu');
                    if (menuId !== 'user-dropdown-menu') closeDropdown('user-dropdown-menu');
                    
                    if (isOpen) {
                        menu.classList.add('hidden');
                    } else {
                        menu.classList.remove('hidden');
                    }
                });

                document.addEventListener('click', function(e) {
                    if (container && !container.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                });
            }
        }

        function closeDropdown(menuId) {
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.add('hidden');
        }

        window.closeAllHeaderDropdowns = function() {
            closeSearchPane();
            closeDropdown('notification-dropdown-menu');
            closeDropdown('user-dropdown-menu');
        }

        setupDropdown('notification-dropdown-trigger', 'notification-dropdown-menu', 'notification-dropdown-container');
        setupDropdown('user-dropdown-trigger', 'user-dropdown-menu', 'user-dropdown-container');

        // Close on Esc
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearchPane();
                closeDropdown('notification-dropdown-menu');
                closeDropdown('user-dropdown-menu');
                closeModal('rulesModal');
                closeModal('helpModal');
            }
        });

        // Mobile Menu Logic
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMobileBtn = document.getElementById('close-mobile-menu');
        const mobilePanel = document.getElementById('mobile-menu-panel');

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.remove('hidden');
                setTimeout(() => mobilePanel.classList.remove('translate-x-full'), 10);
            });

            closeMobileBtn.addEventListener('click', () => {
                mobilePanel.classList.add('translate-x-full');
                setTimeout(() => mobileMenu.classList.add('hidden'), 300);
            });
        }
    });

    // Modals
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.getElementById('mobile-menu').classList.add('hidden'); // Close mobile menu if open
    }
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>

{{-- Modals Content (Help & Rules) --}}
<div id="rulesModal" class="fixed inset-0 z-[200] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('rulesModal')"></div>
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg">
            <div class="bg-brand-green px-6 py-5 flex justify-between items-center">
                <h3 class="text-xl font-bold leading-6 text-white font-serif"><i class="fas fa-gavel mr-2"></i> Quy Tắc Cộng Đồng</h3>
                <button onclick="closeModal('rulesModal')" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="px-6 py-6 text-sm text-gray-700 space-y-4 max-h-[50vh] overflow-y-auto">
                <p class="font-bold text-gray-900 text-base">1. Tôn trọng tinh thần ẩm thực:</p>
                <p class="text-gray-600">Luôn sử dụng ngôn từ hòa nhã, lịch sự. Góc Bếp là nơi chia sẻ tình yêu nấu nướng, không đả kích hay phân biệt.</p>
                <p class="font-bold text-gray-900 text-base mt-2">2. Không nội dung rác (Spam):</p>
                <p class="text-gray-600">Nghiêm cấm quảng cáo sai sự thật, đăng tải công thức trùng lặp hoặc chứa link độc hại.</p>
                <p class="font-bold text-gray-900 text-base mt-2">3. Trân trọng bản quyền:</p>
                <p class="text-gray-600">Mỗi công thức là một tâm huyết. Quý khách vui lòng trích dẫn nguồn rõ ràng nếu tham khảo từ nơi khác.</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" class="rounded-xl bg-brand-green px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-black transition" onclick="closeModal('rulesModal')">Tuyệt vời, Đã hiểu</button>
            </div>
        </div>
    </div>
</div>

<div id="helpModal" class="fixed inset-0 z-[200] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('helpModal')"></div>
    <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg">
            <div class="bg-brand-accent px-6 py-5 flex justify-between items-center">
                <h3 class="text-xl font-bold leading-6 text-white font-serif"><i class="fas fa-headset mr-2"></i> Trung Tâm Hỗ Trợ</h3>
                <button onclick="closeModal('helpModal')" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="px-6 py-6 text-sm text-gray-700 space-y-5">
                <div class="flex items-start gap-4">
                    <div class="bg-amber-100 p-3 rounded-2xl text-amber-600"><i class="fas fa-utensils"></i></div>
                    <div><h4 class="font-bold text-gray-900">Chia sẻ công thức thế nào?</h4><p class="text-gray-600 mt-1">Bạn có thể nhấp vào nút "Đăng Công Thức" tại trang Thử Thách hoặc trong Hồ Sơ cá nhân để chia sẻ những món ăn tuyệt vời của mình.</p></div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="bg-amber-100 p-3 rounded-2xl text-amber-600"><i class="fas fa-envelope"></i></div>
                    <div><h4 class="font-bold text-gray-900">Gặp lỗi trong quá trình sử dụng?</h4><p class="text-gray-600 mt-1">Xin vui lòng gửi phản hồi về <a href="mailto:support@kitchencorner.com" class="text-brand-accent font-bold hover:underline">support@kitchencorner.com</a> để được hỗ trợ 24/7.</p></div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button type="button" class="rounded-xl bg-gray-200 px-6 py-2.5 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-300 transition" onclick="closeModal('helpModal')">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Live Search Ajax Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('header-search-input');
        const resultsBox = document.getElementById('header-search-results');
        let timeout = null;

        if (searchInput && resultsBox) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.trim();
                clearTimeout(timeout);

                if (keyword.length < 2) {
                    resultsBox.classList.add('hidden');
                    resultsBox.innerHTML = '';
                    return;
                }

                resultsBox.innerHTML = '<div class="p-6 text-center text-gray-400 font-medium"><i class="fas fa-spinner fa-spin mr-2 text-brand-green"></i>Đang tìm món ngon...</div>';
                resultsBox.classList.remove('hidden');

                timeout = setTimeout(() => {
                    fetch(`/ajax-search?keyword=${encodeURIComponent(keyword)}`)
                        .then(response => response.json())
                        .then(data => renderResults(data, keyword))
                        .catch(() => { resultsBox.innerHTML = '<div class="p-4 text-center text-red-400">Có lỗi cực nhỏ xảy ra, vui lòng thử lại sau.</div>'; });
                }, 300);
            });

            function renderResults(recipes, keyword) {
                if (recipes.length > 0) {
                    let html = '<div class="grid grid-cols-1 gap-2 pt-2">';
                    recipes.forEach(recipe => {
                        let imgUrl = recipe.image ? (recipe.image.startsWith('http') ? recipe.image : '/storage/' + recipe.image) : 'https://placehold.co/50x50?text=Food';
                        let detailUrl = `/cong-thuc/${recipe.slug || recipe.id}`;
                        let highlightedTitle = recipe.title.replace(new RegExp(`(${keyword})`, 'gi'), '<span class="bg-amber-100 text-brand-accent font-black">$1</span>');

                        html += `
                        <a href="${detailUrl}" class="flex items-center gap-4 p-3 hover:bg-red-50/50 rounded-2xl transition cursor-pointer group border border-transparent hover:border-red-100">
                            <img loading="lazy" src="${imgUrl}" class="w-14 h-14 object-cover rounded-[1rem] shadow-sm flex-shrink-0 group-hover:scale-105 transition-transform" onerror="this.src='https://placehold.co/50x50?text=Food'">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[13px] font-bold text-gray-800 line-clamp-1 group-hover:text-brand-green transition-colors">${highlightedTitle}</h4>
                                <p class="text-[11px] text-gray-500 font-medium mt-1 uppercase tracking-wider flex gap-3">
                                    <span><i class="far fa-clock mr-1 text-gray-400"></i>${recipe.cooking_time || 0} Phút</span>
                                    <span><i class="far fa-eye mr-1 text-gray-400"></i>${recipe.view_count || 0} Lượt xem</span>
                                </p>
                            </div>
                            <div class="w-8 flex justify-center text-gray-300 group-hover:text-brand-green transition-colors"><i class="fas fa-angle-right"></i></div>
                        </a>`;
                    });
                    html += '</div>';
                    resultsBox.innerHTML = html;
                } else {
                    resultsBox.innerHTML = '<div class="p-8 text-center text-gray-500"><i class="fas fa-search-minus text-3xl mb-3 text-gray-300"></i><p class="font-medium text-sm">Tiếc quá, chưa tìm thấy công thức nào phù hợp.</p></div>';
                }
            }
        }
    });

    /* Styling to ensure scrolling inside max-height */
    const style = document.createElement('style');
    style.innerHTML = `
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(155,34,38,0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(155,34,38,0.5); }
    `;
    document.head.appendChild(style);
</script>

