@extends('layouts.app')

@section('title', 'Tìm kiếm công thức – Góc Bếp')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Search bar lớn --}}
    <div class="bg-gradient-to-br from-green-700 to-emerald-500 rounded-2xl p-8 mb-8 text-white text-center">
        <h1 class="text-3xl font-extrabold mb-2">🔍 Tìm kiếm công thức</h1>
        <p class="text-green-100 mb-5">Tìm theo tên món ăn hoặc mô tả</p>
        <form method="GET" action="{{ route('recipes.search') }}" class="flex max-w-2xl mx-auto gap-2">
            <input type="text" name="q" value="{{ $keyword }}"
                placeholder="VD: Phở, bún bò, salad..."
                class="flex-1 px-5 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 shadow">
            <select name="category" class="px-4 py-3 rounded-full text-gray-700 border-0 focus:outline-none shadow text-sm bg-white">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold rounded-full transition shadow">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    {{-- Kết quả --}}
    @if($keyword)
    <div class="mb-4 flex items-center gap-2">
        <span class="text-gray-500 text-sm">
            Tìm kiếm: <strong class="text-green-700">"{{ $keyword }}"</strong>
            – Tìm thấy <strong>{{ $recipes->total() }}</strong> kết quả
        </span>
        @if(request('category'))
        <a href="{{ route('recipes.search', ['q'=>$keyword]) }}" class="text-xs text-red-400 hover:underline">✕ Bỏ lọc danh mục</a>
        @endif
    </div>
    @endif

    {{-- Smart search suggestion --}}
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <i class="fas fa-lightbulb text-orange-500 text-xl"></i>
            <div>
                <p class="font-semibold text-orange-800 text-sm">Gợi ý: Dùng Smart Search!</p>
                <p class="text-orange-600 text-xs">Nhập nguyên liệu có sẵn trong tủ lạnh → Hệ thống gợi ý món phù hợp</p>
            </div>
        </div>
        <a href="{{ route('recipes.smart-search') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition whitespace-nowrap">
            Thử ngay →
        </a>
    </div>

    @if($recipes->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fas fa-search text-5xl mb-4 opacity-30"></i>
        <p class="text-lg">Không tìm thấy công thức nào cho "<strong>{{ $keyword }}</strong>"</p>
        <p class="text-sm mt-2">Thử từ khóa khác hoặc <a href="{{ route('recipes.smart-search') }}" class="text-orange-500 hover:underline">tìm theo nguyên liệu</a>.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($recipes as $recipe)
        <a href="{{ route('recipes.show', $recipe->slug) }}"
            class="group bg-white rounded-2xl shadow hover:shadow-lg overflow-hidden transition-all duration-300 flex flex-col">
            <div class="relative overflow-hidden aspect-[4/3]">
                <img src="{{ $recipe->thumbnail }}"
                    alt="{{ $recipe->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    onerror="this.src='https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400'">
            </div>
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-gray-800 group-hover:text-green-600 transition line-clamp-2 mb-1 text-sm">{{ $recipe->title }}</h3>
                <div class="flex items-center gap-2 text-xs text-gray-400 mt-auto pt-2 border-t border-gray-50">
                    @if($recipe->category)
                    <span class="text-green-600">{{ $recipe->category->name }}</span>
                    @endif
                    @if($recipe->cooking_time)
                    <span>· {{ $recipe->cooking_time }}p</span>
                    @endif
                    <span class="ml-auto"><i class="fas fa-eye"></i> {{ number_format($recipe->view_count) }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    <div class="mt-8">{{ $recipes->links() }}</div>
    @endif
</div>
@endsection
