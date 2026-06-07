@extends('layouts.admin')
@section('title', 'Sửa Biểu Tượng')
@section('header', 'Sửa Biểu Tượng: ' . $badge->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-gray-100 dark:border-slate-700 p-8 transition-all duration-300 hover:shadow-xl">

            {{-- Header with current icon preview --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-slate-700">
                <div
                    class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                    @if($badge->icon && Str::startsWith($badge->icon, 'http'))
                        <img loading="lazy" src="{{ $badge->icon }}" alt="{{ $badge->name }}" class="w-10 h-10 object-contain"
                            referrerpolicy="no-referrer">
                    @else
                        <span class="text-3xl">{{ $badge->icon ?? '🏅' }}</span>
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $badge->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Chỉnh sửa thông tin biểu tượng</p>
                </div>
            </div>

            <form action="{{ route('admin.badges.update', $badge) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    {{-- Tên biểu tượng --}}
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-tag text-blue-500 mr-1"></i>
                            Tên biểu tượng <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="name-count">{{ strlen($badge->name) }}</span>/50)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $badge->name) }}" maxlength="50"
                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white transition-all duration-200 hover:border-gray-300 dark:hover:border-slate-500"
                            placeholder="VD: Mọt Công thức Mùa Đông"
                            oninput="document.getElementById('name-count').textContent = this.value.length">
                        @error('name') <p class="text-red-500 text-xs mt-2 flex items-center"><i
                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-align-left text-green-500 mr-1"></i>
                            Mô tả
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="desc-count">{{ strlen($badge->description ?? '') }}</span>/150)</span>
                        </label>
                        <textarea name="description" rows="4" maxlength="150"
                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white transition-all duration-200 hover:border-gray-300 dark:hover:border-slate-500 resize-y min-h-[100px]"
                            placeholder="Mô tả biểu tượng..."
                            oninput="document.getElementById('desc-count').textContent = this.value.length">{{ old('description', $badge->description) }}</textarea>
                    </div>

                    {{-- Icon Section --}}
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-icons text-purple-500 mr-1"></i>
                            Icon
                        </label>

                        {{-- Icon Type Selector --}}
                        <div class="flex gap-2 mb-3">
                            <button type="button" onclick="setIconTypeEdit('emoji')" id="btn-emoji-type-edit"
                                class="icon-type-btn-edit flex-1 px-4 py-2 rounded-xl border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-sm font-semibold transition-all duration-200 hover:shadow-md">
                                😀 Emoji
                            </button>
                            <button type="button" onclick="setIconTypeEdit('image')" id="btn-image-type-edit"
                                class="icon-type-btn-edit flex-1 px-4 py-2 rounded-xl border-2 border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-400 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 transition-all duration-200">
                                🖼️ Hình ảnh
                            </button>
                        </div>

                        {{-- Emoji Picker Section --}}
                        <div id="emoji-picker-section-edit">
                            {{-- Emoji Categories --}}
                            <div class="flex gap-1 mb-2 overflow-x-auto pb-1">
                                <button type="button" onclick="showEmojiCategoryEdit('awards')"
                                    class="emoji-cat-btn-edit px-3 py-1 text-xs rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-medium whitespace-nowrap transition-all hover:scale-105"
                                    data-category="awards">
                                    🏆 Giải thưởng
                                </button>
                                <button type="button" onclick="showEmojiCategoryEdit('books')"
                                    class="emoji-cat-btn-edit px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400 font-medium whitespace-nowrap transition-all hover:scale-105"
                                    data-category="books">
                                    📚 Sách
                                </button>
                                <button type="button" onclick="showEmojiCategoryEdit('nature')"
                                    class="emoji-cat-btn-edit px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400 font-medium whitespace-nowrap transition-all hover:scale-105"
                                    data-category="nature">
                                    🌸 Thiên nhiên
                                </button>
                                <button type="button" onclick="showEmojiCategoryEdit('hearts')"
                                    class="emoji-cat-btn-edit px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400 font-medium whitespace-nowrap transition-all hover:scale-105"
                                    data-category="hearts">
                                    💖 Trái tim
                                </button>
                                <button type="button" onclick="showEmojiCategoryEdit('misc')"
                                    class="emoji-cat-btn-edit px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-400 font-medium whitespace-nowrap transition-all hover:scale-105"
                                    data-category="misc">
                                    🎨 Khác
                                </button>
                            </div>

                            {{-- Emoji Grid --}}
                            <div id="emoji-grid-edit"
                                class="grid grid-cols-8 gap-1.5 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl mb-3 max-h-36 overflow-y-auto">
                                {{-- Awards (default) --}}
                                @php
                                    $emojiCategories = [
                                        'awards' => ['🏆', '🥇', '🥈', '🥉', '🏅', '🎖️', '⭐', '🌟', '✨', '💎', '👑', '🔥', '💪', '🎯', '🎪', '🎭'],
                                        'books' => ['📚', '📖', '📕', '📗', '📘', '📙', '✍️', '🖊️', '📝', '💡', '🧠', '📰', '📑', '🔖', '📋', '✏️'],
                                        'nature' => ['🌸', '🌺', '🌻', '🌈', '☀️', '🌙', '❄️', '🍂', '🌊', '🦋', '🐝', '🦄', '🐉', '🌴', '🍀', '🌵'],
                                        'hearts' => ['💝', '💖', '❤️', '🧡', '💛', '💚', '💙', '💜', '🤍', '🖤', '🤎', '💔', '❣️', '💗', '💓', '💕'],
                                        'misc' => ['🎨', '🎬', '🎵', '🎸', '🎮', '🚀', '✈️', '🏠', '⚡', '🎁', '🎈', '🎉', '🎊', '🔮', '🎲', '🧩']
                                    ];
                                @endphp
                                @foreach($emojiCategories['awards'] as $emoji)
                                    <button type="button" onclick="selectEmojiEdit('{{ $emoji }}')"
                                        class="emoji-btn-edit w-9 h-9 flex items-center justify-center text-xl hover:bg-white dark:hover:bg-slate-600 rounded-lg transition-all duration-150 hover:scale-110 hover:shadow-md">
                                        {{ $emoji }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Hidden data for JS --}}
                            <script type="application/json" id="emoji-categories-data-edit">
                                                @json($emojiCategories)
                                            </script>

                            {{-- Icon Input --}}
                            <input type="text" id="icon-input-edit" name="icon" value="{{ old('icon', $badge->icon) }}"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-center text-2xl transition-all duration-200"
                                placeholder="Chọn hoặc nhập emoji" oninput="updateIconPreviewEdit()">
                        </div>

                        {{-- Image URL Section --}}
                        <div id="image-url-section-edit" class="hidden">
                            <input type="text" id="icon-url-input-edit"
                                class="w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-sm transition-all duration-200"
                                placeholder="https://example.com/icon.png hoặc .gif" oninput="updateImageUrlInputEdit()">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Hỗ trợ: .png, .jpg, .gif, .webp, .svg
                            </p>
                        </div>

                        {{-- Preview --}}
                        <div id="icon-preview-edit"
                            class="mt-3 p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 rounded-xl text-center hidden transition-all duration-300">
                            <span
                                class="text-xs text-gray-500 dark:text-slate-400 block mb-2 uppercase tracking-wide font-semibold">Xem
                                trước</span>
                            <div id="preview-content-edit"
                                class="text-5xl transform hover:scale-110 transition-transform duration-200"></div>
                        </div>
                    </div>

                    {{-- Kích hoạt --}}
                    <div
                        class="flex items-center p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl transition-all duration-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                        <input type="checkbox" name="is_active" id="is_active_edit" {{ $badge->is_active ? 'checked' : '' }}
                            class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 cursor-pointer">
                        <label for="is_active_edit"
                            class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300 cursor-pointer select-none">
                            <i class="fas fa-toggle-on text-green-500 mr-1"></i>
                            Kích hoạt biểu tượng
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white py-3 rounded-xl hover:from-emerald-700 hover:to-emerald-800 font-semibold transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0">
                            <i class="fas fa-save mr-2"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.game.index') }}"
                            class="px-6 py-3 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 font-semibold transition-all duration-200 hover:shadow-md">
                            <i class="fas fa-times mr-1"></i> Hủy
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // ========== ICON TYPE FUNCTIONS (EDIT) ==========
            let currentIconTypeEdit = 'emoji';
            let currentCategoryEdit = 'awards';

            function setIconTypeEdit(type) {
                currentIconTypeEdit = type;
                const btnEmoji = document.getElementById('btn-emoji-type-edit');
                const btnImage = document.getElementById('btn-image-type-edit');
                const emojiSection = document.getElementById('emoji-picker-section-edit');
                const imageSection = document.getElementById('image-url-section-edit');
                const iconInput = document.getElementById('icon-input-edit');
                const iconUrlInput = document.getElementById('icon-url-input-edit');

                if (type === 'emoji') {
                    btnEmoji.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400');
                    btnEmoji.classList.remove('border-gray-200', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400');
                    btnImage.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400');
                    btnImage.classList.add('border-gray-200', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400');
                    emojiSection.classList.remove('hidden');
                    imageSection.classList.add('hidden');
                    if (isImageUrlEdit(iconInput.value)) {
                        iconInput.value = '';
                    }
                    iconUrlInput.value = '';
                    updateIconPreviewEdit();
                } else {
                    btnImage.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400');
                    btnImage.classList.remove('border-gray-200', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400');
                    btnEmoji.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400');
                    btnEmoji.classList.add('border-gray-200', 'dark:border-slate-600', 'text-gray-600', 'dark:text-slate-400');
                    emojiSection.classList.add('hidden');
                    imageSection.classList.remove('hidden');
                    // Transfer current icon to URL input if it's a URL
                    if (isImageUrlEdit(iconInput.value)) {
                        iconUrlInput.value = iconInput.value;
                    }
                    updateImageUrlInputEdit();
                }
            }

            function showEmojiCategoryEdit(category) {
                currentCategoryEdit = category;
                const grid = document.getElementById('emoji-grid-edit');
                const categories = JSON.parse(document.getElementById('emoji-categories-data-edit').textContent);
                const emojis = categories[category] || [];

                // Update category buttons
                document.querySelectorAll('.emoji-cat-btn-edit').forEach(btn => {
                    if (btn.dataset.category === category) {
                        btn.classList.remove('bg-gray-100', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-slate-400');
                        btn.classList.add('bg-emerald-100', 'dark:bg-emerald-900/50', 'text-emerald-700', 'dark:text-emerald-300');
                    } else {
                        btn.classList.add('bg-gray-100', 'dark:bg-slate-700', 'text-gray-600', 'dark:text-slate-400');
                        btn.classList.remove('bg-emerald-100', 'dark:bg-emerald-900/50', 'text-emerald-700', 'dark:text-emerald-300');
                    }
                });

                // Regenerate emoji buttons
                grid.innerHTML = emojis.map(emoji =>
                    `<button type="button" onclick="selectEmojiEdit('${emoji}')"
                                                        class="emoji-btn-edit w-9 h-9 flex items-center justify-center text-xl hover:bg-white dark:hover:bg-slate-600 rounded-lg transition-all duration-150 hover:scale-110 hover:shadow-md">
                                                        ${emoji}
                                                    </button>`
                ).join('');
            }

            function isImageUrlEdit(str) {
                if (!str) return false;
                const imageExtensions = ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg'];
                const lowerStr = str.toLowerCase();
                return lowerStr.startsWith('http') && imageExtensions.some(ext => lowerStr.includes(ext));
            }

            function updateIconPreviewEdit() {
                const iconInput = document.getElementById('icon-input-edit');
                const preview = document.getElementById('icon-preview-edit');
                const previewContent = document.getElementById('preview-content-edit');
                const value = iconInput.value.trim();

                if (value) {
                    preview.classList.remove('hidden');
                    if (isImageUrlEdit(value)) {
                        previewContent.innerHTML = `<img loading="lazy" src="${value}" alt="Preview" class="w-16 h-16 object-contain mx-auto rounded-lg" referrerpolicy="no-referrer">`;
                    } else {
                        previewContent.innerHTML = value;
                    }
                } else {
                    preview.classList.add('hidden');
                    previewContent.innerHTML = '';
                }
            }

            function selectEmojiEdit(emoji) {
                const iconInput = document.getElementById('icon-input-edit');
                iconInput.value = emoji;
                updateIconPreviewEdit();
            }

            function updateImageUrlInputEdit() {
                const iconUrlInput = document.getElementById('icon-url-input-edit');
                const iconInput = document.getElementById('icon-input-edit');
                const preview = document.getElementById('icon-preview-edit');
                const previewContent = document.getElementById('preview-content-edit');
                const url = iconUrlInput.value.trim();

                iconInput.value = url;

                if (url && isImageUrlEdit(url)) {
                    preview.classList.remove('hidden');
                    previewContent.innerHTML = `<img loading="lazy" src="${url}" alt="Preview" class="w-16 h-16 object-contain mx-auto rounded-lg" referrerpolicy="no-referrer">`;
                } else if (url) {
                    preview.classList.remove('hidden');
                    previewContent.innerHTML = '<span class="text-yellow-500 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i>URL không hợp lệ</span>';
                } else {
                    preview.classList.add('hidden');
                    previewContent.innerHTML = '';
                }
            }

            // ========== DOM READY ==========
            document.addEventListener('DOMContentLoaded', function () {
                const iconInput = document.getElementById('icon-input-edit');
                const currentIcon = iconInput.value;

                // Auto-detect if current icon is URL or emoji
                if (isImageUrlEdit(currentIcon)) {
                    setIconTypeEdit('image');
                    document.getElementById('icon-url-input-edit').value = currentIcon;
                }

                // Show preview on load
                updateIconPreviewEdit();
            });
        </script>
    @endpush
@endsection
