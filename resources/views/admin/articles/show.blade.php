@extends('layouts.app')

@section('title', $article->title ?? 'Tạp Chí Góc Bếp')

@section('content')
    {{-- CSS riêng cho bài viết --}}
    <style>
        .article-content h2 { font-size: 1.5rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #111827; }
        .article-content h3 { font-size: 1.25rem; font-weight: bold; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #374151; }
        .article-content p { margin-bottom: 1rem; line-height: 1.8; color: #4B5563; }
        .article-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .article-content img { border-radius: 0.75rem; margin: 1.5rem 0; width: 100%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .article-content blockquote { border-left: 4px solid #10B981; padding-left: 1rem; font-style: italic; color: #6B7280; background: #F9FAFB; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; }
    </style>

    {{-- [BREADCRUMB] --}}
    <div class="bg-gray-50 border-b border-gray-200 py-4">
        <div class="container mx-auto px-4">
            <nav class="flex text-sm font-medium text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2">/</span>
                <span class="text-brand-green font-bold truncate max-w-[300px]">{{ $article->title }}</span>
            </nav>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- [CỘT TRÁI - 8 PHẦN] NỘI DUNG BÀI VIẾT --}}
            <div class="lg:col-span-8">
                <article class="bg-white rounded-2xl p-0 md:p-8">
                    
                    {{-- 1. Header Bài Viết --}}
                    <header class="mb-8">
                        <div class="flex items-center gap-3 text-sm text-gray-500 mb-4">
                            <span class="bg-brand-green/10 text-brand-green px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ $article->tag ?? 'Góc Nhìn' }}
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1"><i class="far fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-serif leading-tight mb-6">
                            {{ $article->title }}
                        </h1>

                        <div class="flex items-center justify-between border-y border-gray-100 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($article->user->name ?? 'A') }}&background=random" 
                                     class="w-10 h-10 rounded-full border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $article->user->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">Tác giả</p>
                                </div>
                            </div>
                            
                            {{-- Social Share (Demo) --}}
                            <div class="flex gap-2">
                                <button class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition"><i class="fab fa-facebook-f"></i></button>
                                <button class="w-8 h-8 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center hover:bg-sky-500 hover:text-white transition"><i class="fab fa-twitter"></i></button>
                                <button class="w-8 h-8 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-gray-600 hover:text-white transition"><i class="fas fa-link"></i></button>
                            </div>
                        </div>
                    </header>

                    {{-- 2. Ảnh Cover Lớn --}}
                    @if($article->thumbnail)
                        <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
                            <img src="{{ Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" 
                                 class="w-full h-auto object-cover" 
                                 alt="{{ $article->title }}">
                        </div>
                    @endif

                    {{-- 3. Nội Dung Chính --}}
                    <div class="article-content text-lg text-gray-700 text-justify">
                        {{-- Hiển thị mô tả ngắn (Sapo) --}}
                        @if($article->excerpt)
                            <p class="font-bold text-gray-800 italic mb-6 text-xl border-l-4 border-brand-accent pl-4">
                                {{ $article->excerpt }}
                            </p>
                        @endif

                        {{-- Nội dung HTML từ CKEditor --}}
                        {!! $article->content !!}
                    </div>

                    {{-- 4. Footer Bài Viết --}}
                    <div class="mt-12 pt-8 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Bài viết liên quan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Placeholder cho bài liên quan --}}
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex gap-3 items-center cursor-pointer hover:bg-gray-100 transition">
                                <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                                    <img src="https://source.unsplash.com/random/100x100?book" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <span class="text-[10px] text-brand-green font-bold uppercase">Mẹo đọc</span>
                                    <h5 class="text-sm font-bold text-gray-800 line-clamp-2">Làm sao để đọc công thức hiệu quả hơn mỗi ngày?</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </article>
            </div>

            {{-- [CỘT PHẢI - 4 PHẦN] SIDEBAR --}}
            <div class="lg:col-span-4 space-y-8">
                
                {{-- Widget: Giới thiệu --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                    <h3 class="font-serif font-bold text-lg text-gray-800 mb-4">Về Tạp Chí</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        Góc Bếp Magazine là nơi chia sẻ những câu chuyện thú vị xoay quanh văn hóa đọc, phỏng vấn tác giả và những tips hay ho cho mọt sách.
                    </p>
                    <a href="{{ route('home') }}" class="text-brand-green text-sm font-bold hover:underline">Xem thêm bài viết &rarr;</a>
                </div>

                {{-- Widget: Mua Công thức (Tái sử dụng từ Home) --}}
                <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-2xl p-4 sm:p-6 border border-amber-100 shadow-lg relative overflow-hidden sticky top-24">
                    {{-- Decorative --}}
                    <div class="absolute -top-6 -right-6 w-20 h-20 bg-amber-200/40 rounded-full blur-2xl pointer-events-none">
                    </div>

                    <div class="flex items-center gap-3 mb-4 relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-shopping-bag text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-gray-800 leading-none">Mua Công thức Giá Tốt
                            </h3>
                            <span class="text-[10px] text-gray-400">Đối tác uy tín</span>
                        </div>
                    </div>


                    <div class="space-y-2 relative">
                        <a href="https://tiki.vn/nha-sach-tiki/c8322" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-blue-300 hover:bg-blue-50 hover:shadow-md transition-all duration-300 group">
                            <div class="flex items-center gap-3">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/43/Logo_Tiki_2023.png" class="w-8 h-8 object-contain" alt="Tiki">
                                <div>
                                    <span class="font-bold text-sm text-gray-700 group-hover:text-blue-600 block">Tiki Trading</span>
                                    <span class="text-[10px] text-green-600 font-bold">🔥 Giảm tới 35%</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                        </a>

                        <a href="https://shopee.vn/nhasachphuongnam" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-orange-300 hover:bg-orange-50 hover:shadow-md transition-all duration-300 group">
                            <div class="flex items-center gap-3">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg" class="w-8 h-8 object-contain" alt="Shopee">
                                <div>
                                    <span class="font-bold text-sm text-gray-700 group-hover:text-orange-600 block">Shopee Mall</span>
                                    <span class="text-[10px] text-orange-500 font-bold">🚚 Freeship Extra</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
                        </a>

                        <a href="https://www.fahasa.com/" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-red-300 hover:bg-red-50 hover:shadow-md transition-all duration-300 group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">F</div>
                                <div>
                                    <span class="font-bold text-sm text-gray-700 group-hover:text-red-600 block">Fahasa.com</span>
                                    <span class="text-[10px] text-gray-500">✓ Công thức chính hãng</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection
