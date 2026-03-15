@extends('layouts.admin')
@section('title', 'Chỉnh Sửa Banner')
@section('header', 'Chỉnh Sửa Banner')

@section('content')
    {{-- Frontend Warning Alert --}}
    <div id="warning-alert"
        class="hidden bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400 px-4 py-3 rounded-lg mb-6 flex items-center justify-between gap-3 shadow-sm transition-all duration-500">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-xl"></i>
            <span class="font-medium" id="warning-message">Thông báo</span>
        </div>
        <button onclick="dismissWarning()"
            class="text-yellow-500 hover:text-yellow-700 dark:hover:text-yellow-300 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 max-w-4xl mx-auto transition-colors duration-300">
        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data"
            id="banner-form">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cột Trái --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tiêu đề <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            required placeholder="Ví dụ: Nhà Giả Kim">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tag quảng bá</label>
                        <input type="text" name="tag" value="{{ old('tag', $banner->tag) }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="Gợi ý: Sách Bán Chạy">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Mô tả</label>
                        <textarea name="description" rows="5"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-y min-h-[120px]"
                            placeholder="Một câu trích dẫn ấn tượng...">{{ old('description', $banner->description) }}</textarea>
                    </div>
                    {{-- Đánh giá --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-slate-300">Đánh giá</label>
                            <span class="rating-inline-value text-sm font-bold text-amber-500 min-w-[60px]"></span>
                            <button type="button" class="rating-clear-btn px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-800/50 text-xs text-red-600 dark:text-red-400 rounded transition-colors hidden">
                                <i class="fas fa-times"></i> Xóa
                            </button>
                        </div>
                        @include('admin.partials.custom-pickers', [
                            'type' => 'rating',
                            'name' => 'rating',
                            'value' => old('rating', $banner->rating),
                            'max' => 5
                        ])
                    </div>

                    {{-- Thứ tự hiển thị --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Thứ tự hiển thị</label>
                        @include('admin.partials.custom-pickers', [
                            'type' => 'scroll',
                            'name' => 'order',
                            'value' => old('order', $banner->order),
                            'min' => 0,
                            'max' => 99,
                            'autoText' => 'Auto'
                        ])
                    </div>
                </div>

                {{-- Cột Phải --}}
                <div class="space-y-4">
                    {{-- Ảnh hiện tại --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ảnh bìa</label>
                        <div
                            class="mb-3 rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 w-full h-48 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                            <img src="{{ Str::startsWith($banner->image, 'http') ? $banner->image : asset('storage/' . $banner->image) }}"
                                class="max-h-full max-w-full object-contain">
                        </div>
                    </div>

                    {{-- Thay ảnh với Tabs --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Thay ảnh mới</label>

                        {{-- Preview ảnh mới --}}
                        <div id="image-preview-container" class="hidden mb-3">
                            <div
                                class="relative rounded-lg overflow-hidden border-2 border-green-400 dark:border-green-500 w-full h-40 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                <img id="image-preview" src="" class="max-h-full max-w-full object-contain">
                                <span
                                    class="absolute top-2 left-2 px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded uppercase">Ảnh
                                    mới</span>
                                <button type="button" onclick="clearPreview()"
                                    class="absolute top-2 right-2 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Tabs chọn hình thức upload --}}
                        <div class="mb-3">
                            <div class="flex gap-2 mb-2">
                                <button type="button" onclick="showUploadTab('file')" id="tab-file"
                                    class="px-3 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 font-bold transition">
                                    <i class="fas fa-upload mr-1"></i> Upload File
                                </button>
                                <button type="button" onclick="showUploadTab('url')" id="tab-url"
                                    class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-400 font-bold transition">
                                    <i class="fas fa-link mr-1"></i> Nhập URL
                                </button>
                            </div>

                            {{-- Fixed height container to prevent content jump --}}
                            <div class="min-h-[100px]">
                                <div id="upload-file">
                                    <input type="file" name="image" id="image-file" accept=".png,.jpg,.jpeg,.gif,.webp,.svg"
                                        onchange="previewFile()"
                                        class="w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/70">
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Hỗ trợ: PNG, JPG, GIF,
                                        WebP</p>
                                </div>

                                <div id="upload-url" class="hidden">
                                    <input type="url" name="image_url" id="image-url" oninput="previewUrl()"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                        placeholder="https://gocsach.vn/sach.png">
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Dán đường dẫn trực tiếp
                                        đến file ảnh</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Đường dẫn đọc Review
                        </label>
                        <input type="text" name="link" id="link-input" value="{{ old('link', $banner->link) }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="https://gocsach.vn/chi-tiet/nha-gia-kim">
                    </div>

                    <div class="pt-4">
                        <label
                            class="flex items-center justify-between p-4 border dark:border-slate-600 rounded-lg bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 cursor-pointer hover:shadow-md transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                                    <i class="fas fa-eye text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 dark:text-white block">Hiển thị trên Trang
                                        Chủ</span>
                                </div>
                            </div>
                            {{-- Toggle Switch --}}
                            <div class="relative">
                                <input type="checkbox" name="is_active" id="toggle-active" {{ $banner->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-14 h-7 bg-gray-300 dark:bg-slate-500 rounded-full peer peer-checked:bg-green-500 transition-colors duration-300">
                                </div>
                                <div
                                    class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300 peer-checked:translate-x-7">
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                <a href="{{ route('admin.banners.index') }}"
                    class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">Hủy</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>

    <script>
        // Show warning function
        function showWarning(message) {
            const alert = document.getElementById('warning-alert');
            const msgEl = document.getElementById('warning-message');
            if (alert && msgEl) {
                msgEl.textContent = message;
                alert.classList.remove('hidden');
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
                setTimeout(dismissWarning, 5000);
            }
        }

        function dismissWarning() {
            const alert = document.getElementById('warning-alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.classList.add('hidden'), 500);
            }
        }

        // Tab switching for upload type
        function showUploadTab(type) {
            document.getElementById('upload-file').classList.add('hidden');
            document.getElementById('upload-url').classList.add('hidden');
            document.getElementById('tab-file').classList.remove('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            document.getElementById('tab-file').classList.add('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
            document.getElementById('tab-url').classList.remove('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            document.getElementById('tab-url').classList.add('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');

            if (type === 'file') {
                document.getElementById('upload-file').classList.remove('hidden');
                document.getElementById('tab-file').classList.remove('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
                document.getElementById('tab-file').classList.add('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            } else {
                document.getElementById('upload-url').classList.remove('hidden');
                document.getElementById('tab-url').classList.remove('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
                document.getElementById('tab-url').classList.add('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            }
        }

        // Preview file from input
        function previewFile() {
            const fileInput = document.getElementById('image-file');
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    showPreview(e.target.result);
                }
                reader.readAsDataURL(file);
            }
        }

        // Preview URL
        function previewUrl() {
            const url = document.getElementById('image-url').value.trim();
            if (url) {
                showPreview(url);
            } else {
                hidePreview();
            }
        }

        // Show preview
        function showPreview(src) {
            const container = document.getElementById('image-preview-container');
            const img = document.getElementById('image-preview');
            img.src = src;
            container.classList.remove('hidden');
        }

        // Hide preview
        function hidePreview() {
            const container = document.getElementById('image-preview-container');
            container.classList.add('hidden');
        }

        // Clear preview and inputs
        function clearPreview() {
            document.getElementById('image-file').value = '';
            document.getElementById('image-url').value = '';
            hidePreview();
        }
    </script>
@endsection