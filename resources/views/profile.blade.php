@extends('layouts.app')

@section('title', 'Trang Cá Nhân - ' . $user->name)

@section('content')
    <main class="container mx-auto px-4 py-12 flex-grow min-h-screen">
        {{-- THÔNG BÁO THÀNH CÔNG (NẾU CÓ) --}}
        @if(session('success'))
            <div
                class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative shadow-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <span class="block sm:inline font-medium">{{ session('success') }}</span>
                <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                    <i class="fas fa-times text-green-500 hover:text-green-700"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- ============================================================== --}}
            {{-- CỘT TRÁI: SIDEBAR THÔNG TIN USER --}}
            {{-- ============================================================== --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-soft p-6 text-center border border-gray-100">

                    <div class="relative w-52 h-52 mx-auto mb-4 group">
                        <!-- Avatar Frame Overlay (rendered first but z-index higher) -->
                        @php
                            $equippedFrame = $user->equippedFrame();
                        @endphp

                        @if($equippedFrame)
                            <img src="{{ Str::startsWith($equippedFrame->frame_image, 'http') ? $equippedFrame->frame_image : asset('storage/' . $equippedFrame->frame_image) }}"
                                alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                        @endif

                        <!-- User Avatar (fixed size, centered, behind frame) -->
                        <div class="absolute inset-0 flex items-center justify-center z-0">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}"
                                class="w-32 h-32 rounded-full border-2 border-brand-beige shadow-md object-cover group-hover:border-brand-green transition duration-300">
                        </div>

                        @if(Auth::id() == $user->id)
                            <button onclick="openEditProfileModal()"
                                class="absolute bottom-0 right-0 bg-white border border-gray-200 p-1.5 rounded-full text-gray-500 hover:text-brand-green hover:border-brand-green shadow-sm transition z-20"
                                title="Đổi ảnh đại diện">
                                <i class="fas fa-camera text-xs"></i>
                            </button>
                        @endif
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 font-serif">{{ $user->name }}</h2>

                    {{-- [MỚI] ACTIVITY TITLE - Danh hiệu hoạt động --}}
                    @if(isset($activityTitle) && $activityTitle)
                        <div class="flex justify-center mt-1 mb-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold shadow-sm transition-transform hover:scale-105"
                                style="background-color: {{ $activityTitle->color }}15; color: {{ $activityTitle->color }}; border: 1px solid {{ $activityTitle->color }}30;">
                                <span class="text-sm">{{ $activityTitle->icon }}</span>
                                {{ $activityTitle->name }}
                            </span>
                        </div>
                    @endif

                    <p class="text-gray-500 text-sm mb-3">{{ $user->email }}</p>

                    <p
                        class="text-gray-600 text-sm italic mb-4 px-2 bg-gray-50 py-2 rounded-lg border border-gray-100 relative">
                        <i class="fas fa-quote-left text-gray-300 absolute top-1 left-1 text-xs"></i>
                        {{ $user->bio ?? 'Thành viên tích cực của Góc Bếp.' }}
                    </p>

                    <div class="flex justify-center gap-2 mb-6 flex-wrap">
                        @if($user->role == 'admin')
                            <span
                                class="px-3 py-1 bg-red-50 text-red-600 text-xs rounded-full font-bold border border-red-100 flex items-center gap-1">
                                <i class="fas fa-shield-alt"></i> Quản trị viên
                            </span>
                        @else
                            <span
                                class="px-3 py-1 bg-brand-green/10 text-brand-green text-xs rounded-full font-bold border border-brand-green/20">
                                Thành viên
                            </span>
                        @endif

                        @if($user->is_active)
                            <span
                                class="px-3 py-1 bg-green-50 text-green-600 text-xs rounded-full font-medium border border-green-100 flex items-center">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                Hoạt động
                            </span>
                        @else
                            <span
                                class="px-3 py-1 bg-red-50 text-red-600 text-xs rounded-full font-medium border border-red-100 flex items-center">
                                <i class="fas fa-ban mr-1.5 text-[10px]"></i>
                                Bị vô hiệu hóa
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-6 border-t border-b border-gray-100 py-4">
                        <div class="text-center">
                            <span class="block font-bold text-xl text-brand-green">{{ $totalSuggestedBooks ?? 0 }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Công thức</span>
                        </div>
                        <div class="text-center border-l border-gray-100">
                            <span class="block font-bold text-xl text-brand-accent">{{ $totalReviews ?? 0 }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Bình luận</span>
                        </div>

                        <div class="text-center mt-2 pt-2 border-t border-gray-50 col-span-2 grid grid-cols-2">
                            <div class="cursor-pointer hover:bg-gray-50 rounded transition p-1"
                                onclick="openFollowModal('following', {{ $user->id }})">
                                <span class="block font-bold text-lg text-gray-800">{{ $totalFollowing ?? 0 }}</span>
                                <span class="text-xs text-gray-400 uppercase hover:text-blue-500 transition">Đang theo
                                    dõi</span>
                            </div>
                            <div class="cursor-pointer hover:bg-gray-50 rounded transition p-1"
                                onclick="openFollowModal('followers', {{ $user->id }})">
                                <span class="block font-bold text-lg text-gray-800"
                                    id="follower-count">{{ $totalFollowers ?? 0 }}</span>
                                <span class="text-xs text-gray-400 uppercase hover:text-blue-500 transition">Người theo
                                    dõi</span>
                            </div>
                        </div>
                    </div>
                    {{-- [MỚI] KHUNG HIỂN THỊ DANH HIỆU (BADGES) --}}
                    <div class="mb-6 border-t border-b border-gray-100 py-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-medal mr-1"></i> Danh Hiệu
                            </h4>
                            @if(isset($isOwnProfile) && $isOwnProfile && $user->activeBadges && $user->activeBadges->count() > 1)
                                <button onclick="toggleBadgeEditMode()" id="btn-edit-badge-order"
                                    class="text-[10px] text-blue-500 hover:text-blue-700 font-bold transition">
                                    <i class="fas fa-arrows-alt mr-1"></i> Sắp xếp
                                </button>
                            @endif
                        </div>

                        @if($user->activeBadges && $user->activeBadges->count() > 0)
                            {{-- Chế độ xem bình thường --}}
                            <div id="badges-view-mode" class="flex justify-center flex-wrap gap-3">
                                @foreach($user->activeBadges as $badge)
                                    @php
                                        $icon = $badge->icon;
                                        $isUrl = $icon && (Str::startsWith($icon, 'http') || Str::startsWith($icon, '/'));
                                        $iconUrl = $isUrl
                                            ? (Str::startsWith($icon, 'http') ? $icon : asset('storage/' . $icon))
                                            : null;
                                    @endphp

                                    <div class="group relative cursor-help">
                                        @if($iconUrl)
                                            <img src="{{ $iconUrl }}" alt="{{ $badge->name }}"
                                                class="w-12 h-12 object-contain drop-shadow-sm transform group-hover:scale-110 transition duration-300"
                                                onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md\'>🏆</div>';">
                                        @elseif($icon && mb_strlen($icon) <= 4)
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-2xl shadow-md transform group-hover:scale-110 transition duration-300">
                                                {{ $icon }}
                                            </div>
                                        @else
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white shadow-md transform group-hover:scale-110 transition duration-300">
                                                <i class="fas fa-medal text-xl"></i>
                                            </div>
                                        @endif

                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-50 w-max">
                                            <div class="bg-gray-800 text-white text-xs rounded py-1 px-3 shadow-lg text-center">
                                                <div class="font-bold">{{ $badge->name }}</div>
                                                @if($badge->description)
                                                    <div class="text-[10px] text-gray-300 font-normal">{{ $badge->description }}</div>
                                                @endif
                                            </div>
                                            <div
                                                class="w-2 h-2 bg-gray-800 transform rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Chế độ sắp xếp (chỉ cho chủ profile) --}}
                            @if(isset($isOwnProfile) && $isOwnProfile && $user->activeBadges->count() > 1)
                                <div id="badges-edit-mode" class="hidden">
                                    <p class="text-[10px] text-gray-400 text-center mb-3">
                                        <i class="fas fa-info-circle"></i> Kéo thả để sắp xếp thứ tự hiển thị (5 cái đầu tiên sẽ
                                        hiển thị ở bình luận)
                                    </p>
                                    <div id="sortable-badges" class="flex justify-center flex-wrap gap-3">
                                        @foreach($user->activeBadges as $badge)
                                            @php
                                                $icon = $badge->icon;
                                                $isUrl = $icon && (Str::startsWith($icon, 'http') || Str::startsWith($icon, '/'));
                                                $iconUrl = $isUrl
                                                    ? (Str::startsWith($icon, 'http') ? $icon : asset('storage/' . $icon))
                                                    : null;
                                            @endphp

                                            <div class="badge-item cursor-move relative" data-badge-id="{{ $badge->id }}">
                                                <div
                                                    class="absolute -top-1 -left-1 w-4 h-4 bg-blue-500 text-white text-[8px] rounded-full flex items-center justify-center font-bold z-10 badge-order-number">
                                                    {{ $loop->iteration }}
                                                </div>
                                                @if($iconUrl)
                                                    <img src="{{ $iconUrl }}" alt="{{ $badge->name }}"
                                                        class="w-12 h-12 object-contain drop-shadow-sm ring-2 ring-blue-300 ring-offset-1 rounded-lg"
                                                        onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22gold%22><circle cx=%2212%22 cy=%2212%22 r=%2210%22/></svg>';">
                                                @elseif($icon && mb_strlen($icon) <= 4)
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-2xl shadow-md ring-2 ring-blue-300 ring-offset-1">
                                                        {{ $icon }}
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white shadow-md ring-2 ring-blue-300 ring-offset-1">
                                                        <i class="fas fa-medal text-xl"></i>
                                                    </div>
                                                @endif
                                                <div class="text-[8px] text-center text-gray-500 mt-1 truncate w-12">
                                                    {{ Str::limit($badge->name, 8) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-center gap-2 mt-4">
                                        <button onclick="saveBadgeOrder()"
                                            class="px-4 py-1.5 bg-blue-500 text-white text-xs font-bold rounded-lg hover:bg-blue-600 transition shadow-sm">
                                            <i class="fas fa-save mr-1"></i> Lưu thứ tự
                                        </button>
                                        <button onclick="toggleBadgeEditMode()"
                                            class="px-4 py-1.5 bg-gray-200 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-300 transition">
                                            <i class="fas fa-times mr-1"></i> Hủy
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @else
                            {{-- Empty State: Chưa có danh hiệu nào --}}
                            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                <i class="fas fa-medal text-3xl text-gray-300 mb-2"></i>
                                @if(isset($isOwnProfile) && $isOwnProfile)
                                    <p class="text-xs text-gray-400">Bạn chưa có danh hiệu nào</p>
                                    <p class="text-[10px] text-gray-300 mt-1">Hoàn thành thử thách để nhận danh hiệu!</p>
                                @else
                                    <p class="text-xs text-gray-400">Chưa có danh hiệu nào</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    {{-- KẾT THÚC KHUNG DANH HIỆU --}}


                    {{-- [MỚI] KHUNG AVATAR --}}
                    <div class="mb-6 border-t border-b border-gray-100 py-4">
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">
                            <i class="fas fa-image mr-1"></i> Khung Avatar
                        </h4>

                        @if($user->avatarFrames && $user->avatarFrames->count() > 0)
                            @if(isset($isOwnProfile) && $isOwnProfile)
                                {{-- Hiển thị cho chủ profile - có thể trang bị/gỡ --}}
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($user->avatarFrames as $frame)
                                        <div class="relative group cursor-pointer border-2 rounded-lg p-1 transition-all
                                                                                                                                                                    {{ $frame->pivot->is_equipped ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}"
                                            onclick="equipFrame({{ $frame->id }})">

                                            <!-- Frame Preview -->
                                            <div
                                                class="aspect-square bg-gray-50 rounded overflow-hidden flex items-center justify-center">
                                                <img src="{{ Str::startsWith($frame->frame_image, 'http') ? $frame->frame_image : asset('storage/' . $frame->frame_image) }}"
                                                    alt="{{ $frame->name }}" class="w-full h-full object-contain">
                                            </div>

                                            <!-- Equipped Badge -->
                                            @if($frame->pivot->is_equipped)
                                                <div
                                                    class="absolute -top-1 -right-1 bg-purple-500 text-white text-[8px] px-1 py-0.5 rounded-full font-bold shadow">
                                                    Đang dùng
                                                </div>
                                            @endif

                                            <!-- Tooltip -->
                                            <div
                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-50 w-max">
                                                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 shadow-lg text-center">
                                                    <div class="font-bold">{{ $frame->name }}</div>
                                                </div>
                                                <div
                                                    class="w-2 h-2 bg-gray-800 transform rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($user->equippedFrame())
                                    <button onclick="unequipFrame()"
                                        class="mt-3 w-full text-[10px] text-gray-500 hover:text-red-500 transition py-1 rounded hover:bg-red-50 border border-transparent hover:border-red-200">
                                        <i class="fas fa-times-circle"></i> Gỡ khung avatar
                                    </button>
                                @endif
                            @else
                                {{-- Hiển thị cho người xem - chỉ xem, không trang bị --}}
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($user->avatarFrames as $frame)
                                        <div
                                            class="relative group border-2 rounded-lg p-1 transition-all
                                                                                                                                                                    {{ $frame->pivot->is_equipped ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }}">

                                            <!-- Frame Preview -->
                                            <div
                                                class="aspect-square bg-gray-50 rounded overflow-hidden flex items-center justify-center">
                                                <img src="{{ Str::startsWith($frame->frame_image, 'http') ? $frame->frame_image : asset('storage/' . $frame->frame_image) }}"
                                                    alt="{{ $frame->name }}" class="w-full h-full object-contain">
                                            </div>

                                            <!-- Equipped Badge -->
                                            @if($frame->pivot->is_equipped)
                                                <div
                                                    class="absolute -top-1 -right-1 bg-purple-500 text-white text-[8px] px-1 py-0.5 rounded-full font-bold shadow">
                                                    Đang dùng
                                                </div>
                                            @endif

                                            <!-- Tooltip -->
                                            <div
                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-50 w-max">
                                                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 shadow-lg text-center">
                                                    <div class="font-bold">{{ $frame->name }}</div>
                                                </div>
                                                <div
                                                    class="w-2 h-2 bg-gray-800 transform rotate-45 absolute -bottom-1 left-1/2 -translate-x-1/2">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            {{-- Empty State: Chưa có khung avatar --}}
                            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                                @if(isset($isOwnProfile) && $isOwnProfile)
                                    <p class="text-xs text-gray-400">Bạn chưa sở hữu khung avatar nào</p>
                                @else
                                    <p class="text-xs text-gray-400">Chưa sở hữu khung avatar nào</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    {{-- KẾT THÚC KHUNG AVATAR --}}


                    <div class="text-xs text-gray-400 space-y-1.5 mb-6 text-left pl-2">
                        <p><i class="far fa-calendar-alt mr-2 w-4 text-center"></i> Tham gia: <span
                                class="text-gray-600">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                        </p>
                        <p><i class="fas fa-sync-alt mr-2 w-4 text-center"></i> Cập nhật: <span
                                class="text-gray-600">{{ $user->updated_at ? $user->updated_at->format('d/m/Y') : 'N/A' }}</span>
                        </p>
                    </div>

                    <div class="space-y-2">
                        @if(Auth::check() && Auth::id() != $user->id)
                            <button onclick="toggleFollow({{ $user->id }})" id="btn-follow"
                                class="w-full py-2.5 rounded-lg font-bold transition mb-4 shadow-md flex items-center justify-center gap-2 {{ Auth::user()->isFollowing($user->id) ? 'bg-gray-200 text-gray-800' : 'bg-blue-600 text-white hover:bg-blue-700' }}">
                                <i class="fas {{ Auth::user()->isFollowing($user->id) ? 'fa-check' : 'fa-user-plus' }}"></i>
                                <span id="follow-text">
                                    {{ Auth::user()->isFollowing($user->id) ? 'Đang theo dõi' : 'Theo dõi' }}
                                </span>
                            </button>
                        @endif

                        @if(Auth::id() == $user->id)
                            <button onclick="openEditProfileModal()"
                                class="block w-full border border-brand-green text-brand-green py-2 rounded-lg font-bold text-sm hover:bg-brand-green hover:text-white transition">
                                <i class="fas fa-edit mr-1"></i> Chỉnh sửa hồ sơ
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    class="block w-full border border-red-200 text-red-500 py-2 rounded-lg font-bold text-sm hover:bg-red-50 transition mt-2">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">

                {{-- ================================================================= --}}
                {{-- TABS NAVIGATION - 4 TABS --}}
                {{-- ================================================================= --}}
                @if(isset($isOwnProfile) && $isOwnProfile)
                    <div class="bg-white rounded-xl shadow-soft border border-gray-100 mb-6 overflow-hidden">
                        <div class="flex flex-wrap border-b border-gray-100">
                            {{-- Tab 1: Tổng quan --}}
                            <button onclick="showProfileTab('overview')" id="tab-btn-overview"
                                class="flex-1 min-w-[120px] px-4 py-4 text-sm font-bold transition-all border-b-2 border-brand-green text-brand-green bg-brand-green/5">
                                <i class="fas fa-th-large mr-2"></i>Tổng quan
                            </button>

                            {{-- Tab 2: Công Thức --}}
                            <button onclick="showProfileTab('reviews')" id="tab-btn-reviews"
                                class="flex-1 min-w-[120px] px-4 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-500 hover:text-brand-green hover:bg-gray-50">
                                <i class="fas fa-utensils mr-2"></i>Công Thức
                                <span
                                    class="ml-1 bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $totalReviews }}</span>
                            </button>

                            {{-- Thẻ Books bị ẩn do dùng chung với Recipes --}}
                            <button onclick="showProfileTab('books')" id="tab-btn-books"
                                class="hidden flex-1 min-w-[120px] px-4 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-500 hover:text-brand-accent hover:bg-gray-50">
                                <i class="fas fa-book-medical mr-2"></i>Công Thức Phụ
                                <span
                                    class="ml-1 bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $totalSuggestedBooks }}</span>
                            </button>

                            {{-- Tab 4: Bài Đã Lưu --}}
                            <button onclick="showProfileTab('saved')" id="tab-btn-saved"
                                class="flex-1 min-w-[120px] px-4 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-500 hover:text-yellow-600 hover:bg-gray-50">
                                <i class="fas fa-bookmark mr-2"></i>Bài Đã Lưu
                                <span
                                    class="ml-1 bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $savedPosts->count() }}</span>
                            </button>

                            {{-- Tab 5: Thùng rác --}}
                            <button onclick="showProfileTab('trash')" id="tab-btn-trash"
                                class="flex-1 min-w-[120px] px-4 py-4 text-sm font-bold transition-all border-b-2 border-transparent text-gray-500 hover:text-red-500 hover:bg-gray-50">
                                <i class="fas fa-trash-alt mr-2"></i>Thùng rác
                                @if($trashedPosts->count() > 0)
                                    <span
                                        class="ml-1 bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $trashedPosts->count() }}</span>
                                @endif
                            </button>
                        </div>
                    </div>
                @endif

                {{-- ================================================================= --}}
                {{-- TAB 1: TỔNG QUAN (OVERVIEW) --}}
                {{-- ================================================================= --}}
                <div id="tab-content-overview" class="tab-content">


                    {{-- Bài Review Gần Đây (3 bài) --}}
                    <div class="bg-white rounded-xl shadow-soft border border-gray-100 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                <i class="fas fa-utensils text-brand-green"></i> Công Thức Mới Nhất
                            </h3>
                            @if($totalReviews > 0)
                                <button onclick="showProfileTab('reviews')"
                                    class="text-sm text-brand-green font-bold hover:underline">
                                    Xem tất cả <i class="fas fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>

                        @if(count($reviews) > 0)
                            <div class="space-y-4">
                                @foreach($reviews->take(3) as $post)
                                    <div class="flex gap-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                        @php
                                            $cover = $post->image ?? null;
                                            $coverUrl = $cover ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover)) : 'https://placehold.co/50';
                                        @endphp
                                        <img src="{{ $coverUrl }}" class="w-12 h-16 object-cover rounded shadow-sm flex-shrink-0">
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-bold text-gray-800 text-sm line-clamp-1 group-hover:text-brand-green transition">
                                                {{ $post->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $post->category->name ?? 'Món ăn' }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="flex text-yellow-400 text-[10px]">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= ($post->rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-[10px] text-gray-400">•
                                                    {{ $post->created_at->diffForHumans() }}</span>
                                                @if($post->status == 'pending')
                                                    <span
                                                        class="text-[10px] text-yellow-600 bg-yellow-50 px-1.5 py-0.5 rounded font-bold">Chờ
                                                        duyệt</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(Auth::check() && Auth::id() == $post->user_id)
                                            {{-- Nút Sửa (chỉ cho chủ bài viết, ẩn khi pending_delete) --}}
                                            @if($post->status != 'pending_delete')
                                                <a href="{{ route('recipes.edit', $post->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 self-center opacity-0 group-hover:opacity-100 transition"
                                                    title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Nút Xóa (chờ admin duyệt) --}}
                                                <button onclick="requestDeleteReview({{ $post->id }})"
                                                    class="text-red-400 hover:text-red-600 self-center opacity-0 group-hover:opacity-100 transition"
                                                    title="Yêu cầu xóa">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @else
                                                {{-- Trạng thái chờ duyệt xóa --}}
                                                <button onclick="cancelDeleteReview({{ $post->id }})"
                                                    class="text-[10px] text-orange-600 bg-orange-50 hover:bg-orange-100 px-1.5 py-0.5 rounded font-bold self-center transition cursor-pointer"
                                                    title="Click để hủy yêu cầu xóa">
                                                    <i class="fas fa-undo mr-1"></i>Hủy xóa
                                                </button>
                                            @endif
                                        @else
                                            {{-- Nút Xem (cho người khác, ẩn khi pending_delete) --}}
                                            @if($post->status != 'pending_delete')
                                                <a href="{{ route('recipes.show', $post->slug ?? $post->id) }}"
                                                    class="text-brand-green hover:text-brand-green/80 self-center opacity-0 group-hover:opacity-100 transition"
                                                    title="Xem công thức">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-400">
                                <i class="fas fa-pen-nib text-2xl mb-2"></i>
                                <p class="text-sm">Chưa có bài review nào</p>
                                @if(isset($isOwnProfile) && $isOwnProfile)
                                    <a href="{{ route('recipes.create') }}"
                                        class="text-brand-accent text-sm font-bold hover:underline mt-2 inline-block">+ Viết công thức đầu
                                        tiên</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Công Thức Của Tôi Gần Đây (3 sách) --}}
                    <div class="bg-white rounded-xl shadow-soft border border-gray-100 p-6">
                        <div class="hidden flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                <i class="fas fa-book-medical text-brand-accent"></i> Công Thức Của Tôi Gần Đây
                            </h3>
                            @if($totalSuggestedBooks > 0)
                                <button onclick="showProfileTab('books')"
                                    class="text-sm text-brand-accent font-bold hover:underline">
                                    Xem tất cả <i class="fas fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>

                        @if(count($suggestedBooks) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach($suggestedBooks->take(3) as $book)
                                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                                        @php
                                            $cover = $book->cover_image ?? null;
                                            $coverUrl = $cover ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover)) : 'https://placehold.co/50x75';
                                        @endphp
                                        <img src="{{ $coverUrl }}" class="w-12 h-16 object-cover rounded shadow-sm flex-shrink-0">
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="font-bold text-gray-800 text-sm line-clamp-2 group-hover:text-brand-accent transition">
                                                {{ $book->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $book->user->name ?? 'Tác giả' }}
                                            </p>
                                            @if($book->status == 'published')
                                                <span class="text-[10px] text-green-600 font-bold"><i class="fas fa-check-circle"></i>
                                                    Đã duyệt</span>
                                            @else
                                                <span class="text-[10px] text-yellow-600 font-bold"><i class="fas fa-clock"></i> Chờ
                                                    duyệt</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-400">
                                <i class="fas fa-book-medical text-2xl mb-2"></i>
                                <p class="text-sm">Chưa chia sẻ công thức nào</p>
                                @if(isset($isOwnProfile) && $isOwnProfile)
                                    <a href="{{ route('recipes.create') }}"
                                        class="text-brand-accent text-sm font-bold hover:underline mt-2 inline-block">+ Chia sẻ công thức
                                        mới</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ================================================================= --}}
                {{-- TAB 2: BÀI REVIEW ĐÃ ĐĂNG (ĐẦY ĐỦ) --}}
                {{-- ================================================================= --}}
                <div id="tab-content-reviews" class="tab-content hidden">

                    <div
                        class="flex items-center justify-between mb-6 bg-white rounded-xl shadow-soft border border-gray-100 p-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800 font-serif border-l-4 border-brand-green pl-3">
                                {{ $totalReviews > 0 ? 'Bài Review Đã Đăng' : 'Chưa có bài viết nào' }}
                            </h3>
                            <span
                                class="bg-brand-green/10 text-brand-green text-sm px-3 py-1 rounded-full font-bold">{{ $totalReviews }}</span>
                        </div>

                        @if(Auth::check() && Auth::id() == $user->id)
                            <a href="{{ route('recipes.create') }}"
                                class="inline-flex items-center gap-1.5 bg-brand-accent hover:bg-[#c29263] text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm transition transform hover:-translate-y-0.5">
                                <i class="fas fa-pen-nib"></i> Viết Review mới
                            </a>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @forelse($reviews as $post)
                            <div
                                class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group relative">

                                {{-- [MỚI] BADGE TRẠNG THÁI (Góc trên cùng bên phải) --}}
                                <div class="absolute top-4 right-4 z-10">
                                    @if($post->status == 'pending')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200 shadow-sm animate-pulse">
                                            <i class="fas fa-clock"></i> Đang chờ duyệt
                                        </span>
                                    @elseif($post->status == 'rejected')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-200 shadow-sm">
                                            <i class="fas fa-times-circle"></i> Bị từ chối
                                        </span>
                                    @elseif($post->status == 'pending_delete')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 text-xs font-bold rounded-full border border-orange-200 shadow-sm animate-pulse">
                                            <i class="fas fa-trash-alt"></i> Chờ duyệt xóa
                                        </span>
                                    @elseif($post->status == 'published')
                                        {{-- Nếu bạn muốn hiện chữ Đã duyệt (thường thì không cần thiết, để trống cho đẹp) --}}
                                        {{-- <span class="text-green-600 text-xs font-bold"><i class="fas fa-check-circle"></i> Đã
                                            duyệt</span> --}}
                                    @endif
                                </div>

                                {{-- THÔNG TIN SÁCH (Giữ nguyên) --}}
                                <div class="flex justify-between items-start mb-4 pr-20"> {{-- Thêm pr-20 để tránh đè lên badge
                                    --}}
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('recipes.show', $post->slug ?? $post->id) }}" class="block shrink-0">
                                            {{-- Sửa lại đường dẫn ảnh cho chuẩn --}}
                                            @php
                                                $cover = $post->image ?? null;
                                                $coverUrl = $cover
                                                    ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                                    : 'https://placehold.co/50x70?text=Recipe';
                                            @endphp
                                            <img src="{{ $coverUrl }}"
                                                class="w-12 h-16 object-cover rounded shadow-sm border border-gray-200">
                                        </a>

                                        <div>
                                            <h4 class="font-bold text-gray-800 text-base mb-1">
                                                <a href="{{ route('recipes.show', $post->slug ?? $post->id) }}"
                                                    class="hover:text-brand-green transition">
                                                    {{ $post->title }}
                                                </a>
                                            </h4>
                                            <div class="flex text-yellow-400 text-xs items-center gap-2">
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= ($post->rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-gray-400 text-[11px]">•
                                                    {{ $post->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TIÊU ĐỀ & NỘI DUNG --}}
                                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-brand-green transition">
                                    {{ $post->title }}
                                </h3>

                                {{-- Nội dung review (Render HTML an toàn) --}}
                                <div class="text-gray-500 text-sm line-clamp-3 prose prose-sm max-w-none">
                                    {!! $post->content !!}
                                </div>

                                {{-- FOOTER --}}
                                <div
                                    class="flex items-center justify-between mt-4 text-xs text-gray-400 border-t border-gray-50 pt-3">
                                    <span class="flex items-center gap-2">
                                        <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                                    </span>

                                    <div class="flex items-center gap-3">
                                        {{-- Nút Sửa (ẩn khi đang chờ xóa) --}}
                                        @if(Auth::check() && Auth::id() == $post->user_id && $post->status != 'pending_delete')
                                            <a href="{{ route('recipes.edit', $post->id) }}"
                                                class="text-blue-500 hover:text-blue-700 font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1 transition">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                        @endif
                                        {{-- Nút Xóa (chờ admin duyệt) --}}
                                        @if(Auth::check() && Auth::id() == $post->user_id)
                                            @if($post->status != 'pending_delete')
                                                <a href="{{ route('recipes.edit', $post->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1 transition">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                {{-- Nút Xóa (chờ admin duyệt) --}}
                                                <button onclick="requestDeleteReview({{ $post->id }})"
                                                    class="text-red-500 hover:text-red-700 font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1 transition">
                                                    <i class="fas fa-trash-alt"></i> Xóa
                                                </button>
                                            @else
                                                {{-- Trạng thái chờ duyệt xóa --}}
                                                <button onclick="cancelDeleteReview({{ $post->id }})"
                                                    class="text-orange-600 hover:text-orange-800 font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1 transition cursor-pointer">
                                                    <i class="fas fa-undo"></i> Hủy xóa
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Link Xem chi tiết (ẩn khi pending_delete) --}}
                                        @if($post->status != 'pending_delete')
                                            @if($post->status == 'pending' && Auth::check() && Auth::id() == $post->user_id)
                                                <a href="{{ route('recipes.edit', $post->id) }}"
                                                    class="text-brand-green font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1">
                                                    Xem & Chỉnh sửa <i class="fas fa-arrow-right"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('recipes.show', $post->slug ?? $post->id) }}"
                                                    class="text-brand-green font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1">
                                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fas fa-pen-nib text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Người dùng này chưa có bài viết nào.</p>
                                @if(Auth::check() && Auth::id() == $user->id)
                                    <a href="{{ route('recipes.create') }}"
                                        class="text-brand-accent font-bold hover:underline text-sm">
                                        Viết công thức đầu tiên ngay
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    {{-- Phân trang Reviews --}}
                    @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                    @endif

                </div>{{-- End tab-content-reviews --}}

                {{-- ================================================================= --}}
                {{-- TAB 3: SÁCH ĐỀ XUẤT (ĐẦY ĐỦ) --}}
                {{-- ================================================================= --}}
                <div id="tab-content-books" class="tab-content hidden">
                    <div
                        class="flex items-center justify-between mb-6 bg-white rounded-xl shadow-soft border border-gray-100 p-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800 font-serif border-l-4 border-brand-accent pl-3">
                                {{ $totalSuggestedBooks > 0 ? 'Công Thức Của Tôi' : 'Chưa có công thức chia sẻ' }}
                            </h3>
                            <span
                                class="bg-brand-accent/10 text-brand-accent text-sm px-3 py-1 rounded-full font-bold">{{ $totalSuggestedBooks }}</span>
                        </div>

                        <a href="{{ route('recipes.create') }}"
                            class="inline-flex items-center gap-1.5 bg-brand-accent hover:bg-[#c29263] text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm transition transform hover:-translate-y-0.5">
                            <i class="fas fa-plus-circle"></i> Chia sẻ công thức mới
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($suggestedBooks as $book)
                            <div
                                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-card hover:-translate-y-1 transition-all duration-300 flex flex-row h-44 relative group">

                                <div class="w-28 relative flex-shrink-0 bg-gray-200">
                                    @if($book->status == 'published')
                                        <a href="{{ route('recipes.show', $book->slug ?? $book->id) }}">
                                    @endif
                                        @php
                                            $cover = $book->image ?? null;
                                            $coverUrl = $cover
                                                ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                                : 'https://placehold.co/150x225?text=' . urlencode(Str::limit($book->title, 10));
                                        @endphp
                                        <img src="{{ $coverUrl }}"
                                            class="w-full h-full object-cover transition group-hover:opacity-90">
                                        @if($book->status == 'published')
                                            </a>
                                        @endif
                                </div>
                                <div class="p-3 flex flex-col justify-between flex-grow min-w-0">
                                    <div>
                                        <h4 class="font-bold font-serif text-gray-800 text-sm mb-1 leading-tight line-clamp-2">
                                            @if($book->status == 'published')
                                                <a href="{{ route('recipes.show', $book->slug ?? $book->id) }}"
                                                    class="hover:text-brand-green transition">
                                                    {{ $book->title }}
                                                </a>
                                            @else
                                                {{ $book->title }}
                                            @endif
                                        </h4>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ $book->user->name ?? 'Tác giả' }}
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            <i class="far fa-calendar-alt mr-1"></i> Gửi:
                                            {{ $book->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    {{-- BADGE TRẠNG THÁI --}}
                                    <div class="mt-auto pt-2">
                                        @if($book->status == 'published')
                                            <a href="{{ route('recipes.show', $book->slug ?? $book->id) }}"
                                                class="inline-flex items-center gap-1 text-brand-green border border-brand-green/30 bg-brand-green/5 px-2.5 py-1 rounded text-[10px] font-bold hover:bg-brand-green hover:text-white transition">
                                                <i class="fas fa-check-circle"></i> ĐÃ DUYỆT
                                            </a>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px] font-bold px-2.5 py-1 rounded">
                                                <i class="fas fa-clock"></i> CHỜ DUYỆT
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fas fa-book-medical text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Bạn chưa đề xuất công thức nào.</p>
                                <p class="text-gray-400 text-xs mt-1 mb-3">Hãy đóng góp công thức mới cho cộng đồng nhé!</p>
                                <a href="{{ route('recipes.create') }}"
                                    class="text-brand-accent text-sm font-bold hover:underline">
                                    + Chia sẻ công thức ngay
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Phân trang Công thức --}}
                    @if($suggestedBooks instanceof \Illuminate\Pagination\LengthAwarePaginator && $suggestedBooks->hasPages())
                        <div class="mt-6">
                            {{ $suggestedBooks->links() }}
                        </div>
                    @endif

                </div>{{-- End tab-content-books --}}

                {{-- ================================================================= --}}
                {{-- TAB 4: BÀI VIẾT ĐÃ LƯU (SAVED POSTS) --}}
                {{-- ================================================================= --}}
                @if(isset($isOwnProfile) && $isOwnProfile)
                    <div id="tab-content-saved" class="tab-content hidden">
                        @if(isset($savedPosts) && $savedPosts->count() > 0)
                            <div class="space-y-6" id="saved-posts-container">
                                @foreach($savedPosts as $savedPost)
                                    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group relative"
                                        id="saved-post-{{ $savedPost->id }}">

                                        {{-- Nút Bỏ lưu --}}
                                        <button onclick="handleUnsavePost({{ $savedPost->id }}, this)"
                                            class="absolute top-4 right-4 text-yellow-500 hover:text-gray-400 transition z-10"
                                            title="Bỏ lưu">
                                            <i class="fas fa-bookmark text-lg"></i>
                                        </button>

                                        <div class="flex gap-5">
                                            @if($savedPost->recipe)
                                                <a href="{{ route('recipes.show', $savedPost->recipe->slug ?? $savedPost->recipe->id) }}" class="flex-shrink-0">
                                                    <img src="{{ $savedPost->recipe->image ? (Str::startsWith($savedPost->recipe->image, 'http') ? $savedPost->recipe->image : asset('storage/' . $savedPost->recipe->image)) : 'https://placehold.co/80x120' }}"
                                                        class="w-20 h-28 object-cover rounded-lg shadow-sm group-hover:shadow-md transition">
                                                </a>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                {{-- Tác giả bài viết --}}
                                                <div class="flex items-center gap-2 mb-2">
                                                    <a href="{{ route('public.profile', $savedPost->user->id) }}">
                                                        <img src="{{ $savedPost->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($savedPost->user->name) }}"
                                                            class="w-7 h-7 rounded-full border border-gray-200">
                                                    </a>
                                                    <a href="{{ route('public.profile', $savedPost->user->id) }}"
                                                        class="text-sm text-gray-700 hover:text-brand-green font-medium">
                                                        {{ $savedPost->user->name }}
                                                    </a>
                                                    <span class="text-gray-300">•</span>
                                                    <span
                                                        class="text-xs text-gray-400">{{ $savedPost->created_at->diffForHumans() }}</span>
                                                </div>

                                                @if($savedPost->recipe)
                                                    <a href="{{ route('recipes.show', $savedPost->recipe->slug ?? $savedPost->recipe->id) }}"
                                                        class="text-xs text-brand-green font-bold uppercase tracking-wider hover:underline mb-1 block">
                                                        {{ $savedPost->recipe->title }}
                                                    </a>
                                                @endif

                                                @if($savedPost->title)
                                                    <h4
                                                        class="font-bold text-gray-800 text-lg mb-2 group-hover:text-brand-green transition line-clamp-2">
                                                        "{{ $savedPost->title }}"
                                                    </h4>
                                                @endif

                                                <div class="text-gray-500 text-sm line-clamp-3 mb-3">
                                                    {!! Str::limit(strip_tags($savedPost->content), 200) !!}
                                                </div>

                                                {{-- Nút Like & Comment & Xem chi tiết --}}
                                                <div class="flex items-center gap-5 pt-3 border-t border-gray-100">
                                                    @php
                                                        $isLiked = Auth::check() && $savedPost->likes->where('user_id', Auth::id())->count() > 0;
                                                    @endphp
                                                    <button onclick="handleLike({{ $savedPost->id }}, 'post')"
                                                        id="like-btn-post-{{ $savedPost->id }}"
                                                        class="flex items-center gap-2 text-sm font-bold transition {{ $isLiked ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }}">
                                                        <i id="like-icon-post-{{ $savedPost->id }}"
                                                            class="{{ $isLiked ? 'fas' : 'far' }} fa-heart"></i>
                                                        <span
                                                            id="like-count-post-{{ $savedPost->id }}">{{ $savedPost->likes_count ?? 0 }}</span>
                                                    </button>

                                                    <button onclick="toggleSavedComment({{ $savedPost->id }})"
                                                        class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-blue-500 transition">
                                                        <i class="far fa-comment-dots"></i>
                                                        <span
                                                            id="comment-count-{{ $savedPost->id }}">{{ $savedPost->comments_count ?? 0 }}</span>
                                                    </button>

                                                    @if($savedPost->recipe)
                                                        <a href="{{ route('recipes.show', $savedPost->recipe->slug ?? $savedPost->recipe->id) }}"
                                                            class="text-brand-green font-bold hover:underline text-sm ml-auto flex items-center gap-1">
                                                            Xem chi tiết <i class="fas fa-arrow-right"></i>
                                                        </a>
                                                    @endif
                                                </div>

                                                {{-- Comment Box --}}
                                                <div id="saved-comment-box-{{ $savedPost->id }}"
                                                    class="hidden mt-4 pt-4 border-t border-dashed border-gray-100">
                                                    @if($savedPost->comments && $savedPost->comments->count() > 0)
                                                        <div
                                                            class="space-y-2 mb-3 pl-3 border-l-2 border-gray-100 max-h-40 overflow-y-auto">
                                                            @foreach($savedPost->comments->take(5) as $comment)
                                                                <div class="flex gap-2">
                                                                    <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}"
                                                                        class="w-6 h-6 rounded-full mt-0.5">
                                                                    <div class="bg-gray-50 px-3 py-2 rounded-lg text-sm flex-1">
                                                                        <span class="font-bold text-gray-700">{{ $comment->user->name }}</span>
                                                                        <span class="text-gray-600 ml-2">{{ $comment->content }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <form onsubmit="submitSavedComment({{ $savedPost->id }}, event)"
                                                        class="flex gap-2 items-center">
                                                        @csrf
                                                        <img src="{{ optional(Auth::user())->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional(Auth::user())->name ?? 'Guest') }}"
                                                            class="w-8 h-8 rounded-full">
                                                        <input type="text" name="content" required
                                                            class="flex-1 px-4 py-2 text-sm border border-gray-200 rounded-full focus:outline-none focus:border-brand-green"
                                                            placeholder="Viết bình luận...">
                                                        <button type="submit"
                                                            class="text-brand-green hover:text-brand-green-light px-2">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                                <div
                                    class="w-16 h-16 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-3 text-yellow-400">
                                    <i class="fas fa-bookmark text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Bạn chưa lưu bài viết nào.</p>
                                <p class="text-gray-400 text-sm mt-1">Nhấn biểu tượng <i class="far fa-bookmark"></i> trên bài
                                    review để lưu lại.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ================================================================= --}}
                {{-- TAB 5: THÙNG RÁC (TRASH) --}}
                {{-- ================================================================= --}}
                @if(isset($isOwnProfile) && $isOwnProfile)
                    <div id="tab-content-trash" class="tab-content hidden">
                        <div class="bg-white rounded-xl shadow-soft border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-bold text-lg text-gray-800">
                                    <i class="fas fa-trash-alt text-red-400 mr-2"></i>Thùng rác
                                </h3>
                                <span class="text-xs text-gray-400">Bài viết đã xóa có thể khôi phục</span>
                            </div>

                            @if($trashedPosts->count() > 0)
                                <div class="space-y-4">
                                    @foreach($trashedPosts as $post)
                                        <div
                                            class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 group hover:bg-gray-100 transition">
                                            {{-- Ảnh món ăn --}}
                                            @php
                                                $cover = $post->image ?? null;
                                                $coverUrl = $cover
                                                    ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                                    : null;
                                            @endphp
                                            @if($coverUrl)
                                                <img src="{{ $coverUrl }}"
                                                    alt="{{ $post->title }}"
                                                    class="w-12 h-16 object-cover rounded shadow-sm flex-shrink-0 opacity-60">
                                            @else
                                                <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-utensils text-gray-400"></i>
                                                </div>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-gray-700 truncate line-through">{{ $post->title }}</h4>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <i class="fas fa-tag mr-1"></i>{{ $post->category->name ?? 'Không phân loại' }}
                                                </p>
                                                <p class="text-xs text-red-400 mt-1">
                                                    <i class="fas fa-trash mr-1"></i>Đã xóa: {{ $post->deleted_at->diffForHumans() }}
                                                </p>
                                            </div>

                                            {{-- Các nút hành động --}}
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <button onclick="restoreReview({{ $post->id }})"
                                                    class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-bold rounded-lg transition transform hover:scale-105 flex items-center gap-1">
                                                    <i class="fas fa-undo"></i> Khôi phục
                                                </button>
                                                <button onclick="forceDeleteReview({{ $post->id }})"
                                                    class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition transform hover:scale-105 flex items-center gap-1">
                                                    <i class="fas fa-times"></i> Xóa vĩnh viễn
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Bài viết trong thùng rác sẽ được giữ vô thời hạn. Bạn có thể khôi phục bất cứ lúc nào.
                                    </p>
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-trash-alt text-3xl text-gray-300"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-600 mb-2">Thùng rác trống</h4>
                                    <p class="text-sm text-gray-400">Không có bài viết nào trong thùng rác.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

        </div>

        {{-- Modal Follow (Giữ nguyên) --}}
        <div id="followModal" class="fixed inset-0 z-[60] hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeFollowModal()"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Danh sách</h3>
                                <button onclick="closeFollowModal()" class="text-gray-400 hover:text-gray-600 transition">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white px-4 py-2 sm:p-6 max-h-[400px] overflow-y-auto" id="modal-body">
                            <div class="flex justify-center py-4">
                                <i class="fas fa-spinner fa-spin text-brand-green text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // --- 0. Xử lý chuyển đổi Tab Profile --        -
        function showProfileTab(tabName) {
            // Danh công thức các tab
            const tabs = ['overview', 'reviews', 'books', 'saved', 'trash'];

            // Ẩn tất cả nội dung tab
            tabs.forEach(tab => {
                const content = document.getElementById(`tab-content-${tab}`);
                const btn = document.getElementById(`tab-btn-${tab}`);

                if (content) {
                    content.classList.add('hidden');
                }

                if (btn) {
                    btn.classList.remove('border-brand-green', 'text-brand-green', 'bg-brand-green/5');
                    btn.classList.remove('border-brand-accent', 'text-brand-accent', 'bg-brand-accent/5');
                    btn.classList.remove('border-yellow-500', 'text-yellow-600', 'bg-yellow-50');
                    btn.classList.remove('border-red-500', 'text-red-500', 'bg-red-50');
                    btn.classList.add('border-transparent', 'text-gray-500');
                }
            });

            // Hiển thị tab được chọn
            const activeContent = document.getElementById(`tab-content-${tabName}`);
            const activeBtn = document.getElementById(`tab-btn-${tabName}`);

            if (activeContent) {
                activeContent.classList.remove('hidden');
            }

            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-500');

                // Màu sắc khác nhau cho mỗi tab
                if (tabName === 'overview' || tabName === 'reviews') {
                    activeBtn.classList.add('border-brand-green', 'text-brand-green', 'bg-brand-green/5');
                } else if (tabName === 'books') {
                    activeBtn.classList.add('border-brand-accent', 'text-brand-accent', 'bg-brand-accent/5');
                } else if (tabName === 'saved') {
                    activeBtn.classList.add('border-yellow-500', 'text-yellow-600', 'bg-yellow-50');
                } else if (tabName === 'trash') {
                    activeBtn.classList.add('border-red-500', 'text-red-500', 'bg-red-50');
                }
            }
        }

        // Auto-switch tab dựa trên URL parameters (khi phân trang)
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('review_page')) {
                showProfileTab('reviews');
            } else if (urlParams.has('book_page')) {
                showProfileTab('books');
            }
        });

        // --- 1. Xử lý Nút Toggle Follow (Một hàm duy nhất) ---
        function toggleFollow(userId) {
            fetch('{{ route('follow.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ user_id: userId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') { alert(data.message); return; }

                    const btn = document.getElementById('btn-follow');
                    const text = document.getElementById('follow-text');
                    const icon = btn.querySelector('i');
                    const countSpan = document.getElementById('follower-count');

                    if (data.follower_count !== undefined) countSpan.innerText = data.follower_count;

                    if (data.action === 'followed') {
                        btn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                        btn.classList.add('bg-gray-200', 'text-gray-800');
                        text.innerText = 'Đang theo dõi';
                        icon.className = 'fas fa-check';
                    } else {
                        btn.classList.remove('bg-gray-200', 'text-gray-800');
                        btn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');
                        text.innerText = 'Theo dõi';
                        icon.className = 'fas fa-user-plus';
                    }
                })
                .catch(error => console.error('Lỗi Follow:', error));
        }

        // --- 2. Xử lý Modal Danh công thức Follow ---
        function openFollowModal(type, userId) {
            const modal = document.getElementById('followModal');
            const title = document.getElementById('modal-title');
            const body = document.getElementById('modal-body');

            // Reset nội dung loading
            body.innerHTML = '<div class="flex justify-center py-4"><i class="fas fa-spinner fa-spin text-brand-green text-2xl"></i></div>';

            // Hiện modal
            modal.classList.remove('hidden');

            // Đặt tiêu đề
            if (type === 'followers') title.innerText = 'Người theo dõi';
            else title.innerText = 'Đang theo dõi';

            // Gọi API lấy danh sách
            fetch(`/api/user/${userId}/${type}`)
                .then(res => res.json())
                .then(users => {
                    body.innerHTML = ''; // Xóa loading

                    if (users.length === 0) {
                        body.innerHTML = '<p class="text-center text-gray-500 py-4 text-sm">Chưa có ai trong danh công thức này.</p>';
                        return;
                    }

                    // Vẽ danh công thức user
                    let html = '<div class="space-y-3">';
                    users.forEach(u => {
                        // Logic lấy avatar (Nếu null thì dùng UI Avatars)
                        const avatar = u.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random`;

                        // Link tới profile người đó
                        html += `
                                                                <a href="/profile/${u.id}" class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition group border border-transparent hover:border-gray-100">
                                                                    <img src="${avatar}" class="w-10 h-10 rounded-full border border-gray-200 object-cover">
                                                                    <div>
                                                                        <h4 class="font-bold text-gray-800 text-sm group-hover:text-brand-green transition">${u.name}</h4>
                                                                    </div>
                                                                    <div class="ml-auto">
                                                                        <span class="text-xs text-gray-400 group-hover:text-brand-green"><i class="fas fa-chevron-right"></i></span>
                                                                    </div>
                                                                </a>
                                                            `;
                    });
                    html += '</div>';
                    body.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    body.innerHTML = '<p class="text-center text-red-500 py-4 text-sm">Không thể tải dữ liệu.</p>';
                });
        }

        function closeFollowModal() {
            document.getElementById('followModal').classList.add('hidden');
        }

        // Đóng modal khi nhấn ESC
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape") {
                closeFollowModal();
                closeEditProfileModal();
            }
        });

        // --- 3. Xử lý Modal Chỉnh sửa Hồ sơ ---
        function openEditProfileModal() {
            document.getElementById('editProfileModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').classList.add('hidden');
            document.body.style.overflow = ''; // Restore scroll
        }

        // Xem trước ảnh khi chọn file
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                    // Xóa URL input khi chọn file
                    document.getElementById('avatarUrlInput').value = '';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Xem trước ảnh từ URL
        function previewAvatarUrl(url) {
            if (url) {
                document.getElementById('avatarPreview').src = url;
                // Xóa file input khi nhập URL
                document.getElementById('avatarInput').value = '';
            }
        }

        // Chuyển tab upload avatar
        function showAvatarTab(type) {
            const fileTab = document.getElementById('avatar-tab-file');
            const urlTab = document.getElementById('avatar-tab-url');
            const fileDiv = document.getElementById('avatar-upload-file');
            const urlDiv = document.getElementById('avatar-upload-url');

            if (type === 'file') {
                fileTab.classList.remove('bg-gray-100', 'text-gray-600');
                fileTab.classList.add('bg-brand-green/10', 'text-brand-green');
                urlTab.classList.remove('bg-brand-green/10', 'text-brand-green');
                urlTab.classList.add('bg-gray-100', 'text-gray-600');
                fileDiv.classList.remove('hidden');
                urlDiv.classList.add('hidden');
            } else {
                urlTab.classList.remove('bg-gray-100', 'text-gray-600');
                urlTab.classList.add('bg-brand-green/10', 'text-brand-green');
                fileTab.classList.remove('bg-brand-green/10', 'text-brand-green');
                fileTab.classList.add('bg-gray-100', 'text-gray-600');
                urlDiv.classList.remove('hidden');
                fileDiv.classList.add('hidden');
            }
        }

        // Submit form chỉnh sửa hồ sơ
        function submitEditProfile(event) {
            event.preventDefault();

            const form = document.getElementById('editProfileForm');
            const formData = new FormData(form);
            const submitBtn = document.getElementById('editProfileSubmitBtn');
            const errorDiv = document.getElementById('editProfileError');

            // Disable button và hiện loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang lưu...';
            errorDiv.classList.add('hidden');

            fetch('{{ route("profile.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật giao diện với dữ liệu mới
                        document.querySelectorAll('[data-user-name]').forEach(el => {
                            el.textContent = data.user.name;
                        });
                        document.querySelectorAll('[data-user-bio]').forEach(el => {
                            el.textContent = data.user.bio || 'Thành viên tích cực của Góc Bếp.';
                        });
                        document.querySelectorAll('[data-user-avatar]').forEach(el => {
                            el.src = data.user.avatar;
                        });

                        // Đóng modal và reload trang để hiển thị đúng
                        closeEditProfileModal();
                        window.location.reload();
                    } else {
                        errorDiv.textContent = data.message || 'Có lỗi xảy ra!';
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.textContent = 'Có lỗi xảy ra, vui lòng thử lại!';
                    errorDiv.classList.remove('hidden');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Lưu thay đổi';
                });
        }

        // --- 4. Xử lý trang bị khung avatar ---
        function equipFrame(frameId) {
            fetch('{{ route("profile.avatar-frame.equip") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ avatar_frame_id: frameId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi trang bị khung!');
                });
        }

        function unequipFrame() {
            fetch('{{ route("profile.avatar-frame.unequip") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // --- 5. Yêu cầu xóa bài review (chờ admin duyệt) ---
        async function requestDeleteReview(postId) {
            const result = await SwalConfirm('Xác nhận yêu cầu', 'Bạn có chắc muốn yêu cầu xóa bài review này?\n\nYêu cầu sẽ được gửi đến Admin để xử lý.', 'question', 'Gửi yêu cầu');
            if (!result.isConfirmed) return;

            fetch(`/reviews/${postId}/request-delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu xóa!');
                });
        }

        // --- 6. Hủy yêu cầu xóa bài review ---
        async function cancelDeleteReview(postId) {
            const result = await SwalConfirm('Xác nhận hủy', 'Bạn có chắc muốn hủy yêu cầu xóa và khôi phục bài viết này?', 'info', 'Khôi phục ngay');
            if (!result.isConfirmed) return;

            fetch(`/reviews/${postId}/cancel-delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi hủy yêu cầu xóa!');
                });
        }

        // --- 7. Khôi phục bài review từ thùng rác ---
        async function restoreReview(postId) {
            const result = await SwalConfirm('Khôi phục bài viết', 'Bạn có chắc muốn khôi phục bài viết này?', 'info', 'Khôi phục');
            if (!result.isConfirmed) return;

            fetch(`/reviews/${postId}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi khôi phục bài viết!');
                });
        }

        // --- 8. Xóa vĩnh viễn bài review ---
        async function forceDeleteReview(postId) {
            const result = await SwalConfirm('CẢNH BÁO', 'Hành động này không thể hoàn tác!\n\nBạn có chắc chắn muốn xóa vĩnh viễn bài viết này?', 'warning', 'Xóa vĩnh viễn');
            if (!result.isConfirmed) return;

            fetch(`/reviews/${postId}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi xóa bài viết!');
                });
        }
    </script>

    {{-- ============================================================== --}}
    {{-- MODAL CHỈNH SỬA HỒ SƠ --}}
    {{-- ============================================================== --}}
    @if(Auth::check() && Auth::id() == $user->id)
        <div id="editProfileModal" class="fixed inset-0 z-[70] hidden" aria-labelledby="edit-profile-title" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditProfileModal()">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md">

                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-brand-green to-emerald-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2" id="edit-profile-title">
                                    <i class="fas fa-user-edit"></i> Chỉnh sửa hồ sơ
                                </h3>
                                <button onclick="closeEditProfileModal()" class="text-white/80 hover:text-white transition p-1">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <form id="editProfileForm" onsubmit="submitEditProfile(event)" enctype="multipart/form-data"
                            class="p-6">

                            {{-- Error message --}}
                            <div id="editProfileError"
                                class="hidden mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm">
                            </div>

                            {{-- Avatar Upload với Tabs --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">
                                    <i class="fas fa-image mr-1 text-brand-green"></i> Ảnh đại diện
                                </label>

                                {{-- Preview ảnh --}}
                                <div class="flex justify-center mb-4">
                                    <div class="relative group">
                                        <img id="avatarPreview"
                                            src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}"
                                            class="w-28 h-28 rounded-full border-4 border-brand-beige shadow-lg object-cover">
                                    </div>
                                </div>

                                {{-- Tabs chọn hình thức upload --}}
                                <div class="flex gap-2 justify-center mb-3">
                                    <button type="button" onclick="showAvatarTab('file')" id="avatar-tab-file"
                                        class="px-3 py-1.5 text-xs rounded-full bg-brand-green/10 text-brand-green font-bold transition">
                                        <i class="fas fa-upload mr-1"></i> Upload File
                                    </button>
                                    <button type="button" onclick="showAvatarTab('url')" id="avatar-tab-url"
                                        class="px-3 py-1.5 text-xs rounded-full bg-gray-100 text-gray-600 font-bold transition">
                                        <i class="fas fa-link mr-1"></i> Nhập URL
                                    </button>
                                </div>

                                {{-- Upload File --}}
                                <div id="avatar-upload-file" class="text-center">
                                    <label for="avatarInput"
                                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition text-sm font-medium text-gray-600">
                                        <i class="fas fa-cloud-upload-alt"></i> Chọn ảnh từ máy
                                    </label>
                                    <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                                        class="hidden" onchange="previewAvatar(this)">
                                    <p class="text-xs text-gray-400 mt-2">JPG, PNG, WebP, GIF, SVG (Tối đa 2MB)</p>
                                </div>

                                {{-- Nhập URL --}}
                                <div id="avatar-upload-url" class="hidden">
                                    <input type="url" name="avatar_url" id="avatarUrlInput"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 text-sm"
                                        placeholder="https://example.com/avatar.jpg" oninput="previewAvatarUrl(this.value)">
                                    <p class="text-xs text-gray-400 mt-2 text-center">Dán đường dẫn trực tiếp đến file ảnh</p>
                                </div>
                            </div>

                            {{-- Name Input --}}
                            <div class="mb-4">
                                <label for="editName" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-user mr-1 text-brand-green"></i> Tên hiển thị <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editName" name="name" value="{{ $user->name }}" required maxlength="100"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800"
                                    placeholder="Nhập tên hiển thị...">
                            </div>

                            {{-- Bio Input --}}
                            <div class="mb-6">
                                <label for="editBio" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-quote-left mr-1 text-brand-accent"></i> Giới thiệu bản thân
                                </label>
                                <textarea id="editBio" name="bio" rows="3" maxlength="500"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 resize-none"
                                    placeholder="Viết vài dòng về bản thân...">{{ $user->bio }}</textarea>
                                <p class="text-xs text-gray-400 mt-1 text-right"><span
                                        id="bioCharCount">{{ strlen($user->bio ?? '') }}</span>/500 ký tự</p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <button type="button" onclick="closeEditProfileModal()"
                                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-lg font-semibold hover:bg-gray-50 transition">
                                    Hủy bỏ
                                </button>
                                <button type="submit" id="editProfileSubmitBtn"
                                    class="flex-1 py-2.5 bg-brand-green text-white rounded-lg font-semibold hover:bg-brand-green/90 transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Đếm ký tự bio
        document.getElementById('editBio').addEventListener('input', function () {
            document.getElementById('bioCharCount').textContent = this.value.length;
        });

        // Handle Unsave Post (Bỏ lưu bài viết)
        async function handleUnsavePost(postId, btnElement) {
            const result = await SwalConfirm('Bỏ lưu', 'Bạn có chắc muốn bỏ lưu bài viết này?', 'question', 'Bỏ lưu');
            if (!result.isConfirmed) return;

            // Visual feedback
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btnElement.disabled = true;

            fetch('/bo-suu-tap/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ post_id: postId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && !data.saved) {
                        // Xóa card bài viết với animation
                        const card = document.getElementById(`saved-post-${postId}`);
                        if (card) {
                            card.style.transition = 'all 0.3s ease-out';
                            card.style.opacity = '0';
                            card.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                card.remove();
                                // Update counter in tab
                                const countSpan = document.querySelector('#tab-btn-saved span');
                                if (countSpan) {
                                    let count = parseInt(countSpan.textContent) - 1;
                                    countSpan.textContent = count;
                                }
                                // Check if empty
                                const container = document.getElementById('saved-posts-container');
                                if (container && container.children.length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        }
                    } else {
                        btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                        btnElement.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                    btnElement.disabled = false;
                });
        }

        // Toggle Comment Box for Saved Posts
        function toggleSavedComment(postId) {
            const box = document.getElementById(`saved-comment-box-${postId}`);
            if (box) {
                box.classList.toggle('hidden');
                // Focus input when shown
                if (!box.classList.contains('hidden')) {
                    const input = box.querySelector('input[name="content"]');
                    if (input) input.focus();
                }
            }
        }

        // Handle Like for Saved Posts  
        function handleLike(id, type) {
            const btn = document.getElementById(`like-btn-${type}-${id}`);
            const icon = document.getElementById(`like-icon-${type}-${id}`);
            const countSpan = document.getElementById(`like-count-${type}-${id}`);

            if (!btn || !icon || !countSpan) return;

            const isLiked = icon.classList.contains('fas');

            // Optimistic update
            if (isLiked) {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
                btn.classList.remove('text-red-500');
                btn.classList.add('text-gray-500');
                countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
                btn.classList.remove('text-gray-500');
                btn.classList.add('text-red-500');
                countSpan.textContent = parseInt(countSpan.textContent) + 1;
            }

            // Send AJAX
            fetch('/like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, type: type })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.count;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Submit Comment for Saved Posts
        function submitSavedComment(postId, event) {
            event.preventDefault();

            const form = event.target;
            const input = form.querySelector('input[name="content"]');
            const content = input.value.trim();

            if (!content) return;

            // Disable form
            input.disabled = true;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(`/cong-thuc/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ content: content })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update count
                        const countSpan = document.getElementById(`comment-count-${postId}`);
                        if (countSpan) {
                            countSpan.textContent = parseInt(countSpan.textContent) + 1;
                        }

                        // Clear input
                        input.value = '';

                        // Add new comment to list
                        const commentBox = document.getElementById(`saved-comment-box-${postId}`);
                        const commentList = commentBox.querySelector('.space-y-2');
                        if (commentList && data.comment) {
                            const newComment = document.createElement('div');
                            newComment.className = 'flex gap-2';
                            newComment.innerHTML = `
                                                                                    <img src="{{ optional(Auth::user())->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional(Auth::user())->name ?? 'Guest') }}" 
                                                                                         class="w-6 h-6 rounded-full mt-0.5">
                                                                                    <div class="bg-gray-50 px-3 py-2 rounded-lg text-sm flex-1">
                                                                                        <span class="font-bold text-gray-700">{{ optional(Auth::user())->name ?? 'Guest' }}</span>
                                                                                        <span class="text-gray-600 ml-2">${content}</span>
                                                                                    </div>
                                                                                `;
                            commentList.prepend(newComment);
                        }
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Không thể gửi bình luận. Vui lòng thử lại.');
                })
                .finally(() => {
                    input.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                });
        }
    </script>

    {{-- ============================================================== --}}
    {{-- MODAL CHỈNH SỬA HỒ SƠ --}}
    {{-- ============================================================== --}}
    @if(Auth::check() && Auth::id() == $user->id)
        <div id="editProfileModal" class="fixed inset-0 z-[70] hidden" aria-labelledby="edit-profile-title" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditProfileModal()">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md">

                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-brand-green to-emerald-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2" id="edit-profile-title">
                                    <i class="fas fa-user-edit"></i> Chỉnh sửa hồ sơ
                                </h3>
                                <button onclick="closeEditProfileModal()" class="text-white/80 hover:text-white transition p-1">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <form id="editProfileForm" onsubmit="submitEditProfile(event)" enctype="multipart/form-data"
                            class="p-6">

                            {{-- Error message --}}
                            <div id="editProfileError"
                                class="hidden mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm">
                            </div>

                            {{-- Avatar Upload với Tabs --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">
                                    <i class="fas fa-image mr-1 text-brand-green"></i> Ảnh đại diện
                                </label>

                                {{-- Preview ảnh --}}
                                <div class="flex justify-center mb-4">
                                    <div class="relative group">
                                        <img id="avatarPreview"
                                            src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}"
                                            class="w-28 h-28 rounded-full border-4 border-brand-beige shadow-lg object-cover">
                                    </div>
                                </div>

                                {{-- Tabs chọn hình thức upload --}}
                                <div class="flex gap-2 justify-center mb-3">
                                    <button type="button" onclick="showAvatarTab('file')" id="avatar-tab-file"
                                        class="px-3 py-1.5 text-xs rounded-full bg-brand-green/10 text-brand-green font-bold transition">
                                        <i class="fas fa-upload mr-1"></i> Upload File
                                    </button>
                                    <button type="button" onclick="showAvatarTab('url')" id="avatar-tab-url"
                                        class="px-3 py-1.5 text-xs rounded-full bg-gray-100 text-gray-600 font-bold transition">
                                        <i class="fas fa-link mr-1"></i> Nhập URL
                                    </button>
                                </div>

                                {{-- Upload File --}}
                                <div id="avatar-upload-file" class="text-center">
                                    <label for="avatarInput"
                                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition text-sm font-medium text-gray-600">
                                        <i class="fas fa-cloud-upload-alt"></i> Chọn ảnh từ máy
                                    </label>
                                    <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                                        class="hidden" onchange="previewAvatar(this)">
                                    <p class="text-xs text-gray-400 mt-2">JPG, PNG, WebP, GIF, SVG (Tối đa 2MB)</p>
                                </div>

                                {{-- Nhập URL --}}
                                <div id="avatar-upload-url" class="hidden">
                                    <input type="url" name="avatar_url" id="avatarUrlInput"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 text-sm"
                                        placeholder="https://example.com/avatar.jpg" oninput="previewAvatarUrl(this.value)">
                                    <p class="text-xs text-gray-400 mt-2 text-center">Dán đường dẫn trực tiếp đến file ảnh</p>
                                </div>
                            </div>

                            {{-- Name Input --}}
                            <div class="mb-4">
                                <label for="editName" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-user mr-1 text-brand-green"></i> Tên hiển thị <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editName" name="name" value="{{ $user->name }}" required maxlength="100"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800"
                                    placeholder="Nhập tên hiển thị...">
                            </div>

                            {{-- Bio Input --}}
                            <div class="mb-6">
                                <label for="editBio" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-quote-left mr-1 text-brand-accent"></i> Giới thiệu bản thân
                                </label>
                                <textarea id="editBio" name="bio" rows="3" maxlength="500"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 resize-none"
                                    placeholder="Viết vài dòng về bản thân...">{{ $user->bio }}</textarea>
                                <p class="text-xs text-gray-400 mt-1 text-right"><span
                                        id="bioCharCount">{{ strlen($user->bio ?? '') }}</span>/500 ký tự</p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <button type="button" onclick="closeEditProfileModal()"
                                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-lg font-semibold hover:bg-gray-50 transition">
                                    Hủy bỏ
                                </button>
                                <button type="submit" id="editProfileSubmitBtn"
                                    class="flex-1 py-2.5 bg-brand-green text-white rounded-lg font-semibold hover:bg-brand-green/90 transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Đếm ký tự bio
            document.getElementById('editBio').addEventListener('input', function () {
                document.getElementById('bioCharCount').textContent = this.value.length;
            });

            // Handle Unsave Post (Bỏ lưu bài viết)
            async function handleUnsavePost(postId, btnElement) {
                const result = await SwalConfirm('Bỏ lưu', 'Bạn có chắc muốn bỏ lưu bài viết này?', 'question', 'Bỏ lưu');
                if (!result.isConfirmed) return;

                // Visual feedback
                btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btnElement.disabled = true;

                fetch('/bo-suu-tap/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ post_id: postId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && !data.saved) {
                            // Xóa card bài viết với animation
                            const card = document.getElementById(`saved-post-${postId}`);
                            if (card) {
                                card.style.transition = 'all 0.3s ease-out';
                                card.style.opacity = '0';
                                card.style.transform = 'translateX(-20px)';
                                setTimeout(() => {
                                    card.remove();
                                    // Update counter in tab
                                    const countSpan = document.querySelector('#tab-btn-saved span');
                                    if (countSpan) {
                                        let count = parseInt(countSpan.textContent) - 1;
                                        countSpan.textContent = count;
                                    }
                                    // Check if empty
                                    const container = document.getElementById('saved-posts-container');
                                    if (container && container.children.length === 0) {
                                        location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                            btnElement.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                        btnElement.disabled = false;
                    });
            }

            // Toggle Comment Box for Saved Posts
            function toggleSavedComment(postId) {
                const box = document.getElementById(`saved-comment-box-${postId}`);
                if (box) {
                    box.classList.toggle('hidden');
                    // Focus input when shown
                    if (!box.classList.contains('hidden')) {
                        const input = box.querySelector('input[name="content"]');
                        if (input) input.focus();
                    }
                }
            }

            // Handle Like for Saved Posts  
            function handleLike(id, type) {
                const btn = document.getElementById(`like-btn-${type}-${id}`);
                const icon = document.getElementById(`like-icon-${type}-${id}`);
                const countSpan = document.getElementById(`like-count-${type}-${id}`);

                if (!btn || !icon || !countSpan) return;

                const isLiked = icon.classList.contains('fas');

                // Optimistic update
                if (isLiked) {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                    btn.classList.remove('text-red-500');
                    btn.classList.add('text-gray-500');
                    countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                    btn.classList.remove('text-gray-500');
                    btn.classList.add('text-red-500');
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                }

                // Send AJAX
                fetch('/like', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id, type: type })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            countSpan.textContent = data.count;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Submit Comment for Saved Posts
            function submitSavedComment(postId, event) {
                event.preventDefault();

                const form = event.target;
                const input = form.querySelector('input[name="content"]');
                const content = input.value.trim();

                if (!content) return;

                // Disable form
                input.disabled = true;
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/cong-thuc/${postId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update count
                            const countSpan = document.getElementById(`comment-count-${postId}`);
                            if (countSpan) {
                                countSpan.textContent = parseInt(countSpan.textContent) + 1;
                            }

                            // Clear input
                            input.value = '';

                            // Add new comment to list
                            const commentBox = document.getElementById(`saved-comment-box-${postId}`);
                            const commentList = commentBox.querySelector('.space-y-2');
                            if (commentList && data.comment) {
                                const newComment = document.createElement('div');
                                newComment.className = 'flex gap-2';
                                newComment.innerHTML = `
                                                                                    <img src="{{ optional(Auth::user())->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional(Auth::user())->name ?? 'Guest') }}" 
                                                                                         class="w-6 h-6 rounded-full mt-0.5">
                                                                                    <div class="bg-gray-50 px-3 py-2 rounded-lg text-sm flex-1">
                                                                                        <span class="font-bold text-gray-700">{{ optional(Auth::user())->name ?? 'Guest' }}</span>
                                                                                        <span class="text-gray-600 ml-2">${content}</span>
                                                                                    </div>
                                                                                `;
                                commentList.prepend(newComment);
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Không thể gửi bình luận. Vui lòng thử lại.');
                    })
                    .finally(() => {
                        input.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    });
            }
        </script>
    @endif

    {{-- ============================================================== --}}
    {{-- MODAL CHỈNH SỬA HỒ SƠ --}}
    {{-- ============================================================== --}}
    @if(Auth::check() && Auth::id() == $user->id)
        <div id="editProfileModal" class="fixed inset-0 z-[70] hidden" aria-labelledby="edit-profile-title" role="dialog"
            aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditProfileModal()">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md">

                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-brand-green to-emerald-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2" id="edit-profile-title">
                                    <i class="fas fa-user-edit"></i> Chỉnh sửa hồ sơ
                                </h3>
                                <button onclick="closeEditProfileModal()" class="text-white/80 hover:text-white transition p-1">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <form id="editProfileForm" onsubmit="submitEditProfile(event)" enctype="multipart/form-data"
                            class="p-6">

                            {{-- Error message --}}
                            <div id="editProfileError"
                                class="hidden mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded-lg text-sm">
                            </div>

                            {{-- Avatar Upload với Tabs --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">
                                    <i class="fas fa-image mr-1 text-brand-green"></i> Ảnh đại diện
                                </label>

                                {{-- Preview ảnh --}}
                                <div class="flex justify-center mb-4">
                                    <div class="relative group">
                                        <img id="avatarPreview"
                                            src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}"
                                            class="w-28 h-28 rounded-full border-4 border-brand-beige shadow-lg object-cover">
                                    </div>
                                </div>

                                {{-- Tabs chọn hình thức upload --}}
                                <div class="flex gap-2 justify-center mb-3">
                                    <button type="button" onclick="showAvatarTab('file')" id="avatar-tab-file"
                                        class="px-3 py-1.5 text-xs rounded-full bg-brand-green/10 text-brand-green font-bold transition">
                                        <i class="fas fa-upload mr-1"></i> Upload File
                                    </button>
                                    <button type="button" onclick="showAvatarTab('url')" id="avatar-tab-url"
                                        class="px-3 py-1.5 text-xs rounded-full bg-gray-100 text-gray-600 font-bold transition">
                                        <i class="fas fa-link mr-1"></i> Nhập URL
                                    </button>
                                </div>

                                {{-- Upload File --}}
                                <div id="avatar-upload-file" class="text-center">
                                    <label for="avatarInput"
                                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition text-sm font-medium text-gray-600">
                                        <i class="fas fa-cloud-upload-alt"></i> Chọn ảnh từ máy
                                    </label>
                                    <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif,.svg"
                                        class="hidden" onchange="previewAvatar(this)">
                                    <p class="text-xs text-gray-400 mt-2">JPG, PNG, WebP, GIF, SVG (Tối đa 2MB)</p>
                                </div>

                                {{-- Nhập URL --}}
                                <div id="avatar-upload-url" class="hidden">
                                    <input type="url" name="avatar_url" id="avatarUrlInput"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 text-sm"
                                        placeholder="https://example.com/avatar.jpg" oninput="previewAvatarUrl(this.value)">
                                    <p class="text-xs text-gray-400 mt-2 text-center">Dán đường dẫn trực tiếp đến file ảnh</p>
                                </div>
                            </div>

                            {{-- Name Input --}}
                            <div class="mb-4">
                                <label for="editName" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-user mr-1 text-brand-green"></i> Tên hiển thị <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editName" name="name" value="{{ $user->name }}" required maxlength="100"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800"
                                    placeholder="Nhập tên hiển thị...">
                            </div>

                            {{-- Bio Input --}}
                            <div class="mb-6">
                                <label for="editBio" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    <i class="fas fa-quote-left mr-1 text-brand-accent"></i> Giới thiệu bản thân
                                </label>
                                <textarea id="editBio" name="bio" rows="3" maxlength="500"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand-green/30 focus:border-brand-green transition text-gray-800 resize-none"
                                    placeholder="Viết vài dòng về bản thân...">{{ $user->bio }}</textarea>
                                <p class="text-xs text-gray-400 mt-1 text-right"><span
                                        id="bioCharCount">{{ strlen($user->bio ?? '') }}</span>/500 ký tự</p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <button type="button" onclick="closeEditProfileModal()"
                                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 rounded-lg font-semibold hover:bg-gray-50 transition">
                                    Hủy bỏ
                                </button>
                                <button type="submit" id="editProfileSubmitBtn"
                                    class="flex-1 py-2.5 bg-brand-green text-white rounded-lg font-semibold hover:bg-brand-green/90 transition flex items-center justify-center gap-2 shadow-md">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Đếm ký tự bio
            document.getElementById('editBio').addEventListener('input', function () {
                document.getElementById('bioCharCount').textContent = this.value.length;
            });

            // Handle Unsave Post (Bỏ lưu bài viết)
            async function handleUnsavePost(postId, btnElement) {
                const result = await SwalConfirm('Bỏ lưu', 'Bạn có chắc muốn bỏ lưu bài viết này?', 'question', 'Bỏ lưu');
                if (!result.isConfirmed) return;

                // Visual feedback
                btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btnElement.disabled = true;

                fetch('/bo-suu-tap/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ post_id: postId })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && !data.saved) {
                            // Xóa card bài viết với animation
                            const card = document.getElementById(`saved-post-${postId}`);
                            if (card) {
                                card.style.transition = 'all 0.3s ease-out';
                                card.style.opacity = '0';
                                card.style.transform = 'translateX(-20px)';
                                setTimeout(() => {
                                    card.remove();
                                    // Update counter in tab
                                    const countSpan = document.querySelector('#tab-btn-saved span');
                                    if (countSpan) {
                                        let count = parseInt(countSpan.textContent) - 1;
                                        countSpan.textContent = count;
                                    }
                                    // Check if empty
                                    const container = document.getElementById('saved-posts-container');
                                    if (container && container.children.length === 0) {
                                        location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                            btnElement.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btnElement.innerHTML = '<i class="fas fa-bookmark"></i>';
                        btnElement.disabled = false;
                    });
            }

            // Toggle Comment Box for Saved Posts
            function toggleSavedComment(postId) {
                const box = document.getElementById(`saved-comment-box-${postId}`);
                if (box) {
                    box.classList.toggle('hidden');
                    // Focus input when shown
                    if (!box.classList.contains('hidden')) {
                        const input = box.querySelector('input[name="content"]');
                        if (input) input.focus();
                    }
                }
            }

            // Handle Like for Saved Posts  
            function handleLike(id, type) {
                const btn = document.getElementById(`like-btn-${type}-${id}`);
                const icon = document.getElementById(`like-icon-${type}-${id}`);
                const countSpan = document.getElementById(`like-count-${type}-${id}`);

                if (!btn || !icon || !countSpan) return;

                const isLiked = icon.classList.contains('fas');

                // Optimistic update
                if (isLiked) {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                    btn.classList.remove('text-red-500');
                    btn.classList.add('text-gray-500');
                    countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                    btn.classList.remove('text-gray-500');
                    btn.classList.add('text-red-500');
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                }

                // Send AJAX
                fetch('/like', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id, type: type })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            countSpan.textContent = data.count;
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Submit Comment for Saved Posts
            function submitSavedComment(postId, event) {
                event.preventDefault();

                const form = event.target;
                const input = form.querySelector('input[name="content"]');
                const content = input.value.trim();

                if (!content) return;

                // Disable form
                input.disabled = true;
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(`/cong-thuc/${postId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ content: content })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update count
                            const countSpan = document.getElementById(`comment-count-${postId}`);
                            if (countSpan) {
                                countSpan.textContent = parseInt(countSpan.textContent) + 1;
                            }

                            // Clear input
                            input.value = '';

                            // Add new comment to list
                            const commentBox = document.getElementById(`saved-comment-box-${postId}`);
                            const commentList = commentBox.querySelector('.space-y-2');
                            if (commentList && data.comment) {
                                const newComment = document.createElement('div');
                                newComment.className = 'flex gap-2';
                                newComment.innerHTML = `
                                                                                    <img src="{{ optional(Auth::user())->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(optional(Auth::user())->name ?? 'Guest') }}" 
                                                                                         class="w-6 h-6 rounded-full mt-0.5">
                                                                                    <div class="bg-gray-50 px-3 py-2 rounded-lg text-sm flex-1">
                                                                                        <span class="font-bold text-gray-700">{{ optional(Auth::user())->name ?? 'Guest' }}</span>
                                                                                        <span class="text-gray-600 ml-2">${content}</span>
                                                                                    </div>
                                                                                `;
                                commentList.prepend(newComment);
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Không thể gửi bình luận. Vui lòng thử lại.');
                    })
                    .finally(() => {
                        input.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    });
            }
        </script>
    @endif


    {{-- SortableJS cho sắp xếp badges --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let badgeSortable = null;

        // Toggle chế độ sắp xếp badges
        function toggleBadgeEditMode() {
            const viewMode = document.getElementById('badges-view-mode');
            const editMode = document.getElementById('badges-edit-mode');
            const editBtn = document.getElementById('btn-edit-badge-order');

            if (!viewMode || !editMode) return;

            const isEditing = !editMode.classList.contains('hidden');

            if (isEditing) {
                // Thoát chế độ sắp xếp
                editMode.classList.add('hidden');
                viewMode.classList.remove('hidden');
                if (editBtn) {
                    editBtn.innerHTML = '<i class="fas fa-arrows-alt mr-1"></i> Sắp xếp';
                }
                // Destroy sortable
                if (badgeSortable) {
                    badgeSortable.destroy();
                    badgeSortable = null;
                }
            } else {
                // Vào chế độ sắp xếp
                viewMode.classList.add('hidden');
                editMode.classList.remove('hidden');
                if (editBtn) {
                    editBtn.innerHTML = '<i class="fas fa-eye mr-1"></i> Xem';
                }
                // Init sortable
                initBadgeSortable();
            }
        }

        // Khởi tạo Sortable
        function initBadgeSortable() {
            const container = document.getElementById('sortable-badges');
            if (!container) return;

            badgeSortable = new Sortable(container, {
                animation: 150,
                ghostClass: 'opacity-50',
                chosenClass: 'scale-110',
                dragClass: 'shadow-lg',
                onEnd: function (evt) {
                    updateBadgeOrderNumbers();
                }
            });
        }

        // Cập nhật số thứ tự hiển thị sau khi kéo thả
        function updateBadgeOrderNumbers() {
            const container = document.getElementById('sortable-badges');
            if (!container) return;

            const badges = container.querySelectorAll('.badge-item');
            badges.forEach((badge, index) => {
                const numberEl = badge.querySelector('.badge-order-number');
                if (numberEl) {
                    numberEl.textContent = index + 1;
                }
            });
        }

        // Lưu thứ tự badges
        function saveBadgeOrder() {
            const container = document.getElementById('sortable-badges');
            if (!container) return;

            const badgeIds = [];
            container.querySelectorAll('.badge-item').forEach(item => {
                badgeIds.push(parseInt(item.dataset.badgeId));
            });

            // Show loading
            const saveBtn = event.target.closest('button') || document.querySelector('[onclick="saveBadgeOrder()"]');
            const oldHtml = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Đang lưu...';
            saveBtn.disabled = true;

            fetch('{{ route("profile.badges.order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ badge_ids: badgeIds })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show new order
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                        saveBtn.innerHTML = oldHtml;
                        saveBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Không thể lưu thứ tự. Vui lòng thử lại.');
                    saveBtn.innerHTML = oldHtml;
                    saveBtn.disabled = false;
                });
        }
    </script>

@endsection
