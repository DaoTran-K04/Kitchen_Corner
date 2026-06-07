<div class="space-y-5">
    @forelse($paginatedComments as $comment)
    <div class="flex gap-3">
        <a href="{{ route('public.profile', $comment->user->id) }}" class="flex-shrink-0">
            @include('partials.user-avatar-with-frame', [
                'user' => $comment->user,
                'size' => 'w-10 h-10',
                'showNameplate' => false
            ])
        </a>
        <div class="flex-1">
            <div class="bg-gray-50 rounded-xl px-4 py-3">
                <div class="flex items-center gap-1 mb-1">
                    <a href="{{ route('public.profile', $comment->user->id) }}" class="hover:text-green-600 transition">
                        <span class="font-bold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                    </a>
                    @include('partials.user-badges', ['user' => $comment->user, 'size' => 'xs'])
                    <span class="text-gray-400 text-xs ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-700 text-sm mt-1">{{ $comment->content }}</p>
            </div>
            
            {{-- Add Like, Reply, Report buttons here --}}
            <div class="flex items-center gap-4 mt-2 ml-2 mb-4">
                @php
                    $isLikedComment = Auth::check() && $comment->likes()->where('user_id', Auth::id())->exists();
                @endphp
                <button onclick="handleLike({{ $comment->id }}, 'comment')" id="like-btn-comment-{{ $comment->id }}" class="text-[11px] font-bold flex items-center gap-1 {{ $isLikedComment ? 'text-red-500' : 'text-gray-500 hover:text-red-500' }} transition">
                    <i id="like-icon-comment-{{ $comment->id }}" class="{{ $isLikedComment ? 'fas' : 'far' }} fa-heart text-xs"></i>
                    <span id="like-count-comment-{{ $comment->id }}">{{ $comment->likes()->count() ?? 0 }}</span>
                </button>
                
                <button onclick="toggleReplySection({{ $comment->id }})" class="text-[11px] font-bold text-gray-500 hover:text-green-600 transition flex items-center gap-1">
                    <i class="fas fa-reply text-[10px]"></i> Trả lời ({{ $comment->replies->count() ?? 0 }})
                </button>

                @auth
                @if(Auth::id() !== $comment->user_id)
                <button onclick="reportComment({{ $comment->id }})" class="text-[11px] font-bold text-gray-400 hover:text-orange-500 transition ml-auto flex items-center gap-1" title="Báo cáo vi phạm">
                    <i class="fas fa-flag text-[10px]"></i> Báo cáo
                </button>
                @endif
                @endauth
            </div>

            {{-- Khu vực Reply Input --}}
            <div id="reply-section-{{ $comment->id }}" class="hidden mt-3 mb-4 border-l-2 border-gray-100 pl-3">
                @auth
                <form action="{{ route('recipes.comment', $recipe->id) }}" method="POST" class="flex gap-2 relative">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="content" rows="1" class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 resize-none" placeholder="Nhập câu trả lời..."></textarea>
                    <button type="submit" class="text-white px-4 py-1.5 bg-green-600 rounded-lg text-xs font-bold hover:bg-green-700 transition shadow">Gửi</button>
                </form>
                @else
                <div class="text-[11px] text-gray-500 italic p-2 bg-gray-50 rounded">Vui lòng <a href="{{ route('login') }}" class="text-green-600 font-bold">đăng nhập</a> để trả lời.</div>
                @endauth
            </div>

            {{-- Replies --}}
            @foreach($comment->replies as $reply)
            <div class="flex gap-3 mt-2 ml-4">
                <a href="{{ route('public.profile', $reply->user->id) }}" class="flex-shrink-0">
                    @include('partials.user-avatar-with-frame', [
                        'user' => $reply->user,
                        'size' => 'w-8 h-8',
                        'showNameplate' => false
                    ])
                </a>
                <div class="flex-1">
                    <div class="bg-white border border-gray-100 rounded-xl px-3 py-2">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('public.profile', $reply->user->id) }}" class="hover:text-green-600 transition">
                                <span class="font-bold text-gray-700 text-xs">{{ $reply->user->name }}</span>
                            </a>
                            @include('partials.user-badges', ['user' => $reply->user, 'size' => 'xs'])
                        </div>
                        <p class="text-gray-600 text-xs mt-0.5">{{ $reply->content }}</p>
                    </div>
                    <div class="flex items-center gap-4 mt-1 ml-2 mb-2">
                        @php
                            $isLikedReply = Auth::check() && $reply->likes()->where('user_id', Auth::id())->exists();
                        @endphp
                        <button onclick="handleLike({{ $reply->id }}, 'comment')" id="like-btn-comment-{{ $reply->id }}" class="text-[10px] font-bold flex items-center gap-1 {{ $isLikedReply ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }} transition">
                            <i id="like-icon-comment-{{ $reply->id }}" class="{{ $isLikedReply ? 'fas' : 'far' }} fa-heart text-[10px]"></i>
                            <span id="like-count-comment-{{ $reply->id }}">{{ $reply->likes()->count() ?? 0 }}</span>
                        </button>
                        
                        {{-- Nút Trả lời cho Reply --}}
                        <button onclick="replyToUser({{ $comment->id }}, '{{ addslashes($reply->user->name) }}')" class="text-[10px] font-bold text-gray-500 hover:text-green-600 transition flex items-center gap-1">
                            <i class="fas fa-reply text-[9px]"></i> Trả lời
                        </button>
                        
                        @auth
                        @if(Auth::id() !== $reply->user_id)
                        <button onclick="reportComment({{ $reply->id }})" class="text-[10px] font-bold text-gray-400 hover:text-orange-500 transition ml-auto flex items-center gap-1" title="Báo cáo vi phạm">
                            <i class="fas fa-flag text-[9px]"></i> Báo cáo
                        </button>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <p class="text-gray-400 text-center py-6">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
    @endforelse
</div>

{{-- Pagination Links --}}
@if($paginatedComments->hasPages())
<div class="mt-8 flex justify-center recipe-comments-pagination">
    {{ $paginatedComments->links('pagination::tailwind') }}
</div>
@endif
