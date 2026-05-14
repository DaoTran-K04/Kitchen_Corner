{{-- CHALLENGES SECTION --}}
<div id="section-challenges" class="tab-content">
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        {{-- Header with Add button --}}
        <div
            class="p-4 border-b border-amber-100 dark:border-slate-700 bg-amber-50 dark:bg-slate-700 flex flex-wrap justify-between items-center gap-3">
            <span class="font-semibold text-gray-700 dark:text-white flex items-center gap-2">
                <i class="fas fa-trophy text-amber-500"></i>
                Tất cả thử thách
                <span
                    class="bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300 px-2 py-0.5 rounded-full text-xs font-bold"
                    id="challenges-count">{{ $challenges->total() }}</span>
            </span>
            <button type="button" onclick="openChallengeModal()"
                class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-all shadow-sm hover:shadow-md flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm thử thách
            </button>
        </div>

        {{-- Search & Filter Toolbar --}}
        <div
            class="p-3 border-b border-gray-100 dark:border-slate-700 bg-amber-50/30 dark:bg-slate-700/50 flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <i
                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-500 text-sm"></i>
                <input type="text" id="challenge-search" placeholder="Tìm kiếm thử thách..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                    oninput="filterChallenges()">
            </div>
            {{-- Filter by Status --}}
            <select id="challenge-filter" onchange="filterChallenges()"
                class="px-4 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer">
                <option value="all">Tất cả trạng thái</option>
                <option value="ongoing">Đang diễn ra</option>
                <option value="upcoming">Sắp diễn ra</option>
                <option value="ended">Đã kết thúc</option>
                <option value="inactive">Đã tắt</option>
            </select>
        </div>

        {{-- Challenge List --}}
        <div class="divide-y divide-gray-100 dark:divide-slate-700" id="challenges-list">
            @forelse($challenges as $challenge)
                @php
                    $status = 'upcoming';
                    if (!$challenge->is_active) {
                        $status = 'inactive';
                    } elseif ($challenge->isOngoing()) {
                        $status = 'ongoing';
                    } elseif ($challenge->end_date < now()) {
                        $status = 'ended';
                    }
                    // Calculate progress for ongoing challenges
                    $progressPercent = 0;
                    $timeLeftText = '';
                    if ($status === 'ongoing') {
                        $totalSeconds = $challenge->start_date->diffInSeconds($challenge->end_date);
                        $passedSeconds = $challenge->start_date->diffInSeconds(now());
                        $remainingSeconds = now()->diffInSeconds($challenge->end_date, false);
                        $progressPercent = $totalSeconds > 0 ? min(100, round(($passedSeconds / $totalSeconds) * 100)) : 0;

                        // Format time left
                        if ($remainingSeconds <= 0) {
                            $timeLeftText = 'Sắp kết thúc';
                        } else {
                            $days = floor($remainingSeconds / 86400);
                            $hours = floor(($remainingSeconds % 86400) / 3600);
                            $minutes = floor(($remainingSeconds % 3600) / 60);

                            if ($days > 0) {
                                $timeLeftText = $days . ' ngày ' . $hours . ' giờ';
                            } elseif ($hours > 0) {
                                $timeLeftText = $hours . ' giờ ' . $minutes . ' phút';
                            } else {
                                $timeLeftText = $minutes . ' phút';
                            }
                        }
                    }
                @endphp
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition-colors challenge-item"
                    data-challenge-id="{{ $challenge->id }}" data-name="{{ strtolower($challenge->name) }}"
                    data-status="{{ $status }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h4 class="font-semibold text-gray-800 dark:text-white">{{ $challenge->name }}</h4>
                                @if($status === 'ongoing')
                                    <span
                                        class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 px-2 py-0.5 rounded text-xs font-bold">Đang
                                        diễn ra</span>
                                @elseif($status === 'ended')
                                    <span
                                        class="bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-400 px-2 py-0.5 rounded text-xs font-bold">Đã
                                        kết thúc</span>
                                @elseif($status === 'upcoming')
                                    <span
                                        class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 px-2 py-0.5 rounded text-xs font-bold">Sắp
                                        diễn ra</span>
                                @endif
                                @if(!$challenge->is_active)
                                    <span
                                        class="bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 px-2 py-0.5 rounded text-xs font-bold">Tắt</span>
                                @endif
                            </div>

                            {{-- Progress bar for ongoing challenges --}}
                            @if($status === 'ongoing')
                                <div class="mb-2">
                                    <div class="flex justify-between text-xs text-gray-500 dark:text-slate-400 mb-1">
                                        <span>Tiến độ thời gian</span>
                                        <span
                                            class="font-medium text-green-600 dark:text-green-400">{{ $remainingSeconds <= 0 ? $timeLeftText : 'Còn ' . $timeLeftText }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-1.5">
                                        <div class="bg-green-500 h-1.5 rounded-full transition-all"
                                            style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                            @endif

                            @if($challenge->description)
                                <p class="text-sm text-gray-500 dark:text-slate-400 mb-2 line-clamp-1">
                                    {{ $challenge->description }}
                                </p>
                            @endif
                            <div class="flex items-center gap-4 text-sm flex-wrap">
                                <span class="text-gray-600 dark:text-slate-300 flex items-center gap-1">
                                    <i class="fas fa-medal text-blue-500"></i>
                                    {{ $challenge->badge?->name ?? 'Biểu tượng đã xóa' }}
                                </span>
                                @if($challenge->avatar_frame_id)
                                    <span class="text-purple-600 dark:text-purple-400 flex items-center gap-1"
                                        title="Có tặng khung hoạt ảnh">
                                        <i class="fas fa-image"></i>
                                        {{ $challenge->avatarFrame?->name ?? 'Khung' }}
                                    </span>
                                @endif
                                <span class="text-gray-600 dark:text-slate-300 flex items-center gap-1">
                                    <i class="fas {{ $challenge->action_type == 'post_comment' ? 'fa-comments' : 'fa-pen' }} text-blue-500"></i>
                                    {{ $challenge->target_count }} {{ $challenge->action_type == 'post_comment' ? 'bình luận' : 'công thức' }}
                                </span>
                                <span class="text-gray-600 dark:text-slate-300 flex items-center gap-1">
                                    <i class="fas fa-calendar text-blue-500"></i>
                                    {{ $challenge->start_date->format('d/m') }} -
                                    {{ $challenge->end_date->format('d/m/Y') }}
                                </span>
                                <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                    <i class="fas fa-users"></i>{{ $challenge->user_challenges_count }} tham gia
                                </span>
                                <span class="text-green-600 dark:text-green-400 flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i>{{ $challenge->completed_count }} hoàn thành
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                            <a href="{{ route('admin.challenges.show', $challenge) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800 transition"
                                title="Xem chi tiết">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <button type="button" onclick="openChallengeModal({{ $challenge->id }})"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-800 transition"
                                title="Chỉnh sửa">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button type="button"
                                onclick="deleteChallenge({{ $challenge->id }}, '{{ addslashes($challenge->name) }}')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 transition"
                                title="Xóa">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500 dark:text-slate-400" id="challenges-empty">
                    <i class="fas fa-trophy text-5xl text-gray-300 dark:text-slate-600 mb-3"></i>
                    <p class="text-lg font-medium">Chưa có thử thách nào</p>
                    <p class="text-sm mt-1">Nhấn nút "Thêm thử thách" để tạo mới</p>
                </div>
            @endforelse
        </div>

        @if($challenges->hasPages())
            <div class="p-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                {{ $challenges->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>
</div>
