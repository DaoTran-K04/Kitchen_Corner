{{-- BADGES SECTION --}}
<div id="section-badges" class="tab-content hidden">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        {{-- Header with Add button --}}
        <div
            class="p-4 border-b border-emerald-100 dark:border-slate-700 bg-emerald-50 dark:bg-slate-700 flex flex-wrap justify-between items-center gap-3">
            <span class="font-semibold text-gray-700 dark:text-white flex items-center gap-2">
                <i class="fas fa-medal text-emerald-500"></i>
                Tất cả biểu tượng
                <span
                    class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-300 px-2 py-0.5 rounded-full text-xs font-bold">{{ $badges->total() }}</span>
            </span>
            <button type="button" onclick="openBadgeModal()"
                class="px-4 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-lg hover:bg-emerald-600 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm biểu tượng
            </button>
        </div>

        {{-- Search & Filter Toolbar --}}
        <div
            class="p-3 border-b border-gray-100 dark:border-slate-700 bg-emerald-50/30 dark:bg-slate-700/50 flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-sm"></i>
                <input type="text" id="badge-search" placeholder="Tìm kiếm biểu tượng..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                    oninput="filterBadges()">
            </div>
            {{-- Filter by Status --}}
            <select id="badge-filter" onchange="filterBadges()"
                class="px-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer">
                <option value="all">Tất cả trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="inactive">Đã tắt</option>
            </select>
        </div>

        {{-- List --}}
        <div class="divide-y divide-gray-100 dark:divide-slate-700" id="badges-list">
            @forelse($badges as $badge)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition-colors badge-item"
                    data-badge-id="{{ $badge->id }}" data-name="{{ strtolower($badge->name) }}"
                    data-status="{{ $badge->is_active ? 'active' : 'inactive' }}">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($badge->icon && (Str::startsWith($badge->icon, 'http') || Str::startsWith($badge->icon, 'data:')))
                                <img src="{{ $badge->icon }}" alt="{{ $badge->name }}" class="w-10 h-10 object-contain rounded"
                                    referrerpolicy="no-referrer">
                            @else
                                <span class="text-3xl">{{ $badge->icon ?? '🏅' }}</span>
                            @endif
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ $badge->name }}</h4>
                                    @if($badge->is_active)
                                        <span
                                            class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 px-2 py-0.5 rounded text-xs font-bold">Hoạt
                                            động</span>
                                    @else
                                        <span
                                            class="bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 px-2 py-0.5 rounded text-xs font-bold">Tắt</span>
                                    @endif
                                </div>
                                @if($badge->description)
                                    <p class="text-sm text-gray-500 dark:text-slate-400 line-clamp-1">{{ $badge->description }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-4 text-sm mt-1">
                                    <span class="text-gray-500 dark:text-slate-400">
                                        <i class="fas fa-trophy text-purple-500 mr-1"></i>{{ $badge->challenges_count }} thử
                                        thách
                                    </span>
                                    <span class="text-gray-500 dark:text-slate-400">
                                        <i class="fas fa-users text-green-500 mr-1"></i>{{ $badge->user_badges_count }}
                                        người nhận
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openBadgeModal({{ $badge->id }})"
                                class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition"
                                title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteBadge({{ $badge->id }}, '{{ $badge->name }}')"
                                class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition"
                                title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-slate-400" id="badges-empty">
                    <i class="fas fa-medal text-5xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-lg font-medium">Chưa có biểu tượng nào</p>
                </div>
            @endforelse
        </div>
        @if($badges->hasPages())
            <div class="p-4 border-t dark:border-slate-700">
                {{ $badges->appends(['tab' => 'badges'])->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>
</div>
