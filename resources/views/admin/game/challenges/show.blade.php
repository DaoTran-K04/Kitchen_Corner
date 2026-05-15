@extends('layouts.admin')
@section('title', 'Chi tiết Thử Thách')
@section('header', 'Chi tiết Thử Thách')

@section('content')
    @php
        $isOngoing = $challenge->isOngoing();
        $isEnded = $challenge->end_date < now();
        $isUpcoming = !$isOngoing && !$isEnded && $challenge->is_active;
        
        // Calculate time left for ongoing
        $timeLeftText = '';
        $progressPercent = 0;
        if ($isOngoing) {
            $totalSeconds = $challenge->start_date->diffInSeconds($challenge->end_date);
            $passedSeconds = $challenge->start_date->diffInSeconds(now());
            $remainingSeconds = now()->diffInSeconds($challenge->end_date, false);
            $progressPercent = $totalSeconds > 0 ? min(100, round(($passedSeconds / $totalSeconds) * 100)) : 0;
            
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

    <div class="space-y-6">
        {{-- Info Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
            {{-- Header --}}
            <div class="px-6 py-4 bg-amber-500 text-white">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">{{ $challenge->badge->icon ?? '🏆' }}</span>
                        <div>
                            <h2 class="text-xl font-bold">{{ $challenge->name }}</h2>
                            <p class="text-amber-100 text-sm">{{ $challenge->description }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openEditModal()"
                            class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition flex items-center gap-1">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <a href="{{ route('admin.game.index', ['tab' => 'challenges']) }}"
                            class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition flex items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    {{-- Mục tiêu --}}
                    <div class="bg-amber-50 dark:bg-amber-900/30 p-4 rounded-lg text-center">
                        <i class="fas {{ $challenge->action_type == 'post_comment' ? 'fa-comments' : 'fa-pen' }} text-amber-500 text-xl mb-2"></i>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $challenge->target_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ $challenge->action_type == 'post_comment' ? 'Bình luận cần viết' : 'Công thức cần đăng' }}</p>
                    </div>
                    {{-- Thời gian --}}
                    <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg text-center">
                        <i class="fas fa-calendar text-purple-500 text-xl mb-2"></i>
                        <p class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ $challenge->start_date->format('d/m') }} - {{ $challenge->end_date->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Thời gian</p>
                    </div>
                    {{-- Biểu tượng --}}
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-lg text-center">
                        <i class="fas fa-medal text-yellow-500 text-xl mb-2"></i>
                        <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">{{ $challenge->badge->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Biểu tượng</p>
                    </div>
                    {{-- Khung hoạt ảnh --}}
                    <div class="bg-pink-50 dark:bg-pink-900/30 p-4 rounded-lg text-center">
                        <i class="fas fa-image text-pink-500 text-xl mb-2"></i>
                        <p class="text-sm font-bold text-pink-600 dark:text-pink-400">{{ $challenge->avatarFrame?->name ?? 'Không' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-400">Khung hoạt ảnh</p>
                    </div>
                </div>

                {{-- Status badges --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($isOngoing)
                        <span class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-play mr-1"></i>Đang diễn ra
                        </span>
                    @elseif($isEnded)
                        <span class="bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-400 px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-stop mr-1"></i>Đã kết thúc
                        </span>
                    @elseif($isUpcoming)
                        <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300 px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-clock mr-1"></i>Sắp diễn ra
                        </span>
                    @endif
                    @if(!$challenge->is_active)
                        <span class="bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 px-3 py-1 rounded-full text-sm font-bold">
                            <i class="fas fa-times mr-1"></i>Đã tắt
                        </span>
                    @endif
                </div>

                {{-- Progress bar for ongoing --}}
                @if($isOngoing)
                    <div class="mb-2">
                        <div class="flex justify-between text-sm text-gray-500 dark:text-slate-400 mb-2">
                            <span>Tiến độ thời gian</span>
                            <span class="font-medium text-green-600 dark:text-green-400">{{ $timeLeftText === 'Sắp kết thúc' ? $timeLeftText : 'Còn ' . $timeLeftText }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-slate-600 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Participants --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
            <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-between items-center">
                <span class="font-semibold text-gray-700 dark:text-white flex items-center gap-2">
                    <i class="fas fa-users text-amber-500"></i>
                    Người tham gia
                    <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300 px-2 py-0.5 rounded-full text-xs font-bold">{{ $userChallenges->total() }}</span>
                </span>
            </div>
            <table class="w-full text-left">
                <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-white dark:bg-slate-800 border-b dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3">Người dùng</th>
                        <th class="px-6 py-3 text-center">Tiến độ</th>
                        <th class="px-6 py-3 text-center">Trạng thái</th>
                        <th class="px-6 py-3 text-center">Hoàn thành lúc</th>
                        <th class="px-6 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($userChallenges as $uc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $uc->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($uc->user->name) }}"
                                        class="w-8 h-8 rounded-full mr-3" alt="">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $uc->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">{{ $uc->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <div class="w-32 bg-gray-200 dark:bg-slate-600 rounded-full h-2 mr-2">
                                        <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $uc->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-slate-300">{{ $uc->current_count }}/{{ $challenge->target_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($uc->is_completed)
                                    <span class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-300 px-2 py-1 rounded text-xs font-bold">
                                        <i class="fas fa-check mr-1"></i>Hoàn thành
                                    </span>
                                @else
                                    <span class="bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-300 px-2 py-1 rounded text-xs font-bold">
                                        Đang thực hiện
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500 dark:text-slate-400">
                                {{ $uc->completed_at ? $uc->completed_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($uc->is_completed)
                                    @php
                                        $hasBadge = \App\Models\UserBadge::where('user_id', $uc->user_id)
                                            ->where('badge_id', $challenge->badge_id)->exists();
                                    @endphp
                                    @if($hasBadge)
                                        <span class="text-green-500 dark:text-green-400 text-sm"><i class="fas fa-medal"></i> Đã cấp</span>
                                    @else
                                        <form action="{{ route('admin.challenges.award-badge', [$challenge, $uc->user_id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600 text-sm transition">
                                                <i class="fas fa-award mr-1"></i>Cấp Biểu Tượng
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-gray-400 dark:text-slate-500 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <i class="fas fa-users text-5xl text-gray-300 dark:text-slate-600 mb-3"></i>
                                <p class="text-lg font-medium">Chưa có ai tham gia thử thách này</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($userChallenges->hasPages())
                <div class="p-4 border-t dark:border-slate-700">
                    {{ $userChallenges->links('vendor.pagination.admin') }}
                </div>
            @endif
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        
        {{-- Modal Content --}}
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="edit-modal-content">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-amber-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-edit"></i>
                            <span>Chỉnh sửa: {{ $challenge->name }}</span>
                        </h3>
                        <button onclick="closeEditModal()" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form id="edit-form">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Cột trái --}}
                            <div class="space-y-4">
                                {{-- Tên --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Tên thử thách <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="edit-name" value="{{ $challenge->name }}" maxlength="50"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                        required>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-name"></p>
                                </div>

                                {{-- Biểu tượng --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Biểu tượng nhận được <span class="text-red-500">*</span>
                                    </label>
                                    <select name="badge_id" id="edit-badge"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white" required>
                                        @foreach($badges ?? [] as $badge)
                                            <option value="{{ $badge->id }}" {{ $challenge->badge_id == $badge->id ? 'selected' : '' }}>
                                                {{ $badge->icon ?? '🏅' }} {{ $badge->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-badge_id"></p>
                                </div>

                                {{-- Khung hoạt ảnh --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Khung hoạt ảnh (tùy chọn)
                                    </label>
                                    <select name="avatar_frame_id" id="edit-frame"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                                        <option value="">-- Không tặng khung --</option>
                                        @foreach($frames ?? [] as $frame)
                                            <option value="{{ $frame->id }}" {{ $challenge->avatar_frame_id == $frame->id ? 'selected' : '' }}>
                                                {{ $frame->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Loại hành động --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Loại hành động <span class="text-red-500">*</span></label>
                                    <select name="action_type" id="edit-action" class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer" required>
                                        <option value="post_recipe" {{ $challenge->action_type == 'post_recipe' ? 'selected' : '' }}>Đăng bài công thức</option>
                                        <option value="post_comment" {{ $challenge->action_type == 'post_comment' ? 'selected' : '' }}>Viết bình luận</option>
                                    </select>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-action_type"></p>
                                </div>

                                {{-- Số bài review --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Số lượng yêu cầu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="target_count" id="edit-target" min="1" max="100" value="{{ $challenge->target_count }}"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white" required>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-target_count"></p>
                                </div>
                            </div>

                            {{-- Cột phải --}}
                            <div class="space-y-4">
                                {{-- Ngày bắt đầu --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Ngày bắt đầu <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="start_date" id="edit-start" value="{{ $challenge->start_date->format('Y-m-d') }}"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white" required>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-start_date"></p>
                                </div>

                                {{-- Ngày kết thúc --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                        Ngày kết thúc <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="end_date" id="edit-end" value="{{ $challenge->end_date->format('Y-m-d') }}"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white" required>
                                    <p class="text-red-500 text-xs mt-1 hidden" id="error-end_date"></p>
                                </div>

                                {{-- Mô tả --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Mô tả</label>
                                    <textarea name="description" id="edit-desc" rows="3" maxlength="150"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-none">{{ $challenge->description }}</textarea>
                                </div>

                                {{-- Kích hoạt Toggle --}}
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700 dark:text-slate-300">Kích hoạt thử thách</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" id="edit-active" {{ $challenge->is_active ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                            Hủy
                        </button>
                        <button type="submit" id="edit-submit-btn"
                            class="px-6 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-lg transition flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span id="edit-submit-text">Lưu thay đổi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal() {
            const modal = document.getElementById('edit-modal');
            const content = document.getElementById('edit-modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
                document.getElementById('edit-name').focus();
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('edit-modal');
            const content = document.getElementById('edit-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('edit-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeEditModal();
                }
            }
        });

        // Form submit
        document.getElementById('edit-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Date validation
            const startDate = document.getElementById('edit-start').value;
            const endDate = document.getElementById('edit-end').value;
            if (new Date(endDate) <= new Date(startDate)) {
                document.getElementById('error-end_date').textContent = 'Ngày kết thúc phải sau ngày bắt đầu!';
                document.getElementById('error-end_date').classList.remove('hidden');
                return;
            }

            const submitBtn = document.getElementById('edit-submit-btn');
            const submitText = document.getElementById('edit-submit-text');
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            // Clear errors
            document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));

            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            fetch('{{ route("admin.challenges.update", $challenge) }}', {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorEl = document.getElementById('error-' + field);
                        if (errorEl) {
                            errorEl.textContent = data.errors[field][0];
                            errorEl.classList.remove('hidden');
                        }
                    });
                }
            })
            .catch(() => alert('Có lỗi xảy ra!'))
            .finally(() => {
                submitBtn.disabled = false;
                submitText.innerHTML = 'Lưu thay đổi';
            });
        });
    </script>
@endsection
