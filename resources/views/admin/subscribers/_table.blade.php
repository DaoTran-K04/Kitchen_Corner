<table class="w-full text-left border-collapse">
    <thead
        class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
        <tr>
            <th class="px-4 py-3 w-12 text-center">#</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3 text-center w-28">Trạng thái</th>
            <th class="px-4 py-3 text-center w-36">Ngày đăng ký</th>
            <th class="px-4 py-3 text-center w-24">Thao tác</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
        @forelse($subscribers as $index => $subscriber)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition">
                <td class="px-5 py-4 text-center text-gray-400 dark:text-slate-500 text-sm">
                    {{ ($subscribers->currentPage() - 1) * $subscribers->perPage() + $index + 1 }}
                </td>
                <td class="px-5 py-4">
                    <span class="font-medium text-gray-800 dark:text-white {{ !$subscriber->is_active ? 'line-through opacity-60' : '' }}">
                        {{ $subscriber->email }}
                    </span>
                </td>
                <td class="px-4 py-4 text-center">
                    @if($subscriber->is_active)
                        <span
                            class="inline-flex items-center px-2.5 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-check mr-1"></i>Active
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 bg-gray-100 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-ban mr-1"></i>Inactive
                        </span>
                    @endif
                </td>
                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-slate-400 italic">
                    {{ $subscriber->subscribed_at ? $subscriber->subscribed_at->format('d/m/Y H:i') : $subscriber->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Toggle Active --}}
                        <form action="{{ route('admin.subscribers.toggle-active', $subscriber->id) }}" method="POST"
                            onsubmit="return confirm('{{ $subscriber->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }} subscriber này?');">
                            @csrf
                            @if($subscriber->is_active)
                                <button
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 hover:bg-orange-500 dark:hover:bg-orange-600 hover:text-white transition opacity-0 group-hover:opacity-100"
                                    title="Vô hiệu hóa">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            @else
                                <button
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition"
                                    title="Kích hoạt lại">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            @endif
                        </form>
                        {{-- Delete --}}
                        <form action="{{ route('admin.subscribers.destroy', $subscriber->id) }}" method="POST"
                            onsubmit="return confirm('Xóa subscriber {{ $subscriber->email }}?');">
                            @csrf
                            @method('DELETE')
                            <button
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition opacity-0 group-hover:opacity-100"
                                title="Xóa">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-12 text-gray-400 dark:text-slate-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>Chưa có subscriber nào</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
