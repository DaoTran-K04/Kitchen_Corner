@extends('layouts.app')

@section('title', 'Đăng công thức – Góc Bếp')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('recipes.list') }}" class="text-gray-400 hover:text-green-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-800">🍳 Đăng công thức mới</h1>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5">{{ session('error') }}</div>
    @endif

    <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" id="recipeForm">
        @csrf

        {{-- ===== THÔNG TIN CƠ BẢN ===== --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-6 space-y-5">
            <h2 class="font-bold text-gray-700 text-lg border-b pb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-green-500"></i> Thông tin cơ bản
            </h2>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tên công thức <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="VD: Phở bò tái Hà Nội truyền thống"
                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none @error('title') border-red-400 @enderror">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Độ khó <span class="text-red-500">*</span></label>
                    <select name="difficulty" required class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                        <option value="easy"   {{ old('difficulty','easy')=='easy'  ?'selected':'' }}>🟢 Dễ</option>
                        <option value="medium" {{ old('difficulty')=='medium'?'selected':'' }}>🟡 Trung bình</option>
                        <option value="hard"   {{ old('difficulty')=='hard'  ?'selected':'' }}>🔴 Khó</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Thời gian nấu (phút)</label>
                    <input type="number" name="cooking_time" value="{{ old('cooking_time') }}" min="1" max="1440"
                        placeholder="VD: 45"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Mô tả ngắn</label>
                <textarea name="description" rows="3"
                    placeholder="Mô tả ngắn về nguồn gốc, đặc điểm hoặc cảm hứng của công thức này..."
                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Ảnh bìa --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Ảnh bìa</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition" onclick="document.getElementById('coverImage').click()">
                    <img id="coverPreview" src="" alt="" class="mx-auto max-h-48 rounded-xl mb-3 hidden object-cover">
                    <div id="coverPlaceholder">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-400 text-sm">Nhấn để chọn ảnh (JPG, PNG, WebP – tối đa 3MB)</p>
                    </div>
                    <input type="file" id="coverImage" name="image" accept="image/*" class="hidden" onchange="previewCover(this)">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Trạng thái đăng</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="pending" {{ old('status','pending')=='pending'?'checked':'' }} class="accent-green-600">
                        <span class="text-sm font-medium text-green-700">🌍 Gửi yêu cầu phê duyệt</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="draft" {{ old('status')=='draft'?'checked':'' }} class="accent-gray-500">
                        <span class="text-sm font-medium text-gray-500">📝 Lưu nháp</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ===== NGUYÊN LIỆU ===== --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 text-lg border-b pb-3 mb-4 flex items-center gap-2">
                <i class="fas fa-shopping-basket text-orange-500"></i> Nguyên liệu
                <span class="text-xs text-gray-400 font-normal">(hệ thống sẽ tự tính dinh dưỡng)</span>
            </h2>

            <div id="ingredientsList" class="space-y-3">
                {{-- Dòng 1 mặc định --}}
                <div class="ingredient-row grid grid-cols-12 gap-2 items-center">
                    <div class="col-span-5">
                        <input type="text" name="ingredients[0][name]" list="ingredientOptions" placeholder="-- Gõ để chọn hoặc điền mới --"
                            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none ingredient-input">
                        <input type="hidden" name="ingredients[0][id]" class="ingredient-id">
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="ingredients[0][quantity]" placeholder="Số lượng" step="0.1" min="0"
                            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                    </div>
                    <div class="col-span-4">
                        <input type="text" name="ingredients[0][notes]" placeholder="Ghi chú (tùy chọn)"
                            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-lg transition" title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addIngredient()"
                class="mt-4 flex items-center gap-2 text-green-600 hover:text-green-800 font-semibold text-sm transition">
                <i class="fas fa-plus-circle text-lg"></i> Thêm nguyên liệu
            </button>
        </div>

        {{-- ===== CÁC BƯỚC NẤU ===== --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 text-lg border-b pb-3 mb-4 flex items-center gap-2">
                <i class="fas fa-list-ol text-blue-500"></i> Các bước thực hiện
            </h2>

            <div id="stepsList" class="space-y-4">
                <div class="step-row border border-gray-100 rounded-xl p-4 bg-gray-50" data-step="1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-7 h-7 rounded-full bg-green-600 text-white font-bold flex items-center justify-center text-sm step-num">1</span>
                        <span class="font-semibold text-gray-700 text-sm">Bước 1</span>
                        <button type="button" onclick="removeStep(this)" class="ml-auto text-red-400 hover:text-red-600 text-sm transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <textarea name="steps[0][instruction]" rows="2"
                        placeholder="Mô tả bước này..."
                        class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none mb-2"></textarea>
                    <input type="file" name="steps[0][image]" accept="image/*" class="text-xs text-gray-400">
                </div>
            </div>

            <button type="button" onclick="addStep()"
                class="mt-4 flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold text-sm transition">
                <i class="fas fa-plus-circle text-lg"></i> Thêm bước
            </button>
        </div>

        {{-- SUBMIT --}}
        <div class="flex items-center gap-4">
            <button type="submit" class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white font-extrabold py-3 px-10 rounded-xl transition shadow-lg text-lg">
                <i class="fas fa-paper-plane mr-2"></i> Đăng công thức
            </button>
            <a href="{{ route('recipes.list') }}" class="text-gray-500 hover:text-gray-700 text-sm">Hủy</a>
        </div>
        <datalist id="ingredientOptions">
            @foreach($ingredients as $ing)
            <option value="{{ $ing->name }}" data-id="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
            @endforeach
        </datalist>
    </form>
</div>

@push('scripts')
<script>
// Biến đếm index động
let ingIndex = 1;
let stepIndex = 1;

// Preview ảnh bìa
function previewCover(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById('coverPreview');
            img.src = e.target.result;
            img.classList.remove('hidden');
            document.getElementById('coverPlaceholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addIngredient() {
    const i = ingIndex++;
    const html = `
    <div class="ingredient-row grid grid-cols-12 gap-2 items-center">
        <div class="col-span-5">
            <input type="text" name="ingredients[${i}][name]" list="ingredientOptions" placeholder="-- Gõ để chọn hoặc điền mới --"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none ingredient-input">
            <input type="hidden" name="ingredients[${i}][id]" class="ingredient-id">
        </div>
        <div class="col-span-2">
            <input type="number" name="ingredients[${i}][quantity]" placeholder="Số lượng" step="0.1" min="0"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>
        <div class="col-span-4">
            <input type="text" name="ingredients[${i}][notes]" placeholder="Ghi chú (tùy chọn)"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-lg transition">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>`;
    document.getElementById('ingredientsList').insertAdjacentHTML('beforeend', html);
    attachIngredientListener();
}

function attachIngredientListener() {
    // Lắng nghe sự kiện input để gán ID nếu chọn từ list
    document.querySelectorAll('.ingredient-input').forEach(input => {
        input.oninput = function() {
            const val = this.value;
            const hidden = this.nextElementSibling;
            const options = document.getElementById('ingredientOptions').childNodes;
            hidden.value = ""; // Reset
            for(let i = 0; i < options.length; i++) {
                if(options[i].value === val) {
                    hidden.value = options[i].getAttribute('data-id');
                    break;
                }
            }
        };
    });
}
// Chạy lần đầu
attachIngredientListener();

function removeRow(btn) {
    btn.closest('.ingredient-row').remove();
}

// Thêm bước nấu
function addStep() {
    const i = stepIndex++;
    const num = document.querySelectorAll('.step-row').length + 1;
    const html = `
    <div class="step-row border border-gray-100 rounded-xl p-4 bg-gray-50" data-step="${num}">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-7 h-7 rounded-full bg-green-600 text-white font-bold flex items-center justify-center text-sm step-num">${num}</span>
            <span class="font-semibold text-gray-700 text-sm">Bước ${num}</span>
            <button type="button" onclick="removeStep(this)" class="ml-auto text-red-400 hover:text-red-600 text-sm transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <textarea name="steps[${i}][instruction]" rows="2"
            placeholder="Mô tả bước này..."
            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none mb-2"></textarea>
        <input type="file" name="steps[${i}][image]" accept="image/*" class="text-xs text-gray-400">
    </div>`;
    document.getElementById('stepsList').insertAdjacentHTML('beforeend', html);
}

function removeStep(btn) {
    btn.closest('.step-row').remove();
    // Cập nhật số bước
    document.querySelectorAll('.step-row').forEach((row, idx) => {
        row.querySelector('.step-num').textContent = idx + 1;
    });
}
</script>
@endpush
@endsection
