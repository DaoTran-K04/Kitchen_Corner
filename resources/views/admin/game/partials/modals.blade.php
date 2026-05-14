{{-- CHALLENGE MODAL --}}
<div id="challenge-modal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeChallengeModal()"></div>

    {{-- Modal Content --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="challenge-modal-content">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-amber-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="challenge-modal-title">
                        <i class="fas fa-trophy"></i>
                        <span>Thêm Thử Thách Mới</span>
                    </h3>
                    <button onclick="closeChallengeModal()" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Form --}}
            <form id="challenge-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST" id="challenge-form-method">
                <input type="hidden" name="challenge_id" id="challenge-id">

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Cột trái --}}
                        <div class="space-y-4">
                            {{-- Tên --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Tên thử thách <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                            id="challenge-name-count">0</span>/50)</span>
                                </label>
                                <input type="text" name="name" id="challenge-name" maxlength="50"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                    placeholder="VD: Season Mùa Đông 2025" required
                                    oninput="document.getElementById('challenge-name-count').textContent = this.value.length">
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-name"></p>
                            </div>

                            {{-- Biểu tượng --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Biểu tượng nhận được <span class="text-red-500">*</span>
                                </label>
                                <select name="badge_id" id="challenge-badge"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer"
                                    required>
                                    <option value="">-- Chọn biểu tượng --</option>
                                    @foreach($badges as $badge)
                                        <option value="{{ $badge->id }}">{{ $badge->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-badge_id"></p>
                            </div>

                            {{-- Khung hoạt ảnh --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Khung hoạt ảnh (tùy chọn)
                                </label>
                                <select name="avatar_frame_id" id="challenge-frame"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer">
                                    <option value="">-- Không tặng khung --</option>
                                    @foreach($frames as $frame)
                                        <option value="{{ $frame->id }}">{{ $frame->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Loại hành động --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Loại hành động <span class="text-red-500">*</span></label>
                                <select name="action_type" id="challenge-action" class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white" required>
                                    <option value="post_recipe">Đăng bài công thức</option>
                                    <option value="post_comment">Viết bình luận</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-action_type"></p>
                            </div>

                            {{-- Số bài review --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Số lượng yêu cầu <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="target_count" id="challenge-target" min="1" max="100"
                                    value="1"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
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
                                <input type="date" name="start_date" id="challenge-start"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-start_date"></p>
                            </div>

                            {{-- Ngày kết thúc --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Ngày kết thúc <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="challenge-end"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-end_date"></p>
                            </div>

                            {{-- Mô tả --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Mô tả
                                    <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                            id="challenge-desc-count">0</span>/150)</span>
                                </label>
                                <textarea name="description" id="challenge-description" rows="3" maxlength="150"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-none"
                                    placeholder="Mô tả ngắn gọn về thử thách..."
                                    oninput="document.getElementById('challenge-desc-count').textContent = this.value.length"></textarea>
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-description"></p>
                            </div>

                            {{-- Kích hoạt Toggle --}}
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-300">
                                    Kích hoạt thử thách
                                </span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="challenge-active" checked
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                    <button type="button" onclick="closeChallengeModal()"
                        class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                        Hủy
                    </button>
                    <button type="submit" id="challenge-submit-btn"
                        class="px-6 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="challenge-submit-text">Tạo thử thách</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- BADGE MODAL --}}
<div id="badge-modal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeBadgeModal()"></div>

    {{-- Modal Content --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="badge-modal-content">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-emerald-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="badge-modal-title">
                        <i class="fas fa-medal"></i>
                        <span>Thêm Biểu Tượng Mới</span>
                    </h3>
                    <button onclick="closeBadgeModal()" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Form --}}
            <form id="badge-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST" id="badge-form-method">
                <input type="hidden" name="badge_id" id="badge-id">

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    {{-- Tên biểu tượng --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Tên biểu tượng <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="badge-name-count">0</span>/50)</span>
                        </label>
                        <input type="text" name="name" id="badge-name" maxlength="50"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                            placeholder="VD: Mọt Công thức Mùa Đông" required
                            oninput="document.getElementById('badge-name-count').textContent = this.value.length">
                        <p class="text-red-500 text-xs mt-1 hidden" id="badge-error-name"></p>
                    </div>

                    {{-- Mô tả --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Mô tả
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="badge-desc-count">0</span>/150)</span>
                        </label>
                        <textarea name="description" id="badge-desc" rows="2" maxlength="150"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-none"
                            placeholder="Mô tả ngắn gọn về biểu tượng..."
                            oninput="document.getElementById('badge-desc-count').textContent = this.value.length"></textarea>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Icon</label>
                        {{-- Icon Type Toggle --}}
                        <div class="flex gap-2 mb-3">
                            <button type="button" onclick="setBadgeIconType('emoji')" id="badge-btn-emoji"
                                class="flex-1 px-3 py-2 rounded-lg border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-sm font-bold transition-all">
                                😀 Emoji
                            </button>
                            <button type="button" onclick="setBadgeIconType('url')" id="badge-btn-url"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                                🔗 URL
                            </button>
                            <button type="button" onclick="setBadgeIconType('file')" id="badge-btn-file"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                                📁 Upload
                            </button>
                        </div>
                        {{-- Hidden input to store icon value --}}
                        <input type="hidden" name="icon" id="badge-icon">
                        {{-- Icon biểu tượng --}}
                        <div id="badge-emoji-section">
                            <div
                                class="grid grid-cols-10 gap-1 p-2 bg-gray-50 dark:bg-slate-700 rounded-lg mb-2 max-h-28 overflow-y-auto">
                                @php $emojis = ['🏆', '🥇', '🥈', '🥉', '🏅', '🎖️', '⭐', '🌟', '✨', '💎', '👑', '🔥', '💪', '🎯', '📚', '📖', '✍️', '💡', '🧠', '❤️', '💙', '💚', '💜', '🦋', '🌈']; @endphp
                                @foreach($emojis as $emoji)
                                    <button type="button" onclick="selectBadgeEmoji('{{ $emoji }}')"
                                        class="w-7 h-7 flex items-center justify-center text-lg hover:bg-white dark:hover:bg-slate-600 rounded transition-colors">
                                        {{ $emoji }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="text" id="badge-emoji-input"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-center text-2xl"
                                placeholder="Chọn hoặc nhập emoji"
                                oninput="document.getElementById('badge-icon').value = this.value">
                        </div>
                        {{-- URL Input --}}
                        <div id="badge-url-section" class="hidden">
                            <input type="url" id="badge-icon-url"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-sm"
                                placeholder="https://example.com/icon.png" oninput="updateBadgeUrlPreview()">
                            <div id="badge-url-preview" class="mt-3 hidden">
                                <div class="p-3 bg-gray-100 dark:bg-slate-700 rounded-lg text-center">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">Xem trước:</p>
                                    <img id="badge-url-preview-img" class="h-16 mx-auto object-contain"
                                        referrerpolicy="no-referrer">
                                </div>
                            </div>
                        </div>
                        {{-- File Upload --}}
                        <div id="badge-file-section" class="hidden">
                            <label
                                class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-4 pb-4">
                                    <i
                                        class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-slate-500 mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Click để chọn file hoặc kéo thả
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">PNG, JPG, GIF, WEBP (Max 2MB)
                                    </p>
                                </div>
                                <input type="file" name="icon_file" id="badge-icon-file" class="hidden"
                                    accept=".png,.jpg,.jpeg,.gif,.webp,.svg" onchange="updateBadgeFilePreview(this)">
                            </label>
                            <div id="badge-file-preview" class="mt-3 hidden">
                                <div class="p-3 bg-gray-100 dark:bg-slate-700 rounded-lg text-center">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">Xem trước:</p>
                                    <img id="badge-file-preview-img" class="h-16 mx-auto object-contain">
                                    <p id="badge-file-name"
                                        class="text-xs text-gray-600 dark:text-slate-400 mt-2 truncate"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kích hoạt Toggle --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-300">Kích hoạt biểu tượng</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="badge-active" checked class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                    <button type="button" onclick="closeBadgeModal()"
                        class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                        Hủy
                    </button>
                    <button type="submit" id="badge-submit-btn"
                        class="px-6 py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="badge-submit-text">Tạo biểu tượng</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- FRAME MODAL --}}
<div id="frame-modal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeFrameModal()"></div>

    {{-- Modal Content --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 scale-95 opacity-0"
            id="frame-modal-content">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-purple-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="frame-modal-title">
                        <i class="fas fa-image"></i>
                        <span>Thêm Khung Hoạt Ảnh</span>
                    </h3>
                    <button onclick="closeFrameModal()" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Form --}}
            <form id="frame-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="POST" id="frame-form-method">
                <input type="hidden" name="frame_id" id="frame-id">

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    {{-- Tên khung --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Tên khung <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="frame-name-count">0</span>/50)</span>
                        </label>
                        <input type="text" name="name" id="frame-name" maxlength="50"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                            placeholder="VD: Khung Mùa Đông 2025" required
                            oninput="document.getElementById('frame-name-count').textContent = this.value.length">
                        <p class="text-red-500 text-xs mt-1 hidden" id="frame-error-name"></p>
                    </div>

                    {{-- Mô tả --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Mô tả
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="frame-desc-count">0</span>/150)</span>
                        </label>
                        <textarea name="description" id="frame-desc" rows="2" maxlength="150"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-none"
                            placeholder="Mô tả khung avatar..."
                            oninput="document.getElementById('frame-desc-count').textContent = this.value.length"></textarea>
                    </div>

                    {{-- Hình ảnh --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Hình ảnh (GIF/PNG) <span class="text-red-500">*</span>
                        </label>
                        {{-- Image Type Toggle --}}
                        <div class="flex gap-2 mb-3">
                            <button type="button" onclick="setFrameImageType('url')" id="frame-btn-url"
                                class="flex-1 px-3 py-2 rounded-lg border-2 border-purple-500 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-sm font-bold transition-all">
                                🔗 URL
                            </button>
                            <button type="button" onclick="setFrameImageType('file')" id="frame-btn-file"
                                class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-400 text-sm font-bold hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                                📁 Upload
                            </button>
                        </div>
                        {{-- URL Input --}}
                        <div id="frame-url-section">
                            <input type="url" name="frame_image_url" id="frame-image-url"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                placeholder="https://example.com/frame.gif" oninput="updateFrameUrlPreview()">
                            <div id="frame-url-preview" class="mt-3 hidden">
                                <div class="p-3 bg-gray-100 dark:bg-slate-700 rounded-lg text-center">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">Xem trước:</p>
                                    <img id="frame-url-preview-img" class="h-20 mx-auto object-contain"
                                        referrerpolicy="no-referrer">
                                </div>
                            </div>
                        </div>
                        {{-- File Upload --}}
                        <div id="frame-file-section" class="hidden">
                            <label
                                class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-slate-700 hover:bg-gray-100 dark:hover:bg-slate-600 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-4 pb-4">
                                    <i
                                        class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-slate-500 mb-2"></i>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Click để chọn file hoặc kéo thả
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">PNG, GIF, WEBP (Max 2MB)</p>
                                </div>
                                <input type="file" name="frame_image" id="frame-image-file" class="hidden"
                                    accept=".png,.gif,.webp,.svg" onchange="updateFrameFilePreview(this)">
                            </label>
                            <div id="frame-file-preview" class="mt-3 hidden">
                                <div class="p-3 bg-gray-100 dark:bg-slate-700 rounded-lg text-center">
                                    <p class="text-xs text-gray-500 dark:text-slate-400 mb-2">Xem trước:</p>
                                    <img id="frame-file-preview-img" class="h-20 mx-auto object-contain">
                                    <p id="frame-file-name"
                                        class="text-xs text-gray-600 dark:text-slate-400 mt-2 truncate"></p>
                                </div>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1 hidden" id="frame-error-image"></p>
                    </div>

                    {{-- Thứ tự --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Thứ tự hiển
                            thị</label>
                        <input type="number" name="order" id="frame-order" value="0" min="0" max="99"
                            class="w-24 px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-center">
                    </div>

                    {{-- Kích hoạt Toggle --}}
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-300">Kích hoạt khung</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="frame-active" checked class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-focus:ring-2 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                    <button type="button" onclick="closeFrameModal()"
                        class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                        Hủy
                    </button>
                    <button type="submit" id="frame-submit-btn"
                        class="px-6 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="frame-submit-text">Tạo khung</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
