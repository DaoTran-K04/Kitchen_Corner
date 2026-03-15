@extends('layouts.admin')
@section('title', 'Kiểm Duyệt Bài Đăng')
@section('header', 'Kiểm Duyệt Bài Đăng')

@section('content')
    @php
        $countAll = \App\Models\Post::whereNotNull('book_id')->count();
        $countPending = \App\Models\Post::whereNotNull('book_id')->where('status', 'pending')->count();
        $countPendingDelete = \App\Models\Post::whereNotNull('book_id')->where('status', 'pending_delete')->count();
        $countPublished = \App\Models\Post::whereNotNull('book_id')->where('status', 'published')->count();
        $countHidden = \App\Models\Post::whereNotNull('book_id')->where('status', 'hidden')->count();
        $countRejected = \App\Models\Post::whereNotNull('book_id')->where('status', 'rejected')->count();
    @endphp

    <div id="posts-container" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        {{-- Tabs lọc theo trạng thái --}}
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
            <div class="flex flex-wrap gap-2" id="status-tabs">
                <button type="button" data-status=""
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-list mr-1"></i> Tất cả
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ !request('status') ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-500 text-slate-600 dark:text-slate-200' }}">{{ $countAll }}</span>
                </button>
                <button type="button" data-status="pending"
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'pending' ? 'bg-yellow-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-clock mr-1"></i> Chờ duyệt
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'pending' ? 'bg-white/20' : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300' }}">{{ $countPending }}</span>
                </button>
                <button type="button" data-status="published"
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'published' ? 'bg-green-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-check-circle mr-1"></i> Đã đăng
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'published' ? 'bg-white/20' : 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300' }}">{{ $countPublished }}</span>
                </button>
                <button type="button" data-status="hidden"
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'hidden' ? 'bg-gray-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-eye-slash mr-1"></i> Đang ẩn
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'hidden' ? 'bg-white/20' : 'bg-gray-200 dark:bg-slate-500 text-gray-600 dark:text-slate-300' }}">{{ $countHidden }}</span>
                </button>
                <button type="button" data-status="rejected"
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'rejected' ? 'bg-red-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-ban mr-1"></i> Từ chối
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'rejected' ? 'bg-white/20' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300' }}">{{ $countRejected }}</span>
                </button>
                <button type="button" data-status="pending_delete"
                    class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'pending_delete' ? 'bg-orange-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                    <i class="fas fa-trash-alt mr-1"></i> Chờ xóa
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'pending_delete' ? 'bg-white/20' : 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300' }}">{{ $countPendingDelete }}</span>
                </button>
            </div>
        </div>

        {{-- Bảng --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 w-56">Người viết & Sách</th>
                        <th class="px-5 py-3">Nội dung</th>
                        <th class="px-5 py-3 text-center w-24 whitespace-nowrap">Đánh giá</th>
                        <th class="px-5 py-3 text-center w-32 whitespace-nowrap">Trạng thái</th>
                        <th class="px-5 py-3 text-center w-24"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition {{ $review->status == 'pending' ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '' }}">
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $review->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) }}"
                                        class="w-9 h-9 rounded-full border dark:border-slate-600">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $review->user->name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500 italic">{{ $review->created_at->diffForHumans() }}</p>
                                        <a href="{{ route('book.show', $review->book->slug ?? '#') }}"
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1 mt-1" target="_blank">
                                            <i class="fas fa-book"></i>
                                            {{ Str::limit($review->book->title ?? 'Sách đã xóa', 25) }}
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 align-top">
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm mb-1 line-clamp-1">{{ $review->title }}</h4>
                                <p class="text-gray-500 dark:text-slate-400 text-sm line-clamp-2 mb-2">
                                    {{ Str::limit(strip_tags($review->content), 100) }}
                                </p>
                                @if($review->status != 'pending_delete')
                                    <button type="button" onclick="showReviewModal({{ $review->id }})"
                                        class="text-xs text-blue-500 hover:text-blue-700 dark:text-blue-400 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Xem chi tiết
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-slate-500 italic">
                                        <i class="fas fa-lock mr-1"></i>Đang chờ xóa
                                    </span>
                                @endif
                                {{-- Hidden content for modal --}}
                                <div id="review-data-{{ $review->id }}" class="hidden"
                                    data-title="{{ $review->title }}"
                                    data-user="{{ $review->user->name }}"
                                    data-book="{{ $review->book->title ?? 'Sách đã xóa' }}"
                                    data-rating="{{ $review->rating }}"
                                    data-date="{{ $review->created_at->format('d/m/Y H:i') }}"
                                    data-content="{{ $review->content }}">
                                </div>
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400 font-bold bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1 rounded text-sm">
                                    {{ $review->rating }} <i class="fas fa-star text-xs"></i>
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                @switch($review->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300">
                                            <i class="fas fa-clock mr-1"></i>Chờ duyệt
                                        </span>
                                        @break
                                    @case('published')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                            <i class="fas fa-check mr-1"></i>Hiển thị
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                            <i class="fas fa-ban mr-1"></i>Từ chối
                                        </span>
                                        @break
                                    @case('hidden')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300">
                                            <i class="fas fa-eye-slash mr-1"></i>Đang ẩn
                                        </span>
                                        @break
                                    @case('pending_delete')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300">
                                            <i class="fas fa-trash-alt mr-1"></i>Chờ xóa
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-600 text-gray-600">{{ $review->status }}</span>
                                @endswitch
                            </td>

                            <td class="px-5 py-4 text-center align-top">
                                <div class="flex justify-center gap-1.5">
                                    {{-- Nút Sửa (ẩn khi đang chờ xóa) --}}
                                    @if($review->status != 'pending_delete')
                                        <a href="{{ route('admin.posts.edit', $review->id) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition" title="Sửa">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endif
                                    @if($review->status == 'pending')
                                        {{-- Duyệt --}}
                                        <form action="{{ route('admin.posts.update', $review->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="published">
                                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition" title="Duyệt">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        {{-- Từ chối --}}
                                        <button type="button" onclick="openRejectModal({{ $review->id }}, '{{ addslashes($review->title) }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition" title="Từ chối">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    @else
                                        @if($review->status == 'published')
                                            <form action="{{ route('admin.posts.update', $review->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="hidden">
                                                <button class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-400 hover:bg-gray-500 hover:text-white transition" title="Ẩn">
                                                    <i class="fas fa-eye-slash text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($review->status == 'hidden')
                                            <form action="{{ route('admin.posts.update', $review->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="published">
                                                <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition" title="Hiện lại">
                                                    <i class="fas fa-eye text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.posts.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn bài này?');">
                                            @csrf @method('DELETE')
                                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition" title="Xóa">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($review->status == 'pending_delete')
                                        {{-- Duyệt xóa --}}
                                        <form action="{{ route('admin.posts.approve-delete', $review->id) }}" method="POST" onsubmit="return confirm('Xác nhận xóa bài viết này?');">
                                            @csrf
                                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 hover:bg-orange-500 dark:hover:bg-orange-600 hover:text-white transition" title="Duyệt xóa">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        {{-- Từ chối xóa --}}
                                        <form action="{{ route('admin.posts.reject-delete', $review->id) }}" method="POST">
                                            @csrf
                                            <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition" title="Giữ bài">
                                                <i class="fas fa-undo text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p>Không có bài đăng nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                {{ $reviews->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>

    {{-- Modal Xem Chi Tiết --}}
    <div id="reviewModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4" onclick="closeReviewModal(event)">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-5 border-b dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-gray-800 dark:text-white"></h3>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500 dark:text-slate-400">
                        <span><i class="fas fa-user mr-1"></i><span id="modal-user"></span></span>
                        <span><i class="fas fa-book mr-1"></i><span id="modal-book"></span></span>
                        <span><i class="fas fa-calendar mr-1"></i><span id="modal-date"></span></span>
                        <span class="text-yellow-500"><i class="fas fa-star mr-1"></i><span id="modal-rating"></span>/5</span>
                    </div>
                </div>
                <button onclick="closeReviewModal()" class="w-9 h-9 rounded-full bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 flex items-center justify-center">
                    <i class="fas fa-times text-gray-500 dark:text-slate-300"></i>
                </button>
            </div>
            <div class="p-5 overflow-y-auto max-h-[55vh]">
                <div id="modal-content" class="prose dark:prose-invert max-w-none text-gray-700 dark:text-slate-300 leading-relaxed"></div>
            </div>
            <div class="p-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-end">
                <button onclick="closeReviewModal()" class="px-5 py-2 rounded-lg bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium hover:bg-gray-300 dark:hover:bg-slate-500 transition">Đóng</button>
            </div>
        </div>
    </div>

    {{-- Modal Từ Chối --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/60" onclick="closeRejectModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md">
                <div class="bg-red-500 text-white px-5 py-4 rounded-t-xl flex justify-between items-center">
                    <h3 class="font-bold"><i class="fas fa-ban mr-2"></i>Từ Chối Bài Viết</h3>
                    <button onclick="closeRejectModal()" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <div class="p-5">
                        <p class="text-sm text-gray-600 dark:text-slate-300 mb-4">
                            Bạn đang từ chối: <strong id="rejectPostTitle" class="text-gray-800 dark:text-white"></strong>
                        </p>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-3">Chọn lý do:</label>
                        <div class="space-y-2 mb-4">
                            @foreach(['Nội dung không liên quan đến sách', 'Vi phạm quy tắc cộng đồng', 'Spam hoặc quảng cáo', 'Nội dung quá ngắn hoặc không đầy đủ', 'Ngôn từ không phù hợp'] as $reason)
                                <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition">
                                    <input type="radio" name="rejection_reason" value="{{ $reason }}" class="text-red-500 focus:ring-red-500" onchange="toggleCustomReason()">
                                    <span class="text-sm text-gray-700 dark:text-slate-200">{{ $reason }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition">
                                <input type="radio" name="rejection_reason" value="other" id="otherReasonRadio" class="text-red-500 focus:ring-red-500" onchange="toggleCustomReason()">
                                <span class="text-sm text-gray-700 dark:text-slate-200">Khác (nhập lý do)</span>
                            </label>
                        </div>
                        <div id="customReasonContainer" class="hidden">
                            <textarea name="custom_reason" id="customReasonInput" rows="3"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-slate-700 dark:text-white text-sm resize-none placeholder:italic"
                                placeholder="Nhập lý do từ chối..."></textarea>
                        </div>
                    </div>
                    <div class="px-5 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg font-medium">Hủy</button>
                        <button type="submit" class="px-5 py-2 bg-red-500 text-white rounded-lg font-medium hover:bg-red-600 transition">
                            <i class="fas fa-ban mr-1"></i>Xác nhận
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Review Modal
        function showReviewModal(id) {
            const data = document.getElementById('review-data-' + id);
            if (!data) return;
            document.getElementById('modal-title').textContent = data.dataset.title;
            document.getElementById('modal-user').textContent = data.dataset.user;
            document.getElementById('modal-book').textContent = data.dataset.book;
            document.getElementById('modal-rating').textContent = data.dataset.rating;
            document.getElementById('modal-date').textContent = data.dataset.date;
            document.getElementById('modal-content').innerHTML = data.dataset.content;
            document.getElementById('reviewModal').classList.remove('hidden');
            document.getElementById('reviewModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeReviewModal(e) {
            if (e && e.target !== e.currentTarget) return;
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('reviewModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Reject Modal
        function openRejectModal(id, title) {
            document.getElementById('rejectForm').action = `/admin/posts/${id}`;
            document.getElementById('rejectPostTitle').textContent = title;
            document.getElementById('rejectForm').reset();
            document.getElementById('customReasonContainer').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
        function toggleCustomReason() {
            const container = document.getElementById('customReasonContainer');
            const radio = document.getElementById('otherReasonRadio');
            if (radio.checked) {
                container.classList.remove('hidden');
                document.getElementById('customReasonInput').focus();
            } else {
                container.classList.add('hidden');
            }
        }
        document.getElementById('rejectForm').addEventListener('submit', function() {
            const radio = document.getElementById('otherReasonRadio');
            const input = document.getElementById('customReasonInput');
            if (radio.checked && input.value.trim()) radio.value = input.value.trim();
        });

        // ESC to close
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closeRejectModal(); closeReviewModal(); }
        });

        // AJAX Tabs
        let currentStatus = '{{ request('status') ?? '' }}';
        function bindAjaxTabs() {
            document.querySelectorAll('.ajax-tab').forEach(tab => {
                tab.onclick = function() {
                    currentStatus = this.dataset.status;
                    loadPosts();
                    updateTabStyles(currentStatus);
                };
            });
        }
        function updateTabStyles(active) {
            document.querySelectorAll('.ajax-tab').forEach(tab => {
                const s = tab.dataset.status;
                const isActive = s === active;
                tab.className = 'ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all';
                if (isActive) {
                    if (s === '') tab.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                    else if (s === 'pending') tab.classList.add('bg-yellow-500', 'text-white', 'shadow-md');
                    else if (s === 'published') tab.classList.add('bg-green-500', 'text-white', 'shadow-md');
                    else if (s === 'hidden') tab.classList.add('bg-gray-500', 'text-white', 'shadow-md');
                    else if (s === 'rejected') tab.classList.add('bg-red-500', 'text-white', 'shadow-md');
                    else if (s === 'pending_delete') tab.classList.add('bg-orange-500', 'text-white', 'shadow-md');
                } else {
                    tab.classList.add('bg-white', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-300', 'hover:bg-gray-100', 'dark:hover:bg-slate-500', 'border', 'border-gray-200', 'dark:border-slate-500');
                }
            });
        }
        function loadPosts() {
            const container = document.getElementById('posts-container');
            const url = new URL(window.location.href);
            url.searchParams.delete('page');
            if (currentStatus) url.searchParams.set('status', currentStatus);
            else url.searchParams.delete('status');
            container.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const nc = doc.getElementById('posts-container');
                    if (nc) container.innerHTML = nc.innerHTML;
                    history.pushState({}, '', url);
                    bindAjaxTabs();
                })
                .finally(() => container.style.opacity = '1');
        }
        bindAjaxTabs();
    </script>
@endsection