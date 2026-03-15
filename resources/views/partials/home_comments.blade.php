@if(isset($latestReviews) && $latestReviews->count() > 0)
    <div class="grid grid-cols-1 gap-4 sm:gap-6">
        @foreach($latestReviews as $comment)
            @php
                $book = $comment->post->book ?? null;
            @endphp
            {{-- ITEM BÌNH LUẬN (COMMENT) --}}
            <div class="bg-white rounded-2xl p-3 sm:p-5 border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">

                {{-- 1. HEADER --}}
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('public.profile', $comment->user->id) }}" class="flex-shrink-0">
                            @include('partials.user-avatar-with-frame', [
                                'user' => $comment->user,
                                'size' => 'w-12 h-12',
                                'avatarSize' => 'w-10 h-10'
                            ])
                        </a>
                        <div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('public.profile', $comment->user->id) }}"
                                    class="hover:text-brand-green transition">
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $comment->user->name }}</h4>
                                </a>
                                @include('partials.user-badges', ['user' => $comment->user, 'size' => 'xs'])
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-1">
                                <span>Đánh giá về:</span>
                                @if($book)
                                    <a href="{{ route('detail', $book->slug ?? '#') }}"
                                        class="font-bold text-brand-green hover:underline">
                                        {{ $book->title ?? 'Sách ẩn' }}
                                    </a>
                                @else
                                    <span class="text-gray-400">Sách không xác định</span>
                                @endif
                                <span class="text-gray-300 mx-1">•</span>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Rating nếu có --}}
                    @if($comment->rating)
                        <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded-full">
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                            <span class="text-xs font-bold text-yellow-600">{{ number_format($comment->rating, 1) }}</span>
                        </div>
                    @endif
                </div>

                {{-- 2. NỘI DUNG BÌNH LUẬN --}}
                <div class="mb-4 pl-1">
                    <div class="bg-gray-50 rounded-xl p-3 text-gray-700 text-sm leading-relaxed relative">
                        {{ $comment->content }}
                    </div>
                </div>

                {{-- 3. ACTIONS FOOTER --}}
                <div class="flex items-center justify-between pt-2 border-t border-gray-50">
                    <div class="flex gap-3 md:gap-4">
                        <button onclick="handleLike({{ $comment->id }}, 'comment')" id="like-btn-comment-{{ $comment->id }}"
                            class="flex items-center gap-1.5 md:gap-1 text-xs font-bold {{ Auth::check() && $comment->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }} transition">
                            <i id="like-icon-comment-{{ $comment->id }}"
                                class="{{ Auth::check() && $comment->likes->contains('user_id', Auth::id()) ? 'fas' : 'far' }} fa-heart"></i>
                            <span class="hidden md:inline">Thích</span>
                            <span id="like-count-comment-{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                        </button>

                        <button onclick="toggleReplySection({{ $comment->id }})"
                            class="flex items-center gap-1.5 md:gap-1 text-xs font-bold text-gray-500 hover:text-brand-green transition group">
                            <i class="far fa-comment-dots group-hover:scale-110 transition-transform"></i>
                            <span class="hidden md:inline">Trả lời</span> <span id="reply-count-{{ $comment->id }}">({{ $comment->replies->count() }})</span>
                            <i id="chevron-reply-{{ $comment->id }}"
                                class="fas fa-chevron-down text-[10px] ml-1 transition-transform duration-300"></i>
                        </button>

                        {{-- Nút Báo cáo Comment --}}
                        @auth
                            @if($comment->user_id !== Auth::id())
                                <button onclick="openReportModal({{ $comment->id }}, 'comment')"
                                    class="flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-red-500 transition"
                                    title="Báo cáo bình luận này">
                                    <i class="far fa-flag"></i>
                                </button>
                            @endif
                        @endauth
                    </div>

                    {{-- Link xem chi tiết sách --}}
                    @if($book)
                        <a href="{{ route('detail', $book->slug ?? '#') }}"
                            class="text-xs text-brand-green font-bold hover:underline">
                            Xem sách →
                        </a>
                    @endif
                </div>

                {{-- 4. KHUNG TRẢ LỜI (ẨN/HIỆN) --}}
                <div id="reply-section-{{ $comment->id }}"
                    class="hidden mt-4 pt-4 border-t border-dashed border-gray-100 bg-gray-50/50 rounded-xl p-4 animate-fade-in">

                    {{-- DANH SÁCH CÁC REPLY --}}
                    <div class="space-y-4 mb-4">
                        @forelse($comment->replies as $reply)
                            <div id="comment-{{ $reply->id }}" class="flex gap-2 scroll-mt-24 transition-all duration-500">
                                <a href="{{ route('public.profile', $reply->user->id) }}" class="flex-shrink-0">
                                    @include('partials.user-avatar-with-frame', [
                                        'user' => $reply->user,
                                        'size' => 'w-9 h-9',
                                        'avatarSize' => 'w-7 h-7'
                                    ])
                                </a>
                                <div class="flex-1">
                                    <div class="bg-white p-2 rounded-xl rounded-tl-none border border-gray-100 shadow-sm">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('public.profile', $reply->user->id) }}"
                                                    class="hover:text-brand-green transition">
                                                    <h6 class="font-bold text-[10px] text-gray-700">{{ $reply->user->name }}</h6>
                                                </a>
                                                @include('partials.user-badges', ['user' => $reply->user, 'size' => 'xs'])
                                            </div>
                                            <span class="text-[9px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-600">{{ $reply->content }}</p>
                                    </div>
                                    {{-- Like cho reply --}}
                                    <div class="flex items-center gap-2 ml-2 mt-1">
                                        <button onclick="handleLike({{ $reply->id }}, 'comment')" id="like-btn-comment-{{ $reply->id }}"
                                            class="text-[9px] font-bold flex items-center gap-1 {{ Auth::check() && $reply->likes->contains('user_id', Auth::id()) ? 'text-red-500' : 'text-gray-400' }}">
                                            <i id="like-icon-comment-{{ $reply->id }}"
                                                class="{{ Auth::check() && $reply->likes->contains('user_id', Auth::id()) ? 'fas' : 'far' }} fa-heart"></i>
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

                    {{-- Ô NHẬP REPLY --}}
                    @auth
                        <div class="flex gap-2 items-start">
                            @include('partials.user-avatar-with-frame', [
                                'user' => Auth::user(),
                                'size' => 'w-9 h-9',
                                'avatarSize' => 'w-7 h-7'
                            ])
                            <div class="flex-1 relative">
                                <textarea id="reply-input-{{ $comment->id }}" rows="1"
                                    class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none pr-16 shadow-sm"
                                    placeholder="Nhập phản hồi..." oninput="autoResize(this)"></textarea>
                                <button type="button" onclick="submitReply({{ $comment->id }}, event)"
                                    class="absolute right-1.5 top-1 text-brand-green px-2 py-1 bg-brand-green/10 rounded-lg text-xs font-bold hover:bg-brand-green hover:text-white transition">
                                    Gửi
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-xs text-gray-400 italic">
                            <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a> để trả
                            lời bình luận này.
                        </p>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    {{-- PHÂN TRANG --}}
    @if ($latestReviews->hasPages())
        <div class="mt-8 flex justify-center">
            <nav class="inline-flex items-center bg-white rounded-full shadow-sm border border-gray-200 px-2 py-1">
                @if (!$latestReviews->onFirstPage())
                    <a href="{{ $latestReviews->previousPageUrl() }}"
                        class="ajax-pagination-link p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-brand-green transition mr-2">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </a>
                @endif
                @for($i = max(1, $latestReviews->currentPage() - 2); $i <= min($latestReviews->lastPage(), $latestReviews->currentPage() + 2); $i++)
                    @if($i == $latestReviews->currentPage())
                        <span class="px-3 py-1 rounded-full bg-brand-green text-white font-bold mx-1 text-xs">{{ $i }}</span>
                    @else
                        <a href="{{ $latestReviews->url($i) }}"
                            class="ajax-pagination-link px-3 py-1 rounded-full text-gray-600 hover:bg-gray-100 transition mx-1 text-xs">{{ $i }}</a>
                    @endif
                @endfor
                @if ($latestReviews->hasMorePages())
                    <a href="{{ $latestReviews->nextPageUrl() }}"
                        class="ajax-pagination-link p-2 rounded-full text-gray-500 hover:bg-gray-100 hover:text-brand-green transition ml-2">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </a>
                @endif
            </nav>
        </div>
    @endif
@else
    <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-200">
        <i class="far fa-comments text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500 text-sm">Chưa có bình luận nào từ cộng đồng.</p>
    </div>
@endif