@extends('layouts.app')

@section('title', 'Tác Giả - Góc Sách')

@section('content')
<section class="container mx-auto px-4 py-12">
    {{-- Header --}}
    <div class="text-center mb-10">
        <h1 class="text-4xl md:text-5xl font-bold font-serif text-gray-800 mb-3">Tác Giả</h1>
        <p class="text-gray-500 text-lg">Khám phá các tác giả được độc giả yêu thích</p>
        <div class="mt-4 inline-flex items-center gap-2 bg-brand-green/10 text-brand-green px-4 py-2 rounded-full text-sm font-medium">
            <i class="fas fa-users"></i>
            {{ $authors->total() ?? 0 }} tác giả
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="max-w-2xl mx-auto mb-10">
        <form method="GET" action="{{ route('authors.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="q" value="{{ request('q') }}" 
                    placeholder="Tìm tác giả theo tên..." 
                    class="w-full bg-white border-2 border-gray-100 rounded-2xl pl-12 pr-4 py-4 text-base shadow-sm focus:outline-none focus:border-brand-green transition">
            </div>
            <select name="sort" class="bg-white border-2 border-gray-100 rounded-2xl px-5 py-4 text-base shadow-sm focus:outline-none focus:border-brand-green">
                <option value="popular" {{ request('sort','popular')=='popular' ? 'selected':'' }}>Nổi bật</option>
                <option value="name" {{ request('sort')=='name' ? 'selected':'' }}>A → Z</option>
            </select>
            <button class="bg-brand-green text-white px-8 py-4 rounded-2xl text-base font-semibold hover:bg-brand-green/90 transition shadow-lg hover:shadow-xl">
                Tìm
            </button>
        </form>
    </div>

    {{-- Authors Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($authors as $author)
            @php
                $photo = $author->photo ?? null;
                $photoUrl = $photo ? (Str::startsWith($photo, 'http') ? $photo : asset('storage/' . $photo)) : null;
                $authorSlug = $author->author_slug ?? \Str::slug($author->name);
                $colors = ['from-violet-500 to-purple-600', 'from-blue-500 to-cyan-500', 'from-emerald-500 to-teal-600', 'from-orange-500 to-red-500', 'from-pink-500 to-rose-500', 'from-indigo-500 to-blue-600'];
                $colorClass = $colors[$loop->index % count($colors)];
            @endphp
            
            <a href="{{ route('authors.show', $authorSlug) }}" class="group block">
                <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    {{-- Header với Gradient --}}
                    <div class="h-28 bg-gradient-to-r {{ $colorClass }} relative">
                        {{-- Pattern overlay --}}
                        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23fff\' fill-opacity=\'1\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'20\' cy=\'20\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E');"></div>
                        
                        {{-- Nationality Badge --}}
                        @if($author->nationality ?? false)
                            <div class="absolute top-3 right-3">
                                <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-medium px-3 py-1 rounded-full">
                                    {{ $author->nationality }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Avatar (overlapping) --}}
                    <div class="flex flex-col items-center -mt-14 relative z-10">
                        <div class="w-28 h-28 rounded-full overflow-hidden ring-4 ring-white shadow-xl">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $author->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br {{ $colorClass }} text-white text-4xl font-bold">
                                    {{ strtoupper(substr($author->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        {{-- Năm sinh - mất ngay dưới ảnh --}}
                        @if($author->birth_year)
                            <div class="mt-2 text-sm font-medium text-gray-500">
                                {{ $author->birth_year }} - {{ $author->death_year ?? 'Nay' }}
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 pt-3 text-center">
                        {{-- Name --}}
                        <h3 class="text-xl font-bold text-gray-800 group-hover:text-brand-green transition-colors">
                            {{ $author->name }}
                        </h3>

                        {{-- Bio --}}
                        <p class="text-sm text-gray-500 mt-3 line-clamp-3 leading-relaxed min-h-[3.75rem]">
                            @if($author->bio ?? false)
                                {{ $author->bio }}
                            @else
                                <span class="italic text-gray-400">Chưa có tiểu sử</span>
                            @endif
                        </p>

                        {{-- Stats --}}
                        <div class="flex justify-center mt-4 pt-4 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-800">{{ $author->books_count }}</div>
                                <div class="text-xs text-gray-400 uppercase tracking-wide">Tác phẩm</div>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="mt-5">
                            <span class="inline-flex items-center gap-2 bg-gray-50 group-hover:bg-brand-green text-gray-600 group-hover:text-white px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300">
                                Xem chi tiết
                                <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-3xl p-16 text-center shadow-sm">
                    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-user-slash text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Không tìm thấy tác giả nào</h3>
                    <p class="text-gray-500">Thử tìm kiếm với từ khóa khác</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($authors->hasPages())
        <div class="mt-12 flex justify-center">
            <div class="inline-flex items-center gap-2 bg-white rounded-2xl p-2 shadow-sm">
                {{-- Previous --}}
                @if($authors->onFirstPage())
                    <span class="w-12 h-12 flex items-center justify-center rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $authors->appends(request()->query())->previousPageUrl() }}" 
                       class="w-12 h-12 flex items-center justify-center rounded-xl bg-white text-gray-600 hover:bg-brand-green hover:text-white transition-all">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @php
                    $start = max($authors->currentPage() - 2, 1);
                    $end = min($start + 4, $authors->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @for($i = $start; $i <= $end; $i++)
                    @if($i == $authors->currentPage())
                        <span class="w-12 h-12 flex items-center justify-center rounded-xl bg-brand-green text-white font-bold shadow-lg">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $authors->appends(request()->query())->url($i) }}" 
                           class="w-12 h-12 flex items-center justify-center rounded-xl bg-white text-gray-600 hover:bg-gray-50 transition-all font-medium">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- Next --}}
                @if($authors->hasMorePages())
                    <a href="{{ $authors->appends(request()->query())->nextPageUrl() }}" 
                       class="w-12 h-12 flex items-center justify-center rounded-xl bg-white text-gray-600 hover:bg-brand-green hover:text-white transition-all">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="w-12 h-12 flex items-center justify-center rounded-xl bg-gray-50 text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        
        {{-- Page info --}}
        <div class="text-center mt-4 text-sm text-gray-400">
            Trang {{ $authors->currentPage() }} / {{ $authors->lastPage() }}
        </div>
    @endif
</section>
@endsection
