@extends('layouts.admin')

@section('title', 'AI Content Safety Center')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trạm kiểm duyệt AI & Báo cáo</h1>
        <p class="text-gray-500 text-sm mt-1">Nơi tập trung theo dõi nhật ký chặn nội dung của AI/Rule, xử lý các báo cáo vi phạm từ người dùng.</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden border border-gray-100 dark:border-slate-700">
    <div class="p-4 border-b border-gray-100 dark:border-slate-700 flex gap-2">
        <button class="px-4 py-2 bg-blue-500 text-white rounded-md text-sm font-medium">Lịch sử chặn (AI & Rule)</button>
        <a href="{{ route('admin.comment-reports.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 rounded-md text-sm font-medium hover:bg-gray-200 dark:hover:bg-slate-600">Chờ xử lý (User Reports)</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                    <th class="px-4 py-3 font-medium">THỜI GIAN</th>
                    <th class="px-4 py-3 font-medium">NGƯỜI DÙNG</th>
                    <th class="px-4 py-3 font-medium">NGUỒN / MỨC ĐỘ</th>
                    <th class="px-4 py-3 font-medium w-1/3">NỘI DUNG CHẶN</th>
                    <th class="px-4 py-3 font-medium">TỪ KHÓA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $log->user ? $log->user->name : 'Khách' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->ip_address }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 text-xs rounded font-medium {{ $log->source == 'gemini_safety' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                {{ $log->source == 'gemini_safety' ? 'AI_ASSISTANT' : 'RULE_BASED' }}
                            </span>
                            @if($log->severity == 'HIGH')
                                <span class="px-2 py-0.5 text-xs rounded font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">HIGH</span>
                            @elseif($log->severity == 'MEDIUM')
                                <span class="px-2 py-0.5 text-xs rounded font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">MEDIUM</span>
                            @else
                                <span class="px-2 py-0.5 text-xs rounded font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $log->severity ?: 'N/A' }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->intent }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-gray-900 dark:text-gray-300 italic whitespace-normal break-words">
                            "{{ $log->blocked_content }}"
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ $log->excerpt }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-shield-alt text-4xl mb-3 text-gray-300 dark:text-slate-600"></i>
                            <p>Chưa có dữ liệu vi phạm nào.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
