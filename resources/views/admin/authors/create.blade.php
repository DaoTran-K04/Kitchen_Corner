@extends('layouts.admin')
@section('title', 'Thêm Tác Giả Mới')
@section('header', 'Thêm Tác Giả Mới')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <style>
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }

        .zoom-slider {
            -webkit-appearance: none;
            background: #e5e7eb;
            border-radius: 8px;
        }

        .zoom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
        }

        .zoom-slider::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }

        .dark .zoom-slider {
            background: #475569;
        }
    </style>

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 max-w-2xl mx-auto">
        <form action="{{ route('admin.authors.store') }}" method="POST" enctype="multipart/form-data"
            onsubmit="return validateYears()">
            @csrf
            <input type="hidden" name="cropped_photo" id="cropped-photo">

            <div class="space-y-5">
                {{-- Tên --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tên tác giả <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', request('name')) }}"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                        placeholder="VD: Nguyễn Nhật Ánh" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ảnh --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Ảnh đại diện</label>

                    {{-- Preview --}}
                    <div id="preview-section" class="hidden mb-3">
                        <div
                            class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <img id="cropped-preview" src=""
                                class="w-16 h-16 rounded-full object-cover border-2 border-green-400">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-700 dark:text-green-300">Ảnh đã sẵn sàng</p>
                                <button type="button" onclick="clearCroppedImage()"
                                    class="text-xs text-red-500 hover:underline">Xóa</button>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <div class="flex gap-2 mb-3">
                        <button type="button" onclick="showUploadTab('file')" id="tab-file"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-500 text-white">
                            <i class="fas fa-upload mr-1"></i>Từ máy
                        </button>
                        <button type="button" onclick="showUploadTab('url')" id="tab-url"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300">
                            <i class="fas fa-link mr-1"></i>URL
                        </button>
                    </div>

                    {{-- File --}}
                    <div id="upload-file">
                        <input type="file" name="photo_file" id="photo-file" accept="image/*" onchange="openCropper(this)"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-600 dark:file:text-blue-400 file:font-medium">
                        <p class="text-xs text-gray-400 mt-1 italic">PNG, JPG, GIF, WebP</p>
                    </div>

                    {{-- URL --}}
                    <div id="upload-url" class="hidden">
                        <div class="flex gap-2">
                            <input type="url" name="photo" id="photo-url" value="{{ old('photo') }}"
                                class="flex-1 px-3 py-2 border dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-sm placeholder:italic"
                                placeholder="https://example.com/photo.jpg">
                            <button type="button" onclick="loadUrlImage()"
                                class="px-3 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-crop-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Năm --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Năm sinh</label>
                        @include('admin.partials.custom-pickers', ['type' => 'year', 'name' => 'birth_year', 'value' => old('birth_year'), 'placeholder' => 'Chọn...'])
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Năm mất</label>
                        @include('admin.partials.custom-pickers', ['type' => 'year', 'name' => 'death_year', 'value' => old('death_year'), 'placeholder' => 'Còn sống...'])
                    </div>
                </div>

                {{-- Quốc tịch --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Quốc tịch</label>
                    <div class="relative" id="nationality-wrapper">
                        <input type="text" name="nationality" id="nationality-input" value="{{ old('nationality') }}"
                            autocomplete="off"
                            class="w-full px-4 py-2 pl-10 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                            placeholder="Chọn hoặc nhập quốc tịch mới..." onfocus="showNationalitySuggestions()"
                            oninput="filterNationalities()">
                        <i class="fas fa-globe-asia absolute left-3 top-1/2 -translate-y-1/2 text-blue-500"></i>

                        {{-- Custom Dropdown --}}
                        <div id="nationality-dropdown"
                            class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-lg shadow-xl z-50 max-h-48 overflow-y-auto hidden">
                            @foreach($nationalities ?? [] as $nat)
                                <button type="button" onclick="selectNationalityOption('{{ $nat }}')"
                                    class="nationality-option w-full text-left px-4 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/30 text-gray-700 dark:text-slate-300 flex items-center gap-2"
                                    data-value="{{ strtolower($nat) }}">
                                    <i class="fas fa-flag text-gray-400 text-xs"></i>{{ $nat }}
                                </button>
                            @endforeach
                            @if(count($nationalities ?? []) == 0)
                                <p class="px-4 py-3 text-sm text-gray-400 italic">Chưa có quốc tịch nào</p>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-2 italic">
                        <i class="fas fa-lightbulb mr-1"></i>Gõ để tìm quốc tịch đã có hoặc nhập mới
                    </p>
                </div>

                {{-- Bio --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tiểu sử</label>
                    <textarea name="bio" rows="4"
                        class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-y placeholder:italic"
                        placeholder="Giới thiệu về tác giả...">{{ old('bio') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-slate-700">
                <a href="{{ route('admin.authors.index') }}"
                    class="px-5 py-2 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-medium">Hủy</a>
                <button type="submit" class="px-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-medium">
                    <i class="fas fa-save mr-1"></i>Lưu
                </button>
            </div>
        </form>
    </div>

    {{-- Cropper Modal --}}
    <div id="cropper-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70" onclick="closeCropper()"></div>
        <div
            class="absolute inset-4 md:inset-10 bg-white dark:bg-slate-800 rounded-xl flex flex-col overflow-hidden shadow-2xl">
            {{-- Header --}}
            <div
                class="flex items-center justify-between px-5 py-3 border-b dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                <h3 class="font-bold text-gray-800 dark:text-white"><i class="fas fa-crop-alt text-blue-500 mr-2"></i>Cắt
                    ảnh đại diện</h3>
                <button onclick="closeCropper()"
                    class="w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-slate-600 transition flex items-center justify-center">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            {{-- Image --}}
            <div class="flex-1 bg-gray-900 flex items-center justify-center p-4 overflow-hidden">
                <img id="cropper-image" src="" class="max-w-full max-h-full">
            </div>

            {{-- Controls --}}
            <div class="px-5 py-4 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="zoomOut()"
                            class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 flex items-center justify-center">
                            <i class="fas fa-minus text-gray-600 dark:text-white text-xs"></i>
                        </button>
                        <input type="range" id="zoom-slider" min="0.5" max="2" step="0.1" value="1"
                            class="zoom-slider w-32 h-2" oninput="setZoom(this.value)">
                        <button type="button" onclick="zoomIn()"
                            class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 flex items-center justify-center">
                            <i class="fas fa-plus text-gray-600 dark:text-white text-xs"></i>
                        </button>
                        <button type="button" onclick="resetCropper()"
                            class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-slate-600 hover:bg-gray-300 dark:hover:bg-slate-500 flex items-center justify-center ml-2">
                            <i class="fas fa-undo text-gray-600 dark:text-white text-xs"></i>
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeCropper()"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-300 font-medium">Hủy
                            bỏ</button>
                        <button type="button" onclick="applyCrop()"
                            class="px-5 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium">
                            <i class="fas fa-check mr-1"></i>Xác nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
        let cropper = null;
        const MIN_ZOOM = 0.5, MAX_ZOOM = 2;

        function showUploadTab(tab) {
            const fileTab = document.getElementById('tab-file'), urlTab = document.getElementById('tab-url');
            const fileUp = document.getElementById('upload-file'), urlUp = document.getElementById('upload-url');
            if (tab === 'file') {
                fileTab.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-500 text-white';
                urlTab.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300';
                fileUp.classList.remove('hidden'); urlUp.classList.add('hidden');
            } else {
                urlTab.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-500 text-white';
                fileTab.className = 'px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-300';
                urlUp.classList.remove('hidden'); fileUp.classList.add('hidden');
            }
        }

        function openCropper(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => initCropper(e.target.result);
                reader.readAsDataURL(input.files[0]);
            }
        }

        function loadUrlImage() {
            const url = document.getElementById('photo-url').value.trim();
            if (!url) return alert('Vui lòng nhập URL ảnh!');

            // Use proxy to avoid CORS issues
            const proxyUrl = "{{ route('admin.authors.proxy-image') }}?url=" + encodeURIComponent(url);

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => initCropper(proxyUrl);
            img.onerror = () => alert('Không thể tải ảnh từ URL này (hoặc ảnh được bảo vệ)!');
            img.src = proxyUrl;
        }

        function initCropper(src) {
            const img = document.getElementById('cropper-image');
            img.src = src;
            document.getElementById('cropper-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (cropper) cropper.destroy();
            setTimeout(() => {
                cropper = new Cropper(img, {
                    aspectRatio: 1, viewMode: 2, dragMode: 'move', autoCropArea: 0.85,
                    guides: false, center: true, highlight: false, background: false,
                    minCropBoxWidth: 50, minCropBoxHeight: 50,
                    ready() { document.getElementById('zoom-slider').value = 1; }
                });
            }, 100);
        }

        function closeCropper() {
            document.getElementById('cropper-modal').classList.add('hidden');
            document.body.style.overflow = '';
            if (cropper) { cropper.destroy(); cropper = null; }
            if (!document.getElementById('cropped-photo').value) document.getElementById('photo-file').value = '';
        }

        function getZoomLevel() {
            if (!cropper) return 1;
            const d = cropper.getImageData();
            return d.width / d.naturalWidth;
        }

        function zoomIn() {
            if (cropper && getZoomLevel() < MAX_ZOOM) { cropper.zoom(0.1); updateSlider(); }
        }
        function zoomOut() {
            if (cropper && getZoomLevel() > MIN_ZOOM) { cropper.zoom(-0.1); updateSlider(); }
        }
        function setZoom(val) {
            if (cropper) {
                val = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, parseFloat(val)));
                cropper.zoomTo(val);
                document.getElementById('zoom-slider').value = val;
            }
        }
        function updateSlider() {
            document.getElementById('zoom-slider').value = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, getZoomLevel()));
        }
        function resetCropper() {
            if (cropper) { cropper.reset(); document.getElementById('zoom-slider').value = 1; }
        }

        function applyCrop() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({ width: 300, height: 300, imageSmoothingQuality: 'high' });
                const base64 = canvas.toDataURL('image/jpeg', 0.9);
                document.getElementById('cropped-photo').value = base64;
                document.getElementById('cropped-preview').src = base64;
                document.getElementById('preview-section').classList.remove('hidden');
                closeCropper();
            }
        }

        function clearCroppedImage() {
            document.getElementById('cropped-photo').value = '';
            document.getElementById('preview-section').classList.add('hidden');
            document.getElementById('photo-file').value = '';
            document.getElementById('photo-url').value = '';
        }

        // ===== Nationality Dropdown =====
        function showNationalitySuggestions() {
            document.getElementById('nationality-dropdown').classList.remove('hidden');
        }

        function hideNationalitySuggestions() {
            document.getElementById('nationality-dropdown').classList.add('hidden');
        }

        function filterNationalities() {
            const input = document.getElementById('nationality-input').value.toLowerCase();
            const options = document.querySelectorAll('.nationality-option');
            let hasVisible = false;
            options.forEach(opt => {
                const match = opt.dataset.value.includes(input);
                opt.classList.toggle('hidden', !match);
                if (match) hasVisible = true;
            });
            if (input.length > 0) {
                document.getElementById('nationality-dropdown').classList.remove('hidden');
            }
        }

        function selectNationalityOption(value) {
            document.getElementById('nationality-input').value = value;
            hideNationalitySuggestions();
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('nationality-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                hideNationalitySuggestions();
            }
        });
    </script>
@endsection