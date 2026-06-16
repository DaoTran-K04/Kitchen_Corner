@extends('layouts.admin')
@section('title', 'Báo Cáo Bình Luận')
@section('header', 'Quản Lý Báo Cáo Bình Luận')

@section('content')
    <div id="reports-container"
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">

        {{-- Thanh lọc với AJAX Tabs + Custom Dropdown --}}
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                {{-- AJAX Tabs lọc theo trạng thái --}}
                <div class="flex flex-wrap gap-2" id="status-tabs">
                    <button type="button" data-status=""
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('status') ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-list mr-1"></i> Tất cả
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ !request('status') ? 'bg-white/20' : 'bg-slate-200 dark:bg-slate-500 text-slate-600 dark:text-slate-200' }}"
                            id="count-all">{{ \App\Models\CommentReport::count() }}</span>
                    </button>
                    <button type="button" data-status="pending"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'pending' ? 'bg-yellow-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-clock mr-1"></i> Chờ xử lý
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'pending' ? 'bg-white/20' : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300' }}"
                            id="count-pending">{{ \App\Models\CommentReport::where('status', 'pending')->count() }}</span>
                    </button>
                    <button type="button" data-status="approved"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'approved' ? 'bg-green-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-check mr-1"></i> Đã chấp thuận
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'approved' ? 'bg-white/20' : 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300' }}"
                            id="count-approved">{{ \App\Models\CommentReport::where('status', 'approved')->count() }}</span>
                    </button>
                    <button type="button" data-status="rejected"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'rejected' ? 'bg-red-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-times mr-1"></i> Đã từ chối
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'rejected' ? 'bg-white/20' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300' }}"
                            id="count-rejected">{{ \App\Models\CommentReport::where('status', 'rejected')->count() }}</span>
                    </button>
                </div>

                {{-- Custom Dropdown lọc theo lý do (text only, no emoji) --}}
                <div class="custom-dropdown" id="reason-dropdown">
                    <div class="custom-dropdown-trigger bg-white dark:bg-slate-600 border border-gray-200 dark:border-slate-500 text-gray-700 dark:text-slate-200"
                        onclick="toggleReasonDropdown()">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-filter text-gray-400 dark:text-slate-400"></i>
                            <span
                                id="reason-label">{{ request('reason') ? \App\Models\CommentReport::getReasonLabels()[request('reason')] ?? 'Tất cả lý do' : 'Tất cả lý do' }}</span>
                        </span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <div class="custom-dropdown-menu min-w-[180px]" style="right: 0; left: auto;">
                        <div class="custom-dropdown-menu-inner">
                            <div class="custom-dropdown-item {{ !request('reason') ? 'active' : '' }}" data-reason=""
                                onclick="selectReason(this)">
                                Tất cả lý do
                            </div>
                            <div class="custom-dropdown-item {{ request('reason') == 'spam' ? 'active' : '' }}"
                                data-reason="spam" onclick="selectReason(this)">
                                Spam
                            </div>
                            <div class="custom-dropdown-item {{ request('reason') == 'offensive' ? 'active' : '' }}"
                                data-reason="offensive" onclick="selectReason(this)">
                                Ngôn từ xúc phạm
                            </div>
                            <div class="custom-dropdown-item {{ request('reason') == 'harassment' ? 'active' : '' }}"
                                data-reason="harassment" onclick="selectReason(this)">
                                Quấy rối
                            </div>
                            <div class="custom-dropdown-item {{ request('reason') == 'inappropriate' ? 'active' : '' }}"
                                data-reason="inappropriate" onclick="selectReason(this)">
                                Không phù hợp
                            </div>
                            <div class="custom-dropdown-item {{ request('reason') == 'other' ? 'active' : '' }}"
                                data-reason="other" onclick="selectReason(this)">
                                Khác
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bảng danh công thức --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3">Người báo cáo</th>
                        <th class="px-6 py-3">Bình luận bị báo cáo</th>
                        <th class="px-6 py-3">Lý do</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-center">Ngày tạo</th>
                        <th class="px-6 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($reports as $report)
                        <tr
                            class="hover:bg-gray-50 dark:hover:bg-slate-700 {{ $report->status == 'pending' ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '' }} transition-colors">

                            {{-- Người báo cáo --}}
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" src="{{ optional($report->user)->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode(optional($report->user)->name ?? 'Ẩn danh') }}"
                                        class="w-8 h-8 rounded-full border dark:border-slate-600">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ optional($report->user)->name ?? 'Ẩn danh' }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ optional($report->user)->email ?? 'Không có' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Bình luận bị báo cáo --}}
                            <td class="px-6 py-4 align-top max-w-xs">
                                @if($report->comment)
                                    <div class="mb-2">
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">
                                            <i class="fas fa-user mr-1"></i> {{ optional($report->comment->user)->name ?? 'Người dùng ẩn' }}
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-slate-300 line-clamp-2">
                                            {{ Str::limit($report->comment->content, 80) }}
                                        </p>
                                    </div>
                                    <button type="button" onclick="showCommentModal({{ $report->id }})"
                                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        <i class="fas fa-eye mr-1"></i> Xem đầy đủ
                                    </button>

                                    {{-- Hidden content for modal --}}
                                    <div id="comment-data-{{ $report->id }}" class="hidden">
                                        <div class="comment-author">{{ optional($report->comment->user)->name ?? 'Người dùng ẩn' }}</div>
                                        <div class="comment-content">{{ $report->comment->content }}</div>
                                        <div class="comment-date">{{ $report->comment->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="reporter-name">{{ optional($report->user)->name ?? 'Ẩn danh' }}</div>
                                        <div class="report-reason">{{ $report->reason_label }}</div>
                                        <div class="report-description">{{ $report->description ?? 'Không có mô tả' }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-slate-500 italic text-sm">
                                        <i class="fas fa-trash mr-1"></i> Bình luận đã bị xóa
                                    </span>
                                @endif
                            </td>

                            {{-- Lý do --}}
                            <td class="px-6 py-4 align-top">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300">
                                    {{ $report->reason_label }}
                                </span>
                                @if($report->description)
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 line-clamp-1">
                                        {{ Str::limit($report->description, 40) }}
                                    </p>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td class="px-6 py-4 text-center align-top">
                                @if($report->status == 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300">
                                        <i class="fas fa-clock mr-1"></i> Chờ xử lý
                                    </span>
                                @elseif($report->status == 'approved')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">
                                        <i class="fas fa-check mr-1"></i> Đã chấp thuận
                                    </span>
                                @elseif($report->status == 'rejected')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">
                                        <i class="fas fa-times mr-1"></i> Đã từ chối
                                    </span>
                                @endif
                            </td>

                            {{-- Ngày tạo --}}
                            <td class="px-6 py-4 text-center align-top">
                                <span class="text-sm text-gray-600 dark:text-slate-300">
                                    {{ $report->created_at->format('d/m/Y') }}
                                </span>
                                <p class="text-xs text-gray-400 dark:text-slate-500">
                                    {{ $report->created_at->format('H:i') }}
                                </p>
                            </td>

                            {{-- Hành động --}}
                            <td class="px-6 py-4 text-center align-top">
                                <div class="flex justify-center gap-1.5">
                                    @if($report->status == 'pending' && $report->comment)
                                        {{-- Chấp thuận --}}
                                        <button type="button"
                                            onclick="openApproveModal({{ $report->id }}, '{{ addslashes(optional(optional($report->comment)->user)->name ?? 'Người dùng') }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition"
                                            title="Chấp thuận">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                        {{-- Từ chối --}}
                                        <button type="button" onclick="openRejectModal({{ $report->id }})"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-400 hover:bg-gray-500 hover:text-white transition"
                                            title="Từ chối">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    @elseif($report->status != 'pending')
                                        @if($report->resolvedBy)
                                            <span class="text-xs text-gray-500 dark:text-slate-400 italic">
                                                <i class="fas fa-user-check mr-1"></i>{{ $report->resolvedBy->name }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-400 dark:text-slate-500 italic">{{ $report->resolved_at->format('d/m H:i') }}</span>
                                        @endif
                                        <form action="{{ route('admin.comment-reports.destroy', $report->id) }}" method="POST"
                                            class="confirm-submit"
                                            data-confirm="Xóa báo cáo này?">
                                            @csrf @method('DELETE')
                                            <button
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition"
                                                title="Xóa">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Bình luận đã xóa</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400 dark:text-slate-500">
                                    <i class="fas fa-flag text-4xl mb-3"></i>
                                    <p class="text-sm">Chưa có báo cáo nào.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="p-4 border-t dark:border-slate-700">
            {{ $reports->links('vendor.pagination.admin') }}
        </div>
    </div>

    @include('admin.comment-reports.modals')

    <script>
        // Modal Xem Chi Tiết
        function showCommentModal(reportId) {
            const container = document.getElementById('comment-data-' + reportId);
            if (!container) return;

            document.getElementById('modal-comment-author').textContent = container.querySelector('.comment-author').textContent;
            document.getElementById('modal-comment-content').textContent = container.querySelector('.comment-content').textContent;
            document.getElementById('modal-comment-date').textContent = container.querySelector('.comment-date').textContent;
            document.getElementById('modal-reporter-name').textContent = container.querySelector('.reporter-name').textContent;
            document.getElementById('modal-report-reason').textContent = container.querySelector('.report-reason').textContent;
            document.getElementById('modal-report-description').textContent = container.querySelector('.report-description').textContent;

            document.getElementById('commentModal').classList.remove('hidden');
            document.getElementById('commentModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeCommentModal(event) {
            if (event && event.target !== event.currentTarget) return;
            document.getElementById('commentModal').classList.add('hidden');
            document.getElementById('commentModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Modal Chấp Thuận
        function openApproveModal(reportId, userName) {
            document.getElementById('approveForm').action = `/admin/comment-reports/${reportId}/approve`;
            document.getElementById('approveUserName').textContent = userName;
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Từ Chối
        function openRejectModal(reportId) {
            document.getElementById('rejectForm').action = `/admin/comment-reports/${reportId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ESC to close
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCommentModal();
                closeApproveModal();
                closeRejectModal();
                closeReasonDropdown();
            }
        });

        // ========== AJAX FILTER SYSTEM ==========
        let currentStatus = '{{ request('status') ?? '' }}';
        let currentReason = '{{ request('reason') ?? '' }}';

        // AJAX Tab Click Handler
        document.querySelectorAll('.ajax-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const status = this.dataset.status;
                currentStatus = status;
                loadReports();
                updateTabStyles(status);
            });
        });

        // Update tab active styles
        function updateTabStyles(activeStatus) {
            document.querySelectorAll('.ajax-tab').forEach(tab => {
                const status = tab.dataset.status;
                const isActive = status === activeStatus;

                // Remove all status classes first
                tab.classList.remove('bg-blue-600', 'bg-yellow-500', 'bg-green-500', 'bg-red-500', 'text-white', 'shadow-md');
                tab.classList.remove('bg-white', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-300', 'border', 'border-gray-200', 'dark:border-slate-500');

                if (isActive) {
                    if (status === '') tab.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                    else if (status === 'pending') tab.classList.add('bg-yellow-500', 'text-white', 'shadow-md');
                    else if (status === 'approved') tab.classList.add('bg-green-500', 'text-white', 'shadow-md');
                    else if (status === 'rejected') tab.classList.add('bg-red-500', 'text-white', 'shadow-md');
                } else {
                    tab.classList.add('bg-white', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-300', 'border', 'border-gray-200', 'dark:border-slate-500');
                }
            });
        }

        // Custom Dropdown Functions
        function toggleReasonDropdown() {
            const dropdown = document.getElementById('reason-dropdown');
            dropdown.classList.toggle('open');
        }

        function closeReasonDropdown() {
            const dropdown = document.getElementById('reason-dropdown');
            dropdown.classList.remove('open');
        }

        function selectReason(element) {
            const reason = element.dataset.reason;
            const label = element.textContent.trim();

            // Update trigger display
            document.getElementById('reason-label').textContent = label;

            // Update active state
            document.querySelectorAll('#reason-dropdown .custom-dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');

            // Close dropdown and filter
            closeReasonDropdown();
            currentReason = reason;
            loadReports();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('reason-dropdown');
            if (!dropdown.contains(e.target)) {
                closeReasonDropdown();
            }
        });

        // Reset all filters (when clicking "Tất cả" tab)
        function resetFilters() {
            currentReason = '';
            document.getElementById('reason-label').textContent = 'Tất cả lý do';
            document.querySelectorAll('#reason-dropdown .custom-dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector('#reason-dropdown .custom-dropdown-item[data-reason=""]').classList.add('active');
        }

        // AJAX Load Reports
        function loadReports() {
            const container = document.getElementById('reports-container');
            const url = new URL('{{ route('admin.comment-reports.index') }}');

            if (currentStatus) url.searchParams.set('status', currentStatus);
            if (currentReason) url.searchParams.set('reason', currentReason);

            // Update browser URL
            window.history.pushState({}, '', url.toString());

            // Show loading
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('reports-container');

                    if (newContainer) {
                        container.innerHTML = newContainer.innerHTML;
                    }

                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';

                    // Re-bind event listeners
                    bindAjaxTabs();
                })
                .catch(error => {
                    console.error('Error loading reports:', error);
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    // Fallback to page reload
                    window.location.href = url.toString();
                });
        }

        // Re-bind AJAX tabs after content update
        function bindAjaxTabs() {
            document.querySelectorAll('.ajax-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    const status = this.dataset.status;
                    currentStatus = status;
                    loadReports();
                    updateTabStyles(status);
                });
            });
        }

        // Handle browser back/forward
        window.addEventListener('popstate', function (e) {
            const url = new URL(window.location.href);
            currentStatus = url.searchParams.get('status') || '';
            currentReason = url.searchParams.get('reason') || '';
            loadReports();
            updateTabStyles(currentStatus);
        });
    </script>
@endsection
