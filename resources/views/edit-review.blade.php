@extends('layouts.app')

@section('title', 'Chỉnh sửa Review - ' . $post->title)

@section('content')
    {{-- BREADCRUMB --}}
    <div class="bg-brand-beige/30 py-4 border-b border-brand-beige">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('profile', $user->id) }}" class="hover:text-brand-green transition">Hồ Sơ</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold">Chỉnh sửa Review</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 flex-grow min-h-screen">
        
        {{-- KHU VỰC HIỂN THỊ THÔNG BÁO --}}
        <div class="max-w-3xl mx-auto lg:max-w-none">
            {{-- THÔNG BÁO CHỜ DUYỆT LẠI --}}
            <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl shadow-sm flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-xl text-yellow-600 mt-0.5"></i>
                <div>
                    <p class="font-bold">Lưu ý quan trọng</p>
                    <p class="text-sm">Sau khi bạn chỉnh sửa bài viết, bài viết sẽ được chuyển sang trạng thái <strong>"Đang chờ duyệt"</strong> và cần Admin phê duyệt lại trước khi hiển thị công khai.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2 font-bold text-red-800">
                        <i class="fas fa-exclamation-triangle"></i> Không thể cập nhật bài:
                    </div>
                    <ul class="list-disc list-inside text-sm ml-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- CỘT TRÁI: SIDEBAR --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-soft p-6 text-center border border-gray-100 sticky top-24">
                    <div class="relative w-32 h-32 mx-auto mb-4 group">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}" 
                             class="rounded-full border-4 border-brand-beige shadow-md object-cover w-full h-full">
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 font-serif">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm mb-3">{{ $user->email }}</p>
                    <p class="text-gray-600 text-sm italic mb-4 px-2 bg-gray-50 py-2 rounded-lg border border-gray-100 relative">
                        <i class="fas fa-quote-left text-gray-300 absolute top-1 left-1 text-xs"></i>
                        {{ $user->bio ?? 'Thành viên tích cực của Góc Sách.' }}
                    </p>
                    <a href="{{ route('profile', $user->id) }}" class="block w-full border border-gray-300 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại Hồ Sơ
                    </a>
                </div>
            </div>

            {{-- CỘT PHẢI: FORM CHỈNH SỬA REVIEW --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden relative min-h-[500px]">
                    <div class="p-6 sm:p-8 border-b border-gray-100">
                        <h1 class="text-2xl font-bold text-gray-800 font-serif text-brand-green flex items-center gap-2">
                            <i class="fas fa-edit"></i> Chỉnh sửa bài review
                        </h1>
                    </div>

                    <div class="p-6 sm:p-8">
                        <form action="{{ route('reviews.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="rating" id="selected-rating-value" value="{{ $post->rating }}">

                            {{-- 1. Thẻ sách đã chọn (không cho phép thay đổi) --}}
                            <div class="w-full bg-brand-green/5 border border-brand-green/20 rounded-xl p-4 relative flex flex-row items-start gap-4">
                                @php
                                    $cover = $post->book->cover_image ?? null;
                                    $coverUrl = $cover
                                        ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                        : 'https://placehold.co/150x200';
                                @endphp
                                <img src="{{ $coverUrl }}" class="w-16 h-24 flex-shrink-0 object-cover rounded shadow-md bg-white">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-800 text-lg font-serif leading-tight">{{ $post->book->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-user-edit mr-1"></i>{{ $post->book->author_name ?? 'Không rõ tác giả' }}
                                        <span class="mx-1">•</span>
                                        {{ $post->book->published_year ?? '' }}
                                    </p>
                                    <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                                        <i class="fas fa-lock text-gray-400"></i> Không thể thay đổi sách
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Chọn sao --}}
                            <div class="flex flex-col items-center justify-center py-4 border-y border-dashed border-gray-100">
                                <label class="block text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">Đánh giá của bạn</label>
                                
                                {{-- Hiển thị số sao --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex text-2xl gap-0.5" id="star-display">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star {{ $i <= $post->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span id="rating-value" class="text-2xl font-bold text-brand-green">{{ number_format($post->rating, 1) }}</span>
                                </div>
                                
                                {{-- Slider --}}
                                <div class="w-full max-w-xs">
                                    <input type="range" id="rating-slider" min="1" max="5" step="0.1" value="{{ $post->rating }}"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-green"
                                        oninput="updateRating(this.value)">
                                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                                        <span>1.0</span>
                                        <span>2.0</span>
                                        <span>3.0</span>
                                        <span>4.0</span>
                                        <span>5.0</span>
                                    </div>
                                </div>
                                
                                @php
                                    $ratingLabel = 'Tuyệt vời';
                                    if ($post->rating < 2) $ratingLabel = 'Tệ';
                                    elseif ($post->rating < 3) $ratingLabel = 'Không hay';
                                    elseif ($post->rating < 3.5) $ratingLabel = 'Bình thường';
                                    elseif ($post->rating < 4.5) $ratingLabel = 'Hay';
                                @endphp
                                <span id="rating-label" class="mt-3 text-sm font-bold text-brand-green bg-brand-green/10 px-3 py-1 rounded-full">{{ $ratingLabel }}</span>
                            </div>

                            {{-- 3. Nội dung & Hình ảnh --}}
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Tiêu đề bài viết</label>
                                    <input type="text" name="title" required value="{{ old('title', $post->title) }}"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition font-bold text-gray-800 placeholder-gray-400" 
                                        placeholder="Tiêu đề (Ví dụ: Một cuốn sách ám ảnh...)">
                                </div>

                                {{-- KHUNG UPLOAD ẢNH BÌA VỚI TABS --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh bìa bài viết (Tùy chọn)</label>
                                    
                                    {{-- Hiển thị ảnh hiện tại nếu có --}}
                                    @if($post->thumbnail)
                                        <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <p class="text-xs text-gray-500 mb-2"><i class="fas fa-image mr-1"></i> Ảnh bìa hiện tại:</p>
                                            @php
                                                $thumbUrl = Str::startsWith($post->thumbnail, 'http') 
                                                    ? $post->thumbnail 
                                                    : asset('storage/' . $post->thumbnail);
                                            @endphp
                                            <img src="{{ $thumbUrl }}" class="w-32 h-20 object-cover rounded shadow-sm">
                                        </div>
                                    @endif

                                    {{-- Tabs chọn hình thức upload --}}
                                    <div class="flex gap-2 mb-3">
                                        <button type="button" onclick="showThumbnailTab('file')" id="thumb-tab-file"
                                            class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-600 font-bold transition">
                                            <i class="fas fa-upload mr-1"></i> Upload File mới
                                        </button>
                                        <button type="button" onclick="showThumbnailTab('url')" id="thumb-tab-url"
                                            class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-bold transition">
                                            <i class="fas fa-link mr-1"></i> Nhập URL
                                        </button>
                                    </div>

                                    {{-- Upload File --}}
                                    <div id="thumb-upload-file">
                                        <div class="flex items-center justify-center w-full">
                                            <label for="thumbnail-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition relative overflow-hidden group">
                                                {{-- Giao diện mặc định --}}
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6" id="upload-placeholder">
                                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 group-hover:text-brand-green transition"></i>
                                                    <p class="text-sm text-gray-500"><span class="font-semibold">Bấm để tải ảnh lên</span></p>
                                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP, GIF, SVG (Tối đa 2MB)</p>
                                                </div>
                                                
                                                {{-- Ảnh Preview (Ẩn mặc định) --}}
                                                <img id="thumbnail-preview" class="absolute inset-0 w-full h-full object-cover hidden" />
                                                
                                                {{-- Input file ẩn --}}
                                                <input id="thumbnail-upload" name="thumbnail" type="file" class="hidden" accept=".jpg,.jpeg,.png,.webp,.gif,.svg" onchange="previewThumbnail(event)" />
                                                
                                                {{-- Nút xóa ảnh (Hiện khi có ảnh) --}}
                                                <button type="button" id="remove-thumbnail" onclick="removeThumbnail(event)" class="hidden absolute top-2 right-2 bg-white text-red-500 rounded-full p-1 shadow-md hover:bg-red-50 z-20">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Nhập URL --}}
                                    <div id="thumb-upload-url" class="hidden">
                                        <input type="url" name="thumbnail_url" id="thumbnail-url-input"
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green transition"
                                            placeholder="https://example.com/image.jpg"
                                            oninput="previewThumbnailUrl(this.value)">
                                        <p class="text-xs text-gray-400 mt-2">Dán đường dẫn trực tiếp đến file ảnh (.jpg, .png, .gif,...)</p>
                                        
                                        {{-- Preview URL --}}
                                        <div id="url-preview-container" class="hidden mt-3">
                                            <div class="relative rounded-lg overflow-hidden border border-gray-200 w-full h-32 bg-gray-100 flex items-center justify-center">
                                                <img id="url-preview" src="" class="max-h-full max-w-full object-contain">
                                                <button type="button" onclick="clearUrlPreview()" class="absolute top-2 right-2 bg-white text-red-500 rounded-full p-1 shadow-md hover:bg-red-50">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nội dung chi tiết</label>
                                    <textarea name="content" id="editor" rows="10" 
                                        class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-700" 
                                        placeholder="Chia sẻ suy nghĩ chân thật của bạn...">{{ old('content', $post->content) }}</textarea>
                                </div>
                            </div>

                            {{-- 4. Nút bấm --}}
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <a href="{{ route('profile', $user->id) }}" class="px-5 py-2.5 rounded-lg font-bold text-gray-500 hover:bg-gray-100 transition">
                                    Hủy bỏ
                                </a>
                                <button type="submit" class="px-8 py-2.5 rounded-lg font-bold bg-brand-accent text-white shadow-md hover:bg-[#c29263] hover:-translate-y-0.5 transform transition flex items-center gap-2">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>

                        {{-- Script Xử lý Ảnh Preview --}}
                        <script>
                            function previewThumbnail(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById('thumbnail-preview').src = e.target.result;
                                        document.getElementById('thumbnail-preview').classList.remove('hidden');
                                        document.getElementById('upload-placeholder').classList.add('opacity-0');
                                        document.getElementById('remove-thumbnail').classList.remove('hidden');
                                        // Xóa URL input khi chọn file
                                        document.getElementById('thumbnail-url-input').value = '';
                                        clearUrlPreview();
                                    }
                                    reader.readAsDataURL(file);
                                }
                            }

                            function removeThumbnail(event) {
                                event.preventDefault();
                                event.stopPropagation(); // Ngăn chặn mở dialog chọn file
                                document.getElementById('thumbnail-upload').value = ""; // Reset input
                                document.getElementById('thumbnail-preview').src = "";
                                document.getElementById('thumbnail-preview').classList.add('hidden');
                                document.getElementById('upload-placeholder').classList.remove('opacity-0');
                                document.getElementById('remove-thumbnail').classList.add('hidden');
                            }

                            // Chuyển tab upload thumbnail
                            function showThumbnailTab(type) {
                                const fileTab = document.getElementById('thumb-tab-file');
                                const urlTab = document.getElementById('thumb-tab-url');
                                const fileDiv = document.getElementById('thumb-upload-file');
                                const urlDiv = document.getElementById('thumb-upload-url');

                                if (type === 'file') {
                                    fileTab.classList.remove('bg-gray-100', 'text-gray-600');
                                    fileTab.classList.add('bg-blue-100', 'text-blue-600');
                                    urlTab.classList.remove('bg-blue-100', 'text-blue-600');
                                    urlTab.classList.add('bg-gray-100', 'text-gray-600');
                                    fileDiv.classList.remove('hidden');
                                    urlDiv.classList.add('hidden');
                                } else {
                                    urlTab.classList.remove('bg-gray-100', 'text-gray-600');
                                    urlTab.classList.add('bg-blue-100', 'text-blue-600');
                                    fileTab.classList.remove('bg-blue-100', 'text-blue-600');
                                    fileTab.classList.add('bg-gray-100', 'text-gray-600');
                                    urlDiv.classList.remove('hidden');
                                    fileDiv.classList.add('hidden');
                                }
                            }

                            // Preview URL ảnh
                            function previewThumbnailUrl(url) {
                                if (url) {
                                    document.getElementById('url-preview').src = url;
                                    document.getElementById('url-preview-container').classList.remove('hidden');
                                    // Xóa file input khi nhập URL
                                    removeThumbnail({ preventDefault: ()=>{}, stopPropagation: ()=>{} });
                                } else {
                                    clearUrlPreview();
                                }
                            }

                            // Xóa URL preview
                            function clearUrlPreview() {
                                document.getElementById('thumbnail-url-input').value = '';
                                document.getElementById('url-preview').src = '';
                                document.getElementById('url-preview-container').classList.add('hidden');
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- NHÚNG SCRIPT CKEDITOR --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        /* Tùy chỉnh chiều cao khung soạn thảo */
        .ck-editor__editable_inline {
            min-height: 300px;
        }
    </style>

    {{-- JAVASCRIPT RATING --}}
    <script>
        // --- 1. Khởi tạo CKEditor ---
        ClassicEditor
            .create(document.querySelector('#editor'), {
                placeholder: 'Viết nội dung review của bạn tại đây...',
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });

        // --- 2. Logic Rating ---
        function updateRating(value) {
            const rating = parseFloat(value);
            document.getElementById('selected-rating-value').value = rating;
            document.getElementById('rating-value').innerText = rating.toFixed(1);
            
            // Cập nhật hiển thị sao
            const stars = document.getElementById('star-display').children;
            for(let i=0; i<5; i++) {
                if (rating >= i + 1) {
                    // Sao đầy
                    stars[i].className = 'fas fa-star text-yellow-400';
                } else if (rating > i && rating < i + 1) {
                    // Sao nửa
                    stars[i].className = 'fas fa-star-half-alt text-yellow-400';
                } else {
                    // Sao rỗng
                    stars[i].className = 'far fa-star text-gray-300';
                }
            }
            
            // Cập nhật label
            let label = '';
            if (rating < 2) label = 'Tệ';
            else if (rating < 3) label = 'Không hay';
            else if (rating < 3.5) label = 'Bình thường';
            else if (rating < 4.5) label = 'Hay';
            else label = 'Tuyệt vời';
            
            document.getElementById('rating-label').innerText = label;
        }
    </script>
@endsection
