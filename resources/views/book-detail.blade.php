@extends('layouts.app')

@section('title', 'Chi Tiết Sách - ' . ($book->title ?? 'Góc Sách'))

@section('content')
    <style>
        html { scroll-behavior: smooth; }
        .text-justify-last-left { text-align: justify; text-align-last: left; }
    </style>

    {{-- [BREADCRUMB] --}}
    <div class="bg-brand-beige/40 py-4 border-b border-brand-beige/50">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('books.list') }}" class="hover:text-brand-green transition">
                    {{ isset($book->categories) && $book->categories->isNotEmpty() ? $book->categories->first()->name : 'Sách' }}
                </a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold truncate max-w-[200px]">{{ $book->title ?? 'Chi tiết sách' }}</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 flex-grow">
        
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm max-w-4xl mx-auto">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span class="font-medium ml-2">{{ session('success') }}</span>
            </div>
        @endif

        {{-- 1. HERO SECTION (GIỮ NGUYÊN) --}}
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-soft border border-gray-100 mb-12 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-green/5 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 relative z-10">
                {{-- Ảnh Bìa --}}
                <div class="md:col-span-4 lg:col-span-3">
                    <div class="relative w-full aspect-[2/3] rounded-r-lg rounded-l-sm shadow-book transform hover:scale-[1.02] transition duration-500 cursor-pointer group">
                        @php
                            $cover = $book->cover_image ?? null;
                            $coverUrl = $cover 
                                ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                : 'https://placehold.co/300x450?text=No+Image';
                        @endphp
                        <img src="{{ $coverUrl }}" 
                             alt="{{ $book->title }}" 
                             class="w-full h-full object-cover rounded-r-lg rounded-l-sm border-l-4 border-gray-200"
                             onerror="this.onerror=null; this.src='https://placehold.co/300x450?text=No+Image'">
                    </div>
                </div>

                {{-- Thông Tin Chi Tiết --}}
                <div class="md:col-span-8 lg:col-span-9 flex flex-col justify-center">
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if($book->categories->isNotEmpty())
                                @foreach($book->categories as $category)
                                    <a href="{{ route('books.list', ['category' => $category->id]) }}" 
                                       class="inline-block py-1 px-3 rounded-full bg-brand-accent/10 text-brand-accent text-xs font-bold uppercase tracking-wider border border-brand-accent/20 hover:bg-brand-accent hover:text-white transition">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            @else
                                <span class="inline-block py-1 px-3 rounded-full bg-brand-accent/10 text-brand-accent text-xs font-bold uppercase tracking-wider border border-brand-accent/20">
                                    Văn Học
                                </span>
                            @endif
                        </div>
                        <h1 class="text-3xl md:text-5xl font-bold text-brand-green font-serif mt-1 mb-3 leading-tight">
                            {{ $book->title ?? 'Tiêu đề sách' }}
                        </h1>
                        <div class="flex items-center gap-4 text-sm font-medium">
                            <span class="text-gray-500">Tác giả:
                                @if(isset($book->authors) && $book->authors->isNotEmpty())
                                    @foreach($book->authors as $a)
                                        <a href="{{ route('authors.show', $a->slug ?? \Str::slug($a->name)) }}" class="text-brand-green font-bold hover:underline">{{ $a->name }}</a>@if(!$loop->last), @endif
                                    @endforeach
                                @elseif($book->author)
                                    <a href="{{ route('authors.show', $book->author->slug ?? \Str::slug($book->author->name)) }}" class="text-brand-green font-bold hover:underline">{{ $book->author->name }}</a>
                                @else
                                    <span class="text-brand-green font-bold">{{ $book->author_name ?? 'Đang cập nhật' }}</span>
                                @endif
                            </span>
                            <span class="text-gray-300">|</span>
                            <div class="flex items-center text-yellow-400">
                                <i class="fas fa-star"></i><span class="text-gray-500 ml-2 text-xs">({{ $book->avg_rating ?? '0' }}/5)</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 leading-relaxed mb-8 text-lg font-light italic line-clamp-3">
                        {{ $book->description ?? 'Chưa có mô tả ngắn.' }}
                    </p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 text-sm">
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Nhà xuất bản</p>
                            <p class="font-bold text-brand-green">{{ $book->publisher ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Năm</p>
                            <p class="font-bold text-brand-green">{{ $book->published_year ?? 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-wide">Lượt xem</p>
                            <p class="font-bold text-brand-green">{{ number_format($book->view_count) }}</p>
                        </div>
                    </div>

                    <div class="mt-auto flex flex-col sm:flex-row gap-4 items-center">
                        <a href="#section-review" class="group flex items-center justify-center gap-2 px-8 py-3.5 rounded-full border-2 border-brand-green text-brand-green font-bold hover:bg-brand-green hover:text-white transition-all duration-300 min-w-[200px] shadow-sm hover:shadow-lg">
                            <i class="fas fa-book-open text-lg"></i>
                            <span>Đọc Review</span>
                        </a>
                        <a href="#" class="flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-brand-accent text-white font-bold shadow-lg hover:bg-[#c29263] hover:-translate-y-1 transition-all duration-300 min-w-[200px]">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Mua Sách Ngay</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- 2. MAIN CONTENT (GRID 2 CỘT: TRÁI 8 - PHẢI 4)                  --}}
        {{-- ============================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            {{-- A. CỘT CHÍNH (BÊN TRÁI - REVIEW CHI TIẾT & BÌNH LUẬN) --}}
            <div class="lg:col-span-8 space-y-12">
                
                {{-- [SECTION 1] BÀI REVIEW CHI TIẾT --}}
                <section id="section-review" class="scroll-mt-28">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <span class="w-1 h-8 bg-brand-green rounded-full"></span>
                            <h2 class="text-2xl font-bold text-gray-800 font-serif">Bài Review Chi Tiết</h2>
                        </div>
                        
                        @auth
                            <a href="{{ route('reviews.create', ['book_id' => $book->id]) }}" class="flex items-center gap-2 px-5 py-2 bg-black text-white text-sm font-bold rounded-full hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-pen-nib"></i> Viết Review
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-brand-green font-bold text-sm hover:underline flex items-center gap-1">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập để viết
                            </a>
                        @endauth
                    </div>

                    @php
                        // Lấy bài viết published mới nhất
                        $mainPost = $book->posts->where('status', 'published')->sortByDesc('created_at')->first();
                    @endphp

                    @if($mainPost)
                        <article class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4 font-serif leading-tight">{{ $mainPost->title }}</h1>
                            
                            <div class="flex items-center gap-3 text-sm text-gray-500 mb-6 bg-gray-50 p-3 rounded-lg w-fit">
                                <a href="{{ route('public.profile', $mainPost->user->id ?? 0) }}">
                                    @if($mainPost->user)
                                        @include('partials.user-avatar-with-frame', [
                                            'user' => $mainPost->user,
                                            'size' => 'w-10 h-10',
                                            'avatarSize' => 'w-8 h-8'
                                        ])
                                    @else
                                        <img src="https://ui-avatars.com/api/?name=Admin&background=random&size=32" class="w-8 h-8 rounded-full object-cover">
                                    @endif
                                </a>
                                <div class="flex flex-col">
                                    <a href="{{ route('public.profile', $mainPost->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                        <span class="font-bold text-gray-800 flex items-center">
                                            {{ $mainPost->user->name ?? 'Quản trị viên' }}
                                            @if($mainPost->user)
                                                @include('partials.user-badges', ['user' => $mainPost->user, 'size' => 'xs'])
                                            @endif
                                        </span>
                                    </a>
                                    <div class="flex items-center gap-2">
                                        @if($mainPost->rating)
                                            @php
                                                $rating = $mainPost->rating ?? 0;
                                                $fullStars = floor($rating);
                                                $hasHalfStar = ($rating - $fullStars) >= 0.3;
                                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                            @endphp
                                            <div class="flex items-center text-yellow-400 text-xs">
                                                @for($i = 0; $i < $fullStars; $i++)<i class="fas fa-star"></i>@endfor
                                                @if($hasHalfStar)<i class="fas fa-star-half-alt"></i>@endif
                                                @for($i = 0; $i < $emptyStars; $i++)<i class="far fa-star text-gray-300"></i>@endfor
                                                <span class="ml-1 text-xs font-semibold text-gray-600">{{ number_format($rating, 1) }}</span>
                                            </div>
                                            <span class="text-gray-300">•</span>
                                        @endif
                                        <span class="text-xs">{{ $mainPost->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if(!empty($mainPost->thumbnail))
                                <div class="mb-8 rounded-2xl overflow-hidden shadow-sm aspect-[21/9]">
                                    <img src="{{ Str::startsWith($mainPost->thumbnail, 'http') ? $mainPost->thumbnail : asset('storage/' . $mainPost->thumbnail) }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endif

                            <div class="prose prose-stone prose-lg max-w-none text-gray-700 text-justify-last-left leading-relaxed">
                                {!! $mainPost->content !!}
                            </div>

                                    {{-- Nút Like và Comment cho bài review --}}
                                    <div class="flex items-center gap-6">
                                        {{-- Nút Like bài review --}}
                                        <button 
                                            type="button"
                                            onclick="handleLike({{ $mainPost->id }}, 'post')" 
                                            id="like-btn-post-{{ $mainPost->id }}"
                                            class="flex items-center gap-2 text-sm font-bold transition {{ Auth::check() && $mainPost->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                                            <i id="like-icon-post-{{ $mainPost->id }}" class="{{ Auth::check() && $mainPost->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart text-lg"></i>
                                            <span id="like-count-post-{{ $mainPost->id }}">{{ $mainPost->likes->count() }}</span> Thích
                                        </button>

                                        {{-- Nút mở khung bình luận --}}
                                        <button 
                                            type="button"
                                            onclick="togglePostCommentSection({{ $mainPost->id }})" 
                                            class="flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-brand-green transition group">
                                            <i class="far fa-comment-dots text-lg group-hover:scale-110 transition-transform"></i>
                                            <span>Bình luận ({{ $mainPost->comments->whereNull('parent_id')->count() }})</span>
                                            <i id="chevron-post-{{ $mainPost->id }}" class="fas fa-chevron-down text-xs ml-1 transition-transform duration-300"></i>
                                        </button>

                                        {{-- Nút Báo cáo Bài viết --}}
                                        @auth
                                            @if($mainPost->user_id !== Auth::id())
                                                <button onclick="openReportModal({{ $mainPost->id }}, 'post')"
                                                    class="flex items-center gap-1 text-sm font-bold text-gray-400 hover:text-red-500 transition ml-auto"
                                                    title="Báo cáo bài viết này">
                                                    <i class="far fa-flag"></i> Báo cáo
                                                </button>
                                            @endif
                                        @endauth
                                    </div>

                                {{-- KHUNG BÌNH LUẬN BÀI REVIEW (ẨN/HIỆN) --}}
                                <div id="post-comment-section-{{ $mainPost->id }}" class="hidden mt-6 bg-gray-50/50 rounded-xl p-4 border border-gray-100 animate-fade-in">
                                    
                                    {{-- Ô nhập bình luận --}}
                                    @auth
                                        <div class="flex gap-3 items-start mb-4 pb-4 border-b border-gray-100">
                                            @include('partials.user-avatar-with-frame', [
                                                'user' => Auth::user(),
                                                'size' => 'w-11 h-11',
                                                'avatarSize' => 'w-9 h-9'
                                            ])
                                            <div class="flex-1 relative">
                                                <textarea id="post-comment-input-{{ $mainPost->id }}" rows="2" 
                                                          class="w-full text-sm p-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-brand-green focus:ring-2 focus:ring-brand-green/10 resize-none pr-20 shadow-sm" 
                                                          placeholder="Viết bình luận về bài review này..."
                                                          oninput="autoResize(this)"></textarea>
                                                <button type="button" onclick="submitPostComment({{ $mainPost->id }}, event)" 
                                                        class="absolute right-2 bottom-2 text-brand-green px-3 py-1.5 bg-brand-green/10 rounded-lg text-xs font-bold hover:bg-brand-green hover:text-white transition">
                                                    <i class="fas fa-paper-plane mr-1"></i> Gửi
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4 mb-4 border-b border-gray-100">
                                            <p class="text-sm text-gray-400">
                                                <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để bình luận bài review này.
                                            </p>
                                        </div>
                                    @endauth

                                    {{-- Danh sách bình luận của bài review --}}
                                    <div id="post-comments-list-{{ $mainPost->id }}" class="space-y-4">
                                        @php
                                            $postComments = $mainPost->comments->whereNull('parent_id')->sortByDesc('created_at');
                                        @endphp
                                        
                                        @forelse($postComments as $comment)
                                            <div id="pr-comment-{{ $comment->id }}" class="flex gap-3 scroll-mt-24 transition-all duration-500">
                                                <a href="{{ route('public.profile', $comment->user->id ?? 0) }}" class="flex-shrink-0">
                                                    @if($comment->user)
                                                        @include('partials.user-avatar-with-frame', [
                                                            'user' => $comment->user,
                                                            'size' => 'w-10 h-10',
                                                            'avatarSize' => 'w-8 h-8'
                                                        ])
                                                    @else
                                                        <img src="https://ui-avatars.com/api/?name=U&background=random" class="w-8 h-8 rounded-full">
                                                    @endif
                                                </a>
                                                <div class="flex-1">
                                                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <a href="{{ route('public.profile', $comment->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                                                <span class="font-bold text-xs text-gray-800">{{ $comment->user->name ?? 'Người dùng' }}</span>
                                                            </a>
                                                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-sm text-gray-600">{{ $comment->content }}</p>
                                                    </div>
                                                    
                                                    {{-- Nút Like và Reply cho comment --}}
                                                    <div class="flex items-center gap-4 mt-2 ml-2">
                                                        <button onclick="handleLike({{ $comment->id }}, 'comment')" 
                                                                id="pr-like-btn-{{ $comment->id }}"
                                                                class="text-[10px] font-bold flex items-center gap-1 {{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }} transition">
                                                            <i id="pr-like-icon-{{ $comment->id }}" class="{{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart"></i>
                                                            <span id="pr-like-count-{{ $comment->id }}">{{ $comment->likes->count() }}</span>
                                                        </button>
                                                        
                                                        <button onclick="togglePRReplySection({{ $comment->id }})" 
                                                                class="text-[10px] font-bold text-gray-400 hover:text-brand-green transition flex items-center gap-1">
                                                            <i class="far fa-comment-dots"></i>
                                                            <span>Trả lời ({{ $comment->replies->count() }})</span>
                                                        </button>

                                                        {{-- Nút Báo cáo comment --}}
                                                        @auth
                                                            @if($comment->user_id !== Auth::id())
                                                                <button onclick="openReportModal({{ $comment->id }}, 'comment')"
                                                                    class="text-[10px] font-bold text-gray-400 hover:text-red-500 transition"
                                                                    title="Báo cáo">
                                                                    <i class="far fa-flag"></i>
                                                                </button>
                                                            @endif
                                                        @endauth
                                                    </div>

                                                    {{-- Khung reply (ẩn) - dùng prefix pr- để phân biệt --}}
                                                    <div id="pr-reply-section-{{ $comment->id }}" class="hidden mt-3 ml-2 bg-gray-50/80 rounded-lg p-3">
                                                        {{-- Danh sách reply --}}
                                                        <div class="space-y-3 mb-3 pr-reply-list">
                                                            @forelse($comment->replies as $reply)
                                                                <div id="pr-reply-{{ $reply->id }}" class="flex gap-2 scroll-mt-24 transition-all duration-500">
                                                                    <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="flex-shrink-0">
                                                                        @if($reply->user)
                                                                            @include('partials.user-avatar-with-frame', [
                                                                                'user' => $reply->user,
                                                                                'size' => 'w-8 h-8',
                                                                                'avatarSize' => 'w-6 h-6'
                                                                            ])
                                                                        @else
                                                                            <img src="https://ui-avatars.com/api/?name=U&background=random" class="w-6 h-6 rounded-full">
                                                                        @endif
                                                                    </a>
                                                                    <div class="flex-1">
                                                                        <div class="bg-white p-2 rounded-lg border border-gray-100">
                                                                            <div class="flex justify-between items-center mb-1">
                                                                                <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                                                                    <span class="font-bold text-[10px] text-gray-700">{{ $reply->user->name ?? 'Người dùng' }}</span>
                                                                                </a>
                                                                                <span class="text-[9px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                            </div>
                                                                            <p class="text-[11px] text-gray-600">{{ $reply->content }}</p>
                                                                        </div>
                                                                        <button onclick="handleLike({{ $reply->id }}, 'comment')" 
                                                                                id="pr-like-btn-{{ $reply->id }}"
                                                                                class="text-[9px] font-bold ml-1 mt-1 flex items-center gap-1 {{ Auth::check() && $reply->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }} transition">
                                                                            <i id="pr-like-icon-{{ $reply->id }}" class="{{ Auth::check() && $reply->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart"></i>
                                                                            <span id="pr-like-count-{{ $reply->id }}">{{ $reply->likes->count() }}</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-center text-[10px] text-gray-400 italic py-2">Chưa có phản hồi.</p>
                                                            @endforelse
                                                        </div>
                                                        
                                                        {{-- Ô nhập reply --}}
                                                        @auth
                                                            <div class="flex gap-2 items-start pt-2 border-t border-gray-200">
                                                                @include('partials.user-avatar-with-frame', [
                                                                    'user' => Auth::user(),
                                                                    'size' => 'w-8 h-8',
                                                                    'avatarSize' => 'w-6 h-6'
                                                                ])
                                                                <div class="flex-1 relative">
                                                                    <textarea id="pr-reply-input-{{ $comment->id }}" rows="1" 
                                                                              class="w-full text-[11px] p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none pr-14 shadow-sm" 
                                                                              placeholder="Nhập phản hồi..."
                                                                              oninput="autoResize(this)"></textarea>
                                                                    <button type="button" onclick="submitPRReply({{ $comment->id }}, event)" 
                                                                            class="absolute right-1 top-1 text-brand-green px-2 py-1 bg-brand-green/10 rounded text-[10px] font-bold hover:bg-brand-green hover:text-white transition">
                                                                        Gửi
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endauth
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-center text-sm text-gray-400 italic py-4">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                                        @endforelse
                                    </div>
                                </div>
                            </article>
                    @else
                        {{-- Trường hợp chưa có review nào --}}
                        <div class="bg-white p-12 rounded-2xl text-center border border-dashed border-gray-300 shadow-sm mb-6">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="fas fa-feather-alt text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Chưa có bài review chi tiết</h3>
                            <p class="text-gray-500 italic mb-4">Hãy là người đầu tiên chia sẻ cảm nhận sâu sắc về cuốn sách này!</p>
                            
                            @if(!empty($book->description))
                                <div class="mt-6 text-justify text-gray-600 bg-gray-50 p-6 rounded-xl border border-gray-100">
                                    <h4 class="font-bold text-sm mb-2 uppercase text-gray-400">Sơ lược nội dung:</h4>
                                    {!! nl2br(e($book->description)) !!}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- NÚT XEM CÁC REVIEW KHÁC --}}
                    @if($book->posts->where('status', 'published')->count() > 0)
                        <div class="text-center border-t border-gray-100 pt-6">
                            <p class="text-gray-500 text-sm mb-3 italic">Bạn muốn đọc thêm các góc nhìn khác về cuốn sách này?</p>
                            <a href="{{ route('book.reviews', $book->slug ?? $book->id) }}" 
                               class="inline-flex items-center gap-2 px-8 py-3 bg-white border-2 border-brand-green text-brand-green font-bold rounded-full hover:bg-brand-green hover:text-white transition-all duration-300 shadow-sm group">
                                <span>Xem Các Bài Review Khác</span>
                                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    @endif

                </section>

                {{-- [SECTION 2] BÌNH LUẬN CỘNG ĐỒNG --}}
                <section id="section-comments">
                    @php
                        // Chỉ lấy COMMENT CHA (không có parent_id)
                        $parentComments = collect();
                        if($book->posts->isNotEmpty()) {
                            foreach($book->posts as $post) {
                                if($post->comments->isNotEmpty()) {
                                    // Lọc chỉ lấy comment có parent_id = null
                                    $parentComments = $parentComments->merge(
                                        $post->comments->whereNull('parent_id')
                                    );
                                }
                            }
                        }
                        $parentComments = $parentComments->sortByDesc('created_at');
                    @endphp

                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-1 h-8 bg-yellow-400 rounded-full"></span>
                        <h2 class="text-2xl font-bold text-gray-800 font-serif">Bình Luận Cộng Đồng</h2>
                        <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded-md">
                            {{ $parentComments->count() }}
                        </span>
                    </div>

                    <div class="space-y-6">
                        @if($parentComments->count() > 0)
                            @foreach($parentComments as $comment)
                                @php
                                    // Lấy các reply của comment này
                                    $replies = $comment->replies ?? collect();
                                @endphp
                                
                                {{-- THẺ CHA COMMENT --}}
                                <div id="comment-{{ $comment->id }}" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 h-fit">
                                            <a href="{{ route('public.profile', $comment->user->id ?? 0) }}">
                                                @if($comment->user)
                                                    @include('partials.user-avatar-with-frame', [
                                                        'user' => $comment->user,
                                                        'size' => 'w-12 h-12',
                                                        'avatarSize' => 'w-10 h-10'
                                                    ])
                                                @else
                                                    <img src="https://ui-avatars.com/api/?name=U&background=random" class="w-10 h-10 rounded-full">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <div>
                                                    <a href="{{ route('public.profile', $comment->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                                        <h4 class="font-bold text-gray-900 text-sm flex items-center gap-1">
                                                            {{ $comment->user->name ?? 'Người dùng' }}
                                                            @if($comment->user)
                                                                @include('partials.user-badges', ['user' => $comment->user, 'size' => 'xs'])
                                                            @endif
                                                        </h4>
                                                    </a>
                                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line mt-2 break-words">
                                                {{ $comment->content }}
                                            </div>

                                            {{-- NÚT LIKE VÀ REPLY --}}
                                            <div class="mt-4 flex items-center gap-4 border-t border-gray-50 pt-3">
                                                {{-- Nút Like --}}
                                                <button 
                                                    type="button"
                                                    onclick="handleLike({{ $comment->id }}, 'comment')" 
                                                    id="like-btn-comment-{{ $comment->id }}"
                                                    class="flex items-center gap-1.5 text-xs font-bold transition {{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                                                    <i id="like-icon-comment-{{ $comment->id }}" class="{{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart"></i>
                                                    <span id="like-count-comment-{{ $comment->id }}">{{ $comment->likes->count() }}</span> Thích
                                                </button>

                                                <button 
                                                    type="button"
                                                    onclick="toggleReplySection({{ $comment->id }})" 
                                                    class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-brand-green transition group">
                                                    <i class="far fa-comment-dots group-hover:scale-110 transition-transform"></i>
                                                    <span>Trả lời</span> <span id="reply-count-{{ $comment->id }}">({{ $replies->count() }})</span>
                                                    <i id="chevron-reply-{{ $comment->id }}" class="fas fa-chevron-down text-[10px] ml-1 transition-transform duration-300"></i>
                                                </button>

                                                {{-- Nút Báo cáo Comment --}}
                                                @auth
                                                    @if($comment->user_id !== Auth::id())
                                                        <button onclick="openReportModal({{ $comment->id }}, 'comment')"
                                                            class="flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-red-500 transition ml-auto"
                                                            title="Báo cáo bình luận này">
                                                            <i class="far fa-flag"></i>
                                                        </button>
                                                    @endif
                                                @endauth
                                            </div>

                                            {{-- KHUNG TRẢ LỜI (ẨN/HIỆN KHI CLICK) --}}
                                            <div id="reply-section-{{ $comment->id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-100 bg-gray-50/50 rounded-xl p-4 animate-fade-in">
                                                
                                                {{-- DANH SÁCH CÁC REPLY --}}
                                                <div class="space-y-4 mb-4">
                                                    @forelse($replies as $reply)
                                                        <div id="comment-{{ $reply->id }}" class="flex gap-2 scroll-mt-24 transition-all duration-500">
                                                            <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="flex-shrink-0">
                                                                @if($reply->user)
                                                                    @include('partials.user-avatar-with-frame', [
                                                                        'user' => $reply->user,
                                                                        'size' => 'w-9 h-9',
                                                                        'avatarSize' => 'w-7 h-7'
                                                                    ])
                                                                @else
                                                                    <img src="https://ui-avatars.com/api/?name=U&background=random" class="w-7 h-7 rounded-full">
                                                                @endif
                                                            </a>
                                                            <div class="flex-1">
                                                                <div class="bg-white p-2 rounded-xl rounded-tl-none border border-gray-100 shadow-sm">
                                                                    <div class="flex justify-between items-center mb-1">
                                                                        <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                                                            <h6 class="font-bold text-[10px] text-gray-700">{{ $reply->user->name ?? 'Người dùng' }}</h6>
                                                                        </a>
                                                                        <span class="text-[9px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                    <p class="text-[11px] text-gray-600">{{ $reply->content }}</p>
                                                                </div>
                                                                {{-- Like cho reply --}}
                                                                <div class="flex items-center gap-2 ml-2 mt-1">
                                                                    <button onclick="handleLike({{ $reply->id }}, 'comment')" 
                                                                            id="like-btn-comment-{{ $reply->id }}"
                                                                            class="text-[9px] font-bold flex items-center gap-1 {{ Auth::check() && $reply->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }} transition">
                                                                        <i id="like-icon-comment-{{ $reply->id }}" class="{{ Auth::check() && $reply->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart"></i>
                                                                        <span id="like-count-comment-{{ $reply->id }}">{{ $reply->likes->count() }}</span>
                                                                    </button>
                                                                    {{-- Nút Báo cáo Reply --}}
                                                                    @auth
                                                                        @if($reply->user_id !== Auth::id())
                                                                            <button onclick="openReportModal({{ $reply->id }}, 'comment')"
                                                                                class="text-[9px] font-bold text-gray-400 hover:text-red-500 transition"
                                                                                title="Báo cáo">
                                                                                <i class="far fa-flag"></i>
                                                                            </button>
                                                                        @endif
                                                                    @endauth
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-xs text-gray-400 italic py-2">Chưa có phản hồi nào.</p>
                                                    @endforelse
                                                </div>

                                                {{-- THÔNG BÁO ĐĂNG NHẬP CHO GUEST --}}
                                                @guest
                                                <div id="login-box-comment-{{ $comment->id }}" class="hidden">
                                                    <div class="text-center py-3 bg-gray-50 rounded-lg text-xs text-gray-500 border border-dashed border-gray-200">
                                                        <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để tham gia thảo luận cùng mọi người.
                                                    </div>
                                                </div>
                                                @endguest

                                                {{-- FORM REPLY --}}
                                                @auth
                                                <div class="mt-3 pt-3 border-t border-gray-100 transition-all duration-300">
                                                    <div class="flex gap-2 items-start">
                                                        @include('partials.user-avatar-with-frame', [
                                                            'user' => Auth::user(),
                                                            'size' => 'w-8 h-8',
                                                            'avatarSize' => 'w-6 h-6'
                                                        ])
                                                        
                                                        <div class="flex-1 relative">
                                                            <textarea id="reply-input-{{ $comment->id }}" 
                                                                      rows="1" 
                                                                      class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-brand-green focus:bg-white transition resize-none pr-10 shadow-sm" 
                                                                      placeholder="Viết phản hồi..."
                                                                      oninput="autoResize(this)"></textarea>
                                                            
                                                            <button type="button" onclick="submitReply({{ $comment->id }}, event)" 
                                                                    class="absolute right-1 top-1 text-brand-green p-1.5 hover:bg-brand-green/10 rounded transition">
                                                                <i class="fas fa-paper-plane text-xs"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endauth
                                                {{-- KẾT THÚC FORM REPLY --}}
                                                {{-- KẾT THÚC FORM REPLY --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12 bg-stone-50 rounded-2xl border border-dashed border-stone-300">
                                <i class="far fa-comments text-4xl text-gray-300 mb-3 block"></i>
                                <p class="text-gray-500 text-sm">Chưa có bình luận nào. Hãy tham gia thảo luận ngay!</p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            {{-- B. CỘT PHỤ (BÊN PHẢI - THÔNG TIN TÁC GIẢ) --}}
            <aside class="lg:col-span-4 space-y-8 ">
                
                {{-- [SECTION 3] CARD TÁC GIẢ --}}
                <div class="bg-white rounded-2xl shadow-soft border border-gray-100 p-6 text-center">
                    <h3 class="font-bold text-gray-500 text-xs uppercase tracking-widest mb-6">Thông Tin Tác Giả</h3>
                    
                    <div class="relative w-24 h-24 mx-auto mb-4">
                        @php
                            $author = $book->author ?? null;
                            $photo = $author->photo ?? null;
                            $photoUrl = $photo ? (Str::startsWith($photo, 'http') ? $photo : asset('storage/' . $photo)) : ('https://ui-avatars.com/api/?name=' . urlencode($author->name ?? $book->author_name ?? 'TG') . '&background=random&size=128');
                            $initials = strtoupper(substr($author->name ?? $book->author_name ?? 'TG', 0, 2));
                        @endphp
                        <img src="{{ $photoUrl }}" 
                             class="w-full h-full rounded-full object-cover border-4 border-stone-100 shadow-md" alt="{{ $author->name ?? $book->author_name ?? 'Tác giả' }}">
                        <div class="absolute bottom-0 right-0 bg-brand-green text-white text-[10px] font-bold px-2 py-0.5 rounded-full border-2 border-white">{{ $initials }}</div>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 font-serif mb-2">
                        @if($author)
                            <a href="{{ route('authors.show', $author->slug ?? \Str::slug($author->name)) }}" class="hover:underline">{{ $author->name }}</a>
                        @else
                            {{ $book->author_name ?? 'Tác giả' }}
                        @endif
                    </h2>
                    
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed px-2">
                        @if($author && !empty($author->bio))
                            {{ \Illuminate\Support\Str::limit($author->bio, 140, '...') }}
                        @else
                            Theo dõi tác giả để nhận thông báo về những tác phẩm mới nhất và các sự kiện ra mắt sách.
                        @endif
                    </p>
                    
                    <a href="{{ $author ? route('authors.show', $author->slug ?? \Str::slug($author->name)) : route('authors.index', ['q' => $book->author_name]) }}" class="w-full py-3 bg-stone-100 hover:bg-brand-green hover:text-white text-gray-700 font-bold rounded-xl transition duration-300 flex items-center justify-center gap-2">
                        Xem Tác Phẩm Khác <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                {{-- Widget Gợi Ý --}}
                <div class="bg-brand-beige/20 rounded-2xl p-6 border border-brand-beige/50">
                    <h3 class="font-bold text-brand-green text-sm mb-4 flex items-center gap-2">
                        <i class="fas fa-lightbulb"></i> Có thể bạn thích
                    </h3>
                    
                    <div class="space-y-4">
                        @if(isset($relatedBooks) && $relatedBooks->count() > 0)
                            @foreach($relatedBooks as $related)
                                @php
                                    // Xử lý ảnh bìa
                                    $cover = $related->cover_image;
                                    $imgSrc = !empty($cover) 
                                        ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover)) 
                                        : 'https://placehold.co/50x75?text=No+Image';
                                @endphp

                                <a href="{{ route('detail', $related->slug) }}" class="flex gap-3 items-center group cursor-pointer">
                                    {{-- Ảnh nhỏ --}}
                                    <div class="w-12 h-16 bg-gray-200 rounded overflow-hidden flex-shrink-0 border border-gray-200 shadow-sm">
                                        <img src="{{ $imgSrc }}" 
                                             alt="{{ $related->title }}" 
                                             class="w-full h-full object-cover transform group-hover:scale-110 transition duration-300">
                                    </div>
                                    
                                    {{-- Thông tin --}}
                                    <div class="flex-1 min-w-0"> {{-- min-w-0 để truncate hoạt động --}}
                                        <h4 class="text-sm font-bold text-gray-800 line-clamp-2 group-hover:text-brand-accent transition leading-tight mb-1" title="{{ $related->title }}">
                                            {{ $related->title }}
                                        </h4>
                                        <span class="text-xs text-gray-500 truncate block">
                                            {{ $related->author_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-400 italic text-center py-2">Chưa có gợi ý phù hợp.</p>
                        @endif
                    </div>
                </div>

            </aside>
        </div>
    </main>

    {{-- ĐÃ XÓA MODAL POPUP Ở ĐÂY --}}
@endsection

@push('scripts')
<script>
    const currentUserId = "{{ Auth::id() }}";

    // --- LOGIC CUỘN XUỐNG VÀ HIGHLIGHT COMMENT/REPLY ---
    document.addEventListener("DOMContentLoaded", function() {
        if(window.location.hash) {
            const targetId = window.location.hash.substring(1); // Ví dụ: "comment-123"
            const targetElement = document.getElementById(targetId);

            if(targetElement) {
                // Kiểm tra xem element này có nằm trong một reply-section ẩn không
                const parentReplySection = targetElement.closest('[id^="reply-section-"]');
                
                if(parentReplySection && parentReplySection.classList.contains('hidden')) {
                    // Đây là một reply → Mở khung reply của comment cha
                    const parentCommentId = parentReplySection.id.replace('reply-section-', '');
                    
                    // Mở khung reply
                    parentReplySection.classList.remove('hidden');
                    
                    // Xoay mũi tên chevron
                    const chevron = document.getElementById(`chevron-reply-${parentCommentId}`);
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                    
                    // Đợi animation mở xong rồi mới scroll
                    setTimeout(() => {
                        targetElement.scrollIntoView({ behavior: "smooth", block: "center" });
                        // Highlight reply
                        targetElement.classList.add('bg-yellow-100', 'rounded-lg', 'ring-2', 'ring-yellow-400');
                        setTimeout(() => {
                            targetElement.classList.remove('bg-yellow-100', 'ring-2', 'ring-yellow-400');
                        }, 3000);
                    }, 300);
                } else {
                    // Đây là comment cha hoặc reply section đã mở
                    targetElement.scrollIntoView({ behavior: "smooth", block: "center" });
                    targetElement.classList.add('bg-yellow-50', 'border-yellow-200');
                    setTimeout(() => {
                        targetElement.classList.remove('bg-yellow-50', 'border-yellow-200');
                    }, 3000);
                }
            }
        }
    });

    // --- LOGIC LIKE ---
    function handleLike(id, type) {
        if (!currentUserId) {
            // Hiển thị thông báo (chỉ show, không ẩn)
            const loginBox = document.getElementById(`login-box-${type}-${id}`);
            if (loginBox) loginBox.classList.remove('hidden');
            return;
        }

        let btn, icon, countSpan;
        
        if (type === 'comment') {
            // Thử tìm element với prefix thường (Bình Luận Cộng Đồng)
            btn = document.getElementById(`like-btn-comment-${id}`);
            icon = document.getElementById(`like-icon-comment-${id}`);
            countSpan = document.getElementById(`like-count-comment-${id}`);
            
            // Nếu không tìm thấy, thử với prefix pr- (Bình luận bài post)
            if (!btn) {
                btn = document.getElementById(`pr-like-btn-${id}`);
                icon = document.getElementById(`pr-like-icon-${id}`);
                countSpan = document.getElementById(`pr-like-count-${id}`);
            }
        } else {
            // Cho post
            btn = document.getElementById(`like-btn-${type}-${id}`);
            icon = document.getElementById(`like-icon-${type}-${id}`);
            countSpan = document.getElementById(`like-count-${type}-${id}`);
        }

        if (!btn || !icon || !countSpan) return;

        const isLiked = icon.classList.contains('fas'); 
        
        if(isLiked) {
            icon.classList.remove('fas', 'text-red-500');
            icon.classList.add('far');
            btn.classList.remove('text-red-500');
            btn.classList.add('text-gray-400');
            let currentCount = parseInt(countSpan.innerText);
            countSpan.innerText = Math.max(0, currentCount - 1);
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas', 'bounce', 'text-red-500');
            btn.classList.remove('text-gray-400');
            btn.classList.add('text-red-500');
            let currentCount = parseInt(countSpan.innerText);
            countSpan.innerText = currentCount + 1;
        }

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
                countSpan.innerText = data.count;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // --- LOGIC REPLY (GIỐNG TRANG HOME) ---
    
    // 1. Toggle mở/đóng khung reply (hiển thị danh sách reply + form nhập)
    function toggleReplySection(commentId) {
        const section = document.getElementById(`reply-section-${commentId}`);
        const chevron = document.getElementById(`chevron-reply-${commentId}`);
        const input = document.getElementById(`reply-input-${commentId}`);
        
        if (section) {
            const isHidden = section.classList.contains('hidden');
            
            // Toggle trạng thái (KHÔNG đóng các section khác để tránh xung đột)
            if (isHidden) {
                section.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
                if (input) input.focus();
            } else {
                section.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }
    }

    // 2. Tự động chỉnh độ cao textarea khi gõ
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // 3. Gửi reply qua AJAX (KHÔNG cần reload trang)
    function submitReply(commentId, event) {
        if (event) event.preventDefault();
        
        if (!currentUserId) {
            // Hiển thị thông báo (chỉ show, không ẩn)
            const loginBox = document.getElementById(`login-box-comment-${commentId}`);
            if (loginBox) loginBox.classList.remove('hidden');
            return;
        }
        
        const input = document.getElementById(`reply-input-${commentId}`);
        if (!input) return;

        const content = input.value.trim();
        if (!content) {
            alert("Vui lòng nhập nội dung!");
            return;
        }

        const btn = event.currentTarget || event.target.closest('button');
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`/comment/${commentId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: content })
        })
        .then(async r => {
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || "Lỗi server");
            return d;
        })
        .then(data => {
            if (data.success) {
                input.value = '';
                input.style.height = 'auto';
                
                // Logic render avatar frame
                let avatarHtml = '';
                if (data.user_frame) {
                    avatarHtml = `
                        <div class="relative w-9 h-9 inline-block flex-shrink-0">
                            <img src="${data.user_frame}" alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                            <div class="absolute inset-0 flex items-center justify-center z-0">
                                <img src="${data.user_avatar}" class="w-7 h-7 rounded-full object-cover">
                            </div>
                        </div>
                    `;
                } else {
                    avatarHtml = `<img src="${data.user_avatar}" class="w-7 h-7 rounded-full flex-shrink-0 hover:ring-2 hover:ring-brand-green transition">`;
                }

                // Tạo HTML reply mới
                const replyHtml = `
                    <div id="comment-${data.reply_id}" class="flex gap-2 animate-fade-in scroll-mt-24 transition-all duration-500">
                        <a href="/thanh-vien/${currentUserId}" class="flex-shrink-0">
                            ${avatarHtml}
                        </a>
                        <div class="flex-1">
                            <div class="bg-white p-2 rounded-xl rounded-tl-none border border-gray-100 shadow-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <h6 class="font-bold text-[10px] text-gray-700">${data.user_name}</h6>
                                    <span class="text-[9px] text-gray-400">${data.time}</span>
                                </div>
                                <p class="text-[11px] text-gray-600">${data.content}</p>
                            </div>
                            <button onclick="handleLike(${data.reply_id}, 'comment')" 
                                    id="like-btn-comment-${data.reply_id}"
                                    class="text-[9px] font-bold ml-2 mt-1 flex items-center gap-1 text-gray-400 hover:text-red-500 transition">
                                <i id="like-icon-comment-${data.reply_id}" class="far fa-heart"></i>
                                <span id="like-count-comment-${data.reply_id}">0</span>
                            </button>
                        </div>
                    </div>
                `;

                // Chèn reply mới vào danh sách - tìm đúng container (space-y-3 hoặc space-y-4)
                const replySection = document.getElementById(`reply-section-${commentId}`);
                if (replySection) {
                    const replyList = replySection.querySelector('.space-y-3') || replySection.querySelector('.space-y-4');
                    if (replyList) {
                        const emptyMsg = replyList.querySelector('p.italic');
                        if (emptyMsg) emptyMsg.remove();
                        replyList.insertAdjacentHTML('beforeend', replyHtml);
                    }
                }
                
                // Cập nhật số lượng reply sử dụng ID cụ thể
                const replyCountSpan = document.getElementById(`reply-count-${commentId}`);
                if (replyCountSpan) {
                    const currentCount = parseInt(replyCountSpan.innerText.match(/\d+/) || 0);
                    replyCountSpan.innerText = `(${currentCount + 1})`;
                }
            }
        })
        .catch(e => {
            alert("Lỗi: " + e.message);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        });
    }

    // --- TOGGLE KHUNG BÌNH LUẬN BÀI REVIEW ---
    function togglePostCommentSection(postId) {
        const section = document.getElementById(`post-comment-section-${postId}`);
        const chevron = document.getElementById(`chevron-post-${postId}`);
        const input = document.getElementById(`post-comment-input-${postId}`);
        
        if (section) {
            const isHidden = section.classList.contains('hidden');
            
            if (isHidden) {
                section.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
                if (input) input.focus();
            } else {
                section.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }
    }

    // --- GỬI BÌNH LUẬN CHO BÀI REVIEW ---
    function submitPostComment(postId, event) {
        if (event) event.preventDefault();
        
        if (!currentUserId) {
            alert("Vui lòng đăng nhập để bình luận!");
            window.location.href = "/login";
            return;
        }
        
        const input = document.getElementById(`post-comment-input-${postId}`);
        if (!input) return;

        const content = input.value.trim();
        if (!content) {
            alert("Vui lòng nhập nội dung!");
            return;
        }

        const btn = event.currentTarget || event.target.closest('button');
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`/posts/${postId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: content })
        })
        .then(async r => {
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || "Lỗi server");
            return d;
        })
        .then(data => {
            if (data.success) {
                input.value = '';
                input.style.height = 'auto';
                
                // Tạo HTML comment mới
                const commentHtml = `
                    <div id="comment-${data.comment_id}" class="flex gap-3 scroll-mt-24 transition-all duration-500 animate-fade-in">
                        <img src="${data.user_avatar}" class="w-8 h-8 rounded-full flex-shrink-0">
                        <div class="flex-1">
                            <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-xs text-gray-800">${data.user_name}</span>
                                    <span class="text-[10px] text-gray-400">Vừa xong</span>
                                </div>
                                <p class="text-sm text-gray-600">${data.content}</p>
                            </div>
                            <div class="flex items-center gap-4 mt-2 ml-2">
                                <button onclick="handleLike(${data.comment_id}, 'comment')" 
                                        id="like-btn-comment-${data.comment_id}"
                                        class="text-[10px] font-bold flex items-center gap-1 text-gray-400 hover:text-red-500 transition">
                                    <i id="like-icon-comment-${data.comment_id}" class="far fa-heart"></i>
                                    <span id="like-count-comment-${data.comment_id}">0</span>
                                </button>
                                <button onclick="toggleReplySection(${data.comment_id})" 
                                        class="text-[10px] font-bold text-gray-400 hover:text-brand-green transition flex items-center gap-1">
                                    <i class="far fa-comment-dots"></i>
                                    <span>Trả lời (0)</span>
                                </button>
                            </div>
                            <div id="reply-section-${data.comment_id}" class="hidden mt-3 ml-2 bg-gray-50/80 rounded-lg p-3">
                                <div class="space-y-4 mb-3">
                                    <p class="text-center text-[10px] text-gray-400 italic py-2">Chưa có phản hồi.</p>
                                </div>
                                <div class="flex gap-2 items-start pt-2 border-t border-gray-200">
                                    <img src="${data.user_avatar}" class="w-7 h-7 rounded-full flex-shrink-0">
                                    <div class="flex-1 relative">
                                        <textarea id="reply-input-${data.comment_id}" rows="1" 
                                                  class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none pr-16 shadow-sm" 
                                                  placeholder="Nhập phản hồi..."
                                                  oninput="autoResize(this)"></textarea>
                                        <button type="button" onclick="submitReply(${data.comment_id}, event)" 
                                                class="absolute right-1.5 top-1 text-brand-green px-2 py-1 bg-brand-green/10 rounded-lg text-xs font-bold hover:bg-brand-green hover:text-white transition">
                                            Gửi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Chèn comment mới vào đầu danh sách
                const commentsList = document.getElementById(`post-comments-list-${postId}`);
                const emptyMsg = commentsList.querySelector('p.italic');
                if (emptyMsg) emptyMsg.remove();
                commentsList.insertAdjacentHTML('afterbegin', commentHtml);
                
                // Cập nhật số lượng comment trên nút
                const commentBtn = document.querySelector(`button[onclick="togglePostCommentSection(${postId})"] span`);
                if (commentBtn) {
                    const currentCount = parseInt(commentBtn.innerText.match(/\d+/) || 0);
                    commentBtn.innerText = `Bình luận (${currentCount + 1})`;
                }
            }
        })
        .catch(e => {
            alert("Lỗi: " + e.message);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        });
    }

    // === PHẦN BÌNH LUẬN BÀI POST (PREFIX pr-) ===
    
    // Toggle khung reply trong phần bình luận bài post
    function togglePRReplySection(commentId) {
        const section = document.getElementById(`pr-reply-section-${commentId}`);
        const input = document.getElementById(`pr-reply-input-${commentId}`);
        
        if (section) {
            const isHidden = section.classList.contains('hidden');
            
            if (isHidden) {
                section.classList.remove('hidden');
                if (input) input.focus();
            } else {
                section.classList.add('hidden');
            }
        }
    }

    // Gửi reply trong phần bình luận bài post
    function submitPRReply(commentId, event) {
        if (event) event.preventDefault();
        
        if (!currentUserId) {
            alert("Vui lòng đăng nhập để bình luận!");
            window.location.href = "/login";
            return;
        }
        
        const input = document.getElementById(`pr-reply-input-${commentId}`);
        if (!input) return;

        const content = input.value.trim();
        if (!content) {
            alert("Vui lòng nhập nội dung!");
            return;
        }

        const btn = event.currentTarget || event.target.closest('button');
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        fetch(`/comment/${commentId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: content })
        })
        .then(async r => {
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || "Lỗi server");
            return d;
        })
        .then(data => {
            if (data.success) {
                input.value = '';
                input.style.height = 'auto';
                
                // Tạo HTML reply mới
                const replyHtml = `
                    <div id="pr-reply-${data.reply_id}" class="flex gap-2 animate-fade-in scroll-mt-24 transition-all duration-500">
                        <img src="${data.user_avatar}" class="w-6 h-6 rounded-full flex-shrink-0">
                        <div class="flex-1">
                            <div class="bg-white p-2 rounded-lg border border-gray-100">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-[10px] text-gray-700">${data.user_name}</span>
                                    <span class="text-[9px] text-gray-400">${data.time}</span>
                                </div>
                                <p class="text-[11px] text-gray-600">${data.content}</p>
                            </div>
                            <button onclick="handleLike(${data.reply_id}, 'comment')" 
                                    id="pr-like-btn-${data.reply_id}"
                                    class="text-[9px] font-bold ml-1 mt-1 flex items-center gap-1 text-gray-400 hover:text-red-500 transition">
                                <i id="pr-like-icon-${data.reply_id}" class="far fa-heart"></i>
                                <span id="pr-like-count-${data.reply_id}">0</span>
                            </button>
                        </div>
                    </div>
                `;

                // Chèn reply mới vào danh sách
                const replySection = document.getElementById(`pr-reply-section-${commentId}`);
                if (replySection) {
                    const replyList = replySection.querySelector('.pr-reply-list');
                    if (replyList) {
                        const emptyMsg = replyList.querySelector('p.italic');
                        if (emptyMsg) emptyMsg.remove();
                        replyList.insertAdjacentHTML('beforeend', replyHtml);
                    }
                }
                
                // Cập nhật số lượng reply trên nút "Trả lời"
                const replyBtn = document.querySelector(`button[onclick="togglePRReplySection(${commentId})"] span`);
                if (replyBtn) {
                    const currentCount = parseInt(replyBtn.innerText.match(/\d+/) || 0);
                    replyBtn.innerText = `Trả lời (${currentCount + 1})`;
                }
            }
        })
        .catch(e => {
            alert("Lỗi: " + e.message);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        });
    }

    // --- THÔNG BÁO ĐĂNG NHẬP CHO GUEST ---
    function showLoginToast(action) {
        // Xóa thông báo cũ nếu có
        const existingMsg = document.getElementById('login-inline-msg');
        if (existingMsg) existingMsg.remove();

        // Tạo thông báo inline
        const msg = document.createElement('div');
        msg.id = 'login-inline-msg';
        msg.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-lg animate-slide-up';
        msg.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 py-3 px-5 text-center text-sm text-gray-500">
                <a href="/login" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để tham gia thảo luận cùng mọi người.
            </div>
        `;
        document.body.appendChild(msg);

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            if (msg) {
                msg.classList.add('animate-slide-down');
                setTimeout(() => msg.remove(), 300);
            }
        }, 5000);
    }

    function closeLoginToast() {
        const msg = document.getElementById('login-inline-msg');
        if (msg) {
            msg.classList.add('animate-slide-down');
            setTimeout(() => msg.remove(), 300);
        }
    }
</script>
<style>
    /* Animation nảy cho tim */
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    .bounce { animation: bounce 0.3s; }

    /* Toast animations */
    @keyframes slideUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    @keyframes slideDown {
        from { opacity: 1; transform: translateX(-50%) translateY(0); }
        to { opacity: 0; transform: translateX(-50%) translateY(20px); }
    }
    .animate-slide-up { animation: slideUp 0.3s ease-out; }
    .animate-slide-down { animation: slideDown 0.3s ease-out; }
</style>
@endpush