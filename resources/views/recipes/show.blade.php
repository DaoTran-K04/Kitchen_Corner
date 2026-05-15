@extends('layouts.app')

@section('title', $recipe->title . ' – Góc Bếp')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-24 lg:pt-32 pb-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-black font-bold mb-5 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-green-600 transition">Trang chủ</a>
        <span class="text-gray-500 font-normal">/</span>
        <a href="{{ route('recipes.list') }}" class="hover:text-green-600 transition">Công thức</a>
        @if($recipe->category)
        <span class="text-gray-500 font-normal">/</span>
        <a href="{{ route('recipes.list', ['category'=>$recipe->category->slug]) }}" class="hover:text-green-600 transition">{{ $recipe->category->name }}</a>
        @endif
        <span class="text-gray-500 font-normal">/</span>
        <span class="text-green-700 font-extrabold">{{ Str::limit($recipe->title, 40) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ===== CỘT TRÁI – NỘI DUNG CHÍNH ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Ảnh bìa --}}
            <div class="rounded-2xl overflow-hidden shadow-lg aspect-video">
                <img src="{{ $recipe->thumbnail }}"
                    alt="{{ $recipe->title }}" class="w-full h-full object-cover"
                    onerror="this.src='https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=1000&auto=format&fit=crop'">

            </div>

            {{-- Tiêu đề & Meta --}}
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @if($recipe->category)
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">{{ $recipe->category->name }}</span>
                    @endif
                    <span class="text-xs font-bold px-3 py-1 rounded-full
                        {{ $recipe->difficulty=='easy' ? 'bg-green-100 text-green-700' : ($recipe->difficulty=='hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $recipe->difficulty=='easy' ? '🟢 Dễ' : ($recipe->difficulty=='hard' ? '🔴 Khó' : '🟡 Trung bình') }}
                    </span>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-800 mb-3">{{ $recipe->title }}</h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-4">
                    <span class="flex items-center gap-1">
                        <img src="{{ $recipe->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($recipe->user->name) }}"
                            class="w-7 h-7 rounded-full object-cover" alt="">
                        <strong class="text-gray-700">{{ $recipe->user->name }}</strong>
                    </span>
                    @if($recipe->cooking_time)
                    <span><i class="fas fa-clock text-green-500"></i> {{ $recipe->cooking_time }} phút</span>
                    @endif
                    <span><i class="fas fa-eye text-blue-400"></i> {{ number_format($recipe->view_count) }} lượt xem</span>
                    <span><i class="fas fa-heart text-red-400"></i> {{ $likeCount }} lượt thích</span>
                    <span><i class="fas fa-comment text-gray-400"></i> {{ $commentCount }} bình luận</span>
                    <span class="text-gray-400">{{ $recipe->created_at->diffForHumans() }}</span>
                </div>

                {{-- Nút Like & Bookmark --}}
                <div class="flex items-center gap-3">
                    @auth
                    <button id="likeBtn"
                        onclick="toggleLike({{ $recipe->id }})"
                        class="flex items-center gap-2 px-5 py-2 rounded-full border-2 font-bold transition
                            {{ $userLiked ? 'bg-red-500 border-red-500 text-white' : 'border-gray-300 text-gray-600 hover:border-red-400 hover:text-red-500' }}">
                        <i class="fas fa-heart"></i>
                        <span id="likeCount">{{ $likeCount }}</span> Thích
                    </button>
                    <button id="bookmarkBtn"
                        onclick="toggleBookmark({{ $recipe->id }})"
                        class="flex items-center gap-2 px-5 py-2 rounded-full border-2 font-bold transition
                            {{ $userSaved ? 'bg-yellow-400 border-yellow-400 text-yellow-900' : 'border-gray-300 text-gray-600 hover:border-yellow-400 hover:text-yellow-500' }}">
                        <i class="{{ $userSaved ? 'fas' : 'far' }} fa-bookmark"></i>
                        {{ $userSaved ? 'Đã lưu' : 'Lưu lại' }}
                    </button>
                    @else
                    <button onclick="requireLogin('Bạn cần đăng nhập để thả tim cho công thức này!')" class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-gray-300 text-gray-500 hover:border-red-400 hover:text-red-500 font-bold transition">
                        <i class="fas fa-heart"></i> Thích
                    </button>
                    <button onclick="requireLogin('Bạn cần đăng nhập để lưu công thức này vào bộ sưu tập!')" class="flex items-center gap-2 px-5 py-2 rounded-full border-2 border-gray-300 text-gray-500 hover:border-yellow-400 hover:text-yellow-500 font-bold transition">
                        <i class="far fa-bookmark"></i> Lưu lại
                    </button>
                    @endauth

                    @if(Auth::check() && (Auth::id() === $recipe->user_id || Auth::user()->is_admin))
                    <a href="{{ route('recipes.edit', $recipe->id) }}"
                        class="ml-auto flex items-center gap-2 px-4 py-2 rounded-full border-2 border-gray-200 text-gray-500 hover:border-green-500 hover:text-green-600 text-sm font-bold transition">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    @endif
                </div>
            </div>

            {{-- Mô tả --}}
            @if($recipe->description)
            <div class="bg-green-50 border border-green-100 rounded-xl p-5">
                <h2 class="font-bold text-green-800 mb-2 flex items-center gap-2"><i class="fas fa-info-circle"></i> Giới thiệu</h2>
                <p class="text-gray-700 leading-relaxed">{{ $recipe->description }}</p>
            </div>
            @endif

            {{-- Nguyên liệu --}}
            @if($recipe->ingredients->count())
            <div class="bg-white rounded-2xl shadow p-5">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-shopping-basket text-orange-500"></i> Nguyên liệu
                    <span class="text-sm text-gray-400 font-normal">({{ $recipe->ingredients->count() }} loại)</span>
                </h2>
                <div class="relative">
                    <div id="ingredientsList" class="grid grid-cols-1 sm:grid-cols-2 gap-3 overflow-hidden transition-all duration-500 relative" style="max-height: 250px;">
                        @foreach($recipe->ingredients as $ing)
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-2 hover:bg-green-50 transition">
                            <span class="font-medium text-gray-700">{{ $ing->name }}</span>
                            <span class="text-sm text-green-700 font-bold">
                                {{ $ing->pivot->quantity }} {{ $ing->unit }}
                                @if($ing->pivot->notes)
                                <span class="text-gray-400 font-normal text-xs">({{ $ing->pivot->notes }})</span>
                                @endif
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <div id="ingredientsOverlay" class="absolute bottom-0 left-0 w-full h-20 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                </div>
                <div class="text-center mt-4">
                    <button id="toggleIngredientsBtn" onclick="toggleExpand('ingredientsList', 'ingredientsOverlay', this, 250)" class="text-orange-500 font-bold hover:text-orange-600 hover:underline transition hidden text-sm flex items-center justify-center gap-1 mx-auto">
                        <span>Xem thêm nguyên liệu</span> <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Các bước nấu --}}
            @if($recipe->steps->count())
            <div class="bg-white rounded-2xl shadow p-5">
                <h2 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-list-ol text-blue-500"></i> Các bước thực hiện
                </h2>
                <div class="relative">
                    <div id="stepsList" class="space-y-6 overflow-hidden transition-all duration-500 relative" style="max-height: 500px;">
                        @foreach($recipe->steps->sortBy('step_number') as $step)
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-9 h-9 rounded-full bg-green-600 text-white font-extrabold flex items-center justify-center text-lg shadow">
                                {{ $step->step_number }}
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 text-lg font-medium leading-[1.8] mb-4">{{ $step->description }}</p>
                                @if($step->image)
                                <img src="{{ Str::startsWith($step->image,'http') ? $step->image : asset('storage/'.$step->image) }}"
                                    alt="Bước {{ $step->step_number }}"
                                    class="rounded-xl max-h-60 object-cover shadow">
                                @endif
                            </div>
                        </div>
                        @unless($loop->last)
                        <div class="border-l-2 border-dashed border-green-200 ml-4 h-4"></div>
                        @endunless
                        @endforeach
                    </div>
                    <div id="stepsOverlay" class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
                </div>
                <div class="text-center mt-4">
                    <button id="toggleStepsBtn" onclick="toggleExpand('stepsList', 'stepsOverlay', this, 500)" class="text-blue-500 font-bold hover:text-blue-600 hover:underline transition hidden text-sm flex items-center justify-center gap-1 mx-auto">
                        <span>Xem toàn bộ các bước</span> <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Bình luận --}}
            <div class="bg-white rounded-2xl shadow p-5" id="comments">
                <h2 class="text-xl font-bold text-gray-800 mb-5 flex items-center gap-2">
                    <i class="fas fa-comments text-purple-500"></i> Bình luận ({{ $commentCount }})
                </h2>

                @auth
                <form action="{{ route('recipes.comment', $recipe->id) }}" method="POST" class="mb-6">
                    @csrf
                    <textarea name="content" rows="3"
                        placeholder="Chia sẻ cảm nhận của bạn về công thức này..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none"></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2 rounded-xl transition text-sm">
                            Gửi bình luận
                        </button>
                    </div>
                </form>
                @else
                {{-- 🔒 GUEST COMMENT GATE --}}
                <div class="relative rounded-2xl overflow-hidden mb-6 group">
                    {{-- Frosted overlay --}}
                    <div class="bg-gradient-to-br from-green-50 via-white to-amber-50 border-2 border-dashed border-green-200 rounded-2xl p-6 text-center">
                        <div class="w-14 h-14 bg-brand-green/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-comments text-brand-green text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-base mb-1">Tham gia bình luận cùng cộng đồng!</h3>
                        <p class="text-sm text-gray-500 mb-4">Đăng nhập để chia sẻ cảm nhận, hỏi đáp và kết nối với các đầu bếp khác.</p>
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('login') }}"
                                class="bg-brand-green text-white font-bold px-5 py-2.5 rounded-full hover:bg-[#1e3828] transition text-sm shadow-md">
                                <i class="fas fa-sign-in-alt mr-1.5"></i>Đăng Nhập
                            </a>
                            <a href="{{ route('register') }}"
                                class="border-2 border-brand-green text-brand-green font-bold px-5 py-2.5 rounded-full hover:bg-green-50 transition text-sm">
                                Đăng Ký Ngay
                            </a>
                        </div>
                    </div>
                </div>
                @endauth

                <div class="space-y-5">
                    @forelse($recipe->comments as $comment)
                    <div class="flex gap-3">
                        <img src="{{ $comment->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name) }}"
                            class="w-9 h-9 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1">
                            <div class="bg-gray-50 rounded-xl px-4 py-3">
                                <span class="font-bold text-gray-800 text-sm">{{ $comment->user->name }}</span>
                                <span class="text-gray-400 text-xs ml-2">{{ $comment->created_at->diffForHumans() }}</span>
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
                                <img src="{{ $reply->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($reply->user->name) }}"
                                    class="w-7 h-7 rounded-full object-cover flex-shrink-0" alt="">
                                <div class="flex-1">
                                    <div class="bg-white border border-gray-100 rounded-xl px-3 py-2">
                                        <span class="font-bold text-gray-700 text-xs">{{ $reply->user->name }}</span>
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
            </div>
        </div>

        {{-- ===== CỘT PHẢI – SIDEBAR ===== --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Bảng dinh dưỡng --}}
            @if($recipe->total_calories)
            <div class="bg-white rounded-2xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-orange-500"></i> Thông tin dinh dưỡng
                </h3>
                <div class="text-center mb-4">
                    <div class="text-4xl font-extrabold text-orange-500">{{ $recipe->total_calories }}</div>
                    <div class="text-gray-400 text-sm">kcal / khẩu phần</div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-blue-50 rounded-xl p-3">
                        <div class="font-bold text-blue-700 text-lg">{{ $recipe->total_protein }}g</div>
                        <div class="text-xs text-gray-500">Protein</div>
                    </div>
                    <div class="bg-yellow-50 rounded-xl p-3">
                        <div class="font-bold text-yellow-700 text-lg">{{ $recipe->total_carbs }}g</div>
                        <div class="text-xs text-gray-500">Carbs</div>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3">
                        <div class="font-bold text-red-500 text-lg">{{ $recipe->total_fat }}g</div>
                        <div class="text-xs text-gray-500">Chất béo</div>
                    </div>
                </div>
                {{-- Progress bars --}}
                @php
                    $total = $recipe->total_protein + $recipe->total_carbs + $recipe->total_fat;
                    $pP = $total > 0 ? round($recipe->total_protein / $total * 100) : 0;
                    $pC = $total > 0 ? round($recipe->total_carbs / $total * 100) : 0;
                    $pF = $total > 0 ? round($recipe->total_fat / $total * 100) : 0;
                @endphp
                <div class="mt-3 space-y-2">
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Protein</span><span>{{ $pP }}%</span></div>
                        <div class="h-2 bg-gray-100 rounded-full"><div class="h-2 bg-blue-500 rounded-full" style="width:{{ $pP }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Carbs</span><span>{{ $pC }}%</span></div>
                        <div class="h-2 bg-gray-100 rounded-full"><div class="h-2 bg-yellow-400 rounded-full" style="width:{{ $pC }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1"><span>Chất béo</span><span>{{ $pF }}%</span></div>
                        <div class="h-2 bg-gray-100 rounded-full"><div class="h-2 bg-red-400 rounded-full" style="width:{{ $pF }}%"></div></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Công thức liên quan --}}
            @if($relatedRecipes->count())
            <div class="bg-white rounded-2xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-th text-green-500"></i> Có thể bạn thích
                </h3>
                <div class="space-y-3">
                    @foreach($relatedRecipes as $rel)
                    <a href="{{ route('recipes.show', $rel->slug) }}" class="flex gap-3 group">
                        <img src="{{ $rel->thumbnail }}"
                            alt="{{ $rel->title }}" class="w-16 h-16 object-cover rounded-xl flex-shrink-0"
                            onerror="this.src='https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?q=80&w=200&auto=format&fit=crop'">

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-green-600 transition line-clamp-2 leading-snug">{{ $rel->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-eye"></i> {{ number_format($rel->view_count) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleExpand(listId, overlayId, btn, threshold) {
        const list = document.getElementById(listId);
        const overlay = document.getElementById(overlayId);
        if (!list) return;

        if (list.style.maxHeight === 'none' || list.style.maxHeight === '') {
            // Thu gọn
            list.style.maxHeight = threshold + 'px';
            if (overlay) overlay.classList.remove('hidden');
            let text = listId === 'ingredientsList' ? 'Xem thêm nguyên liệu' : 'Xem toàn bộ các bước';
            btn.innerHTML = `<span>${text}</span> <i class="fas fa-chevron-down"></i>`;
        } else {
            // Xem thêm
            list.style.maxHeight = 'none';
            if (overlay) overlay.classList.add('hidden');
            btn.innerHTML = `<span>Thu gọn</span> <i class="fas fa-chevron-up"></i>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checkExpandable = (listId, btnId, threshold) => {
            const list = document.getElementById(listId);
            const btn = document.getElementById(btnId);
            if (list && btn) {
                if (list.scrollHeight > threshold) {
                    btn.classList.remove('hidden');
                } else {
                    list.style.maxHeight = 'none';
                    const overlay = document.getElementById(listId.replace('List', 'Overlay'));
                    if (overlay) overlay.classList.add('hidden');
                }
            }
        };
        checkExpandable('ingredientsList', 'toggleIngredientsBtn', 250);
        checkExpandable('stepsList', 'toggleStepsBtn', 500);
    });
</script>
<script>
async function toggleLike(recipeId) {
    const res = await fetch('{{ route("handle.like") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({id: recipeId, type: 'post'})
    });
    const data = await res.json();
    if (data.success) {
        const btn = document.getElementById('likeBtn');
        document.getElementById('likeCount').textContent = data.count;
        if (data.liked) {
            btn.classList.replace('border-gray-300','border-red-500');
            btn.classList.add('bg-red-500', 'text-white');
            btn.classList.remove('text-gray-600');
        } else {
            btn.classList.replace('border-red-500','border-gray-300');
            btn.classList.remove('bg-red-500', 'text-white');
            btn.classList.add('text-gray-600');
        }
    }
}

async function toggleBookmark(recipeId) {
    const res = await fetch('{{ route("recipes.bookmark") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({recipe_id: recipeId})
    });
    const data = await res.json();
    if (data.success) {
        const btn = document.getElementById('bookmarkBtn');
        if (data.saved) {
            btn.innerHTML = '<i class="fas fa-bookmark"></i> Đã lưu';
            btn.classList.add('bg-yellow-400','border-yellow-400','text-yellow-900');
            btn.classList.remove('border-gray-300','text-gray-600');
        } else {
            btn.innerHTML = '<i class="far fa-bookmark"></i> Lưu lại';
            btn.classList.remove('bg-yellow-400','border-yellow-400','text-yellow-900');
            btn.classList.add('border-gray-300','text-gray-600');
        }
    }
}

function handleLike(id, type) {
    if (!{{ Auth::check() ? 'true' : 'false' }}) { alert("Vui lòng đăng nhập để thích."); return; }
    const btn = document.getElementById(`like-btn-${type}-${id}`);
    const icon = document.getElementById(`like-icon-${type}-${id}`);
    const countSpan = document.getElementById(`like-count-${type}-${id}`);
    if (!icon || !countSpan) return;

    fetch('{{ route("handle.like") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ id: id, type: type })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            icon.classList.toggle('fas', data.liked);
            icon.classList.toggle('far', !data.liked);
            if (btn) {
                btn.classList.toggle('text-red-500', data.liked);
                btn.classList.toggle('text-gray-500', !data.liked);
                btn.classList.toggle('text-gray-400', !data.liked);
            }
            countSpan.innerText = data.count;
        }
    });
}

function toggleReplySection(commentId) {
    const section = document.getElementById(`reply-section-${commentId}`);
    if (section) {
        section.classList.toggle('hidden');
    }
}

function replyToUser(parentCommentId, username) {
    const section = document.getElementById(`reply-section-${parentCommentId}`);
    if (section) {
        section.classList.remove('hidden');
        const textarea = section.querySelector('textarea[name="content"]');
        if (textarea) {
            const replyText = `@${username} `;
            if (!textarea.value.startsWith(replyText)) {
                textarea.value = replyText + textarea.value;
            }
            textarea.focus();
        }
    }
}

async function reportComment(commentId) {
    if (!{{ Auth::check() ? 'true' : 'false' }}) { alert("Vui lòng đăng nhập để báo cáo."); return; }
    
    let reason = prompt("Vui lòng nhập lý do báo cáo (spam, offensive, harassment, inappropriate, other):", "inappropriate");
    if (!reason) return;
    
    const validReasons = ['spam','offensive','harassment','inappropriate','other'];
    if (!validReasons.includes(reason)) {
        reason = 'other';
    }

    const res = await fetch(`/report/comment/${commentId}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ reason: reason })
    });
    const data = await res.json();
    alert(data.message || "Đã gửi báo cáo!");
}
</script>
@endpush
@endsection
