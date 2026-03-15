@extends('layouts.admin')
@section('title', 'Thêm Sách Mới')
@section('header', 'Thêm sách mới')

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
        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" id="book-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Cột Trái --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tên sách <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="Nhập tên sách...">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tác giả <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="author_name" value="{{ old('author_name') }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="Nhập tên tác giả (Ví dụ: Nam Cao)">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Nhập nhiều tác giả phân cách bằng dấu phẩy hoặc xuống dòng.
                            Các tác giả mới sẽ được tạo tự động trong cơ sở dữ liệu.</p>
                        @error('author_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Danh mục (Chọn
                            nhiều) <span class="text-red-500">*</span></label>
                        <div
                            class="h-48 overflow-y-auto border dark:border-slate-600 rounded-lg p-3 bg-gray-50 dark:bg-slate-700 grid grid-cols-2 gap-2">
                            @foreach($categories as $cat)
                                <label
                                    class="flex items-center space-x-2 cursor-pointer hover:bg-white dark:hover:bg-slate-600 p-1 rounded">
                                    <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                                        class="w-4 h-4 text-blue-600 rounded border-gray-300 dark:border-slate-500 focus:ring-blue-500"
                                        {{ in_array($cat->id, old('category_ids', [])) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-slate-300">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('category_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Cột Phải --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Nhà xuất bản</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="VD: NXB Trẻ">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Năm xuất bản</label>
                        @include('admin.partials.custom-pickers', [
                            'type' => 'year',
                            'name' => 'published_year',
                            'value' => old('published_year'),
                            'placeholder' => 'Chọn năm xuất bản...'
                        ])
                    </div>

                    {{-- Ảnh bìa với Tabs (Đồng bộ với Banners/Articles) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ảnh bìa</label>

                        {{-- Preview ảnh --}}
                        <div id="image-preview-container" class="hidden mb-3">
                            <div
                                class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 w-full h-40 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                                <img id="image-preview" src="" class="max-h-full max-w-full object-contain">
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
                                    <i class="fas fa-upload mr-1"></i> Tải từ máy
                                </button>
                                <button type="button" onclick="showUploadTab('url')" id="tab-url"
                                    class="px-3 py-1 text-xs rounded-full bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-400 font-bold transition">
                                    <i class="fas fa-link mr-1"></i> URL Ngoài
                                </button>
                            </div>

                            {{-- Fixed height container to prevent content jump --}}
                            <div class="min-h-[100px]">
                                <div id="upload-file">
                                    <input type="file" name="cover_image" id="image-file"
                                        accept=".jpg,.jpeg,.png,.webp,.gif,.svg" onchange="previewFile()"
                                        class="w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/70">
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">
                                        Hỗ trợ: PNG, JPG, GIF, WebP, SVG.
                                    </p>
                                </div>

                                <div id="upload-url" class="hidden">
                                    <input type="url" name="cover_image_url" id="image-url" value="{{ old('cover_image_url') }}"
                                        oninput="previewUrl()"
                                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                        placeholder="https://gocsach.vn/sach.png">
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Dán đường dẫn trực tiếp
                                        đến file ảnh (.png, .jpg, .gif,...)</p>
                                </div>
                            </div>
                        </div>

                        @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('cover_image_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Mô tả nội dung</label>
                    <textarea name="description" rows="5"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-y min-h-[120px]"
                        placeholder="Giới thiệu về nội dung cuốn sách...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                <a href="{{ route('admin.books.index') }}"
                    class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">Hủy</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i> Thêm sách
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

        // Dismiss warning function
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