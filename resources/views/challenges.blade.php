@extends('layouts.app')

@section('title', 'Thử Thách Đọc Công thức - Góc Bếp')

@section('content')
    
    {{-- 1. HEADER HERO NỔI BẬT --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-[#7a1a1e] via-[#5c1316] to-[#3d0c0e] text-white py-16 sm:py-24 shadow-2xl">
        {{-- Background Image Overlay --}}
        <div class="absolute inset-0 opacity-20 mix-blend-overlay">
            <img src="{{ asset('images/auth/kitchen_1.png') }}" class="w-full h-full object-cover" alt="Background">
        </div>
        
        {{-- Decorative Orbs --}}
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-brand-accent rounded-full blur-[120px] opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-yellow-500 rounded-full blur-[100px] opacity-40 pointer-events-none"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <span class="inline-flex items-center gap-2 bg-yellow-500/20 border border-yellow-400/30 text-yellow-300 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider mb-5 shadow-lg shadow-yellow-500/20 backdrop-blur-sm">
                <i class="fas fa-fire animate-pulse text-yellow-400"></i> Sự kiện đặc biệt
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-serif font-black mb-4 leading-tight drop-shadow-lg text-transparent bg-clip-text bg-gradient-to-r from-white via-yellow-100 to-yellow-300">
                Sàn Đấu Thử Thách
            </h1>
            <p class="text-white/80 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed font-medium">
                Vượt qua giới hạn, hoàn thành mục tiêu để nhận ngay những 
                <strong class="text-yellow-400 drop-shadow-md">huy hiệu danh giá</strong> và 
                <strong class="text-yellow-400 drop-shadow-md">khung avatar độc quyền</strong> từ Góc Bếp.
            </p>
            
            {{-- Quick Info Badges --}}
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <div class="flex items-center gap-3 bg-black/30 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shadow-lg hover:bg-black/40 transition">
                    <i class="fas fa-trophy text-yellow-400 text-2xl"></i>
                    <div class="text-left">
                        <p class="text-white font-bold leading-none mb-1">Vinh Danh</p>
                        <p class="text-[11px] text-white/60 leading-none">Bảng xếp hạng</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-black/30 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shadow-lg hover:bg-black/40 transition">
                    <i class="fas fa-gift text-brand-accent text-2xl"></i>
                    <div class="text-left">
                        <p class="text-white font-bold leading-none mb-1">Quà Tặng</p>
                        <p class="text-[11px] text-white/60 leading-none">Vật phẩm độc quyền</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DANH SÁCH THỬ THÁCH (STYLE MỚI) --}}
    <main class="container mx-auto px-4 py-12 flex-grow min-h-screen max-w-5xl">
    
    {{-- Thông báo Flash Message --}}
    @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-8 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm animate-fade-in">
            <i class="fas fa-info-circle text-xl"></i>
            <span class="font-medium">{{ session('info') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        {{-- VÒNG LẶP: Duyệt qua từng thử thách thật trong Database --}}
        @foreach($challenges as $challenge)
            
            @php
                // Logic kiểm tra trạng thái của User với Thử thách này
                $userChallenge = null;
                $isJoined = false;
                $isCompleted = false;
                $percent = 0;

                if(Auth::check()) {
                    // Lấy thông tin từ bảng trung gian (pivot)
                    $userChallenge = Auth::user()->challenges->find($challenge->id);
                    if($userChallenge) {
                        $isJoined = true;
                        $isCompleted = $userChallenge->pivot->is_completed;
                        
                        // Tính % tiến độ
                        $current = $userChallenge->pivot->current_count;
                        $target = $challenge->target_count;
                        $percent = ($target > 0) ? ($current / $target) * 100 : 0;
                        if($percent > 100) $percent = 100;
                    }
                }
            @endphp

            {{-- TRƯỜNG HỢP 1: ĐÃ HOÀN THÀNH (Thẻ Vàng) --}}
            @if($isCompleted)
                <div class="bg-gradient-to-r from-yellow-50 to-white border border-yellow-200 rounded-2xl p-6 md:p-8 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 text-yellow-100 opacity-50 transform rotate-12">
                        <i class="fas fa-trophy text-9xl"></i>
                    </div>

                    <div class="flex-1 text-center md:text-left relative z-10">
                        <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider mb-3 inline-block">
                            Hoàn Thành
                        </span>
                        <h3 class="text-xl md:text-2xl font-serif font-bold text-gray-800 mb-1">
                            {{ $challenge->name }}
                        </h3>
                        <p class="text-gray-500 text-sm">
                            Bạn đã xuất sắc hoàn thành mục tiêu!
                        </p>
                    </div>

                    <div class="flex-shrink-0 relative z-10 flex flex-col md:flex-row items-center gap-3">
                        <!-- Badge -->
                        <div class="flex flex-col items-center">
                            @if($challenge->badge)
                                @php
                                    $completedBadgeIcon = $challenge->badge->icon;
                                    $isCompletedBadgeUrl = $completedBadgeIcon && (Str::startsWith($completedBadgeIcon, 'http') || Str::startsWith($completedBadgeIcon, 'badges/'));
                                    $completedBadgeIconUrl = $isCompletedBadgeUrl 
                                        ? (Str::startsWith($completedBadgeIcon, 'http') ? $completedBadgeIcon : asset('storage/' . $completedBadgeIcon))
                                        : null;
                                @endphp
                                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 mb-1 shadow-sm border border-yellow-200">
                                    @if($completedBadgeIconUrl)
                                        <img src="{{ $completedBadgeIconUrl }}" alt="{{ $challenge->badge->name }}" class="w-10 h-10 object-contain">
                                    @elseif($completedBadgeIcon && mb_strlen($completedBadgeIcon) <= 4)
                                        <span class="text-3xl">{{ $completedBadgeIcon }}</span>
                                    @else
                                        <i class="fas fa-medal text-3xl"></i>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-yellow-700 uppercase tracking-wide">{{ $challenge->badge->name }}</span>
                            @else
                                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 mb-1 shadow-sm border border-yellow-200">
                                    <i class="fas fa-medal text-3xl"></i>
                                </div>
                                <span class="text-xs font-bold text-yellow-700 uppercase tracking-wide">Danh hiệu</span>
                            @endif
                        </div>
                        
                        <!-- Avatar Frame (if exists) -->
                        @if($challenge->avatarFrame)
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-1 shadow-sm border border-purple-200 overflow-hidden p-1">
                                    <img src="{{ Str::startsWith($challenge->avatarFrame->frame_image, 'http') ? $challenge->avatarFrame->frame_image : asset('storage/' . $challenge->avatarFrame->frame_image) }}" 
                                         alt="{{ $challenge->avatarFrame->name }}" 
                                         class="w-full h-full object-contain">
                                </div>
                                <span class="text-xs font-bold text-purple-700 uppercase tracking-wide">Khung Avatar</span>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- TRƯỜNG HỢP 2: ĐANG THỰC HIỆN (Thẻ Design Mới) --}}
            @elseif($isJoined)
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden group hover:shadow-lg transition duration-300">
                    {{-- Header với badge trạng thái --}}
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 px-6 py-3 border-b border-gray-100">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                    Đang Diễn Ra
                                </span>
                                <span class="text-xs text-gray-500 font-medium">
                                    <i class="fas fa-clock mr-1"></i> Còn {{ (int) now()->diffInDays(\Carbon\Carbon::parse($challenge->end_date)) }} ngày
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">
                                Hạn chót: {{ \Carbon\Carbon::parse($challenge->end_date)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8">
                        <div class="flex flex-col lg:flex-row gap-6">
                            {{-- Left: Thông tin & Tiến độ --}}
                            <div class="flex-1">
                                <h3 class="text-xl md:text-2xl font-serif font-bold text-gray-800 mb-2">
                                    {{ $challenge->name }}
                                </h3>
                                <p class="text-gray-500 text-sm mb-5">
                                    {{ $challenge->description ?? 'Cố lên! Bạn đang làm rất tốt.' }}
                                </p>

                                {{-- Thanh tiến độ đẹp hơn --}}
                                <div class="bg-gray-100 rounded-full h-4 w-full overflow-hidden relative border border-gray-200 mb-2">
                                    <div class="bg-gradient-to-r from-brand-green to-emerald-400 h-full rounded-full relative transition-all duration-1000 flex items-center justify-end pr-2" style="width: {{ max($percent, 5) }}%">
                                        @if($percent >= 15)
                                            <span class="text-[10px] text-white font-bold">{{ round($percent) }}%</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs font-bold mb-6">
                                    <span class="text-brand-green flex items-center gap-1">
                                        <i class="fas {{ $challenge->action_type == 'post_comment' ? 'fa-comments' : 'fa-pen-fancy' }}"></i>
                                        {{ $userChallenge->pivot->current_count }} / {{ $challenge->target_count }} {{ $challenge->action_type == 'post_comment' ? 'bình luận' : 'bài' }}
                                    </span>
                                    @if($percent < 15)
                                        <span class="text-gray-400">{{ round($percent) }}%</span>
                                    @endif
                                </div>
                                
                                {{-- CTA Button --}}
                                <a href="{{ route('recipes.create') }}" class="inline-flex items-center gap-2 bg-brand-green text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg hover:bg-[#1e3a2f] transition transform hover:-translate-y-0.5 hover:shadow-xl">
                                    <i class="fas fa-feather-alt"></i>
                                    Đăng Công Thức Ngay
                                </a>
                            </div>
                            
                            {{-- Right: Phần thưởng --}}
                            <div class="lg:w-64 flex-shrink-0">
                                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <i class="fas fa-gift text-brand-accent"></i> Phần Thưởng Nhận Được
                                    </h4>
                                    
                                    <div class="space-y-3">
                                        {{-- Badge Reward --}}
                                        @if($challenge->badge)
                                            @php
                                                $badgeIcon = $challenge->badge->icon;
                                                $isBadgeUrl = $badgeIcon && (Str::startsWith($badgeIcon, 'http') || Str::startsWith($badgeIcon, 'badges/'));
                                                $badgeIconUrl = $isBadgeUrl 
                                                    ? (Str::startsWith($badgeIcon, 'http') ? $badgeIcon : asset('storage/' . $badgeIcon))
                                                    : null;
                                            @endphp
                                            <div class="flex items-center gap-3 bg-yellow-50 p-2.5 rounded-lg border border-yellow-100">
                                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    @if($badgeIconUrl)
                                                        <img src="{{ $badgeIconUrl }}" alt="" class="w-6 h-6 object-contain">
                                                    @elseif($badgeIcon && mb_strlen($badgeIcon) <= 4)
                                                        <span class="text-xl">{{ $badgeIcon }}</span>
                                                    @else
                                                        <i class="fas fa-medal text-white"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $challenge->badge->name }}</p>
                                                    <p class="text-[10px] text-yellow-600">Huy Hiệu</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 bg-yellow-50 p-2.5 rounded-lg border border-yellow-100">
                                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center shadow-sm">
                                                    <i class="fas fa-medal text-white"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-800">Danh Hiệu</p>
                                                    <p class="text-[10px] text-yellow-600">Huy Hiệu Vinh Danh</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Avatar Frame Reward --}}
                                        @if($challenge->avatarFrame)
                                            <div class="flex items-center gap-3 bg-purple-50 p-2.5 rounded-lg border border-purple-100">
                                                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center shadow-sm overflow-hidden p-1">
                                                    <img src="{{ Str::startsWith($challenge->avatarFrame->frame_image, 'http') ? $challenge->avatarFrame->frame_image : asset('storage/' . $challenge->avatarFrame->frame_image) }}" 
                                                         alt="{{ $challenge->avatarFrame->name }}" 
                                                         class="w-full h-full object-contain">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $challenge->avatarFrame->name }}</p>
                                                    <p class="text-[10px] text-purple-600">Khung Avatar</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- TRƯỜNG HỢP 3: CHƯA THAM GIA (Thẻ Design Mới) --}}
            @else
                @php
                    $today = \Carbon\Carbon::now()->startOfDay();
                    $startDate = \Carbon\Carbon::parse($challenge->start_date)->startOfDay();
                    $endDate = \Carbon\Carbon::parse($challenge->end_date)->startOfDay();
                    $isUpcoming = $startDate->gt($today);
                    $isEnded = $endDate->lt($today);
                @endphp
                <div class="bg-gradient-to-br from-[#1a2f25] via-[#2A483A] to-[#1a2f25] rounded-2xl shadow-xl text-white relative overflow-hidden group hover:-translate-y-1 transition duration-300">
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 opacity-5" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
                    
                    {{-- Decorative Elements --}}
                    <div class="absolute -right-8 -top-8 w-40 h-40 bg-brand-accent/10 rounded-full blur-2xl"></div>
                    <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-yellow-400/10 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10 p-6 md:p-8">
                        {{-- Header với thời gian --}}
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            @if($isUpcoming)
                                <span class="inline-flex items-center gap-2 bg-orange-500/20 border border-orange-400/30 text-orange-400 text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                    <i class="far fa-clock"></i>
                                    Sắp Diễn Ra
                                </span>
                            @elseif($isEnded)
                                <span class="inline-flex items-center gap-2 bg-gray-500/20 border border-gray-400/30 text-gray-400 text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                    <i class="fas fa-flag-checkered"></i>
                                    Đã Kết Thúc
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 bg-brand-accent/20 border border-brand-accent/30 text-brand-accent text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                    <span class="w-2 h-2 bg-brand-accent rounded-full animate-pulse"></span>
                                    Đang Diễn Ra
                                </span>
                            @endif
                            <div class="flex items-center gap-4 text-xs text-white/60 font-medium">
                                <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full">
                                    <i class="far fa-calendar-alt"></i> 
                                    {{ \Carbon\Carbon::parse($challenge->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($challenge->end_date)->format('d/m/Y') }}
                                </span>
                                <span class="flex items-center gap-1.5 bg-white/5 px-3 py-1.5 rounded-full">
                                    <i class="fas fa-users"></i>
                                    {{ $challenge->users->count() }} người tham gia
                                </span>
                            </div>
                        </div>
                        
                        {{-- Main Content --}}
                        <div class="flex flex-col lg:flex-row gap-6">
                            {{-- Left: Info --}}
                            <div class="flex-1">
                                <h3 class="text-2xl md:text-3xl font-serif font-bold mb-3 text-white leading-tight">
                                    {{ $challenge->name }}
                                </h3>
                                <p class="text-white/70 text-sm md:text-base font-light mb-5 leading-relaxed">
                                    {{ $challenge->description ?? 'Hoàn thành mục tiêu để nhận huy hiệu danh giá và khung avatar độc quyền.' }}
                                </p>
                                
                                {{-- Mục tiêu với visual --}}
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/10">
                                        <i class="fas {{ $challenge->action_type == 'post_comment' ? 'fa-comments' : 'fa-bullseye' }} text-brand-accent"></i>
                                        <span class="text-sm font-semibold">{{ $challenge->target_count }} {{ $challenge->action_type == 'post_comment' ? 'bình luận' : 'bài đăng' }}</span>
                                    </div>
                                    @if($isUpcoming)
                                        <div class="text-xs text-white/50">
                                            Bắt đầu sau <span class="text-orange-400 font-bold">{{ (int) now()->diffInDays(\Carbon\Carbon::parse($challenge->start_date)) }}</span> ngày
                                        </div>
                                    @elseif(!$isEnded)
                                        <div class="text-xs text-white/50">
                                            Còn <span class="text-brand-accent font-bold">{{ (int) now()->diffInDays(\Carbon\Carbon::parse($challenge->end_date)) }}</span> ngày
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- CTA Button --}}
                                @if($isUpcoming)
                                    <div class="inline-flex items-center gap-2 bg-gray-500/50 text-white/70 px-8 py-3 rounded-full font-bold cursor-not-allowed">
                                        <i class="far fa-clock"></i>
                                        <span>Sẽ mở vào {{ \Carbon\Carbon::parse($challenge->start_date)->format('d/m/Y') }}</span>
                                    </div>
                                @elseif($isEnded)
                                    <div class="inline-flex items-center gap-2 bg-gray-500/50 text-white/70 px-8 py-3 rounded-full font-bold cursor-not-allowed">
                                        <i class="fas fa-ban"></i>
                                        <span>Thử thách đã kết thúc</span>
                                    </div>
                                @else
                                    <form action="{{ route('challenge.join', $challenge->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-gradient-to-r from-brand-accent to-[#c29263] hover:from-[#c29263] hover:to-brand-accent text-white px-8 py-3 rounded-full font-bold shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-brand-accent/25 active:scale-95 flex items-center gap-2 group">
                                            <span>Tham Gia Ngay</span>
                                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            {{-- Right: Phần thưởng --}}
                            <div class="lg:w-72 flex-shrink-0">
                                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
                                    <h4 class="text-xs font-bold text-white/60 uppercase tracking-wider mb-4 flex items-center gap-2">
                                        <i class="fas fa-gift text-brand-accent"></i> Phần Thưởng
                                    </h4>
                                    
                                    <div class="space-y-4">
                                        {{-- Badge Reward --}}
                                        @if($challenge->badge)
                                            @php
                                                $badgeIcon2 = $challenge->badge->icon;
                                                $isBadgeUrl2 = $badgeIcon2 && (Str::startsWith($badgeIcon2, 'http') || Str::startsWith($badgeIcon2, 'badges/'));
                                                $badgeIconUrl2 = $isBadgeUrl2 
                                                    ? (Str::startsWith($badgeIcon2, 'http') ? $badgeIcon2 : asset('storage/' . $badgeIcon2))
                                                    : null;
                                            @endphp
                                            <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-500/10 to-transparent p-3 rounded-xl border border-yellow-400/20">
                                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg shadow-yellow-500/20">
                                                    @if($badgeIconUrl2)
                                                        <img src="{{ $badgeIconUrl2 }}" alt="" class="w-8 h-8 object-contain">
                                                    @elseif($badgeIcon2 && mb_strlen($badgeIcon2) <= 4)
                                                        <span class="text-2xl">{{ $badgeIcon2 }}</span>
                                                    @else
                                                        <i class="fas fa-medal text-white text-xl"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-white truncate">{{ $challenge->badge->name }}</p>
                                                    <p class="text-[10px] text-yellow-400/80 uppercase tracking-wide">Huy Hiệu Danh Dự</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-500/10 to-transparent p-3 rounded-xl border border-yellow-400/20">
                                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg shadow-yellow-500/20">
                                                    <i class="fas fa-medal text-white text-xl"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-white">Danh Hiệu</p>
                                                    <p class="text-[10px] text-yellow-400/80 uppercase tracking-wide">Huy Hiệu Vinh Danh</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        {{-- Avatar Frame Reward --}}
                                        @if($challenge->avatarFrame)
                                            <div class="flex items-center gap-3 bg-gradient-to-r from-purple-500/10 to-transparent p-3 rounded-xl border border-purple-400/20">
                                                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20 overflow-hidden p-1">
                                                    <img src="{{ Str::startsWith($challenge->avatarFrame->frame_image, 'http') ? $challenge->avatarFrame->frame_image : asset('storage/' . $challenge->avatarFrame->frame_image) }}" 
                                                         alt="{{ $challenge->avatarFrame->name }}" 
                                                         class="w-full h-full object-contain">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-white truncate">{{ $challenge->avatarFrame->name }}</p>
                                                    <p class="text-[10px] text-purple-400/80 uppercase tracking-wide">Khung Avatar Độc Quyền</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @endforeach

        {{-- Nếu không có thử thách nào --}}
        @if($challenges->isEmpty())
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-clipboard-list text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-600">Chưa có thử thách nào</h3>
                <p class="text-gray-500 text-sm">Hãy quay lại sau nhé!</p>
            </div>
        @endif
    </div>
</main>
@endsection
