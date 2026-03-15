@extends('layouts.app')

@section('title', 'Trang Chủ - Góc Sách')

@section('content')
    <section id="hero-carousel" class="relative text-white py-8 sm:py-12 lg:py-16 overflow-hidden bg-[#2A483A] group">
        {{-- Background Pattern --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none"
            style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-black/20 to-transparent pointer-events-none">
        </div>

        {{-- Slider Wrapper --}}
        <div class="hero-slider-wrapper flex w-full transition-transform duration-700 ease-in-out" id="sliderWrapper">
            @foreach($heroSlides as $index => $slide)
                <div class="w-full flex-shrink-0 px-4 relative group/edit">
                    {{-- [ADMIN TOOL] NÃºt Sửa Banner --}}
                    @if(Auth::check() && Auth::user()->isAdmin() && isset($slide->id))
                        <a href="{{ route('admin.banners.edit', $slide->id) }}"
                            class="absolute top-0 right-10 z-50 bg-white/90 text-blue-600 px-4 py-2 rounded-full shadow-lg hover:bg-blue-600 hover:text-white transition font-bold flex items-center gap-2 opacity-0 group-hover/edit:opacity-100 backdrop-blur-sm cursor-pointer">
                            <i class="fas fa-cog"></i> Sửa Banner
                        </a>
                    @endif

                    <div class="container mx-auto flex flex-col md:flex-row items-center gap-12 justify-center">
                        {{-- 1. Ảnh Bìa Sách --}}
                        <div class="w-full md:w-5/12 flex justify-center md:justify-end perspective-1000">
                            @php
                                $imagePath = is_object($slide) ? $slide->image : $slide['image'];
                                $imgSrc = Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath);
                                $bannerLink = is_object($slide) ? ($slide->link ?? '#') : '#';
                            @endphp
                            <a href="{{ $bannerLink }}"
                                class="relative w-36 h-52 sm:w-48 sm:h-72 md:w-56 md:h-80 shadow-[0_20px_50px_rgba(0,0,0,0.5)] rounded-r-lg rounded-l-sm transform rotate-y-12 hover:rotate-y-0 hover:scale-105 transition-all duration-700 cursor-pointer group/book block">
                                <div
                                    class="absolute inset-0 bg-white/10 opacity-0 group-hover/book:opacity-20 transition-opacity z-20 rounded-r-lg rounded-l-sm">
                                </div>
                                <img src="{{ $imgSrc }}"
                                    class="w-full h-full object-cover rounded-r-lg rounded-l-sm border-l-4 border-white/10">
                                <div
                                    class="absolute top-0 left-0 w-2 h-full bg-gradient-to-r from-white/30 to-transparent z-10">
                                </div>
                            </a>
                        </div>


                        {{-- 2. nội dung Banner --}}
                        <div class="w-full md:w-7/12 text-center md:text-left space-y-6">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm">
                                <span class="flex h-2 w-2 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-xs font-bold uppercase tracking-widest text-brand-beige">
                                    {{ is_object($slide) ? ($slide->tag ?? 'Nổi bật') : $slide['tag'] }}
                                </span>
                            </div>

                            <h1 class="text-3xl md:text-5xl font-bold leading-tight font-serif text-brand-beige drop-shadow-md">
                                {{ is_object($slide) ? $slide->title : $slide['title'] }}
                            </h1>


                            <div class="flex items-center justify-center md:justify-start gap-4">
                                @php
                                    $slideRating = floatval(is_object($slide) ? ($slide->rating ?? 5.0) : ($slide['rating'] ?? 5.0));
                                    $fullStars = floor($slideRating);
                                    $hasHalfStar = ($slideRating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp
                                <div class="flex text-yellow-400 text-lg">
                                    @for($i = 0; $i < $fullStars; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                    @if($hasHalfStar)
                                        <i class="fas fa-star-half-alt"></i>
                                    @endif
                                    @for($i = 0; $i < $emptyStars; $i++)
                                        <i class="far fa-star opacity-50"></i>
                                    @endfor
                                </div>
                                <span class="text-white/80 text-sm font-medium px-2 py-0.5 bg-white/10 rounded">
                                    {{ number_format($slideRating, 1) }}
                                </span>
                            </div>

                            <p class="text-gray-200 text-lg font-light italic max-w-2xl leading-relaxed drop-shadow">
                                {{ is_object($slide) ? ($slide->description ?? $slide->desc ?? '') : $slide['desc'] }}
                            </p>


                            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-2">
                                @php
                                    $link = is_object($slide) ? ($slide->link ?? '#') : '#';
                                @endphp
                                <a href="{{ $link }}"
                                    class="inline-flex items-center justify-center gap-2 bg-brand-accent text-white font-bold px-6 py-3 rounded-full shadow-lg hover:bg-[#c29263] transition-all transform hover:-translate-y-1">
                                    <span>Đọc review</span> <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Nút Điều Hướng --}}
        <button id="heroPrevBtn"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20 hover:bg-brand-accent/80 text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 hover:scale-110 z-20 cursor-pointer">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>
        <button id="heroNextBtn"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/20 hover:bg-brand-accent/80 text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 hover:scale-110 z-20 cursor-pointer">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>

        {{-- Dots --}}
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-20">
            @foreach($heroSlides as $index => $slide)
                <button
                    class="indicator-dot w-3 h-3 rounded-full bg-white/30 hover:bg-white transition-all {{ $index === 0 ? 'bg-brand-accent w-8' : '' }}"
                    data-index="{{ $index }}"></button>
            @endforeach
        </div>
    </section>

    {{-- MAIN LAYOUT --}}
    <main class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- [CỘT TRÁI - CHIẾM 8 PHẦN] --}}
            <div class="lg:col-span-8 space-y-16">

                {{-- 1. TẠP CHÍ ĐỌC --}}
                <section class="relative">
                    {{-- Decorative --}}
                    <div
                        class="absolute -top-6 -left-6 w-32 h-32 bg-brand-accent/5 rounded-full blur-3xl pointer-events-none">
                    </div>

                    <div class="flex justify-between items-end mb-8">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-brand-accent to-brand-green rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-newspaper text-white text-lg"></i>
                            </div>
                            <div>
                                <h2
                                    class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 font-serif flex items-center gap-2 sm:gap-3">
                                    Tạp Chí Đọc
                                    <span
                                        class="text-xs bg-brand-green/10 text-brand-green px-2.5 py-1 rounded-full font-bold">FEATURED</span>
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">Góc nhìn sâu sắc về sách và cuộc sống</p>
                            </div>
                        </div>
                        <a href="#"
                            class="hidden md:flex items-center gap-2 text-sm font-bold text-brand-green hover:text-brand-accent transition group">
                            <span>Xem tất cả</span>
                            <i
                                class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        @if(isset($featuredArticle))
                            <article class="md:col-span-3 group cursor-pointer relative"
                                onclick="window.location.href='{{ route('articles.show', $featuredArticle->slug ?? $featuredArticle->id) }}'">
                                @if(Auth::check() && Auth::user()->isAdmin())
                                    {{-- Nút Sửa (Ngăn chặn click bong bóng để không nhảy trang) --}}
                                    <a href="{{ route('admin.articles.edit', $featuredArticle->id) }}"
                                        onclick="event.stopPropagation()"
                                        class="absolute top-4 right-4 z-20 bg-white/90 text-blue-600 p-2 rounded-full shadow-lg hover:bg-blue-600 hover:text-white transition opacity-0 group-hover:opacity-100">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                <div class="relative h-64 md:h-80 rounded-2xl overflow-hidden mb-4 shadow-md">
                                    <img src="{{ $featuredArticle->thumbnail }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent">
                                    </div>
                                    <span
                                        class="absolute top-4 left-4 bg-brand-accent text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        {{ $featuredArticle->tag ?? 'Tiêu Điểm' }}
                                    </span>
                                    <div class="absolute bottom-4 left-4 right-4 text-white">
                                        <div class="text-xs opacity-80 mb-2"><i class="far fa-calendar-alt mr-1"></i>
                                            {{ $featuredArticle->created_at->format('d/m/Y') }} • Bởi
                                            {{ $featuredArticle->user->name }}
                                        </div>
                                        <h3
                                            class="text-2xl font-bold font-serif leading-tight group-hover:text-brand-beige transition">
                                            {{ $featuredArticle->title }}
                                        </h3>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">{{ $featuredArticle->excerpt }}
                                </p>
                            </article>
                        @endif

                        <div class="md:col-span-2 flex flex-col gap-6">
                            @if(isset($sidebarArticles))
                                @foreach($sidebarArticles as $article)
                                    <article class="flex flex-col group cursor-pointer relative"
                                        onclick="window.location.href='{{ route('articles.show', $article->slug ?? $article->id) }}'">
                                        @if(Auth::check() && Auth::user()->isAdmin())
                                            <a href="{{ route('admin.articles.edit', $article->id) }}" onclick="event.stopPropagation()"
                                                class="absolute top-2 right-2 z-20 bg-white/90 text-blue-600 p-1.5 rounded-full shadow hover:bg-blue-600 hover:text-white opacity-0 group-hover:opacity-100 transition">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        @endif

                                        <div class="h-32 rounded-xl overflow-hidden mb-3 relative">
                                            <img src="{{ $article->thumbnail }}"
                                                class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                        </div>
                                        <div>
                                            <span
                                                class="text-brand-green text-xs font-bold uppercase">{{ $article->tag ?? 'Tin Tức' }}</span>
                                            <h3
                                                class="font-serif font-bold text-base text-gray-800 leading-snug group-hover:text-brand-green transition mt-1">
                                                {{ $article->title }}
                                            </h3>
                                        </div>
                                    </article>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </section>

                {{-- 1.5. BÀI REVIEW SÁCH --}}
                @if((isset($latestPosts) && $latestPosts->count() > 0) || (isset($hotPosts) && $hotPosts->count() > 0))
                    <section id="featured-reviews"
                        class="relative group/slider bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 rounded-2xl p-4 sm:p-6 border border-rose-100 shadow-sm">
                        {{-- Decorative --}}
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-rose-200/30 rounded-full blur-2xl pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 w-20 h-20 bg-purple-200/30 rounded-full blur-xl pointer-events-none">
                        </div>

                        {{-- Header với Tabs --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 relative gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-rose-500 to-pink-500 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-pen-fancy text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 font-serif">
                                        Bài Review Sách
                                    </h2>
                                    <p class="text-xs text-gray-500">Những bài review hay nhất từ cộng đồng</p>
                                </div>
                            </div>

                            {{-- Tabs + Xem tất cả --}}
                            <div class="flex items-center gap-3">
                                <div class="bg-rose-100 rounded-full p-1 flex text-xs font-bold">
                                    <button onclick="switchReviewTab('latest')" id="tab-review-latest"
                                        class="px-3 sm:px-4 py-1.5 rounded-full transition-all duration-300 bg-white text-rose-600 shadow-sm">
                                        <i class="fas fa-clock mr-1"></i>Mới nhất
                                    </button>
                                    <button onclick="switchReviewTab('hot')" id="tab-review-hot"
                                        class="px-3 sm:px-4 py-1.5 rounded-full transition-all duration-300 text-gray-500 hover:text-rose-500">
                                        <i class="fas fa-fire mr-1"></i>Hot nhất
                                    </button>
                                </div>
                                <a href="{{ route('books.search') }}"
                                    class="text-xs font-bold px-3 py-1.5 bg-rose-500 text-white hover:bg-rose-600 rounded-full transition shadow-md flex items-center gap-1">
                                    <span class="hidden sm:inline">Xem tất cả</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Slider Container cho MỚI NHẤT --}}
                        <div id="reviews-latest-container" class="relative px-2 group/slider">
                            {{-- Prev Button --}}
                            <button
                                class="btn-prev-reviews absolute left-0 top-1/2 -translate-y-1/2 -ml-1 sm:-ml-3 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-rose-500 hover:scale-110 transition-all opacity-100 sm:opacity-0 group-hover/slider:opacity-100 duration-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <div class="slider-reviews flex gap-4 sm:gap-5 overflow-x-auto scroll-smooth no-scrollbar pb-4">
                                @foreach($latestPosts as $index => $post)
                                    @php
                                        $thumbnailUrl = !empty($post->thumbnail)
                                            ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset('storage/' . $post->thumbnail))
                                            : ($post->book && $post->book->cover_image
                                                ? (str_starts_with($post->book->cover_image, 'http') ? $post->book->cover_image : asset('storage/' . $post->book->cover_image))
                                                : 'https://placehold.co/300x200?text=No+Image');
                                        $rating = $post->rating ?? 0;
                                        $fullStars = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                        $isNew = $index < 3; // 3 bài đầu hiện NEW
                                    @endphp

                                    <div class="w-64 sm:w-72 md:w-80 flex-shrink-0 group">
                                        <a href="{{ $post->book ? route('book.reviews', $post->book->slug) . '#post-' . $post->id : '#' }}"
                                            class="block">
                                            <div
                                                class="relative w-full aspect-[16/10] rounded-xl overflow-hidden shadow-lg mb-3 bg-gradient-to-br from-gray-100 to-gray-200 transform transition-all duration-500 group-hover:scale-[1.02] group-hover:shadow-xl">
                                                <img src="{{ $thumbnailUrl }}" alt="{{ $post->title }}"
                                                    class="w-full h-full object-cover transition duration-700 group-hover:brightness-110"
                                                    onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                                                </div>

                                                {{-- NEW Badge --}}
                                                @if($isNew)
                                                    <div
                                                        class="absolute top-3 left-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 animate-pulse">
                                                        <i class="fas fa-sparkles text-[8px]"></i> NEW
                                                    </div>
                                                @else
                                                    <div
                                                        class="absolute top-3 left-3 bg-black/50 backdrop-blur-sm text-white text-[10px] font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                                                        <i class="far fa-eye"></i>
                                                        <span>{{ number_format($post->view_count ?? 0) }}</span>
                                                    </div>
                                                @endif

                                                @if($rating > 0)
                                                    <div
                                                        class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1">
                                                        <i class="fas fa-star text-yellow-400"></i>
                                                        <span>{{ number_format($rating, 1) }}</span>
                                                    </div>
                                                @endif

                                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                                    @if($rating > 0)
                                                        <div class="flex text-yellow-400 text-xs mb-2">
                                                            @for($i = 0; $i < $fullStars; $i++)<i class="fas fa-star"></i>@endfor
                                                            @if($hasHalfStar)<i class="fas fa-star-half-alt"></i>@endif
                                                            @for($i = 0; $i < $emptyStars; $i++)<i
                                                            class="far fa-star opacity-50"></i>@endfor
                                                        </div>
                                                    @endif
                                                    <h3
                                                        class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow-md">
                                                        {{ $post->title }}
                                                    </h3>
                                                    <div class="flex items-center gap-2 mt-2">
                                                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? 'User') }}"
                                                            class="w-5 h-5 rounded-full border border-white/50 object-cover">
                                                        <span
                                                            class="text-white/80 text-[10px] font-medium truncate">{{ $post->user->name ?? 'Ẩn danh' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        @if($post->book)
                                            <div class="px-1">
                                                <p class="text-[11px] text-gray-500 truncate flex items-center gap-1">
                                                    <i class="fas fa-book text-[9px] text-rose-400"></i>
                                                    <a href="{{ route('detail', $post->book->slug ?? $post->book->id) }}"
                                                        class="hover:text-rose-500 transition">{{ $post->book->title }}</a>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <button
                                class="btn-next-reviews absolute right-0 top-1/2 -translate-y-1/2 -mr-1 sm:-mr-3 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-rose-500 hover:scale-110 transition-all opacity-100 sm:opacity-0 group-hover/slider:opacity-100 duration-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        {{-- Slider Container cho HOT NHẤT (ẩn ban đầu) --}}
                        <div id="reviews-hot-container" class="relative px-2 group/slider hidden">
                            <button
                                class="btn-prev-reviews absolute left-0 top-1/2 -translate-y-1/2 -ml-1 sm:-ml-3 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-rose-500 hover:scale-110 transition-all opacity-100 sm:opacity-0 group-hover/slider:opacity-100 duration-300">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <div class="slider-reviews flex gap-4 sm:gap-5 overflow-x-auto scroll-smooth no-scrollbar pb-4">
                                @foreach($hotPosts as $index => $post)
                                    @php
                                        $thumbnailUrl = !empty($post->thumbnail)
                                            ? (str_starts_with($post->thumbnail, 'http') ? $post->thumbnail : asset('storage/' . $post->thumbnail))
                                            : ($post->book && $post->book->cover_image
                                                ? (str_starts_with($post->book->cover_image, 'http') ? $post->book->cover_image : asset('storage/' . $post->book->cover_image))
                                                : 'https://placehold.co/300x200?text=No+Image');
                                        $rating = $post->rating ?? 0;
                                        $fullStars = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                        $isHot = $index < 3; // 3 bài đầu hiện HOT
                                    @endphp

                                    <div class="w-64 sm:w-72 md:w-80 flex-shrink-0 group">
                                        <a href="{{ $post->book ? route('book.reviews', $post->book->slug) . '#post-' . $post->id : '#' }}"
                                            class="block">
                                            <div
                                                class="relative w-full aspect-[16/10] rounded-xl overflow-hidden shadow-lg mb-3 bg-gradient-to-br from-gray-100 to-gray-200 transform transition-all duration-500 group-hover:scale-[1.02] group-hover:shadow-xl">
                                                <img src="{{ $thumbnailUrl }}" alt="{{ $post->title }}"
                                                    class="w-full h-full object-cover transition duration-700 group-hover:brightness-110"
                                                    onerror="this.src='https://placehold.co/300x200?text=No+Image'">
                                                <div
                                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                                                </div>

                                                {{-- HOT Badge --}}
                                                @if($isHot)
                                                    <div
                                                        class="absolute top-3 left-3 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 animate-pulse">
                                                        <i class="fas fa-fire text-[8px]"></i> HOT
                                                    </div>
                                                @else
                                                    <div
                                                        class="absolute top-3 left-3 bg-black/50 backdrop-blur-sm text-white text-[10px] font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                                                        <i class="far fa-eye"></i>
                                                        <span>{{ number_format($post->view_count ?? 0) }}</span>
                                                    </div>
                                                @endif

                                                @if($rating > 0)
                                                    <div
                                                        class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1">
                                                        <i class="fas fa-star text-yellow-400"></i>
                                                        <span>{{ number_format($rating, 1) }}</span>
                                                    </div>
                                                @endif

                                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                                    @if($rating > 0)
                                                        <div class="flex text-yellow-400 text-xs mb-2">
                                                            @for($i = 0; $i < $fullStars; $i++)<i class="fas fa-star"></i>@endfor
                                                            @if($hasHalfStar)<i class="fas fa-star-half-alt"></i>@endif
                                                            @for($i = 0; $i < $emptyStars; $i++)<i
                                                            class="far fa-star opacity-50"></i>@endfor
                                                        </div>
                                                    @endif
                                                    <h3
                                                        class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow-md">
                                                        {{ $post->title }}
                                                    </h3>
                                                    <div class="flex items-center gap-2 mt-2">
                                                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? 'User') }}"
                                                            class="w-5 h-5 rounded-full border border-white/50 object-cover">
                                                        <span
                                                            class="text-white/80 text-[10px] font-medium truncate">{{ $post->user->name ?? 'Ẩn danh' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                        @if($post->book)
                                            <div class="px-1">
                                                <p class="text-[11px] text-gray-500 truncate flex items-center gap-1">
                                                    <i class="fas fa-book text-[9px] text-rose-400"></i>
                                                    <a href="{{ route('detail', $post->book->slug ?? $post->book->id) }}"
                                                        class="hover:text-rose-500 transition">{{ $post->book->title }}</a>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <button
                                class="btn-next-reviews absolute right-0 top-1/2 -translate-y-1/2 -mr-1 sm:-mr-3 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-rose-500 hover:scale-110 transition-all opacity-100 sm:opacity-0 group-hover/slider:opacity-100 duration-300">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </section>
                @endif

                {{-- 2. SÁCH MỚI CẬP NHẬT --}}
                <section id="new-books"
                    class="relative group/slider bg-gradient-to-br from-brand-green/5 via-white to-brand-beige/20 rounded-2xl p-6 border border-gray-100 shadow-sm">
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-brand-green/10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-open text-brand-green"></i>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-gray-800 font-serif flex items-center gap-2">
                                    Sách Mới Cập Nhật
                                    <span
                                        class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full font-bold animate-pulse">MỚI</span>
                                </h2>
                                <p class="text-xs text-gray-500">Những tựa sách mới nhất trong thư viện</p>
                            </div>
                        </div>
                        <a href="{{ route('books.list') }}"
                            class="text-xs font-bold px-4 py-2 bg-brand-green text-white hover:bg-brand-accent rounded-full transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>Xem kho sách</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>


                    {{-- Slider Container --}}
                    <div class="relative px-2">
                        {{-- Prev Button --}}
                        <button id="btnPrevNewBooks"
                            class="absolute left-0 top-1/2 -translate-y-1/2 -ml-3 z-10 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-brand-green hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        {{-- Books Slider --}}
                        <div id="sliderNewBooks" class="flex gap-5 overflow-x-auto scroll-smooth no-scrollbar pb-4"
                            style="scroll-behavior: smooth;">
                            @if(isset($books) && $books->count() > 0)
                                @foreach($books->take(10) as $book)
                                    @php
                                        $coverUrl = !empty($book->cover_image)
                                            ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset('storage/' . $book->cover_image))
                                            : 'https://placehold.co/150x225?text=No+Image';
                                        $rating = $book->avg_rating ?? rand(35, 50) / 10;
                                    @endphp

                                    <div class="w-32 sm:w-36 md:w-44 flex-shrink-0 group">
                                        {{-- Book Card --}}
                                        <div
                                            class="relative w-full aspect-[2/3] rounded-xl overflow-hidden shadow-lg mb-3 bg-gradient-to-br from-gray-100 to-gray-200 transform transition-all duration-500 group-hover:scale-105 group-hover:shadow-xl">
                                            {{-- Book Cover --}}
                                            <a href="{{ route('detail', $book->slug) }}" class="block w-full h-full">
                                                <img src="{{ $coverUrl }}" alt="{{ $book->title }}"
                                                    class="w-full h-full object-cover transition duration-700 group-hover:brightness-110"
                                                    onerror="this.src='https://placehold.co/150x225?text=No+Image'">
                                            </a>


                                            {{-- Overlay Gradient --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            </div>

                                            {{-- Badge NEW --}}
                                            @if($loop->index < 3)
                                                <div
                                                    class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-orange-400 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 animate-pulse">
                                                    <i class="fas fa-fire text-[8px]"></i> MỚI
                                                </div>
                                            @endif


                                            {{-- Rating Badge --}}
                                            <div
                                                class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm text-gray-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm flex items-center gap-1">
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <span>{{ number_format($rating, 1) }}</span>
                                            </div>


                                            {{-- Quick Actions (on hover) --}}
                                            <div
                                                class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                                <a href="{{ route('detail', $book->slug) }}"
                                                    class="w-full bg-white text-brand-green font-bold text-xs py-2 rounded-lg flex items-center justify-center gap-2 shadow-lg hover:bg-brand-green hover:text-white transition">
                                                    <i class="fas fa-eye"></i> Xem chi tiết
                                                </a>
                                            </div>
                                        </div>


                                        {{-- Book Info --}}
                                        <div class="px-1">
                                            <h3
                                                class="font-serif font-bold text-sm text-gray-800 leading-tight mb-1 line-clamp-2 group-hover:text-brand-green transition min-h-[2.5rem]">
                                                <a href="{{ route('detail', $book->slug) }}"
                                                    title="{{ $book->title }}">{{ $book->title }}</a>
                                            </h3>
                                            <p class="text-[11px] text-gray-500 truncate flex items-center gap-1">
                                                <i class="fas fa-user-edit text-[9px] text-gray-400"></i>
                                                {{ $book->author_name ?? 'Ẩn danh' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="w-full py-12 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                    <i class="fas fa-books text-4xl mb-3 block text-gray-300"></i>
                                    <p class="font-medium">Chưa có sách mới trong thư viện.</p>
                                    <a href="{{ route('books.list') }}"
                                        class="text-brand-green text-sm font-bold hover:underline mt-2 inline-block">Khám phá
                                        kho sách →</a>
                                </div>
                            @endif
                        </div>

                        {{-- Next Button --}}
                        <button id="btnNextNewBooks"
                            class="absolute right-0 top-1/2 -translate-y-1/2 -mr-3 z-10 w-10 h-10 bg-white rounded-full shadow-lg border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-brand-green hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>


                    {{-- Decorative Element --}}
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-brand-accent/10 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -bottom-4 -left-4 w-32 h-32 bg-brand-green/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-brand-accent/10 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -bottom-4 -left-4 w-32 h-32 bg-brand-green/10 rounded-full blur-3xl pointer-events-none">
                    </div>
                </section>

                {{-- 3. CỘNG ĐỒNG REVIEW --}}
                <section id="community-posts" class="mb-16 scroll-mt-24">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1 h-8 bg-brand-accent rounded-full"></div>
                                <div>
                                    <h2
                                        class="text-2xl font-bold text-gray-800 font-serif leading-none flex items-center gap-3">
                                        Cộng Đồng Review
                                        {{-- Dữ liệu từ bảng comments --}}
                                        <span
                                            class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold">{{ $communityStats['comments'] ?? 0 }}
                                            bình luận</span>
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">Góc chia sẻ cảm nhận từ độc giả</p>
                                </div>
                            </div>

                            {{-- Bộ lọc Review --}}
                            <div class="flex items-center gap-3">
                                <div class="bg-brand-green/10 rounded-full p-1.5 flex text-xs font-bold">
                                    <button onclick="loadComments('latest')" id="tab-latest"
                                        class="px-4 py-1.5 rounded-full transition-all duration-300 bg-white text-brand-green shadow-sm">
                                        Mới nhất
                                    </button>
                                    <button onclick="loadComments('popular')" id="tab-popular"
                                        class="px-4 py-1.5 rounded-full transition-all duration-300 text-gray-500 hover:bg-gray-50">
                                        Nổi bật
                                    </button>
                                </div>
                                <a href="{{ route('books.search') }}"
                                    class="text-xs text-gray-400 hover:text-gray-600 ml-3">Xem tất cả</a>
                            </div>
                        </div>

                        {{-- Container chứa danh sách --}}
                        <div id="comments-container"
                            class="relative min-h-[200px] bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            {{-- Loading Spinner --}}
                            <div id="loading-spinner"
                                class="hidden absolute inset-0 bg-white/80 z-20 flex items-center justify-center rounded-2xl transition-opacity duration-300">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-2 border-brand-green border-t-transparent">
                                </div>
                            </div>

                            {{-- Nội dung AJAX SẼ ĐỔ VÀO ĐÂY --}}
                            <div id="comments-content-wrapper">
                                {{-- [ĐÃ Sửa]: Truyền biến latestReviews --}}
                                @include('partials.home_comments', ['latestReviews' => $latestReviews])
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Banner Sự Kiện - PREMIUM --}}
                @if(isset($activeChallenge) && $activeChallenge)
                <div
                    class="bg-gradient-to-br from-[#2A483A] via-[#1e3a2f] to-[#0f1f17] rounded-2xl p-8 relative overflow-hidden shadow-xl text-white group hover:shadow-2xl transition-all duration-500">
                    {{-- Decorative Elements --}}
                    <div
                        class="absolute top-0 right-0 w-72 h-72 bg-brand-accent/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-green-500/10 rounded-full blur-2xl -ml-12 -mb-12">
                    </div>
                    <div
                        class="absolute top-0 right-0 w-72 h-72 bg-brand-accent/10 rounded-full blur-3xl -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-green-500/10 rounded-full blur-2xl -ml-12 -mb-12">
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            {{-- Icon Trophy --}}
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-brand-accent to-yellow-400 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:rotate-6 transition-transform duration-300">
                                <i class="fas fa-trophy text-white text-2xl"></i>
                            </div>

                            <div>
                                <span
                                    class="inline-flex items-center gap-2 text-brand-accent text-xs font-bold uppercase tracking-wider border border-brand-accent/40 bg-brand-accent/10 px-3 py-1 rounded-full mb-2">
                                    <span class="w-1.5 h-1.5 bg-brand-accent rounded-full"></span>
                                    Sự kiện HOT
                                </span>
                                <h3 class="text-2xl md:text-3xl font-serif font-bold mb-2 text-brand-beige">{{ $activeChallenge->name }}</h3>
                                <p class="text-white/70 text-sm font-light max-w-md leading-relaxed">
                                    <i class="fas fa-medal text-yellow-400 mr-1"></i>
                                    Hoàn thành <span class="text-brand-accent font-bold">{{ $activeChallenge->target_count }} cuốn sách</span> 
                                    @if($activeChallenge->badge)
                                        để nhận huy hiệu "{{ $activeChallenge->badge->name }}"
                                    @endif
                                    @if($activeChallenge->description)
                                        - {{ Str::limit($activeChallenge->description, 60) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('challenges.index') }}"
                            class="bg-gradient-to-r from-brand-accent to-yellow-500 hover:from-yellow-500 hover:to-brand-accent text-white px-8 py-3.5 rounded-full font-bold shadow-xl hover:shadow-2xl transition-all text-sm whitespace-nowrap flex items-center gap-2 transform hover:-translate-y-1">
                            <i class="fas fa-rocket"></i>
                            Tham Gia Ngay
                        </a>
                    </div>
                </div>
                @endif
            </div> {{-- END CỘT 8 --}}

            {{-- [CỘT PHẢI - 4 PHẦN] --}}
            <div class="lg:col-span-4">
                <div class="space-y-6 sm:space-y-8">
                    {{-- Widget 0: Châm Ngôn Hôm Nay --}}
                    @if(isset($dailyQuote) && $dailyQuote)
                        <div
                            class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 rounded-2xl p-7 border border-amber-100 shadow-lg relative overflow-hidden group hover:shadow-xl transition-shadow duration-300">
                            {{-- Decorative Elements --}}
                            <div
                                class="absolute -top-6 -right-6 w-24 h-24 bg-amber-200/30 rounded-full blur-2xl pointer-events-none group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div
                                class="absolute -bottom-4 -left-4 w-16 h-16 bg-orange-200/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            {{-- Quote Icon --}}
                            <div class="flex items-start gap-3 mb-4 relative">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                    <i class="fas fa-quote-left text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif font-bold text-gray-800 text-lg leading-none">Châm Ngôn Hôm Nay</h3>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ now()->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- Quote Content --}}
                            <blockquote class="relative pl-4 border-l-3 border-amber-300">
                                <p class="text-gray-700 text-sm leading-relaxed italic font-serif">
                                    "{{ $dailyQuote->content }}"
                                </p>
                            </blockquote>

                            {{-- Author --}}
                            <div class="mt-4 flex items-center justify-end gap-2">
                                <div
                                    class="w-6 h-6 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-feather-alt text-gray-500 text-[10px]"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-800">{{ $dailyQuote->author }}</p>
                                    @if($dailyQuote->source)
                                        <p class="text-[10px] text-gray-500 italic">{{ $dailyQuote->source }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Widget: Hôm nay đọc gì? --}}
                    @if(isset($randomBook) && $randomBook)
                        <div
                            class="bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 rounded-2xl p-7 border border-purple-100 shadow-lg relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                            {{-- Decorative --}}
                            <div
                                class="absolute -top-8 -right-8 w-28 h-28 bg-purple-200/30 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500">
                            </div>
                            <div
                                class="absolute -bottom-6 -left-6 w-20 h-20 bg-pink-200/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-5 relative">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-dice text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-serif font-bold text-gray-800 text-base leading-none">Hôm nay đọc gì?
                                        </h3>
                                        <span class="text-xs text-gray-400">Gợi ý cho bạn</span>
                                    </div>
                                </div>
                                <a href="{{ route('books.list') }}"
                                    class="text-xs text-purple-500 hover:text-purple-700 font-bold flex items-center gap-1"
                                    title="Xem tất cả sách">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                            {{-- Book Card --}}
                            <a href="{{ route('detail', $randomBook->slug) }}" class="block group/book">
                                <div class="flex gap-5">
                                    {{-- Book Cover --}}
                                    <div
                                        class="w-28 h-40 rounded-xl overflow-hidden shadow-lg flex-shrink-0 transform group-hover/book:scale-105 transition-transform duration-300 border-2 border-white">
                                        @php
                                            $coverUrl = !empty($randomBook->cover_image)
                                                ? (str_starts_with($randomBook->cover_image, 'http') ? $randomBook->cover_image : asset('storage/' . $randomBook->cover_image))
                                                : 'https://placehold.co/112x160?text=No+Image';
                                        @endphp
                                        <img src="{{ $coverUrl }}" alt="{{ $randomBook->title }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    {{-- Book Info --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <h4
                                            class="font-bold text-gray-800 text-base leading-snug line-clamp-2 group-hover/book:text-purple-600 transition-colors">
                                            {{ $randomBook->title }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
                                            <i class="fas fa-user-edit text-xs text-gray-400"></i>
                                            {{ $randomBook->author_name ?? 'Ẩn danh' }}
                                        </p>

                                        {{-- Rating --}}
                                        <div class="flex items-center gap-2 mt-3">
                                            <div class="flex text-yellow-400 text-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= round($randomBook->avg_rating ?? 0) ? '' : 'opacity-30' }}"></i>
                                                @endfor
                                            </div>
                                            <span
                                                class="text-xs text-gray-400">({{ number_format($randomBook->view_count ?? 0) }}
                                                lượt xem)</span>
                                        </div>

                                        {{-- CTA Button --}}
                                        <div class="mt-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-full group-hover/book:bg-purple-700 transition shadow-md">
                                                Khám phá ngay <i
                                                    class="fas fa-arrow-right text-xs group-hover/book:translate-x-1 transition-transform"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    {{-- Widget: Tác giả ngày hôm nay --}}
                    @if(isset($dailyAuthor) && $dailyAuthor)
                        <div
                            class="bg-gradient-to-br from-sky-50 via-blue-50 to-cyan-50 rounded-2xl p-7 border border-sky-100 shadow-lg relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                            {{-- Decorative --}}
                            <div
                                class="absolute -top-8 -right-8 w-28 h-28 bg-sky-200/30 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500">
                            </div>
                            <div
                                class="absolute -bottom-6 -left-6 w-20 h-20 bg-blue-200/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-5 relative">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-11 h-11 bg-gradient-to-br from-sky-500 to-blue-500 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-user-pen text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-serif font-bold text-gray-800 text-base leading-none">Tác Giả Hôm Nay
                                        </h3>
                                        <span class="text-xs text-gray-400">Khám phá tác giả</span>
                                    </div>
                                </div>
                                <a href="{{ route('authors.index') }}"
                                    class="text-xs text-sky-500 hover:text-sky-700 font-bold flex items-center gap-1"
                                    title="Xem tất cả tác giả">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                            {{-- Author Card --}}
                            <a href="{{ route('authors.show', $dailyAuthor->slug ?? $dailyAuthor->id) }}" class="block group/author">
                                <div class="flex gap-5">
                                    {{-- Author Photo --}}
                                    <div
                                        class="w-24 h-24 rounded-full overflow-hidden shadow-lg flex-shrink-0 transform group-hover/author:scale-105 transition-transform duration-300 border-3 border-white">
                                        @php
                                            $photoUrl = !empty($dailyAuthor->photo)
                                                ? (str_starts_with($dailyAuthor->photo, 'http') ? $dailyAuthor->photo : asset('storage/' . $dailyAuthor->photo))
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($dailyAuthor->name) . '&background=0284C7&color=fff&size=96';
                                        @endphp
                                        <img src="{{ $photoUrl }}" alt="{{ $dailyAuthor->name }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    {{-- Author Info --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <h4
                                            class="font-bold text-gray-800 text-base leading-snug group-hover/author:text-sky-600 transition-colors">
                                            {{ $dailyAuthor->name }}
                                        </h4>
                                        
                                        @if($dailyAuthor->nationality)
                                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                                                <i class="fas fa-globe-asia text-xs text-gray-400"></i>
                                                {{ $dailyAuthor->nationality }}
                                            </p>
                                        @endif

                                        @if($dailyAuthor->birth_year)
                                            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1.5">
                                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                                {{ $dailyAuthor->birth_year }}{{ $dailyAuthor->death_year ? ' - ' . $dailyAuthor->death_year : '' }}
                                            </p>
                                        @endif

                                        {{-- CTA Button --}}
                                        <div class="mt-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 text-white text-xs font-bold rounded-full group-hover/author:bg-sky-700 transition shadow-md">
                                                Xem tác giả <i
                                                    class="fas fa-arrow-right text-[10px] group-hover/author:translate-x-1 transition-transform"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Full Bio --}}
                                @if($dailyAuthor->bio)
                                    <p class="text-gray-600 text-sm mt-4 leading-relaxed italic">
                                        "{{ strip_tags($dailyAuthor->bio) }}"
                                    </p>
                                @endif
                            </a>
                        </div>
                    @endif

                    {{-- Widget 1: Top Thịnh Hành - Redesigned --}}
                    <div
                        class="bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 rounded-2xl p-7 border border-orange-100 shadow-lg relative overflow-hidden group/widget hover:shadow-xl transition-shadow duration-300">
                        {{-- Decorative Elements --}}
                        <div
                            class="absolute -top-8 -right-8 w-32 h-32 bg-gradient-to-br from-orange-200/40 to-red-200/30 rounded-full blur-2xl pointer-events-none group-hover/widget:scale-110 transition-transform duration-500">
                        </div>
                        <div
                            class="absolute -bottom-6 -left-6 w-24 h-24 bg-gradient-to-tr from-amber-200/30 to-yellow-200/40 rounded-full blur-xl pointer-events-none">
                        </div>
                        <div
                            class="absolute top-1/2 right-4 w-16 h-16 bg-red-100/20 rounded-full blur-xl pointer-events-none">
                        </div>

                        {{-- Header --}}
                        <div class="flex items-center justify-between mb-6 relative">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg animate-pulse">
                                    <i class="fas fa-fire-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif font-bold text-lg text-gray-800 leading-none">Top Thịnh Hành
                                    </h3>
                                    <span class="text-xs text-orange-600 font-medium">🔥 Được đọc nhiều nhất</span>
                                </div>
                            </div>
                            <a href="{{ route('books.list', ['sort' => 'views']) }}"
                                class="text-xs text-orange-500 hover:text-orange-700 font-bold flex items-center gap-1 transition">
                                Xem tất cả <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>


                        {{-- Book List --}}
                        <div class="space-y-3 relative">
                            @if(isset($books) && $books->count() > 0)
                                @foreach($books->sortByDesc('view_count')->take(5)->values() as $index => $book)
                                    @php
                                        $coverUrl = !empty($book->cover_image)
                                            ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset('storage/' . $book->cover_image))
                                            : 'https://placehold.co/150x225?text=No+Image';

                                        // Medal colors for top 3
                                        $medalColors = [
                                            0 => 'from-yellow-400 to-amber-500 text-yellow-900', // Gold
                                            1 => 'from-gray-300 to-slate-400 text-gray-700',     // Silver
                                            2 => 'from-orange-400 to-orange-600 text-orange-900' // Bronze
                                        ];
                                        $rankBg = $medalColors[$index] ?? 'from-gray-100 to-gray-200 text-gray-500';
                                        $isTop3 = $index < 3;
                                    @endphp

                                    <a href="{{ route('detail', $book->slug) }}"
                                        class="flex items-center gap-4 p-3 rounded-xl {{ $isTop3 ? 'bg-white/70 border border-orange-100' : 'bg-white/50 hover:bg-white/80' }} hover:shadow-md transition-all duration-300 cursor-pointer group/item transform hover:-translate-x-1">

                                        {{-- Rank Badge --}}
                                        <div class="relative flex-shrink-0">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $rankBg }} flex items-center justify-center shadow-md font-bold text-sm {{ $isTop3 ? 'ring-2 ring-white' : '' }}">
                                                @if($index == 0)
                                                    <i class="fas fa-crown text-xs"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </div>
                                        </div>


                                        {{-- Book Cover --}}
                                        <div
                                            class="w-14 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0 shadow-md border-2 border-white transform group-hover/item:scale-105 transition-transform duration-300">
                                            <img src="{{ $coverUrl }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        </div>

                                        {{-- Book Info --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-gray-800 line-clamp-2 group-hover/item:text-orange-600 transition leading-snug"
                                                title="{{ $book->title }}">
                                                {{ $book->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                {{ $book->author_name ?? 'Ẩn danh' }}
                                            </p>

                                            <div class="flex items-center gap-3 text-xs mt-2">
                                                {{-- Rating --}}
                                                <span
                                                    class="flex items-center gap-1 text-yellow-500 font-bold bg-yellow-50 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-star text-[10px]"></i>
                                                    {{ number_format($book->posts_avg_rating ?? $book->avg_rating ?? 0, 1) }}
                                                </span>

                                                {{-- Views --}}
                                                <span
                                                    class="flex items-center gap-1 text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-full"
                                                    title="Lượt đọc">
                                                    <i class="far fa-eye text-[10px]"></i>
                                                    {{ number_format($book->view_count) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Arrow --}}
                                        <i
                                            class="fas fa-chevron-right text-gray-300 group-hover/item:text-orange-500 group-hover/item:translate-x-1 transition-all"></i>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center text-sm text-gray-400 py-8 italic">
                                    <i class="fas fa-book-open text-2xl text-gray-300 mb-2 block"></i>
                                    Dữ liệu đang cập nhật...
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Widget 2: Thể Loại - Redesigned --}}
                    <div
                        class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 rounded-2xl p-6 border border-emerald-100 shadow-lg relative overflow-hidden">
                        {{-- Decorative --}}
                        <div
                            class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-200/40 rounded-full blur-2xl pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 w-16 h-16 bg-teal-200/30 rounded-full blur-xl pointer-events-none">
                        </div>

                        <div class="flex items-center gap-3 mb-4 relative">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-gray-800 leading-none">Thể Loại Sách</h3>
                                <span class="text-[10px] text-gray-400">Khám phá theo sở thích</span>
                            </div>
                        </div>


                        <div class="flex flex-wrap gap-2 relative">
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories->take(12) as $category)
                                    <a href="{{ route('books.list', ['categories' => [$category->name]]) }}"
                                        class="group flex items-center gap-1.5 bg-white/80 backdrop-blur-sm text-gray-600 px-3 py-1.5 rounded-full text-xs font-medium border border-white hover:border-emerald-400 hover:bg-emerald-500 hover:text-white hover:shadow-lg transition-all duration-300">
                                        <span>{{ $category->name }}</span>
                                        <span
                                            class="bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded-full text-[9px] font-bold group-hover:bg-white/30 group-hover:text-white transition">
                                            {{ $category->books_count ?? 0 }}
                                        </span>
                                    </a>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-400 italic">Đang cập nhật...</span>
                            @endif
                        </div>

                        @if(isset($categories) && $categories->count() > 12)
                            <div class="mt-4 text-center relative">
                                <a href="{{ route('books.list') }}"
                                    class="inline-flex items-center gap-1 text-xs text-emerald-600 font-bold hover:text-emerald-700 transition">
                                    Xem tất cả <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Widget 3: Liên Kết Mua Sách - Redesigned --}}
                    <div
                        class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-2xl p-6 border border-amber-100 shadow-lg relative overflow-hidden">
                        {{-- Decorative --}}
                        <div
                            class="absolute -top-6 -right-6 w-20 h-20 bg-amber-200/40 rounded-full blur-2xl pointer-events-none">
                        </div>

                        <div class="flex items-center gap-3 mb-4 relative">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-shopping-bag text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-gray-800 leading-none">Mua Sách Giá Tốt</h3>
                                <span class="text-[10px] text-gray-400">Đối tác uy tín</span>
                            </div>
                        </div>


                        <div class="space-y-2 relative">
                            <a href="https://tiki.vn/nha-sach-tiki/c8322" target="_blank"
                                class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-blue-300 hover:bg-blue-50 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/43/Logo_Tiki_2023.png"
                                        class="w-8 h-8 object-contain" alt="Tiki">
                                    <div>
                                        <span class="font-bold text-sm text-gray-700 group-hover:text-blue-600 block">Tiki
                                            Trading</span>
                                        <span class="text-[10px] text-green-600 font-bold">🔥 Giảm tới 35%</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <a href="https://shopee.vn/nhasachphuongnam" target="_blank"
                                class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-orange-300 hover:bg-orange-50 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg"
                                        class="w-8 h-8 object-contain" alt="Shopee">
                                    <div>
                                        <span
                                            class="font-bold text-sm text-gray-700 group-hover:text-orange-600 block">Shopee
                                            Mall</span>
                                        <span class="text-[10px] text-orange-500 font-bold">🚚 Freeship Extra</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <a href="https://www.fahasa.com/" target="_blank"
                                class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-red-300 hover:bg-red-50 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        F</div>
                                    <div>
                                        <span
                                            class="font-bold text-sm text-gray-700 group-hover:text-red-600 block">Fahasa.com</span>
                                        <span class="text-[10px] text-gray-500">✓ Sách chính hãng</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Widget: Thống Kê Cộng Đồng - Light Style --}}
                    @if(isset($communityStats))
                        <div
                            class="bg-gradient-to-br from-blue-50 via-indigo-50 to-violet-50 rounded-2xl p-6 border border-blue-100 shadow-lg relative overflow-hidden">
                            {{-- Decorative --}}
                            <div
                                class="absolute -top-6 -right-6 w-24 h-24 bg-blue-200/40 rounded-full blur-2xl pointer-events-none">
                            </div>
                            <div
                                class="absolute -bottom-4 -left-4 w-16 h-16 bg-indigo-200/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            <div class="flex items-center gap-3 mb-4 relative">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-chart-pie text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif font-bold text-gray-800 leading-none">Thống Kê</h3>
                                    <span class="text-[10px] text-gray-400">Số liệu của Góc Sách</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 relative">
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-blue-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-blue-600 transition">
                                        {{ number_format($communityStats['books']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Cuốn sách</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-blue-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                        {{ number_format($communityStats['members']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Thành viên</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-blue-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-violet-600 transition">
                                        {{ number_format($communityStats['reviews']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Đánh giá</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-blue-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-purple-600 transition">
                                        {{ number_format($communityStats['comments']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Bình luận</div>
                                </div>
                            </div>

                            {{-- Online Users --}}
                            <div class="mt-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl p-3 flex items-center justify-between text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                    <span class="text-sm font-medium">Đang online</span>
                                </div>
                                <span class="text-xl font-bold">{{ number_format($communityStats['online_users'] ?? 0) }}</span>
                            </div>

                            {{-- Total Visits --}}
                            <div class="mt-2 bg-white/60 rounded-xl p-3 border border-white">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 flex items-center gap-2">
                                        <i class="fas fa-eye text-blue-500"></i>
                                        Tổng lượt truy cập
                                    </span>
                                    <span class="font-bold text-gray-800">{{ number_format($communityStats['total_visits'] ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div> {{-- END DIV STICKY GROUP --}}

            </div>
        </div> {{-- END CỘT 4 --}}
        </div>
    </main>
    </div>
    </main>

    {{-- Đã chuyển Thống kê cộng đồng lên sidebar --}}
    {{-- Đã chuyển Thống kê cộng đồng lên sidebar --}}

    {{-- Bỏ MODAL POPUP CŨ --}}
@endsection

@push('scripts')
    <script>
        // --- BIẾN TOÀN CỤC ---
        const currentUserId = "{{ Auth::id() }}";

        // --- 1. KHỞI TẠO KHI TRANG LOAD ---
        document.addEventListener('DOMContentLoaded', function () {
            // Hero Slider
            const sliderWrapper = document.getElementById('sliderWrapper');
            const dots = document.querySelectorAll('.indicator-dot');
            const prevBtn = document.getElementById('heroPrevBtn');
            const nextBtn = document.getElementById('heroNextBtn');
            const totalSlides = {{ isset($heroSlides) ? count($heroSlides) : 0 }};
            let currentSlide = 0;
            let slideInterval;

            if (totalSlides > 1) {
                function updateSlider() {
                    if (!sliderWrapper) return;
                    sliderWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('bg-brand-accent', index === currentSlide);
                        dot.classList.toggle('w-8', index === currentSlide);
                        dot.classList.toggle('bg-white/30', index !== currentSlide);
                    });
                }
                function nextSlide() { currentSlide = (currentSlide + 1) % totalSlides; updateSlider(); resetTimer(); }
                function prevSlide() { currentSlide = (currentSlide - 1 + totalSlides) % totalSlides; updateSlider(); resetTimer(); }
                function startTimer() { slideInterval = setInterval(nextSlide, 5000); }
                function resetTimer() { clearInterval(slideInterval); startTimer(); }
                if (nextBtn) nextBtn.addEventListener('click', nextSlide);
                if (prevBtn) prevBtn.addEventListener('click', prevSlide);
                dots.forEach((dot) => {
                    dot.addEventListener('click', function () {
                        currentSlide = parseInt(this.getAttribute('data-index'));
                        updateSlider(); resetTimer();
                    });
                });
                startTimer();
            }

            // New Books Slider
            const sliderNewBooks = document.getElementById('sliderNewBooks');
            const btnPrevNew = document.getElementById('btnPrevNewBooks');
            const btnNextNew = document.getElementById('btnNextNewBooks');
            if (sliderNewBooks && btnPrevNew && btnNextNew) {
                btnNextNew.addEventListener('click', () => sliderNewBooks.scrollBy({ left: 220, behavior: 'smooth' }));
                btnPrevNew.addEventListener('click', () => sliderNewBooks.scrollBy({ left: -220, behavior: 'smooth' }));
            }

            attachPaginationEvents();
            const initialSort = new URLSearchParams(window.location.search).get('sort_review') || 'latest';
            updateTabUI(initialSort);

            // --- LOGIC CUỘN XUỐNG VÀ HIGHLIGHT COMMENT/REPLY TỪ THÔNG BÁO ---
            if (window.location.hash) {
                const targetId = window.location.hash.substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    const parentReplySection = targetElement.closest('[id^="reply-section-"]');

                    if (parentReplySection && parentReplySection.classList.contains('hidden')) {
                        const parentCommentId = parentReplySection.id.replace('reply-section-', '');
                        parentReplySection.classList.remove('hidden');
                        const chevron = document.getElementById(`chevron-reply-${parentCommentId}`);
                        if (chevron) chevron.style.transform = 'rotate(180deg)';

                        setTimeout(() => {
                            targetElement.scrollIntoView({ behavior: "smooth", block: "center" });
                            targetElement.classList.add('bg-yellow-100', 'rounded-lg', 'ring-2', 'ring-yellow-400');
                            setTimeout(() => {
                                targetElement.classList.remove('bg-yellow-100', 'ring-2', 'ring-yellow-400');
                            }, 3000);
                        }, 300);
                    } else if (targetElement) {
                        targetElement.scrollIntoView({ behavior: "smooth", block: "center" });
                        targetElement.classList.add('bg-yellow-50', 'border-yellow-200');
                        setTimeout(() => {
                            targetElement.classList.remove('bg-yellow-50', 'border-yellow-200');
                        }, 3000);
                    }
                }
            }
        });

        // --- 2. HÀM ĐIỀU KHIỂN GIAO DIỆN (TOGGLE) ---

        function togglePostComments(postId) {
            const list = document.getElementById(`comments-list-${postId}`);
            const chevron = document.getElementById(`chevron-${postId}`);
            const input = document.getElementById(`post-comment-input-${postId}`);

            if (list) {
                const isHidden = list.classList.contains('hidden');
                list.classList.toggle('hidden');
                if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                if (isHidden && input) input.focus();
            }
        }

        function toggleReplySection(commentId) {
            const section = document.getElementById(`reply-section-${commentId}`);
            const input = document.getElementById(`reply-input-${commentId}`);

            if (section) {
                const isHidden = section.classList.contains('hidden');
                document.querySelectorAll('[id^="reply-section-"]').forEach(el => el.classList.add('hidden'));
                section.classList.toggle('hidden');
                if (!isHidden) section.classList.add('hidden');
                else if (input) input.focus();
            }
        }

        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // --- 3. HÀM Xử LÝ DỮ LIỆU (AJAX & FETCH) ---

        function loadComments(urlOrSortType) {
            // Xác định sortType từ url hoặc tham số
            let sortType = urlOrSortType;
            let url;

            if (urlOrSortType.includes('http') || urlOrSortType.includes('?')) {
                url = urlOrSortType;
                // Trích xuất sortType từ URL nếu có
                const urlParams = new URLSearchParams(urlOrSortType.includes('?') ? urlOrSortType.split('?')[1] : '');
                sortType = urlParams.get('sort_review') || 'latest';
            } else {
                url = `/?sort_review=${urlOrSortType}`;
            }

            const spinner = document.getElementById('loading-spinner');
            const contentWrapper = document.getElementById('comments-content-wrapper');

            if (spinner) spinner.classList.remove('hidden');
            if (contentWrapper) contentWrapper.style.opacity = '0.5';

            // Cập nhật UI tab ngay lập tức
            updateTabUI(sortType);

            fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest" } })
                .then(response => response.text())
                .then(html => {
                    if (contentWrapper) {
                        contentWrapper.innerHTML = html;
                        contentWrapper.style.opacity = '1';
                        attachPaginationEvents();
                    }
                })
                .finally(() => { if (spinner) spinner.classList.add('hidden'); });
        }

        function submitComment(postId, parentId = null, event) {
            if (event) event.preventDefault();
            if (!currentUserId) { alert("Vui lòng đăng nhập!"); window.location.href = "/login"; return; }

            const elementBox = parentId
                ? document.getElementById(`post-comment-input-${parentId}`)
                : document.getElementById(`post-comment-input-${postId}`);

            if (!elementBox) return;

            const valueContent = elementBox.value.trim();
            if (!valueContent) {
                alert("Vui lòng nhập nội dung!");
                return;
            }

            const btnAction = event.currentTarget || event.target.closest('button');
            const oldHtml = btnAction ? btnAction.innerHTML : '';
            if (btnAction) {
                btnAction.disabled = true;
                btnAction.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`/post/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: valueContent, parent_id: parentId })
            })
                .then(async r => {
                    const d = await r.json();
                    if (!r.ok) throw new Error(d.message || "Lá»i server");
                    return d;
                })
                .then(data => {
                    if (data.success) {
                        elementBox.value = '';
                        elementBox.style.height = 'auto';

                        const countLabels = document.querySelectorAll(`.comment-count-${postId}`);
                        countLabels.forEach(el => {
                            el.innerText = `Bình luận (${data.new_count})`;
                        });

                        let avatarHtml = '';
                        if (data.comment.user_frame) {
                            avatarHtml = `
                                                                <div class="relative w-10 h-10 inline-block flex-shrink-0">
                                                                    <img src="${data.comment.user_frame}" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                                                                    <div class="absolute inset-0 flex items-center justify-center z-0">
                                                                        <img src="${data.comment.user_avatar}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                                                    </div>
                                                                </div>
                                                            `;
                        } else {
                            avatarHtml = `<img src="${data.comment.user_avatar}" class="w-9 h-9 rounded-full border border-white shadow-sm flex-shrink-0">`;
                        }

                        const newCommentHtml = `
                                                        <div class="flex gap-3 animate-fade-in mb-6">
                                                            ${avatarHtml}
                                                            <div class="flex-1">
                                                                <div class="bg-white p-3 rounded-2xl rounded-tl-none border border-gray-100 shadow-sm">
                                                                    <div class="flex justify-between items-center mb-1">
                                                                        <h5 class="font-bold text-xs text-gray-800">${data.comment.user_name}</h5>
                                                                        <span class="text-[10px] text-gray-400">${data.comment.created_at}</span>
                                                                    </div>
                                                                    <p class="text-xs text-gray-600">${data.comment.content}</p>
                                                                </div>
                                                                ${!parentId ? `
                                                                <div class="flex gap-3 mt-1 ml-2">
                                                                    <button onclick="handleLike(${data.comment.id}, 'comment')" 
                                                                            id="like-btn-comment-${data.comment.id}"
                                                                            class="text-[10px] font-bold flex items-center gap-1 text-gray-400 hover:text-red-500">
                                                                        <i id="like-icon-comment-${data.comment.id}" class="far fa-heart text-xs"></i>
                                                                        <span id="like-count-comment-${data.comment.id}">0</span>
                                                                    </button>
                                                                    <button onclick="toggleReplySection(${data.comment.id})" class="text-[10px] font-bold text-gray-400 hover:text-blue-500 transition">
                                                                        Trả lời (0)
                                                                    </button>
                                                                </div>
                                                                <div id="reply-section-${data.comment.id}" class="hidden mt-3 space-y-4 border-l-2 border-gray-100 pl-4 animate-fade-in">
                                                                    <div class="flex gap-2 relative mt-2">
                                                                        <textarea id="reply-input-${data.comment.id}" rows="1" 
                                                                                  class="w-full text-xs p-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green resize-none shadow-sm" 
                                                                                  placeholder="Nhập câu trả lời..."></textarea>
                                                                        <button type="button" onclick="submitComment(${postId}, ${data.comment.id}, event)" 
                                                                                class="text-brand-green px-3 py-1 bg-brand-green/10 rounded-lg text-xs font-bold hover:bg-brand-green hover:text-white transition">Gửi</button>
                                                                    </div>
                                                                </div>
                                                                ` : `
                                                                <button onclick="handleLike(${data.comment.id}, 'comment')" 
                                                                        id="like-btn-comment-${data.comment.id}"
                                                                        class="text-[9px] font-bold ml-2 mt-1 flex items-center gap-1 text-gray-400">
                                                                    <i id="like-icon-comment-${data.comment.id}" class="far fa-heart"></i>
                                                                    <span id="like-count-comment-${data.comment.id}">0</span>
                                                                </button>
                                                                `}
                                                            </div>
                                                        </div>`;

                        if (parentId) {
                            const replySection = document.getElementById(`reply-section-${parentId}`);
                            replySection.classList.remove('hidden');
                            replySection.insertAdjacentHTML('beforeend', newCommentHtml);
                        } else {
                            const list = document.querySelector(`#comments-list-${postId} .space-y-6`);
                            const emptyMsg = list.querySelector('p.italic');
                            if (emptyMsg) emptyMsg.remove();
                            list.insertAdjacentHTML('afterbegin', newCommentHtml);
                        }

                        if (btnAction) {
                            btnAction.disabled = false;
                            btnAction.innerHTML = oldHtml;
                        }
                    }
                })
                .catch(e => {
                    alert("Lá»i: " + e.message);
                    if (btnAction) {
                        btnAction.disabled = false;
                        btnAction.innerHTML = oldHtml;
                    }
                });
        }

        function resetBtn(btn, html) {
            if (btn) {
                btn.innerHTML = html;
                btn.disabled = false;
            }
        }

        function handleLike(id, type) {
            if (!currentUserId) { alert("Vui lòng đăng nhập!"); window.location.href = "/login"; return; }
            const btn = document.getElementById(`like-btn-${type}-${id}`);
            const icon = document.getElementById(`like-icon-${type}-${id}`);
            const countSpan = document.getElementById(`like-count-${type}-${id}`);
            if (!btn || !icon || !countSpan) return;

            const isLiked = icon.classList.contains('fas');
            fetch('/like', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ id: id, type: type })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        icon.classList.toggle('fas', data.liked);
                        icon.classList.toggle('far', !data.liked);
                        icon.classList.toggle('text-red-500', data.liked);
                        btn.classList.toggle('text-red-500', data.liked);
                        countSpan.innerText = data.count;
                    }
                });
        }

        function submitReply(commentId, event) {
            if (event) event.preventDefault();

            const input = document.getElementById(`reply-input-${commentId}`);
            if (!input) return;

            const content = input.value.trim();
            if (!content) {
                alert("Vui lòng nhập nội dung!");
                return;
            }

            const btn = event.currentTarget || event.target.closest('button');
            const oldHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`/comment/${commentId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: content })
            })
                .then(async r => {
                    const d = await r.json();
                    if (!r.ok) throw new Error(d.message || "Lá»i server");
                    return d;
                })
                .then(data => {
                    if (data.success) {
                        input.value = '';
                        input.style.height = 'auto';

                        let avatarHtml = '';
                        if (data.user_frame) {
                            avatarHtml = `
                                                                <div class="relative w-8 h-8 inline-block flex-shrink-0">
                                                                    <img src="${data.user_frame}" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                                                                    <div class="absolute inset-0 flex items-center justify-center z-0">
                                                                        <img src="${data.user_avatar}" class="w-6 h-6 rounded-full object-cover">
                                                                    </div>
                                                                </div>
                                                            `;
                        } else {
                            avatarHtml = `<img src="${data.user_avatar}" class="w-7 h-7 rounded-full flex-shrink-0">`;
                        }

                        const replyHtml = `
                                                        <div class="flex gap-2 animate-fade-in">
                                                            ${avatarHtml}
                                                            <div class="flex-1">
                                                                <div class="bg-white p-2 rounded-xl rounded-tl-none border border-gray-100 shadow-sm">
                                                                    <div class="flex justify-between items-center mb-1">
                                                                        <h6 class="font-bold text-[10px] text-gray-700">${data.user_name}</h6>
                                                                        <span class="text-[9px] text-gray-400">${data.time}</span>
                                                                    </div>
                                                                    <p class="text-[11px] text-gray-600">${data.content}</p>
                                                                </div>
                                                                <button onclick="handleLike(${data.reply_id}, 'comment')"
                                                                    id="like-btn-comment-${data.reply_id}"
                                                                    class="text-[9px] font-bold ml-2 mt-1 flex items-center gap-1 text-gray-400 hover:text-red-500 transition">
                                                                    <i id="like-icon-comment-${data.reply_id}" class="far fa-heart"></i>
                                                                    <span id="like-count-comment-${data.reply_id}">0</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        `;

                        const replySection = document.getElementById(`reply-section-${commentId}`);
                        const replyList = replySection.querySelector('.space-y-4');
                        const emptyMsg = replyList.querySelector('p.italic');
                        if (emptyMsg) emptyMsg.remove();
                        replyList.insertAdjacentHTML('beforeend', replyHtml);

                        // Cập nhật số lượng reply sử dụng ID cụ thể
                        const replyCountSpan = document.getElementById(`reply-count-${commentId}`);
                        if (replyCountSpan) {
                            const currentCount = parseInt(replyCountSpan.innerText.match(/\d+/) || 0);
                            replyCountSpan.innerText = `(${currentCount + 1})`;
                        }
                    }
                })
                .catch(e => {
                    alert("Lá»i: " + e.message);
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = oldHtml;
                    }
                });
        }

        // --- 4. HÀM ĐIỀU KHIỂN BÀI REVIEW SÁCH ---
        function switchReviewTab(tabType) {
            const latestContainer = document.getElementById('reviews-latest-container');
            const hotContainer = document.getElementById('reviews-hot-container');
            const tabLatest = document.getElementById('tab-review-latest');
            const tabHot = document.getElementById('tab-review-hot');

            if (!tabLatest || !tabHot) return;

            const activeClasses = ['bg-white', 'text-rose-600', 'shadow-sm'];
            const inactiveClasses = ['text-gray-500', 'hover:text-rose-500'];

            if (tabType === 'latest') {
                if (latestContainer) latestContainer.classList.remove('hidden');
                if (hotContainer) hotContainer.classList.add('hidden');
                tabLatest.classList.remove(...inactiveClasses);
                tabLatest.classList.add(...activeClasses);
                tabHot.classList.remove(...activeClasses);
                tabHot.classList.add(...inactiveClasses);
            } else {
                if (latestContainer) latestContainer.classList.add('hidden');
                if (hotContainer) hotContainer.classList.remove('hidden');
                tabLatest.classList.remove(...activeClasses);
                tabLatest.classList.add(...inactiveClasses);
                tabHot.classList.remove(...inactiveClasses);
                tabHot.classList.add(...activeClasses);
            }
        }

        // --- Slider controls for reviews ---
        document.addEventListener('DOMContentLoaded', function () {
            const sliderContainers = document.querySelectorAll('.slider-reviews');
            sliderContainers.forEach(slider => {
                const parent = slider.parentElement;
                const prevBtn = parent.querySelector('.btn-prev-reviews');
                const nextBtn = parent.querySelector('.btn-next-reviews');
                if (prevBtn) prevBtn.addEventListener('click', () => slider.scrollBy({ left: -300, behavior: 'smooth' }));
                if (nextBtn) nextBtn.addEventListener('click', () => slider.scrollBy({ left: 300, behavior: 'smooth' }));
            });
        });

        function attachPaginationEvents() {
            document.querySelectorAll('.ajax-pagination-link').forEach(link => {
                link.onclick = function (e) { e.preventDefault(); loadComments(this.getAttribute('href')); };
            });
        }

        function updateTabUI(sortType) {
            const tabLatest = document.getElementById('tab-latest');
            const tabPopular = document.getElementById('tab-popular');
            if (!tabLatest || !tabPopular) return;
            const active = ['bg-white', 'text-brand-green', 'shadow-sm'];
            const inactive = ['text-gray-500', 'hover:text-gray-700'];

            tabLatest.classList.remove(...(sortType === 'latest' ? inactive : active));
            tabLatest.classList.add(...(sortType === 'latest' ? active : inactive));
            tabPopular.classList.remove(...(sortType === 'popular' ? inactive : active));
            tabPopular.classList.add(...(sortType === 'popular' ? active : inactive));
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush