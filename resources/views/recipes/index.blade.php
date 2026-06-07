@extends('layouts.app')

@section('title', 'Khám phá Công thức – Góc Bếp')

@section('content')
<div class="min-h-screen">

    {{-- ===== HERO BANNER ===== --}}
    <div class="bg-gradient-to-br from-emerald-800 via-teal-700 to-emerald-600 text-white pt-24 lg:pt-32 pb-14 px-4 shadow-inner">
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
            <div id="recipe-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($recipes as $recipe)
                    @include('partials.recipe-card')
                @endforeach
            </div>

            <div class="mt-10 flex justify-center">
                {{ $recipes->links('pagination::tailwind') }}
            </div>
            @endif
        </main>
    </div>
</div>
@endsection

