@extends('layouts.admin')
@section('title', 'Chi tiết Nhật Ký #' . $activityLog->id)
@section('header', 'Chi tiết Nhật Ký')

@section('content')
    {{-- Back button --}}
    <div class="mb-6">
        <a href="{{ route('admin.activity-logs.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition font-medium text-sm">
            <i class="fas fa-arrow-left"></i>Quay lại Nhật ký
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Action Info Card --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>Thông tin hành động
                    </h3>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold {{ $activityLog->action_color }}">
                        <i class="fas {{ $activityLog->action_icon }}"></i>{{ ucfirst($activityLog->action) }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Mô tả --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Mô
                                tả</label>
                            <p class="text-gray-800 dark:text-white bg-gray-50 dark:bg-slate-700 p-4 rounded-lg">
                                {{ $activityLog->description }}</p>
                        </div>
                        {{-- Thời gian --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Thời
                                gian</label>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-lg">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 dark:text-white">
                                        {{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400 italic">
                                        {{ $activityLog->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- Đối tượng --}}
                        @if($activityLog->model_type)
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Đối
                                    tượng</label>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 flex items-center justify-center bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 rounded-lg">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-white">
                                            {{ class_basename($activityLog->model_type) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">ID: #{{ $activityLog->model_id }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- IP Address --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Địa chỉ
                                IP</label>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 flex items-center justify-center bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 rounded-lg">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <span
                                    class="font-mono text-sm bg-gray-100 dark:bg-slate-700 px-3 py-1.5 rounded-lg text-gray-800 dark:text-white">{{ $activityLog->ip_address ?? 'N/A' }}</span>
                            </div>
                        </div>
                        {{-- User Agent --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Trình
                                duyệt</label>
                            <p
                                class="text-sm text-gray-600 dark:text-slate-400 bg-gray-50 dark:bg-slate-700 p-3 rounded-lg break-all">
                                {{ $activityLog->user_agent ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Restore button --}}
                    @if($activityLog->action === 'delete' && $activityLog->old_values)
                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-700">
                            <form method="POST" action="{{ route('admin.activity-logs.restore', $activityLog) }}"
                                class="confirm-submit"
                                data-confirm="Bạn có chắc muốn khôi phục mục này?"
                                data-confirm-type="info">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg shadow-md hover:bg-green-700 transition-all">
                                    <i class="fas fa-undo"></i>Khôi phục mục này
                                </button>
                            </form>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-3 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i>Hệ thống sẽ cố gắng khôi phục dữ liệu từ log đã lưu.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Old Values --}}
            @if($activityLog->old_values)
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                    <div
                        class="p-4 border-b border-gray-100 dark:border-slate-700 bg-red-50 dark:bg-red-900/20 flex items-center gap-2">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 rounded-lg">
                            <i class="fas fa-minus"></i>
                        </div>
                        <h3 class="font-bold text-red-700 dark:text-red-300">Giá trị cũ (Trước)</h3>
                    </div>
                    <div class="p-4">
                        <pre
                            class="bg-slate-900 text-green-400 p-4 rounded-xl text-sm overflow-x-auto font-mono leading-relaxed">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

            {{-- New Values --}}
            @if($activityLog->new_values)
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                    <div
                        class="p-4 border-b border-gray-100 dark:border-slate-700 bg-green-50 dark:bg-green-900/20 flex items-center gap-2">
                        <div
                            class="w-8 h-8 flex items-center justify-center bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-lg">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3 class="font-bold text-green-700 dark:text-green-300">Giá trị mới (Sau)</h3>
                    </div>
                    <div class="p-4">
                        <pre
                            class="bg-slate-900 text-green-400 p-4 rounded-xl text-sm overflow-x-auto font-mono leading-relaxed">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Admin Info --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user-shield text-blue-500"></i>Thực hiện bởi
                    </h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="relative inline-block mb-4">
                            <img src="{{ $activityLog->admin->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($activityLog->admin->name) . '&size=120' }}"
                                class="w-20 h-20 rounded-full border-4 border-blue-100 dark:border-blue-900/40 shadow-lg">
                            <span
                                class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </span>
                        </div>
                        <h4 class="font-bold text-gray-800 dark:text-white text-lg">{{ $activityLog->admin->name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-slate-400">{{ $activityLog->admin->email }}</p>
                        <div class="mt-4">
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                                <i class="fas fa-crown"></i>Administrator
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activities --}}
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                    <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2 text-sm">
                        <i class="fas fa-history text-gray-500"></i>Hoạt động gần đây
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-slate-700 max-h-[320px] overflow-y-auto">
                    @php
                        $recentLogs = \App\Models\AdminActivityLog::where('admin_id', $activityLog->admin_id)
                            ->where('id', '!=', $activityLog->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($recentLogs as $recentLog)
                        <a href="{{ route('admin.activity-logs.show', $recentLog) }}"
                            class="flex items-center gap-3 p-4 hover:bg-gray-50 dark:hover:bg-slate-700 transition group">
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs {{ $recentLog->action_color }}">
                                <i class="fas {{ $recentLog->action_icon }}"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm text-gray-700 dark:text-slate-300 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                    {{ Str::limit($recentLog->description, 40) }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500 italic">
                                    {{ $recentLog->created_at->diffForHumans() }}</p>
                            </div>
                            <i
                                class="fas fa-chevron-right text-gray-300 dark:text-slate-600 group-hover:text-blue-500 transition"></i>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-400 dark:text-slate-500">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p class="text-sm">Không có hoạt động khác</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
