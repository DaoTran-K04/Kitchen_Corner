@extends('layouts.app')

@section('title', 'Tất Cả Đánh Giá - ' . $book->title)

@section('content')
    {{-- 1. HEADER NHỎ --}}
    <div class="bg-[#2A483A] text-white py-10 border-b border-brand-green-light relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <div class="flex items-center gap-2 text-white/60 text-xs mb-3 font-medium uppercase tracking-wider">
                        <a href="{{ route('home') }}" class="hover:text-white transition">Trang chủ</a>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                        <a href="{{ route('detail', $book->slug) }}" class="hover:text-white transition">Chi tiết sách</a>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                        <span class="text-white">Đánh giá</span>
                    </div>
                    <h1 class="text-2xl md:text-4xl font-serif font-bold leading-tight mb-2">
                        {{ $book->title }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm text-white/80">
                        <span>Tổng hợp đánh giá từ cộng đồng</span>
                    </div>
                </div>
                
                <a href="{{ route('detail', $book->slug) }}" class="group flex items-center gap-3 px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full transition backdrop-blur-sm shadow-lg">
                    <div class="w-8 h-8 rounded-full bg-white text-brand-green flex items-center justify-center group-hover:-translate-x-1 transition-transform">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <span class="text-sm font-bold">Quay lại trang sách</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. MAIN CONTENT --}}
    <main class="container mx-auto px-4 py-12 flex-grow min-h-[600px]">
        <div class="max-w-4xl mx-auto">
            
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 font-serif flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-brand-green/10 flex items-center justify-center text-brand-green"><i class="fas fa-comments"></i></span>
                    Danh Sách Review ({{ $reviews->total() }})
                </h2>
                
                <a href="{{ route('reviews.create', ['book_id' => $book->id]) }}" class="inline-flex items-center gap-2 text-sm font-bold text-brand-accent hover:text-brand-brown hover:underline transition">
                    <i class="fas fa-pen"></i> Viết đánh giá mới
                </a>
            </div>

            <div class="space-y-6">
                @forelse($reviews as $review)
                    <div id="post-{{ $review->id }}" class="bg-white p-6 md:p-8 rounded-2xl shadow-soft border border-gray-100 hover:shadow-card transition duration-300 scroll-mt-24">
                        <div class="flex items-start gap-5">
                            <div class="flex-shrink-0">
                                <a href="{{ route('public.profile', $review->user->id) }}">
                                    @include('partials.user-avatar-with-frame', [
                                        'user' => $review->user,
                                        'size' => 'w-14 h-14',
                                        'avatarSize' => 'w-12 h-12'
                                    ])
                                </a>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <a href="{{ route('public.profile', $review->user->id) }}" class="hover:text-brand-green transition">
                                            <h4 class="font-bold text-gray-800 text-base flex items-center">
                                                {{ $review->user->name }}
                                                @if($review->user)
                                                    @include('partials.user-badges', ['user' => $review->user, 'size' => 'xs'])
                                                @endif
                                            </h4>
                                        </a>
                                        <div class="flex items-center gap-3 mt-1">
                                            @php
                                                $rating = $review->rating ?? 0;
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
                                            <span class="text-gray-300 text-xs">•</span>
                                            <span class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($review->title)
                                    <h3 class="font-bold text-brand-green text-lg mb-2">"{{ $review->title }}"</h3>
                                @endif
                                
                                {{-- [MỚI] Hiển thị ảnh Thumbnail của bài Review --}}
                                @if(!empty($review->thumbnail))
                                    <div class="mb-4 rounded-xl overflow-hidden shadow-sm border border-gray-100 max-w-lg">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($review->thumbnail, 'http') ? $review->thumbnail : asset('storage/' . $review->thumbnail) }}" 
                                             class="w-full h-auto object-cover hover:scale-105 transition duration-700">
                                    </div>
                                @endif
                                
                                {{-- [SỬA] Dùng {!! !!} để render HTML từ CKEditor --}}
                                <div class="text-gray-600 text-sm leading-7 mb-4 text-justify prose prose-sm max-w-none">
                                    {!! $review->content !!}
                                </div>

                                <div class="flex items-center justify-between md:justify-start gap-4 md:gap-6 pt-4 border-t border-gray-50">
                                    {{-- Nút Like --}}
                                    <button 
                                        type="button"
                                        onclick="handleLike({{ $review->id }}, 'post')" 
                                        id="like-btn-post-{{ $review->id }}"
                                        class="flex items-center gap-1.5 md:gap-2 text-xs font-bold transition group {{ Auth::check() && $review->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }}">
                                        <i id="like-icon-post-{{ $review->id }}" class="{{ Auth::check() && $review->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart text-sm group-hover:bg-red-50 p-1.5 rounded-full"></i> 
                                        <span class="hidden md:inline">Thích (</span><span id="like-count-post-{{ $review->id }}">{{ $review->likes_count ?? 0 }}</span><span class="hidden md:inline">)</span>
                                    </button>

                                    {{-- Nút Bình luận --}}
                                    <button onclick="toggleComment({{ $review->id }})" class="flex items-center gap-1.5 md:gap-2 text-xs font-bold text-gray-500 hover:text-blue-500 transition group">
                                        <i class="far fa-comment-dots text-sm group-hover:bg-blue-50 p-1.5 rounded-full"></i> 
                                        <span class="hidden md:inline">Bình luận (</span><span id="comment-count-{{ $review->id }}">{{ $review->comments_count ?? 0 }}</span><span class="hidden md:inline">)</span>
                                    </button>

                                    {{-- Nút Lưu bài viết --}}
                                    @auth
                                        @php
                                            $isSaved = Auth::user()->savedPosts()->where('post_id', $review->id)->exists();
                                        @endphp
                                        <button 
                                            type="button"
                                            onclick="handleSavePost({{ $review->id }})" 
                                            id="save-btn-{{ $review->id }}"
                                            class="flex items-center gap-1.5 md:gap-2 text-xs font-bold transition group {{ $isSaved ? 'text-yellow-500' : 'text-gray-500 hover:text-yellow-500' }}">
                                            <i id="save-icon-{{ $review->id }}" class="{{ $isSaved ? 'fas' : 'far' }} fa-bookmark text-sm group-hover:bg-yellow-50 p-1.5 rounded-full"></i> 
                                            <span id="save-text-{{ $review->id }}" class="hidden md:inline">{{ $isSaved ? 'Đã lưu' : 'Lưu' }}</span>
                                        </button>

                                        {{-- Nút Báo cáo bài viết --}}
                                        @if($review->user_id !== Auth::id())
                                            <button onclick="openReportModal({{ $review->id }}, 'post')"
                                                class="flex items-center gap-1.5 md:gap-2 text-xs font-bold text-gray-500 hover:text-red-500 transition group ml-auto md:ml-auto"
                                                title="Báo cáo bài viết này">
                                                <i class="far fa-flag text-sm group-hover:bg-red-50 p-1.5 rounded-full"></i> 
                                                <span class="hidden md:inline">Báo cáo</span>
                                            </button>
                                        @endif
                                    @else
                                        <button 
                                            type="button"
                                            onclick="showLoginToast('lưu')" 
                                            class="flex items-center gap-1.5 md:gap-2 text-xs font-bold text-gray-500 hover:text-yellow-500 transition group">
                                            <i class="far fa-bookmark text-sm group-hover:bg-yellow-50 p-1.5 rounded-full"></i> 
                                            <span class="hidden md:inline">Lưu</span>
                                        </button>
                                    @endauth
                                </div>

                                {{-- THÔNG BÁO ĐĂNG NHẬP CHUNG (GUEST) - Hiển thị khi click Like hoặc Bình luận --}}
                                @guest
                                <div id="guest-login-box-{{ $review->id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-100">
                                    <div class="text-center py-3 bg-gray-50 rounded-lg text-xs text-gray-500 border border-dashed border-gray-200">
                                        <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để tham gia thảo luận cùng mọi người.
                                    </div>
                                </div>
                                @endguest

                                {{-- KHUNG BÌNH LUẬN --}}
                                <div id="comment-box-{{ $review->id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-100 animate-fade-in-down">
                                    
                                    @if($review->comments && $review->comments->count() > 0)
                                        <div class="space-y-4 mb-4 pl-4 border-l-2 border-gray-100">
                                            @php
                                                // Lọc comment cha và sắp xếp mới nhất
                                                $parentComments = $review->comments->whereNull('parent_id')->sortByDesc('created_at');
                                            @endphp
                                            @foreach($parentComments as $comment)
                                                @php
                                                    // Lấy replies của comment này
                                                    $replies = $review->comments->where('parent_id', $comment->id)->sortBy('created_at');
                                                @endphp
                                                <div id="comment-{{ $comment->id }}">
                                                    <div class="flex gap-3">
                                                        <a href="{{ route('public.profile', $comment->user->id ?? 0) }}" class="flex-shrink-0">
                                                            @include('partials.user-avatar-with-frame', [
                                                                'user' => $comment->user,
                                                                'size' => 'w-10 h-10',
                                                                'avatarSize' => 'w-8 h-8'
                                                            ])
                                                        </a>
                                                        <div class="flex-1">
                                                            <div class="bg-gray-50 p-3 rounded-r-xl rounded-bl-xl text-xs w-full">
                                                                <div class="flex justify-between mb-1">
                                                                    <a href="{{ route('public.profile', $comment->user->id ?? 0) }}" class="hover:text-brand-green transition">
                                                                        <span class="font-bold text-gray-800 flex items-center">
                                                                            {{ $comment->user->name ?? 'Ẩn danh' }}
                                                                            @if($comment->user)
                                                                                @include('partials.user-badges', ['user' => $comment->user, 'size' => 'xs', 'max' => 2])
                                                                            @endif
                                                                        </span>
                                                                    </a>
                                                                    <span class="text-gray-400 text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <span class="text-gray-600 block leading-relaxed whitespace-pre-line">{{ $comment->content }}</span>
                                                            </div>

                                                            {{-- ACTIONS --}}
                                                            <div class="flex items-center gap-4 mt-2 ml-2">
                                                                <button onclick="handleLike({{ $comment->id }}, 'comment')" 
                                                                        id="like-btn-comment-{{ $comment->id }}"
                                                                        class="text-[10px] font-bold text-gray-400 hover:text-red-500 flex gap-1 items-center transition {{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : '' }}">
                                                                    <i id="like-icon-comment-{{ $comment->id }}" class="{{ Auth::check() && $comment->likes->where('user_id', Auth::id())->count() > 0 ? 'fas' : 'far' }} fa-heart"></i>
                                                                    <span id="like-count-comment-{{ $comment->id }}">{{ $comment->likes->count() }}</span>
                                                                </button>
                                                                <button onclick="toggleReplySection({{ $comment->id }})" 
                                                                        class="text-[10px] font-bold text-gray-400 hover:text-brand-green flex gap-1 items-center transition">
                                                                    <i class="far fa-comment-dots"></i>
                                                                    <span>Trả lời</span> <span id="reply-count-{{ $comment->id }}">({{ $replies->count() }})</span>
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

                                                            {{-- REPLY SECTION --}}
                                                            <div id="reply-section-{{ $comment->id }}" class="hidden mt-3 ml-2 pl-4 border-l border-gray-200">
                                                                {{-- List Replies --}}
                                                                <div class="space-y-3 mb-3">
                                                                    @foreach($replies as $reply)
                                                                        <div id="comment-{{ $reply->id }}" class="flex gap-2">
                                                                            <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="flex-shrink-0">
                                                                                @include('partials.user-avatar-with-frame', [
                                                                                    'user' => $reply->user,
                                                                                    'size' => 'w-8 h-8',
                                                                                    'avatarSize' => 'w-6 h-6'
                                                                                ])
                                                                            </a>
                                                                            <div class="flex-1">
                                                                                <div class="bg-white p-2 border border-gray-100 rounded-lg text-xs">
                                                                                    <div class="flex justify-between mb-1">
                                                                                        <a href="{{ route('public.profile', $reply->user->id ?? 0) }}" class="font-bold text-gray-700 hover:text-brand-green">{{ $reply->user->name ?? 'User' }}</a>
                                                                                        <span class="text-[9px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                                    </div>
                                                                                    <p class="text-gray-600 whitespace-pre-line">{{ $reply->content }}</p>
                                                                                </div>
                                                                                <div class="flex items-center gap-2 ml-1 mt-1">
                                                                                    <button onclick="handleLike({{ $reply->id }}, 'comment')" 
                                                                                            id="like-btn-comment-{{ $reply->id }}"
                                                                                            class="text-[9px] font-bold text-gray-400 hover:text-red-500 flex gap-1 items-center transition {{ Auth::check() && $reply->likes->where('user_id', Auth::id())->count() > 0 ? 'text-red-500' : '' }}">
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
                                                                    @endforeach
                                                                </div>

                                                                {{-- Reply Form --}}
                                                                @auth
                                                                    <div class="flex gap-2 items-start pt-2 border-t border-gray-100">
                                                                        @include('partials.user-avatar-with-frame', [
                                                                            'user' => Auth::user(),
                                                                            'size' => 'w-8 h-8',
                                                                            'avatarSize' => 'w-6 h-6'
                                                                        ])
                                                                        <div class="flex-1 relative">
                                                                            <textarea id="reply-input-{{ $comment->id }}" rows="1" 
                                                                                      class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none pr-10 shadow-sm" 
                                                                                      placeholder="Viết phản hồi..."></textarea>
                                                                            <button type="button" onclick="submitReply({{ $comment->id }}, event)" 
                                                                                    class="absolute right-1 top-1 text-brand-green p-1 hover:bg-brand-green/10 rounded transition">
                                                                                <i class="fas fa-paper-plane text-xs"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="text-center py-2 text-xs text-gray-400 bg-gray-50 rounded">
                                                                        <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để trả lời.
                                                                    </div>
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Form bình luận AJAX --}}
                                    @auth
                                        <form data-comment-form data-review-id="{{ $review->id }}" 
                                              onsubmit="submitComment({{ $review->id }}, event)"
                                              class="flex gap-3 items-start mt-2">
                                            @csrf
                                            @include('partials.user-avatar-with-frame', [
                                                'user' => Auth::user(),
                                                'size' => 'w-10 h-10',
                                                'avatarSize' => 'w-8 h-8'
                                            ])
                                            
                                            <div class="flex-1 relative group">
                                                <textarea name="content" rows="1" required
                                                          class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-2 pl-4 pr-10 text-sm focus:outline-none focus:border-brand-green focus:bg-white resize-none transition-all shadow-inner"
                                                          placeholder="Viết bình luận của bạn..."></textarea>
                                                <button type="submit" class="absolute right-2 top-1.5 text-gray-400 hover:text-brand-green p-1 transition-colors">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="text-center py-3 bg-gray-50 rounded-lg text-xs text-gray-500 border border-dashed border-gray-200">
                                            <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để tham gia thảo luận cùng mọi người.
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                            <i class="fas fa-comment-slash text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-600 mb-2">Chưa có đánh giá nào</h3>
                        <p class="text-gray-500 mb-6 text-sm">Hãy quay lại trang chi tiết sách để viết bài đầu tiên nhé.</p>
                        <a href="{{ route('detail', $book->slug) }}#section-review" class="inline-block bg-brand-green text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:bg-brand-green-light transition">
                            Viết đánh giá ngay
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-12 flex justify-center">
                {{ $reviews->links() }}
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    const currentUserId = "{{ Auth::id() }}";

    // --- 0. Reply Logic (Mới) ---
    function toggleReplySection(commentId) {
        const section = document.getElementById(`reply-section-${commentId}`);
        const input = document.getElementById(`reply-input-${commentId}`);
        
        if (section) {
            const isHidden = section.classList.contains('hidden');
            if (isHidden) {
                section.classList.remove('hidden');
                if (input) setTimeout(() => input.focus(), 100);
            } else {
                section.classList.add('hidden');
            }
        }
    }

    function submitReply(commentId, event) {
        if (event) event.preventDefault();
        
        if (!currentUserId) {
            showLoginToast('bình luận');
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
                
                // Logic render avatar có frame hoặc không
                let avatarHtml = '';
                if (data.user_frame) {
                    avatarHtml = `
                        <div class="relative w-8 h-8 inline-block flex-shrink-0">
                            <img src="${data.user_frame}" alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                            <div class="absolute inset-0 flex items-center justify-center z-0">
                                <img src="${data.user_avatar}" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                            </div>
                        </div>
                    `;
                } else {
                    avatarHtml = `
                        <img src="${data.user_avatar}" class="w-8 h-8 rounded-full border-2 border-brand-green object-cover p-[1px]">
                    `;
                }

                // HTML cho reply mới
                const replyHtml = `
                    <div id="comment-${data.reply_id}" class="flex gap-2 animate-fade-in">
                        <a href="/thanh-vien/${currentUserId}" class="flex-shrink-0">
                            ${avatarHtml}
                        </a>
                        <div class="flex-1">
                            <div class="bg-white p-2 border border-gray-100 rounded-lg text-xs">
                                <div class="flex justify-between mb-1">
                                    <strong class="text-gray-700">${data.user_name}</strong>
                                    <span class="text-[9px] text-gray-400">Vừa xong</span>
                                </div>
                                <p class="text-gray-600 whitespace-pre-line">${data.content}</p>
                            </div>
                            <button onclick="handleLike(${data.reply_id}, 'comment')" 
                                    id="like-btn-comment-${data.reply_id}"
                                    class="text-[9px] font-bold text-gray-400 hover:text-red-500 mt-1 ml-1 flex gap-1 items-center transition">
                                <i id="like-icon-comment-${data.reply_id}" class="far fa-heart"></i>
                                <span id="like-count-comment-${data.reply_id}">0</span>
                            </button>
                        </div>
                    </div>
                `;

                // Chèn vào list
                const section = document.getElementById(`reply-section-${commentId}`);
                if (section) {
                    const list = section.querySelector('.space-y-3');
                    if (list) list.insertAdjacentHTML('beforeend', replyHtml);
                }

                // Update count button sử dụng ID cụ thể
                const replyCountSpan = document.getElementById(`reply-count-${commentId}`);
                if(replyCountSpan) {
                     const currentCount = parseInt(replyCountSpan.innerText.match(/\d+/) || 0);
                     replyCountSpan.innerText = `(${currentCount + 1})`;
                }
            }
        })
        .catch(e => {
            console.error(e);
            alert("Có lỗi xảy ra: " + e.message);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = oldHtml;
            }
        });
    }

    // --- 1. Toggle Comment Box ---
    function toggleComment(reviewId) {
        // Nếu chưa đăng nhập, hiển thị thông báo và dừng
        if (!currentUserId) {
            const guestBox = document.getElementById(`guest-login-box-${reviewId}`);
            if (guestBox) guestBox.classList.remove('hidden');
            return;
        }

        const commentBox = document.getElementById(`comment-box-${reviewId}`);
        if (commentBox.classList.contains('hidden')) {
            commentBox.classList.remove('hidden');
            const textarea = commentBox.querySelector('textarea');
            if(textarea) setTimeout(() => textarea.focus(), 100);
        } else {
            commentBox.classList.add('hidden');
        }
    }

    // --- 2. Handle Like (AJAX) ---
    function handleLike(id, type) {
        if (!currentUserId) {
            // Hiển thị thông báo (chỉ show, không ẩn)
            const guestBox = document.getElementById(`guest-login-box-${id}`);
            if (guestBox) guestBox.classList.remove('hidden');
            return;
        }

        const btnId = `like-btn-${type}-${id}`;
        const iconId = `like-icon-${type}-${id}`;
        const countId = `like-count-${type}-${id}`;

        const btn = document.getElementById(btnId);
        const icon = document.getElementById(iconId);
        const countSpan = document.getElementById(countId);

        if (!btn || !icon || !countSpan) return;

        // Optimistic UI update
        const isLiked = icon.classList.contains('fas'); 
        
        if(isLiked) {
            icon.classList.remove('fas', 'text-red-500');
            icon.classList.add('far');
            btn.classList.remove('text-red-500');
            btn.classList.add('text-gray-500');
            let currentCount = parseInt(countSpan.innerText);
            countSpan.innerText = Math.max(0, currentCount - 1);
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas', 'bounce', 'text-red-500');
            btn.classList.remove('text-gray-500');
            btn.classList.add('text-red-500');
            let currentCount = parseInt(countSpan.innerText);
            countSpan.innerText = currentCount + 1;
        }

        // Send AJAX request
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

    // --- 2.5. Handle Save Post (AJAX) ---
    function handleSavePost(postId) {
        const btn = document.getElementById(`save-btn-${postId}`);
        const icon = document.getElementById(`save-icon-${postId}`);
        const text = document.getElementById(`save-text-${postId}`);

        if (!btn || !icon || !text) return;

        // Optimistic UI update
        const isSaved = icon.classList.contains('fas');
        
        if(isSaved) {
            icon.classList.remove('fas', 'text-yellow-500');
            icon.classList.add('far');
            btn.classList.remove('text-yellow-500');
            btn.classList.add('text-gray-500');
            text.innerText = 'Lưu';
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas', 'bounce', 'text-yellow-500');
            btn.classList.remove('text-gray-500');
            btn.classList.add('text-yellow-500');
            text.innerText = 'Đã lưu';
        }

        // Send AJAX request
        fetch('/post/save', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI based on actual response
                if (data.saved) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.classList.add('text-yellow-500');
                    btn.classList.remove('text-gray-500');
                    text.innerText = 'Đã lưu';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.classList.remove('text-yellow-500');
                    btn.classList.add('text-gray-500');
                    text.innerText = 'Lưu';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // --- 3. Handle Comment Submit (AJAX) ---
    function submitComment(reviewId, event) {
        event.preventDefault();
        
        const form = event.target;
        const textarea = form.querySelector('textarea[name="content"]');
        const content = textarea.value.trim();

        if (!content) {
            alert("Nội dung bình luận không được để trống!");
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const oldHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`/posts/${reviewId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ content: content })
        })
        .then(async r => {
             const d = await r.json();
             if (!r.ok) throw new Error(d.error || d.message || "Lỗi server");
             return d;
        })
        .then(data => {
            if (data.success) {
                // Update comment count global
                const countSpan = document.getElementById(`comment-count-${reviewId}`);
                if (countSpan) countSpan.innerText = data.count;

                textarea.value = '';

                // 1. Tạo avatar HTML
                let avatarHtml = '';
                if (data.user_frame) {
                    avatarHtml = `
                        <div class="relative w-10 h-10 inline-block flex-shrink-0">
                            <img src="${data.user_frame}" alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                            <div class="absolute inset-0 flex items-center justify-center z-0">
                                <img src="${data.user_avatar}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                            </div>
                        </div>
                    `;
                } else {
                     avatarHtml = `<img src="${data.user_avatar}" class="w-10 h-10 rounded-full border border-gray-200 object-cover p-[1px]">`;
                }

                // Avatar nhỏ hơn cho form reply bên trong
                let replyAvatarHtml = '';
                 if (data.user_frame) {
                    replyAvatarHtml = `
                        <div class="relative w-8 h-8 inline-block flex-shrink-0">
                            <img src="${data.user_frame}" alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                            <div class="absolute inset-0 flex items-center justify-center z-0">
                                <img src="${data.user_avatar}" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200">
                            </div>
                        </div>
                    `;
                 } else {
                    replyAvatarHtml = `<img src="${data.user_avatar}" class="w-8 h-8 rounded-full border border-gray-200 object-cover p-[1px]">`;
                 }

                // 2. Tạo Comment HTML
                const commentHtml = `
                    <div id="comment-${data.comment_id}" class="animate-fade-in-down">
                        <div class="flex gap-3">
                            <a href="/thanh-vien/${currentUserId}" class="flex-shrink-0">
                                ${avatarHtml}
                            </a>
                            <div class="flex-1">
                                <div class="bg-gray-50 p-3 rounded-r-xl rounded-bl-xl text-xs w-full">
                                    <div class="flex justify-between mb-1">
                                        <a href="/thanh-vien/${currentUserId}" class="hover:text-brand-green transition">
                                            <span class="font-bold text-gray-800 flex items-center">
                                                ${data.user_name}
                                            </span>
                                        </a>
                                        <span class="text-gray-400 text-[10px]">Vừa xong</span>
                                    </div>
                                    <span class="text-gray-600 block leading-relaxed whitespace-pre-line">${data.content}</span>
                                </div>
                                <div class="flex items-center gap-4 mt-2 ml-2">
                                     <button onclick="handleLike(${data.comment_id}, 'comment')" 
                                            id="like-btn-comment-${data.comment_id}"
                                            class="text-[10px] font-bold text-gray-400 hover:text-red-500 flex gap-1 items-center transition">
                                        <i id="like-icon-comment-${data.comment_id}" class="far fa-heart"></i>
                                        <span id="like-count-comment-${data.comment_id}">0</span>
                                    </button>
                                    <button onclick="toggleReplySection(${data.comment_id})" 
                                            class="text-[10px] font-bold text-gray-400 hover:text-brand-green flex gap-1 items-center transition">
                                        <i class="far fa-comment-dots"></i>
                                        <span>Trả lời</span> <span id="reply-count-${data.comment_id}">(0)</span>
                                    </button>
                                </div>
                                <div id="reply-section-${data.comment_id}" class="hidden mt-3 ml-2 pl-4 border-l border-gray-200">
                                    <div class="space-y-3 mb-3"></div>
                                    <div class="mt-3 pt-3 border-t border-gray-100 transition-all duration-300">
                                        <div class="flex gap-2 items-start">
                                            ${replyAvatarHtml}
                                            <div class="flex-1 relative">
                                                <textarea id="reply-input-${data.comment_id}" rows="1" 
                                                          class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none pr-10 shadow-sm" 
                                                          placeholder="Viết phản hồi..."
                                                          oninput="autoResize(this)"></textarea>
                                                <button type="button" onclick="submitReply(${data.comment_id}, event)" 
                                                        class="absolute right-1 top-1 text-brand-green p-1.5 hover:bg-brand-green/10 rounded transition">
                                                    <i class="fas fa-paper-plane text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // 3. Append to list
                const box = document.getElementById(`comment-box-${reviewId}`);
                let list = box.querySelector('.space-y-4');
                if (!list) {
                    list = document.createElement('div');
                    list.className = 'space-y-4 mb-4 pl-4 border-l-2 border-gray-100';
                    box.insertBefore(list, form);
                }
                
                list.insertAdjacentHTML('afterbegin', commentHtml);

            } else {
                alert(data.error || "Có lỗi xảy ra.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Có lỗi xảy ra: " + error.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = oldHtml;
        });
    }

    // --- THÔNG BÁO ĐĂNG NHẬP CHO GUEST ---
    function showLoginToast(action) {
        const existingMsg = document.getElementById('login-inline-msg');
        if (existingMsg) existingMsg.remove();

        const msg = document.createElement('div');
        msg.id = 'login-inline-msg';
        msg.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-lg animate-slide-up';
        msg.innerHTML = `
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 py-3 px-5 text-center text-sm text-gray-500">
                <a href="/login" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để tham gia thảo luận cùng mọi người.
            </div>
        `;
        document.body.appendChild(msg);
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