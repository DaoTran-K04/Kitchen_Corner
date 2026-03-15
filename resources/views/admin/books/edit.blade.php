@extends('layouts.admin')
@section('title', 'Cập nhật Sách')
@section('header', 'Cập nhật: ' . $book->title)

@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 max-w-4xl mx-auto transition-colors duration-300">
    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Cột Trái --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tên sách <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                        placeholder="Nhập tên sách...">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tác giả <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="author_name" value="{{ old('author_name', $book->author_name) }}"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                        placeholder="Nhập tên tác giả (Ví dụ: Nam Cao)">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Nhập nhiều tác giả phân cách bằng dấu phẩy hoặc xuống dòng.
                        Các tác giả mới sẽ được tạo tự động trong cơ sở dữ liệu.</p>
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
                                    {{ in_array($cat->id, old('category_ids', $book->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-slate-300">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Cột Phải --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Nhà xuất bản</label>
                    <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                        placeholder="VD: NXB Trẻ">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Năm xuất bản</label>
                    @include('admin.partials.custom-pickers', [
                        'type' => 'year',
                        'name' => 'published_year',
                        'value' => old('published_year', $book->published_year),
                        'placeholder' => 'Chọn năm xuất bản...'
                    ])
                </div>

                {{-- Ảnh bìa với Tabs (Đồng bộ với Create) --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ảnh bìa</label>

                    {{-- Xử lý logic hiển thị ảnh cũ --}}
                    @php
                        $coverImage = $book->cover_image;
                        $isUrl = false;
                        if ($coverImage) {
                            $coverImage = trim($coverImage);
                            $isUrl = str_starts_with($coverImage, 'http');
                            if (!$isUrl) {
                                $coverImage = asset('storage/' . $coverImage);
                            }
                        }
                        $coverSrc = $coverImage ?: '';
                    @endphp

                    {{-- Preview ảnh --}}
                    <div id="image-preview-container" class="{{ $coverSrc ? '' : 'hidden' }} mb-3">
                        <div
                            class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 w-full h-40 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                            <img id="image-preview" src="{{ $coverSrc }}" class="max-h-full max-w-full object-contain">
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
                                    Hỗ trợ: PNG, JPG, GIF, WebP, SVG. Để trống nếu giữ ảnh cũ.
                                </p>
                            </div>

                            <div id="upload-url" class="hidden">
                                <input type="url" name="cover_image_url" id="image-url" value="{{ old('cover_image_url', $isUrl ? $book->cover_image : '') }}"
                                    oninput="previewUrl()"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                    placeholder="https://gocsach.vn/sach.png">
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">Dán đường dẫn trực tiếp
                                    đến file ảnh (.png, .jpg, .gif,...)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Mô tả nội dung</label>
                <textarea name="description" rows="5"
                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-y min-h-[120px]"
                    placeholder="Giới thiệu về nội dung cuốn sách...">{{ old('description', $book->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
            <a href="{{ route('admin.books.index') }}"
                class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">Hủy</a>
            <button type="submit"
                class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i> Cập nhật
            </button>
        </div>
    </form>
</div>

<script>
    // Tab switching
    function showUploadTab(tab) {
        const fileTab = document.getElementById('tab-file');
        const urlTab = document.getElementById('tab-url');
        const fileDiv = document.getElementById('upload-file');
        const urlDiv = document.getElementById('upload-url');

        if (tab === 'file') {
            fileTab.classList.remove('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
            fileTab.classList.add('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            urlTab.classList.remove('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            urlTab.classList.add('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
            fileDiv.classList.remove('hidden');
            urlDiv.classList.add('hidden');
        } else {
            urlTab.classList.remove('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
            urlTab.classList.add('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            fileTab.classList.remove('bg-blue-100', 'dark:bg-blue-900/50', 'text-blue-600', 'dark:text-blue-300');
            fileTab.classList.add('bg-gray-100', 'dark:bg-slate-600', 'text-gray-600', 'dark:text-slate-400');
            urlDiv.classList.remove('hidden');
            fileDiv.classList.add('hidden');
        }
    }

    // Preview file upload
    function previewFile() {
        const input = document.getElementById('image-file');
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');
        const urlInput = document.getElementById('image-url');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
            urlInput.value = ''; // Clear URL input
        }
    }

    // Preview URL
    function previewUrl() {
        const urlInput = document.getElementById('image-url');
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');
        const fileInput = document.getElementById('image-file');

        if (urlInput.value) {
            preview.src = urlInput.value;
            container.classList.remove('hidden');
            fileInput.value = ''; // Clear file input
        }
    }

    // Clear preview
    function clearPreview() {
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('image-preview-container');
        const fileInput = document.getElementById('image-file');
        const urlInput = document.getElementById('image-url');

        preview.src = '';
        container.classList.add('hidden');
        fileInput.value = '';
        urlInput.value = '';
    }
</script>
@endsection