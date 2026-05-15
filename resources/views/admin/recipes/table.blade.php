<table class="w-full text-left">
    <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800 border-b dark:border-slate-700">
        <tr>
            <th class="px-5 py-3 w-12 text-center">#</th>
            <th class="px-5 py-3">Ảnh</th>
            <th class="px-5 py-3">Công thức</th>
            <th class="px-5 py-3 text-center">Đánh giá</th>
            <th class="px-5 py-3 text-center">Trạng thái</th>
            <th class="px-5 py-3 text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
        @forelse($recipes as $index => $recipe)
        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition">
            <td class="px-5 py-3 text-center text-gray-400 dark:text-slate-500 text-sm">
                {{ ($recipes->currentPage() - 1) * $recipes->perPage() + $index + 1 }}
            </td>
            <td class="px-5 py-3">
                @php
                    $recipeImg = trim($recipe->image);
                    $displayImg = $recipeImg 
                        ? (str_starts_with($recipeImg, 'http') ? $recipeImg : asset('storage/' . $recipeImg)) 
                        : 'https://images.unsplash.com/photo-1495195129352-aed325a55b65?w=200';
                @endphp
                <img src="{{ $displayImg }}" 
                     alt="{{ $recipe->title }}" 
                     class="w-14 h-14 rounded-xl object-cover border-2 border-white dark:border-slate-600 shadow-md transform group-hover:scale-105 transition-transform duration-300">
            </td>
            <td class="px-5 py-3">
                <div class="font-semibold text-gray-800 dark:text-white">{{ $recipe->title }}</div>
                <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 flex flex-wrap gap-2">
                    <span><i class="fas fa-user mr-1"></i>{{ $recipe->user->name ?? 'N/A' }}</span> |
                    <span><i class="fas fa-tag mr-1"></i>{{ $recipe->category->name ?? 'N/A' }}</span>
                </div>
            </td>
            <td class="px-5 py-3 text-center whitespace-nowrap">
                <div class="flex flex-col items-center gap-1">
                    <span class="text-xs text-blue-600 font-bold bg-blue-50 px-2 rounded-full"><i class="fas fa-eye mr-1"></i>{{ number_format($recipe->view_count) }}</span>
                    <span class="text-xs text-red-600 font-bold bg-red-50 px-2 rounded-full"><i class="fas fa-heart mr-1"></i>{{ number_format($recipe->likes_count) }}</span>
                </div>
            </td>
            <td class="px-5 py-3 text-center">
                @if($recipe->status == 'published')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700">
                        <i class="fas fa-check-circle mr-1"></i>Đã xuất bản
                    </span>
                @elseif($recipe->status == 'pending')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-700">
                        <i class="fas fa-clock mr-1"></i>Chờ duyệt
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700">
                        <i class="fas fa-file-alt mr-1"></i>Nháp
                    </span>
                @endif
                @if($recipe->is_featured)
                <div class="mt-1">
                    <span class="text-[10px] bg-yellow-400 text-white px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wider"><i class="fas fa-star mr-1"></i>Nổi bật</span>
                </div>
                @endif
            </td>
            <td class="px-5 py-3 text-center">
                <div class="flex justify-center items-center gap-2">
                    <a href="{{ route('admin.recipes.show', $recipe) }}" class="w-8 h-8 rounded-full bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Xem chi tiết">
                        <i class="fas fa-search-plus text-xs"></i>
                    </a>
                    
                    @if($recipe->status !== 'published')
                    <form action="{{ route('admin.recipes.approve', $recipe) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-full bg-green-50 dark:bg-slate-700 text-green-600 dark:text-green-400 flex items-center justify-center hover:bg-green-600 hover:text-white transition" title="Duyệt bài">
                            <i class="fas fa-check text-xs"></i>
                        </button>
                    </form>
                    @endif

                    @if($recipe->status !== 'draft')
                    <button type="button" onclick="openRejectModal('{{ route('admin.recipes.reject', $recipe) }}')" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-gray-600 hover:text-white transition" title="Từ chối">
                        <i class="fas fa-ban text-xs"></i>
                    </button>
                    @endif

                    <form action="{{ route('admin.recipes.feature', $recipe) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-full bg-yellow-50 dark:bg-slate-700 text-yellow-600 dark:text-yellow-400 flex items-center justify-center hover:bg-yellow-600 hover:text-white transition" title="Đặt nổi bật">
                            <i class="fas fa-star text-xs"></i>
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST"
                        class="inline confirm-submit"
                        data-confirm="Bạn có chắc chắn muốn xóa công thức này?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-full bg-red-50 dark:bg-slate-700 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-600 hover:text-white transition" title="Xóa">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-5 py-20 text-center text-gray-400">
                <div class="flex flex-col items-center">
                    <i class="fas fa-box-open text-5xl mb-4 opacity-20"></i>
                    <p>Không tìm thấy công thức nào.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($recipes->hasPages())
<div class="p-5 border-t dark:border-slate-700" id="pagination-container">
    {{ $recipes->links() }}
</div>
@endif
