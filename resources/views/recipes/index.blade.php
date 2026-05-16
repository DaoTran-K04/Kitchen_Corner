@extends('layouts.app')

@section('title', 'Khám phá Công thức – Góc Bếp')

@section('content')
<div class="min-h-screen">

    {{-- ===== HERO BANNER ===== --}}
    <div class="bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 text-white pt-24 lg:pt-32 pb-14 px-4">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3 drop-shadow">🍽️ Khám phá Công thức</h1>
            <p class="text-green-100 text-lg mb-6">Hàng trăm công thức nấu ăn ngon từ cộng đồng đầu bếp Việt</p>

            {{-- Live search --}}
            @php
                $placeholders = [
                    "Tìm công thức, nguyên liệu...",
                    "Hôm nay ăn gì: Bò lúc lắc...",
                    "Thử tìm: Gà xào sả ớt...",
                    "Gợi ý: Canh chua cá lóc...",
                    "Tìm món ngon cuối tuần...",
                    "Khám phá: Món chay thanh đạm...",
                    "Tủ lạnh có trứng, thịt xay: Trứng đúc thịt...",
                    "Thử làm bánh flan ngọt ngào...",
                    "Gợi ý mâm cơm gia đình: Thịt kho tiêu..."
                ];
                $randomPlaceholder = $placeholders[array_rand($placeholders)];
            @endphp
            <form action="{{ route('recipes.search') }}" method="GET" class="flex max-w-xl mx-auto gap-2">
                <input type="text" name="q" placeholder="{{ $randomPlaceholder }}"
                    class="flex-1 px-5 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 shadow">
                <button type="submit"
                    class="px-6 py-3 bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold rounded-full transition shadow">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- ===== SIDEBAR BỘ LỌC ===== --}}
        <aside class="lg:col-span-1">
            <form method="GET" action="{{ route('recipes.list') }}" id="filterForm">
                <div class="bg-white rounded-2xl shadow p-5 mb-5">
                    <h3 class="font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="fas fa-filter text-green-600"></i> Bộ lọc
                    </h3>

                    {{-- Danh mục --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">📂 Danh mục</label>
                        <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                            <label class="flex items-center gap-2 cursor-pointer hover:text-green-600">
                                <input type="radio" name="category" value=""
                                    {{ !request('category') ? 'checked' : '' }} class="accent-green-600">
                                <span class="text-sm">Tất cả</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-green-600">
                                <input type="radio" name="category" value="{{ $cat->slug }}"
                                    {{ request('category') == $cat->slug ? 'checked' : '' }} class="accent-green-600">
                                <span class="text-sm">{{ $cat->name }}
                                    <span class="text-gray-400">({{ $cat->recipes_count }})</span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Độ khó --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">⚡ Độ khó</label>
                        <select name="difficulty" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                            <option value="">Tất cả</option>
                            <option value="easy"   {{ request('difficulty')=='easy'   ? 'selected':'' }}>🟢 Dễ</option>
                            <option value="medium" {{ request('difficulty')=='medium' ? 'selected':'' }}>🟡 Trung bình</option>
                            <option value="hard"   {{ request('difficulty')=='hard'   ? 'selected':'' }}>🔴 Khó</option>
                        </select>
                    </div>

                    {{-- Thời gian --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">⏱️ Thời gian nấu</label>
                        <select name="cooking_time" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                            <option value="">Tất cả</option>
                            <option value="quick"  {{ request('cooking_time')=='quick'  ? 'selected':'' }}>≤ 20 phút</option>
                            <option value="medium" {{ request('cooking_time')=='medium' ? 'selected':'' }}>21 – 60 phút</option>
                            <option value="long"   {{ request('cooking_time')=='long'   ? 'selected':'' }}>> 60 phút</option>
                        </select>
                    </div>

                    {{-- Sắp xếp --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">📊 Sắp xếp</label>
                        <select name="sort" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-400">
                            <option value="latest"  {{ request('sort','latest')=='latest'  ? 'selected':'' }}>Mới nhất</option>
                            <option value="popular" {{ request('sort')=='popular' ? 'selected':'' }}>Nhiều lượt xem</option>
                            <option value="top"     {{ request('sort')=='top'     ? 'selected':'' }}>Nhiều lượt thích</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 rounded-lg transition">
                        Áp dụng bộ lọc
                    </button>
                    @if(request()->hasAny(['category','difficulty','cooking_time','sort']))
                    <a href="{{ route('recipes.list') }}" class="block text-center text-sm text-gray-500 hover:text-red-500 mt-2">
                        Xóa bộ lọc
                    </a>
                    @endif
                </div>
            </form>

            {{-- Smart Search box --}}
            <div class="bg-gradient-to-br from-orange-50 to-yellow-50 border border-orange-200 rounded-2xl p-5 shadow">
                <h3 class="font-bold text-orange-700 mb-2 flex items-center gap-2">
                    <i class="fas fa-magic"></i> Tìm theo nguyên liệu
                </h3>
                <p class="text-sm text-gray-600 mb-3">Có gì trong tủ lạnh? Gõ vào và hệ thống gợi ý món ngay!</p>
                <a href="{{ route('recipes.smart-search') }}"
                    class="block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg transition">
                    🧊 Thử Smart Search
                </a>
            </div>
        </aside>

        {{-- ===== DANH SÁCH CÔNG THỨC ===== --}}
        <main class="lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <p class="text-gray-500 text-sm">
                    Tìm thấy <span class="font-bold text-green-700">{{ $recipes->total() }}</span> công thức
                </p>
                @auth
                <a href="{{ route('recipes.create') }}"
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow">
                    <i class="fas fa-plus"></i> Đăng công thức
                </a>
                @endauth
            </div>

            @if($recipes->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <i class="fas fa-utensils text-5xl mb-4 opacity-30"></i>
                <p class="text-lg">Chưa có công thức nào. Hãy là người đầu tiên chia sẻ!</p>
                @auth
                <a href="{{ route('recipes.create') }}" class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-full font-bold hover:bg-green-700 transition">
                    Đăng công thức ngay
                </a>
                @endauth
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($recipes as $recipe)
                <a href="{{ route('recipes.show', $recipe->slug) }}"
                    class="group bg-white rounded-2xl shadow hover:shadow-xl overflow-hidden transition-all duration-300 flex flex-col">
                    {{-- Ảnh --}}
                    <div class="relative overflow-hidden aspect-[4/3]">
                        <img src="{{ $recipe->thumbnail }}"
                            alt="{{ $recipe->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            onerror="this.src='https://images.unsplash.com/photo-1560180474-e8563fd75bab?w=600'">
                        {{-- Badges --}}
                        <div class="absolute top-2 left-2 flex gap-1 flex-wrap">
                            @if($recipe->is_featured)
                            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">⭐ Nổi bật</span>
                            @endif
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                {{ $recipe->difficulty=='easy' ? 'bg-green-100 text-green-700' : ($recipe->difficulty=='hard' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $recipe->difficulty=='easy' ? '🟢 Dễ' : ($recipe->difficulty=='hard' ? '🔴 Khó' : '🟡 TB') }}
                            </span>
                        </div>
                        @if($recipe->total_calories)
                        <div class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-0.5 rounded-full backdrop-blur-sm">
                            🔥 {{ $recipe->total_calories }} kcal
                        </div>
                        @endif
                    </div>
                    {{-- Nội dung --}}
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center gap-1 text-xs text-gray-400 mb-1">
                            @if($recipe->category)
                            <span class="text-green-600 font-medium">{{ $recipe->category->name }}</span>
                            <span>•</span>
                            @endif
                            @if($recipe->cooking_time)
                            <span><i class="fas fa-clock"></i> {{ $recipe->cooking_time }} phút</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-800 group-hover:text-green-600 transition line-clamp-2 mb-2 leading-snug">
                            {{ $recipe->title }}
                        </h3>
                        <p class="text-gray-500 text-xs line-clamp-2 flex-1 mb-3">{{ $recipe->description }}</p>
                        <div class="flex items-center justify-between text-xs text-gray-400 mt-auto pt-2 border-t border-gray-50">
                            <span class="flex items-center gap-1">
                                <img src="{{ $recipe->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($recipe->user->name).'&size=20' }}"
                                    class="w-5 h-5 rounded-full object-cover" alt="">
                                {{ Str::limit($recipe->user->name, 15) }}
                            </span>
                            <span class="flex items-center gap-2">
                                <span><i class="fas fa-eye"></i> {{ number_format($recipe->view_count) }}</span>
                            </span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
            @endif
        </main>
    </div>
</div>
@endsection
