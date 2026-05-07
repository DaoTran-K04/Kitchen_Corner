@extends('layouts.app')

@section('title', 'Tạp Chí Ẩm Thực – Góc Bếp')

@section('content')
{{-- HERO HEADER --}}
<div class="relative overflow-hidden bg-gradient-to-br from-brand-green via-[#2a4a35] to-[#1e3828] text-white py-16 sm:py-24">
    <div class="absolute inset-0 opacity-20">
        <img src="{{ asset('images/auth/kitchen_1.png') }}" class="w-full h-full object-cover" alt="">
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-brand-green/70 to-brand-green/90"></div>
    <div class="container mx-auto px-4 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 bg-brand-accent/20 border border-brand-accent/30 text-brand-accent text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider mb-5">
            <i class="fas fa-newspaper"></i> Tạp Chí Ẩm Thực
        </span>
        <h1 class="text-4xl sm:text-5xl font-serif font-black mb-4 leading-tight">
            Thế Giới Hương Vị <br>
            <span class="text-brand-accent">Góc Bếp Magazine</span>
        </h1>
        <p class="text-white/70 max-w-xl mx-auto text-base sm:text-lg leading-relaxed">
            Khám phá văn hóa ẩm thực, bí quyết nấu nướng, và câu chuyện đằng sau những món ăn ngon.
        </p>
    </div>
</div>

{{-- TAG FILTER --}}
@if($tags->isNotEmpty())
<div class="bg-white border-b border-gray-100 sticky top-[72px] z-30 shadow-sm">
    <div class="container mx-auto px-4 py-3 flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <a href="{{ route('articles.index') }}"
            class="flex-shrink-0 text-xs font-bold px-4 py-2 rounded-full transition {{ !$tag ? 'bg-brand-green text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas fa-th mr-1"></i>Tất cả
        </a>
        @foreach($tags as $t)
        <a href="{{ route('articles.index', ['tag' => $t]) }}"
            class="flex-shrink-0 text-xs font-bold px-4 py-2 rounded-full transition {{ $tag === $t ? 'bg-brand-green text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $t }}
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ARTICLES GRID --}}
<main class="container mx-auto px-4 py-12">
    @if($articles->count() > 0)

        {{-- Grid of articles --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($articles as $article)
            <a href="{{ route('articles.show', $article->slug) }}"
                class="group bg-white rounded-2xl overflow-hidden shadow hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
                <div class="relative overflow-hidden h-56">
                    <img src="{{ $article->thumbnail ? (Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset('storage/'.$article->thumbnail)) : 'https://images.unsplash.com/photo-1495546992359-f3f5af44a0c0?w=600' }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        alt="{{ $article->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    @if($article->tag)
                    <span class="absolute top-3 left-3 bg-brand-accent/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-md">
                        {{ $article->tag }}
                    </span>
                    @endif
                    <div class="absolute bottom-3 left-3 right-3 flex items-center gap-2 text-white/90 text-[10px] font-medium">
                        <span class="flex items-center gap-1 bg-black/40 backdrop-blur-md px-2 py-1 rounded-md"><i class="far fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                        <span class="flex items-center gap-1 bg-black/40 backdrop-blur-md px-2 py-1 rounded-md"><i class="far fa-eye"></i> {{ number_format($article->view_count ?? 0) }}</span>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="font-serif font-bold text-gray-800 group-hover:text-brand-green transition-colors leading-snug line-clamp-2 text-lg mb-2 flex-1">
                        {{ $article->title }}
                    </h3>
                    @if($article->excerpt)
                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $article->excerpt }}</p>
                    @endif
                    <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-xs font-bold text-brand-green group-hover:text-brand-accent transition-colors">Đọc ngay <i class="fas fa-arrow-right text-[10px] ml-1 group-hover:translate-x-1 transition-transform"></i></span>
                        @if($article->user)
                            <span class="text-[10px] text-gray-400 font-medium italic">Bởi {{ $article->user->name }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10 flex justify-center">
            {{ $articles->links() }}
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-24">
            <div class="inline-block bg-white rounded-3xl shadow-xl px-12 py-14 max-w-md mx-auto">
                <div class="w-20 h-20 bg-brand-green/10 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-newspaper text-3xl text-brand-green"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Chưa có bài viết nào</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">Tạp Chí Ẩm Thực đang trong quá trình chuẩn bị nội dung. Hãy quay lại sớm nhé!</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-brand-green text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#1e3828] transition shadow-md">
                    <i class="fas fa-home"></i> Về Trang Chủ
                </a>
            </div>
        </div>
    @endif
</main>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(155,34,38,0.2); border-radius: 10px; }
</style>
@endsection
