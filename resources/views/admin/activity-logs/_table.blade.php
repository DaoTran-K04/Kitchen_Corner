<table class="w-full text-left border-collapse">
    <thead
        class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
        <tr>
            <th class="px-4 py-3 w-28 whitespace-nowrap">Thời gian</th>
            <th class="px-4 py-3 w-48">Admin</th>
            <th class="px-4 py-3 w-24 whitespace-nowrap">Hành động</th>
            <th class="px-4 py-3 col-description">Mô tả</th>
            <th class="px-4 py-3 w-28 whitespace-nowrap">IP</th>
            <th class="px-4 py-3 w-16 text-center"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
        @forelse($logs as $log)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition">
                <td class="px-4 py-3 whitespace-nowrap align-top">
                    <div class="text-sm text-gray-800 dark:text-white font-medium">{{ $log->created_at->format('d/m/Y') }}
                    </div>
                    <div class="text-xs text-gray-400 dark:text-slate-500 italic">{{ $log->created_at->format('H:i:s') }}
                    </div>
                </td>
                <td class="px-4 py-3 align-top">
                    <div class="flex items-center gap-2">
                        <img src="{{ $log->admin->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($log->admin->name) }}"
                            class="w-8 h-8 rounded-full border dark:border-slate-600 object-cover">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $log->admin->name }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-slate-500 truncate">{{ $log->admin->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 align-top">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold whitespace-nowrap {{ $log->action_color }}">
                        <i class="fas {{ $log->action_icon }}"></i>{{ ucfirst($log->action) }}
                    </span>
                </td>
                <td class="px-4 py-3 align-top col-description">
                    <div class="text-sm text-gray-700 dark:text-slate-300 line-clamp-1" title="{{ $log->description }}">
                        {{ $log->description }}
                    </div>
                    @if($log->model_type)
                        <div class="text-xs text-gray-400 dark:text-slate-500 italic mt-0.5">
                            {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                        </div>
                    @endif
                </td>
                <td class="px-4 py-3 align-top">
                    <span
                        class="text-xs text-gray-500 dark:text-slate-400 font-mono bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded">
                        {{ $log->ip_address }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center align-top">
                    <a href="{{ route('admin.activity-logs.show', $log) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition opacity-0 group-hover:opacity-100">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">
                    <i class="fas fa-search text-4xl mb-3"></i>
                    <p>Không tìm thấy nhật ký nào</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>