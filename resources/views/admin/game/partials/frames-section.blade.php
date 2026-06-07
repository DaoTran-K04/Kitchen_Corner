{{-- AVATAR FRAMES SECTION --}}
<div id="section-frames" class="tab-content hidden">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        {{-- Header with Add button --}}
        <div
            class="p-4 border-b border-purple-100 dark:border-slate-700 bg-purple-50 dark:bg-slate-700 flex flex-wrap justify-between items-center gap-3">
            <span class="font-semibold text-gray-700 dark:text-white flex items-center gap-2">
                <i class="fas fa-image text-purple-500"></i>
                Tất cả khung hoạt ảnh
                <span
                    class="bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-300 px-2 py-0.5 rounded-full text-xs font-bold">{{ $frames->total() }}</span>
            </span>
            <button type="button" onclick="openFrameModal()"
                class="px-4 py-2 bg-purple-500 text-white text-sm font-semibold rounded-lg hover:bg-purple-600 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm khung
            </button>
        </div>

        {{-- Search & Filter Toolbar --}}
        <div
            class="p-3 border-b border-gray-100 dark:border-slate-700 bg-purple-50/30 dark:bg-slate-700/50 flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-sm"></i>
                <input type="text" id="frame-search" placeholder="Tìm kiếm khung..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                    oninput="filterFrames()">
            </div>
            {{-- Filter by Status --}}
            <select id="frame-filter" onchange="filterFrames()"
                class="px-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer">
                <option value="all">Tất cả trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="inactive">Đã tắt</option>
            </select>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 p-4" id="frames-list">
            @forelse($frames as $frame)
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-3 border border-gray-200 dark:border-slate-600 hover:shadow-lg transition-all group relative frame-item"
                    data-frame-id="{{ $frame->id }}" data-name="{{ strtolower($frame->name) }}"
                    data-status="{{ $frame->is_active ? 'active' : 'inactive' }}">
                    {{-- Preview Image --}}
                    <div class="h-20 w-20 mx-auto mb-3 flex items-center justify-center">
                        <img loading="lazy" src="{{ (Str::startsWith($frame->frame_image, 'http') || Str::startsWith($frame->frame_image, 'data:')) ? $frame->frame_image : asset('storage/' . $frame->frame_image) }}"
                            alt="{{ $frame->name }}" class="max-h-full max-w-full object-contain"
                            referrerpolicy="no-referrer">
                    </div>
                    {{-- Info --}}
                    <div class="text-center">
                        <p class="font-semibold text-gray-800 dark:text-white text-sm truncate">{{ $frame->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mb-1">{{ $frame->user_avatar_frames_count }}
                            người dùng</p>
                        @if($frame->is_active)
                            <span
                                class="inline-block bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 px-2 py-0.5 rounded text-xs font-bold">Hoạt
                                động</span>
                        @else
                            <span
                                class="inline-block bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 px-2 py-0.5 rounded text-xs font-bold">Tắt</span>
                        @endif
                    </div>
                    {{-- Actions --}}
                    <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="openFrameModal({{ $frame->id }})"
                            class="w-7 h-7 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center hover:bg-purple-200 transition"
                            title="Sửa">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button onclick="deleteFrame({{ $frame->id }}, '{{ $frame->name }}')"
                            class="w-7 h-7 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 transition"
                            title="Xóa">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full px-6 py-12 text-center text-gray-500 dark:text-slate-400" id="frames-empty">
                    <i class="fas fa-image text-5xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-lg font-medium">Chưa có khung hoạt ảnh nào</p>
                </div>
            @endforelse
        </div>
        @if($frames->hasPages())
            <div class="p-4 border-t dark:border-slate-700">
                {{ $frames->appends(['tab' => 'frames'])->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>
</div>
