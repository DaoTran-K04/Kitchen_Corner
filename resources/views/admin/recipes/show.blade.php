@extends('layouts.admin')
@section('title', 'Chi Tiết Công Thức')
@section('header', 'Chi Tiết Công Thức')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.recipes.index') }}" class="text-blue-500 hover:underline">
        <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1 space-y-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-5">
            @php
                $recipeImg = trim($recipe->image);
                $displayImg = $recipeImg 
                    ? (str_starts_with($recipeImg, 'http') ? $recipeImg : asset('storage/' . $recipeImg)) 
                    : 'https://images.unsplash.com/photo-1495195129352-aed325a55b65?w=600';
            @endphp
            <img src="{{ $displayImg }}" 
                 alt="{{ $recipe->title }}" 
                 class="w-full h-48 object-cover rounded-xl shadow-md mb-4 border dark:border-slate-700">
            
            <h2 class="text-xl font-bold mb-2">{{ $recipe->title }}</h2>
            <div class="flex gap-2 mb-4">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded shadow-sm">
                    <i class="fas fa-clock"></i> {{ $recipe->cooking_time }} phút
                </span>
                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded shadow-sm">
                    <i class="fas fa-signal"></i> 
                    @if($recipe->difficulty == 'easy') Dễ @elseif($recipe->difficulty == 'medium') Nhỡ @else Khó @endif
                </span>
            </div>

            <p class="text-sm text-gray-600 mb-4">{{ $recipe->description }}</p>

            <ul class="text-sm space-y-2 border-t pt-4">
                <li><strong class="inline-block w-24">Tác giả:</strong> {{ $recipe->user->name }}</li>
                <li><strong class="inline-block w-24">Lượt xem:</strong> {{ $recipe->view_count }}</li>
                <li><strong class="inline-block w-24">Calo:</strong> {{ $recipe->total_calories }} kcal</li>
                <li><strong class="inline-block w-24">Danh mục:</strong> {{ optional($recipe->category)->name }}</li>
            </ul>

            <div class="mt-6 flex flex-wrap gap-2">
                @if($recipe->status == 'draft')
                <form action="{{ route('admin.recipes.approve', $recipe) }}" method="POST" class="inline">
                    @csrf<button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"><i class="fas fa-check mr-1"></i> Duyệt</button>
                </form>
                @endif
                <form action="{{ route('admin.recipes.feature', $recipe) }}" method="POST" class="inline">
                    @csrf<button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600"><i class="fas fa-star mr-1"></i> Đổi nổi bật</button>
                </form>
                <form action="{{ route('admin.recipes.destroy', $recipe) }}" method="POST"
                    class="inline confirm-submit"
                    data-confirm="Bạn có chắc chắn muốn xóa công thức này?">
                    @csrf @method('DELETE')<button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"><i class="fas fa-trash mr-1"></i> Xóa</button>
                </form>
            </div>
        </div>
    </div>

    <div class="md:col-span-2 space-y-6">
        <!-- Nguyên liệu -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-5">
            <h3 class="font-bold border-b pb-2 mb-4 text-orange-500"><i class="fas fa-shopping-basket mr-2"></i>Nguyên liệu</h3>
            <div class="grid grid-cols-2 gap-4">
                @forelse($recipe->ingredients as $in)
                <div class="flex justify-between border-b border-gray-50 pb-2">
                    <span class="font-medium text-gray-700">{{ $in->name }} <small class="text-gray-400">({{ $in->pivot->notes }})</small></span>
                    <span class="text-green-600 font-bold">{{ $in->pivot->quantity }} {{ $in->unit }}</span>
                </div>
                @empty
                <p>Không có nguyên liệu.</p>
                @endforelse
            </div>
        </div>

        <!-- Các bước -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-5">
            <h3 class="font-bold border-b pb-2 mb-4 text-blue-500"><i class="fas fa-list-ol mr-2"></i>Các bước thực hiện</h3>
            <div class="space-y-4">
                @forelse($recipe->steps as $idx => $step)
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                        {{ $idx + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-700 dark:text-slate-300 whitespace-pre-wrap">{{ $step->description }}</p>
                        @if($step->image)
                        @php
                            $stepImg = trim($step->image);
                            $displayStepImg = str_starts_with($stepImg, 'http') ? $stepImg : asset('storage/' . $stepImg);
                        @endphp
                        <img src="{{ $displayStepImg }}" alt="Bước {{ $idx + 1 }}" class="mt-2 w-48 h-32 object-cover rounded-xl shadow-sm border border-gray-100">
                        @endif
                    </div>
                </div>
                @empty
                <p>Chưa có bước thực hiện.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
