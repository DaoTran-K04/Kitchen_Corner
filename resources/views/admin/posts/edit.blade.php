@extends('layouts.admin')
@section('title', 'Chỉnh sửa Bài Đăng')
@section('header', 'Chỉnh sửa: ' . Str::limit($post->title, 50))

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 max-w-4xl mx-auto transition-colors duration-300">
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Hidden fields để controller biết đây là edit nội dung --}}
        <input type="hidden" name="edit_content" value="1">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Cột Trái: Thông tin sách & tác giả --}}
            <div class="space-y-4">
                {{-- Thông tin sách (readonly) --}}
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                        <i class="fas fa-book mr-1"></i> Sách được review
                    </label>
                    @php
                        $cover = $post->book->cover_image ?? null;
                        $coverUrl = $cover
                            ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                            : 'https://placehold.co/150x200';
                    @endphp
                    <div class="flex items-start gap-3">
                        <img src="{{ $coverUrl }}" class="w-16 h-24 object-cover rounded shadow-md flex-shrink-0">
                        <div class="min-w-0">
                            <h4 class="font-bold text-gray-800 dark:text-white text-sm line-clamp-2">{{ $post->book->title ?? 'Sách đã xóa' }}</h4>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                <i class="fas fa-user-edit mr-1"></i>{{ $post->book->author_name ?? 'Không rõ' }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">
                                <i class="fas fa-lock mr-1"></i> Không thể thay đổi
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Thông tin người viết --}}
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                        <i class="fas fa-user mr-1"></i> Người viết
                    </label>
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}" 
                             class="w-10 h-10 rounded-full border dark:border-slate-600">
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white text-sm">{{ $post->user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Trạng thái hiện tại --}}
                <div class="bg-gray-50 dark:bg-slate-700 rounded-xl p-4">
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Trạng thái hiện tại
                    </label>
                    @switch($post->status)
                        @case('pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300">
                                <i class="fas fa-clock mr-1"></i> Chờ duyệt
                            </span>
                            @break
                        @case('published')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                <i class="fas fa-check mr-1"></i> Đã đăng
                            </span>
                            @break
                        @case('hidden')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300">
                                <i class="fas fa-eye-slash mr-1"></i> Đang ẩn
                            </span>
                            @break
                        @case('rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                <i class="fas fa-ban mr-1"></i> Từ chối
                            </span>
                            @break
                    @endswitch
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-2 italic">
                        <i class="fas fa-info-circle mr-1"></i> Sau khi lưu, bài viết sẽ tự động được duyệt.
                    </p>
                </div>
            </div>

            {{-- Cột Phải: Form chỉnh sửa nội dung --}}
            <div class="md:col-span-2 space-y-5">
                {{-- Tiêu đề --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Tiêu đề bài viết <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                        placeholder="Nhập tiêu đề bài viết...">
                </div>

                {{-- Đánh giá sao --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Điểm đánh giá <span class="text-red-500">*</span>
                    </label>
                    @include('admin.partials.custom-pickers', [
                        'type' => 'rating',
                        'name' => 'rating',
                        'value' => old('rating', $post->rating)
                    ])
                </div>

                {{-- Ảnh bìa --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ảnh bìa bài viết</label>
                    
                    {{-- Xử lý logic hiển thị ảnh cũ --}}
                    @php
                        $thumbnail = $post->thumbnail;
                        $isUrl = false;
                        if ($thumbnail) {
                            $thumbnail = trim($thumbnail);
                            $isUrl = str_starts_with($thumbnail, 'http');
                            if (!$isUrl) {
                                $thumbnail = asset('storage/' . $thumbnail);
                            }
                        }
                        $thumbSrc = $thumbnail ?: '';
                    @endphp

                    {{-- Preview ảnh --}}
                    <div id="image-preview-container" class="{{ $thumbSrc ? '' : 'hidden' }} mb-3">
                        <div class="relative rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 w-full h-40 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                            <img id="image-preview" src="{{ $thumbSrc }}" class="max-h-full max-w-full object-contain">
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

                        <div class="min-h-[100px]">
                            <div id="upload-file">
                                <input type="file" name="thumbnail" id="image-file"
                                    accept=".jpg,.jpeg,.png,.webp,.gif,.svg" onchange="previewFile()"
                                    class="w-full text-sm text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/50 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/70">
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">
                                    Hỗ trợ: PNG, JPG, GIF, WebP, SVG. Để trống nếu giữ ảnh cũ.
                                </p>
                            </div>

                            <div id="upload-url" class="hidden">
                                <input type="url" name="thumbnail_url" id="image-url" 
                                    value="{{ old('thumbnail_url', $isUrl ? $post->thumbnail : '') }}"
                                    oninput="previewUrl()"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                    placeholder="https://example.com/image.png">
                                <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 italic">
                                    Dán đường dẫn trực tiếp đến file ảnh (.png, .jpg, .gif,...)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nội dung --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Nội dung bài viết <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" id="editor" rows="10"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-y min-h-[200px]"
                        placeholder="Nội dung bài review...">{{ old('content', $post->content) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
            <a href="{{ route('admin.posts.index') }}"
                class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                Hủy
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style>
    .ck-editor__editable_inline { min-height: 200px; }
    .ck.ck-editor__main>.ck-editor__editable { background: transparent; }
</style>

<script>
    // Khởi tạo CKEditor
    ClassicEditor
        .create(document.querySelector('#editor'), {
            placeholder: 'Viết nội dung review...',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo']
        })
        .catch(error => console.error(error));

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
            urlInput.value = '';
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
            fileInput.value = '';
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
