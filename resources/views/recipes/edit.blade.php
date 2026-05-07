@extends('layouts.app')

@section('title', 'Chỉnh sửa công thức – Góc Bếp')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('recipes.show', $recipe->slug) }}" class="text-gray-400 hover:text-green-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-800">🍳 Chỉnh sửa công thức</h1>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5">{{ session('error') }}</div>
    @endif

    <form action="{{ route('recipes.update', $recipe->id) }}" method="POST" enctype="multipart/form-data" id="recipeForm">
        @csrf
        @method('PUT')

        {{-- ===== THÔNG TIN CƠ BẢN ===== --}}
        <div class="bg-white rounded-2xl shadow p-6 mb-6 space-y-5">
            <h2 class="font-bold text-gray-700 text-lg border-b pb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-green-500"></i> Thông tin cơ bản
            </h2>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Tên công thức <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $recipe->title) }}" required
                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none @error('title') border-red-400 @enderror">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $recipe->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Độ khó <span class="text-red-500">*</span></label>
                    <select name="difficulty" required class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                        <option value="easy"   {{ old('difficulty', $recipe->difficulty) == 'easy'   ? 'selected':'' }}>🟢 Dễ</option>
                        <option value="medium" {{ old('difficulty', $recipe->difficulty) == 'medium' ? 'selected':'' }}>🟡 Trung bình</option>
                        <option value="hard"   {{ old('difficulty', $recipe->difficulty) == 'hard'   ? 'selected':'' }}>🔴 Khó</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Thời gian nấu (phút)</label>
                    <input type="number" name="cooking_time" value="{{ old('cooking_time', $recipe->cooking_time) }}" min="1" max="1440"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Mô tả ngắn</label>
                <textarea name="description" rows="3"
                    class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-400 focus:outline-none resize-none">{{ old('description', $recipe->description) }}</textarea>
            </div>

            {{-- Ảnh bìa --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Ảnh bìa</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center cursor-pointer hover:border-green-400 transition" onclick="document.getElementById('coverImage').click()">
                    @if($recipe->image)
                        <img id="coverPreview" src="{{ asset('storage/' . $recipe->image) }}" alt="{{ $recipe->title }}" class="mx-auto max-h-48 rounded-xl mb-3 object-cover">
                        <div id="coverPlaceholder" class="hidden">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-400 text-sm">Nhấn để thay đổi ảnh</p>
                        </div>
                    @else
                        <img id="coverPreview" src="" alt="" class="mx-auto max-h-48 rounded-xl mb-3 hidden object-cover">
                        <div id="coverPlaceholder">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-400 text-sm">Nhấn để chọn ảnh (JPG, PNG, WebP – tối đa 3MB)</p>
                        </div>
                    @endif
                    <input type="file" id="coverImage" name="image" accept="image/*" class="hidden" onchange="previewCover(this)">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1">Trạng thái đăng</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="published" {{ old('status', $recipe->status) == 'published' ? 'checked':'' }} class="accent-green-600">
                        <span class="text-sm font-medium text-green-700">🌍 Công khai (Cần phê duyệt)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="draft" {{ old('status', $recipe->status) == 'draft' ? 'checked':'' }} class="accent-gray-500">
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
                @php $oldIngredients = old('ingredients', $recipe->ingredients->toArray()); @endphp
                @if(count($oldIngredients) > 0)
                    @foreach($oldIngredients as $idx => $ing)
                        @php 
                            $ingId = is_array($ing) ? ($ing['id'] ?? ($ing['pivot']['ingredient_id'] ?? '')) : ($ing->id ?? ''); 
                            $ingQty = is_array($ing) ? ($ing['quantity'] ?? ($ing['pivot']['quantity'] ?? '')) : ''; 
                            $ingNotes = is_array($ing) ? ($ing['notes'] ?? ($ing['pivot']['notes'] ?? '')) : ''; 
                        @endphp
                        <div class="ingredient-row grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-5">
                                <select name="ingredients[{{ $idx }}][id]" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                                    <option value="">-- Chọn nguyên liệu --</option>
                                    @foreach($ingredients as $ai)
                                    <option value="{{ $ai->id }}" {{ $ingId == $ai->id ? 'selected' : '' }}>{{ $ai->name }} ({{ $ai->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="number" name="ingredients[{{ $idx }}][quantity]" value="{{ $ingQty }}" placeholder="Số lượng" step="0.1" min="0"
                                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                            </div>
                            <div class="col-span-4">
                                <input type="text" name="ingredients[{{ $idx }}][notes]" value="{{ $ingNotes }}" placeholder="Ghi chú"
                                    class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                            </div>
                            <div class="col-span-1 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-lg transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Dòng mặc định --}}
                    <div class="ingredient-row grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-5">
                            <select name="ingredients[0][id]" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                                <option value="">-- Chọn nguyên liệu --</option>
                                @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="ingredients[0][quantity]" placeholder="Số" step="0.1" min="0" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                        </div>
                        <div class="col-span-4">
                            <input type="text" name="ingredients[0][notes]" placeholder="Ghi chú" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                        </div>
                        <div class="col-span-1 text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-lg transition"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                @endif
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
                @php $oldSteps = old('steps', $recipe->steps->toArray()); @endphp
                @if(count($oldSteps) > 0)
                    @foreach($oldSteps as $idx => $step)
                        @php $instruction = is_array($step) ? ($step['instruction'] ?? '') : ($step->instruction ?? ''); @endphp
                        <div class="step-row border border-gray-100 rounded-xl p-4 bg-gray-50" data-step="{{ $idx + 1 }}">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-7 h-7 rounded-full bg-green-600 text-white font-bold flex items-center justify-center text-sm step-num">{{ $idx + 1 }}</span>
                                <span class="font-semibold text-gray-700 text-sm">Bước {{ $idx + 1 }}</span>
                                <button type="button" onclick="removeStep(this)" class="ml-auto text-red-400 hover:text-red-600 text-sm transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <textarea name="steps[{{ $idx }}][instruction]" rows="2"
                                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none mb-2">{{ $instruction }}</textarea>
                            <div class="flex items-center gap-3">
                                <input type="file" name="steps[{{ $idx }}][image]" accept="image/*" class="text-xs text-gray-400">
                                @if(is_array($step) && isset($step['image']) && is_string($step['image']))
                                    <img src="{{ asset('storage/' . $step['image']) }}" alt="Ảnh bước {{ $idx + 1 }}" class="h-10 w-10 object-cover rounded-md border">
                                @elseif(isset($step['image']) && is_string($step['image']))
                                    <img src="{{ asset('storage/' . $step['image']) }}" alt="Ảnh bước {{ $idx + 1 }}" class="h-10 w-10 object-cover rounded-md border">
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="step-row border border-gray-100 rounded-xl p-4 bg-gray-50" data-step="1">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-full bg-green-600 text-white font-bold flex items-center justify-center text-sm step-num">1</span>
                            <span class="font-semibold text-gray-700 text-sm">Bước 1</span>
                            <button type="button" onclick="removeStep(this)" class="ml-auto text-red-400 hover:text-red-600 text-sm transition"><i class="fas fa-times"></i></button>
                        </div>
                        <textarea name="steps[0][instruction]" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none mb-2"></textarea>
                        <input type="file" name="steps[0][image]" accept="image/*" class="text-xs text-gray-400">
                    </div>
                @endif
            </div>

            <button type="button" onclick="addStep()"
                class="mt-4 flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold text-sm transition">
                <i class="fas fa-plus-circle text-lg"></i> Thêm bước
            </button>
        </div>

        {{-- SUBMIT --}}
        <div class="flex items-center gap-4">
            <button type="submit" class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white font-extrabold py-3 px-10 rounded-xl transition shadow-lg text-lg">
                <i class="fas fa-save mr-2"></i> Lưu thay đổi
            </button>
            <a href="{{ route('recipes.show', $recipe->slug) }}" class="text-gray-500 hover:text-gray-700 text-sm">Hủy</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let ingIndex = {{ count(old('ingredients', $recipe->ingredients->toArray())) > 0 ? count(old('ingredients', $recipe->ingredients->toArray())) : 1 }};
let stepIndex = {{ count(old('steps', $recipe->steps->toArray())) > 0 ? count(old('steps', $recipe->steps->toArray())) : 1 }};

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

const ingredientOptions = `{!! addslashes(implode('', $ingredients->map(fn($i) => "<option value='{$i->id}'>{$i->name} ({$i->unit})</option>")->toArray())) !!}`;

function addIngredient() {
    const i = ingIndex++;
    const html = `
    <div class="ingredient-row grid grid-cols-12 gap-2 items-center">
        <div class="col-span-5">
            <select name="ingredients[${i}][id]" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
                <option value="">-- Chọn nguyên liệu --</option>
                ${ingredientOptions}
            </select>
        </div>
        <div class="col-span-2">
            <input type="number" name="ingredients[${i}][quantity]" placeholder="Số lượng" step="0.1" min="0"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>
        <div class="col-span-4">
            <input type="text" name="ingredients[${i}][notes]" placeholder="Ghi chú"
                class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none">
        </div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600 text-lg transition">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>`;
    document.getElementById('ingredientsList').insertAdjacentHTML('beforeend', html);
}

function removeRow(btn) {
    btn.closest('.ingredient-row').remove();
}

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
            class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:outline-none resize-none mb-2"></textarea>
        <input type="file" name="steps[${i}][image]" accept="image/*" class="text-xs text-gray-400">
    </div>`;
    document.getElementById('stepsList').insertAdjacentHTML('beforeend', html);
}

function removeStep(btn) {
    btn.closest('.step-row').remove();
    document.querySelectorAll('.step-row').forEach((row, idx) => {
        row.querySelector('.step-num').textContent = idx + 1;
    });
}
</script>
@endpush
@endsection
