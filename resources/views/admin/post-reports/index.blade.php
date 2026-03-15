@extends('layouts.admin')
@section('title', 'Báo Cáo Bài Viết')
@section('header', 'Quản Lý Báo Cáo Bài Viết')

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
                            id="count-all">{{ \App\Models\PostReport::count() }}</span>
                    </button>
                    <button type="button" data-status="pending"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'pending' ? 'bg-yellow-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-clock mr-1"></i> Chờ xử lý
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'pending' ? 'bg-white/20' : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300' }}"
                            id="count-pending">{{ \App\Models\PostReport::where('status', 'pending')->count() }}</span>
                    </button>
                    <button type="button" data-status="approved"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'approved' ? 'bg-green-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-check mr-1"></i> Đã chấp thuận
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'approved' ? 'bg-white/20' : 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300' }}"
                            id="count-approved">{{ \App\Models\PostReport::where('status', 'approved')->count() }}</span>
                    </button>
                    <button type="button" data-status="rejected"
                        class="ajax-tab px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') == 'rejected' ? 'bg-red-500 text-white shadow-md' : 'bg-white dark:bg-slate-600 text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-500 border border-gray-200 dark:border-slate-500' }}">
                        <i class="fas fa-times mr-1"></i> Đã từ chối
                        <span
                            class="ml-1 px-1.5 py-0.5 rounded text-xs font-bold {{ request('status') == 'rejected' ? 'bg-white/20' : 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300' }}"
                            id="count-rejected">{{ \App\Models\PostReport::where('status', 'rejected')->count() }}</span>
                    </button>
                </div>

                {{-- Custom Dropdown lọc theo lý do --}}
                <div class="custom-dropdown" id="reason-dropdown">
                    <div class="custom-dropdown-trigger bg-white dark:bg-slate-600 border border-gray-200 dark:border-slate-500 text-gray-700 dark:text-slate-200"
                        onclick="toggleReasonDropdown()">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-filter text-gray-400 dark:text-slate-400"></i>
                            <span
                                id="reason-label">{{ request('reason') ? \App\Models\PostReport::getReasonLabels()[request('reason')] ?? 'Tất cả lý do' : 'Tất cả lý do' }}</span>
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
                            @foreach(\App\Models\PostReport::getReasonLabels() as $key => $label)
                                <div class="custom-dropdown-item {{ request('reason') == $key ? 'active' : '' }}"
                                    data-reason="{{ $key }}" onclick="selectReason(this)">
                                    {{ $label }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3">Người báo cáo</th>
                        <th class="px-6 py-3">Bài viết bị báo cáo</th>
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
                                    <img src="{{ $report->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($report->user->name) }}"
                                        class="w-8 h-8 rounded-full border dark:border-slate-600">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $report->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ $report->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Bài viết bị báo cáo --}}
                            <td class="px-6 py-4 align-top max-w-xs">
                                @if($report->post)
                                    <div class="mb-2">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white line-clamp-2">
                                            {{ Str::limit($report->post->title, 50) }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                            <i class="fas fa-user mr-1"></i> {{ $report->post->user->name ?? 'Người dùng ẩn' }}
                                            @if($report->post->book)
                                                <span class="mx-1">•</span>
                                                <i class="fas fa-book mr-1"></i> {{ Str::limit($report->post->book->title, 20) }}
                                            @endif
                                        </p>
                                    </div>
                                    <button type="button" onclick="showPostModal({{ $report->id }})"
                                        class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        <i class="fas fa-eye mr-1"></i> Xem chi tiết
                                    </button>

                                    {{-- Hidden content for modal --}}
                                    <div id="post-data-{{ $report->id }}" class="hidden">
                                        <div class="post-title">{{ $report->post->title }}</div>
                                        <div class="post-author">{{ $report->post->user->name ?? 'Người dùng ẩn' }}</div>
                                        <div class="post-content">{{ Str::limit($report->post->content, 500) }}</div>
                                        <div class="post-date">{{ $report->post->created_at->format('d/m/Y H:i') }}</div>
                                        <div class="reporter-name">{{ $report->user->name }}</div>
                                        <div class="report-reason">{{ $report->reason_label }}</div>
                                        <div class="report-description">{{ $report->description ?? 'Không có mô tả' }}</div>
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-slate-500 italic text-sm">
                                        <i class="fas fa-trash mr-1"></i> Bài viết đã bị xóa
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
                                    @if($report->status == 'pending' && $report->post)
                                        {{-- Ẩn bài --}}
                                        <button type="button"
                                            onclick="openApproveModal({{ $report->id }}, '{{ addslashes($report->post->title ?? 'Bài viết') }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition"
                                            title="Ẩn bài">
                                            <i class="fas fa-eye-slash text-xs"></i>
                                        </button>
                                        {{-- Xóa bài --}}
                                        <button type="button"
                                            onclick="openDeleteModal({{ $report->id }}, '{{ addslashes($report->post->title ?? 'Bài viết') }}')"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition"
                                            title="Xóa bài">
                                            <i class="fas fa-trash text-xs"></i>
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
                                        @endif
                                        <form action="{{ route('admin.post-reports.destroy', $report->id) }}" method="POST"
                                            onsubmit="return confirm('Xóa báo cáo này?');">
                                            @csrf @method('DELETE')
                                            <button
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition"
                                                title="Xóa">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Bài viết đã xóa</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400 dark:text-slate-500">
                                    <i class="fas fa-flag text-4xl mb-3"></i>
                                    <p class="text-sm">Chưa có báo cáo bài viết nào.</p>
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

    {{-- Modal Xem Chi Tiết Bài Viết --}}
    <div id="postModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4"
        onclick="closePostModal(event)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
            onclick="event.stopPropagation()">
            <div class="flex items-center justify-between p-6 border-b dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-file-alt mr-2 text-blue-500"></i>Chi Tiết Bài Viết
                </h3>
                <button onclick="closePostModal()"
                    class="w-10 h-10 rounded-full bg-gray-100 dark:bg-slate-600 hover:bg-gray-200 dark:hover:bg-slate-500 flex items-center justify-center transition">
                    <i class="fas fa-times text-gray-500 dark:text-slate-300"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto max-h-[60vh]">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-xl p-4">
                    <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Bài viết bị báo cáo
                    </p>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2" id="modal-post-title"></h4>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mb-3">
                        <i class="fas fa-user mr-1"></i> <span id="modal-post-author"></span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar mr-1"></i> <span id="modal-post-date"></span>
                    </p>
                    <div class="text-gray-700 dark:text-slate-300 text-sm leading-relaxed" id="modal-post-content"></div>
                </div>

                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <p class="text-xs text-gray-600 dark:text-slate-400 font-bold mb-2">
                        <i class="fas fa-flag mr-1"></i> Thông tin báo cáo
                    </p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Người báo cáo:</span>
                            <p class="font-medium text-gray-800 dark:text-white" id="modal-reporter-name"></p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Lý do:</span>
                            <p class="font-medium text-orange-600 dark:text-orange-400" id="modal-report-reason"></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-gray-500 dark:text-slate-400 text-sm">Mô tả:</span>
                        <p class="text-gray-700 dark:text-slate-300 text-sm mt-1" id="modal-report-description"></p>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-end">
                <button onclick="closePostModal()"
                    class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium hover:bg-gray-300 dark:hover:bg-slate-500 transition">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Ẩn Bài Viết (Approve) --}}
    <div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeApproveModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md">
                <div class="bg-green-500 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                    <h3 class="font-bold text-lg"><i class="fas fa-eye-slash mr-2"></i>Ẩn Bài Viết</h3>
                    <button onclick="closeApproveModal()" class="text-white/70 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="p-6">
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                            <p class="text-sm text-yellow-800 dark:text-yellow-300">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Bạn đang ẩn bài viết: <strong id="approvePostTitle"></strong>
                                <br><br>
                                <strong>Hành động:</strong> Bài viết sẽ bị <span class="text-orange-600 font-bold">ẩn</span>
                                khỏi trang công khai.
                            </p>
                        </div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-2">Ghi chú (tùy
                            chọn):</label>
                        <textarea name="admin_note" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-green-500 dark:bg-slate-700 dark:text-white text-sm"
                            placeholder="Nhập ghi chú..."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                        <button type="button" onclick="closeApproveModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 transition text-sm font-bold">Hủy</button>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm font-bold">
                            <i class="fas fa-eye-slash mr-1"></i> Xác nhận ẩn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Xóa Bài Viết --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md">
                <div class="bg-red-500 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                    <h3 class="font-bold text-lg"><i class="fas fa-trash mr-2"></i>Xóa Bài Viết</h3>
                    <button onclick="closeDeleteModal()" class="text-white/70 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="deleteForm" method="POST">
                    @csrf
                    <div class="p-6">
                        <div
                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
                            <p class="text-sm text-red-800 dark:text-red-300">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Bạn đang xóa bài viết: <strong id="deletePostTitle"></strong>
                                <br><br>
                                <strong>Cảnh báo:</strong> Bài viết sẽ bị <span class="text-red-600 font-bold">xóa vĩnh
                                    viễn</span>!
                            </p>
                        </div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-2">Ghi chú (tùy
                            chọn):</label>
                        <textarea name="admin_note" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-slate-700 dark:text-white text-sm"
                            placeholder="Nhập ghi chú..."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 transition text-sm font-bold">Hủy</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-bold">
                            <i class="fas fa-trash mr-1"></i> Xác nhận xóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Từ Chối --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRejectModal()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md">
                <div class="bg-gray-500 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
                    <h3 class="font-bold text-lg"><i class="fas fa-ban mr-2"></i>Từ Chối Báo Cáo</h3>
                    <button onclick="closeRejectModal()" class="text-white/70 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="p-6">
                        <p class="text-sm text-gray-600 dark:text-slate-300 mb-4">
                            Bài viết sẽ được giữ nguyên và báo cáo sẽ được đánh dấu là đã từ chối.
                        </p>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-200 mb-2">Lý do từ chối (tùy
                            chọn):</label>
                        <textarea name="admin_note" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-gray-500 dark:bg-slate-700 dark:text-white text-sm"
                            placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-slate-700 rounded-b-xl flex justify-end gap-3">
                        <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-300 transition text-sm font-bold">Hủy</button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-sm font-bold">
                            <i class="fas fa-ban mr-1"></i> Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal Xem Chi Tiết
        function showPostModal(reportId) {
            const container = document.getElementById('post-data-' + reportId);
            if (!container) return;

            document.getElementById('modal-post-title').textContent = container.querySelector('.post-title').textContent;
            document.getElementById('modal-post-author').textContent = container.querySelector('.post-author').textContent;
            document.getElementById('modal-post-content').textContent = container.querySelector('.post-content').textContent;
            document.getElementById('modal-post-date').textContent = container.querySelector('.post-date').textContent;
            document.getElementById('modal-reporter-name').textContent = container.querySelector('.reporter-name').textContent;
            document.getElementById('modal-report-reason').textContent = container.querySelector('.report-reason').textContent;
            document.getElementById('modal-report-description').textContent = container.querySelector('.report-description').textContent;

            document.getElementById('postModal').classList.remove('hidden');
            document.getElementById('postModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePostModal(event) {
            if (event && event.target !== event.currentTarget) return;
            document.getElementById('postModal').classList.add('hidden');
            document.getElementById('postModal').classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Modal Ẩn Bài Viết
        function openApproveModal(reportId, postTitle) {
            document.getElementById('approveForm').action = `/admin/post-reports/${reportId}/approve`;
            document.getElementById('approvePostTitle').textContent = postTitle;
            document.getElementById('approveModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Xóa Bài Viết
        function openDeleteModal(reportId, postTitle) {
            document.getElementById('deleteForm').action = `/admin/post-reports/${reportId}/delete-post`;
            document.getElementById('deletePostTitle').textContent = postTitle;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Modal Từ Chối
        function openRejectModal(reportId) {
            document.getElementById('rejectForm').action = `/admin/post-reports/${reportId}/reject`;
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
                closePostModal();
                closeApproveModal();
                closeDeleteModal();
                closeRejectModal();
                closeReasonDropdown();
            }
        });

        // Custom Dropdown
        function toggleReasonDropdown() {
            document.getElementById('reason-dropdown').classList.toggle('open');
        }

        function closeReasonDropdown() {
            document.getElementById('reason-dropdown').classList.remove('open');
        }

        function selectReason(element) {
            const reason = element.dataset.reason;
            const label = element.textContent.trim();
            document.getElementById('reason-label').textContent = label;
            document.querySelectorAll('#reason-dropdown .custom-dropdown-item').forEach(item => item.classList.remove('active'));
            element.classList.add('active');
            closeReasonDropdown();

            // Redirect with filter
            const url = new URL(window.location.href);
            if (reason) {
                url.searchParams.set('reason', reason);
            } else {
                url.searchParams.delete('reason');
            }
            window.location.href = url.toString();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('reason-dropdown');
            if (!dropdown.contains(e.target)) closeReasonDropdown();
        });

        // AJAX Tab Handler
        document.querySelectorAll('.ajax-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const status = this.dataset.status;
                const url = new URL(window.location.href);
                if (status) {
                    url.searchParams.set('status', status);
                } else {
                    url.searchParams.delete('status');
                }
                window.location.href = url.toString();
            });
        });
    </script>
@endsection