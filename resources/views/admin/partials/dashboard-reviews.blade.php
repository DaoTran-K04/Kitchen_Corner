<div
    class="p-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-700">
    <h4 class="font-bold text-gray-800 dark:text-slate-200">
        <i class="fas fa-utensils text-[#9b2226] mr-2"></i>Công Thức Tháng {{ $selectedMonth }}/{{ $selectedYear }}
    </h4>
    <span
        class="text-xs bg-red-100 dark:bg-red-900/50 text-[#9b2226] dark:text-red-300 px-2 py-1 rounded-full font-bold">{{ $monthlyReviewsList->total() }}
        công thức</span>
</div>
@if($monthlyReviewsList->count() > 0)
    <table class="w-full text-left text-sm">
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
            @foreach($monthlyReviewsList as $review)
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800 dark:text-slate-200 line-clamp-1">
                            {{ $review->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Bởi: {{ $review->user->name ?? 'N/A' }} •
                            {{ $review->created_at->format('d/m') }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <span
                            class="inline-block px-2 py-1 rounded text-xs font-bold whitespace-nowrap {{ $review->status == 'published' ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300' }}">
                            {{ $review->status == 'published' ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($monthlyReviewsList->hasPages())
        <div class="p-3 border-t border-gray-100 dark:border-slate-700 text-xs">
            {{ $monthlyReviewsList->links('vendor.pagination.dashboard') }}
        </div>
    @endif
@else
    <div class="flex-1 flex flex-col items-center justify-center p-8 text-gray-400 dark:text-slate-500">
        <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
        <p>Không có công thức nào trong tháng này.</p>
    </div>
@endif
