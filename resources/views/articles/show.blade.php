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
            <nav class="flex text-sm font-bold text-black">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-500 font-normal">/</span>
                <span class="text-brand-green font-extrabold truncate max-w-[300px]">{{ $article->title }}</span>
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

                        @php
                            $wordCount = str_word_count(strip_tags($article->content ?? ''));
                            $readTime = max(1, (int)ceil($wordCount / 200));
                            $tags = collect(array_filter(array_map('trim', preg_split('/[,;]+/', $article->tag ?? ''))));
                        @endphp

                        <div class="flex items-center justify-between border-y border-gray-100 py-4 flex-col sm:flex-row gap-3">
                            <div class="flex items-center gap-3 w-full sm:w-auto">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($article->user->name ?? 'A') }}&background=random" 
                                     class="w-10 h-10 rounded-full border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $article->user->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">Tác giả · <span class="text-gray-400">{{ $article->created_at->format('d/m/Y') }}</span></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                                <div class="hidden md:flex items-center gap-3 text-sm text-gray-500 mr-2">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-medium">{{ $readTime }} phút đọc</span>
                                    <span class="text-xs text-gray-400">|</span>
                                    <span class="text-xs text-gray-500">{{ number_format($article->view_count ?? 0) }} lượt xem</span>
                                </div>

                                @if($tags->isNotEmpty())
                                    <div class="hidden md:flex items-center gap-2 mr-2">
                                        @foreach($tags as $t)
                                            <a href="{{ route('books.list', ['q' => $t]) }}" class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-100 hover:bg-green-100 transition">{{ $t }}</a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
                                    <button onclick="shareFacebook()" title="Chia sẻ lên Facebook" class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></button>
                                    <button onclick="shareTwitter()" title="Chia sẻ lên Twitter" class="w-8 h-8 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center hover:bg-sky-500 hover:text-white transition" aria-label="Share on Twitter"><i class="fab fa-twitter"></i></button>
                                    <button onclick="copyLink()" title="Sao chép link" class="w-8 h-8 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-gray-600 hover:text-white transition" aria-label="Copy link"><i class="fas fa-link"></i></button>
                                    <button onclick="printArticle()" title="In bài viết" class="w-8 h-8 rounded-full bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-gray-600 hover:text-white transition" aria-label="Print article"><i class="fas fa-print"></i></button>
                                </div>
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

                    {{-- 4. Footer Bài Viết - Bài viết liên quan --}}
                    @if(isset($relatedArticles) && $relatedArticles->count() > 0)
                    <div class="mt-12 pt-8 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-bookmark text-brand-green"></i> Bài viết liên quan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($relatedArticles as $related)
                                @php
                                    $thumbnail = $related->thumbnail 
                                        ? (Str::startsWith($related->thumbnail, 'http') ? $related->thumbnail : asset('storage/' . $related->thumbnail))
                                        : 'https://placehold.co/100x100?text=No+Image';
                                @endphp
                                <a href="{{ route('articles.show', $related->slug) }}" 
                                   class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex gap-3 items-center hover:bg-gray-100 hover:shadow-md transition group">
                                    <div class="w-16 h-16 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                                        <img src="{{ $thumbnail }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-[10px] text-brand-green font-bold uppercase">{{ $related->tag ?? 'Góc Nhìn' }}</span>
                                        <h5 class="text-sm font-bold text-gray-800 line-clamp-2 group-hover:text-brand-green transition">{{ $related->title }}</h5>
                                        <p class="text-xs text-gray-400 mt-1">{{ $related->created_at->format('d/m/Y') }}</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-brand-green transition"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </article>
            </div>

            {{-- [CỘT PHẢI - 4 PHẦN] SIDEBAR - STICKY --}}
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    
                    {{-- Widget: Giới thiệu Tạp Chí - Redesigned --}}
                    <div class="relative bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 rounded-2xl p-6 border border-emerald-100 shadow-lg overflow-hidden group">
                        {{-- Decorative Elements --}}
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-200/30 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-teal-200/30 rounded-full blur-2xl pointer-events-none"></div>
                        
                        {{-- Icon Header --}}
                        <div class="flex items-center gap-3 mb-4 relative">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <i class="fas fa-book-open text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-gray-800 text-lg leading-tight">Về Tạp Chí</h3>
                                <span class="text-xs text-emerald-600 font-medium">Góc Bếp Magazine</span>
                            </div>
                        </div>
                        
                        {{-- Content --}}
                        <p class="text-sm text-gray-600 leading-relaxed mb-5 relative">
                            Góc Bếp Magazine là nơi chia sẻ những câu chuyện thú vị xoay quanh <span class="text-emerald-600 font-semibold">văn hóa đọc</span>, phỏng vấn tác giả và những tips hay ho cho mọt sách.
                        </p>
                        
                        {{-- Stats --}}
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 text-center border border-white/80">
                                <span class="block text-lg font-bold text-emerald-600">50+</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wide">Bài viết</span>
                            </div>
                            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 text-center border border-white/80">
                                <span class="block text-lg font-bold text-teal-600">10K+</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wide">Lượt xem</span>
                            </div>
                        </div>
                        
                        {{-- CTA Button --}}
                        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group/btn">
                            <span>Khám phá thêm</span>
                            <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                    </div>


                </div>
            </div>
        </div>
    </main>

    {{-- Toast nhỏ hiển thị thông báo (copy link, etc.) --}}
    <div id="article-toast" class="fixed bottom-6 right-6 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg opacity-0 pointer-events-none transition-opacity" role="status" aria-live="polite"></div>

    <script>
        function openPopup(url, w = 640, h = 480) {
            const left = (screen.width - w) / 2;
            const top = (screen.height - h) / 2;
            window.open(url, '_blank', `toolbar=0,status=0,width=${w},height=${h},top=${top},left=${left}`);
        }

        function shareFacebook() {
            const url = encodeURIComponent(window.location.href);
            openPopup(`https://www.facebook.com/sharer/sharer.php?u=${url}`, 640, 480);
        }

        function shareTwitter() {
            const text = encodeURIComponent(@json($article->title));
            const url = encodeURIComponent(window.location.href);
            openPopup(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, 640, 480);
        }

        function copyLink() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('Đã sao chép liên kết');
                }).catch(() => showToast('Không thể sao chép'));
            } else {
                // fallback
                const el = document.createElement('textarea');
                el.value = window.location.href;
                document.body.appendChild(el);
                el.select();
                try { document.execCommand('copy'); showToast('Đã sao chép liên kết'); } catch(e){ showToast('Không thể sao chép'); }
                el.remove();
            }
        }

        function printArticle() {
            window.print();
        }

        function showToast(msg) {
            const t = document.getElementById('article-toast');
            t.textContent = msg;
            t.classList.remove('opacity-0');
            t.classList.add('opacity-100');
            t.style.pointerEvents = 'auto';
            setTimeout(() => {
                t.classList.add('opacity-0');
                t.style.pointerEvents = 'none';
            }, 2000);
        }
    </script>
@endsection
