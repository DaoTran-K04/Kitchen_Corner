@extends('layouts.app')

@section('title', 'Trang Chủ - Góc Bếp')

@section('content')
    {{-- Hero: Full-viewport, ảnh nền theo chuẩn lý thuyết --}}
    @include('partials.hero-section')


    {{-- 🔒 GUEST BANNER - Chỉ hiển thị khi CHƯA đăng nhập --}}
    @guest
    <div class="relative overflow-hidden bg-gradient-to-r from-brand-green via-[#3a5a45] to-[#2D4539] text-white">
        {{-- Decorative blobs --}}
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-brand-accent/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-8 right-20 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand-accent/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-4 py-6 sm:py-8 flex flex-col sm:flex-row items-center justify-between gap-5 relative z-10">
            {{-- Text Content --}}
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <span class="text-3xl">🔓</span>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-serif font-bold leading-tight">
                        Mở khóa trải nghiệm đầy đủ cùng <span class="text-brand-accent">Góc Bếp</span>!
                    </h2>
                    <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2 text-sm text-white/80 font-medium">
                        <span><i class="fas fa-lock-open text-brand-accent mr-1.5"></i>Tủ Lạnh Web AI</span>
                        <span><i class="fas fa-heart text-brand-accent mr-1.5"></i>Lưu &amp; Thích công thức</span>
                        <span><i class="fas fa-comments text-brand-accent mr-1.5"></i>Bình luận &amp; Theo dõi</span>
                        <span><i class="fas fa-plus-circle text-brand-accent mr-1.5"></i>Đăng công thức của bạn</span>
                    </div>
                </div>
            </div>
            {{-- CTA Buttons --}}
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('register') }}"
                    class="bg-brand-accent text-brand-green font-black px-6 py-3 rounded-full hover:bg-amber-300 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 transform text-sm whitespace-nowrap">
                    <i class="fas fa-rocket mr-2"></i>Đăng Ký Miễn Phí
                </a>
                <a href="{{ route('login') }}"
                    class="bg-white/10 backdrop-blur-sm border border-white/30 text-white font-bold px-5 py-3 rounded-full hover:bg-white/20 transition text-sm whitespace-nowrap">
                    Đăng Nhập
                </a>
            </div>
        </div>
    </div>
    @endguest

    {{-- MAIN LAYOUT --}}
    <main class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">


            <div class="lg:col-span-8 space-y-16 order-1 lg:order-2">

                {{-- 1. TẠP CHÍ Ẩm Thực --}}
                <section class="relative reveal">
                    {{-- Decorative --}}
                    <div
                        class="absolute -top-6 -left-6 w-32 h-32 bg-brand-accent/5 rounded-full blur-3xl pointer-events-none">
                    </div>

                    <div class="flex justify-between items-end mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-brand-accent to-brand-green rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-newspaper text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 font-serif flex items-center gap-2 sm:gap-3">
                                    Tạp Chí Ẩm Thực
                                    <span class="text-xs bg-brand-green/10 text-brand-green px-2.5 py-1 rounded-full font-bold">LIVE NEWS</span>
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">Góc nhìn sâu sắc về văn hóa ẩm thực và sức khỏe (Cập nhật tự động)</p>
                            </div>
                        </div>
                        <a href="{{ route('articles.index') }}" class="hidden md:flex items-center gap-2 text-sm font-bold text-brand-green hover:text-brand-accent transition group">
                            <span>Xem tất cả</span>
                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        @if(isset($featuredArticle))
                            <a href="{{ $featuredArticle->link }}" class="md:col-span-3 group relative block">
                                <div class="relative h-64 md:h-80 rounded-2xl overflow-hidden mb-4 shadow-md">
                                    <img loading="lazy" src="{{ $featuredArticle->image }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                    <span class="absolute top-4 left-4 bg-brand-accent text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Tiêu Điểm</span>
                                    <div class="absolute bottom-4 left-4 right-4 text-white">
                                        <div class="text-xs opacity-80 mb-2"><i class="far fa-calendar-alt mr-1"></i> {{ $featuredArticle->date }} • Theo Báo Điện Tử</div>
                                        <h3 class="text-2xl font-bold font-serif leading-tight group-hover:text-brand-beige transition">{{ $featuredArticle->title }}</h3>
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">{{ $featuredArticle->description }}</p>
                            </a>
                        @endif


                        <div class="md:col-span-2 flex flex-col gap-6">
                            @if(isset($sidebarArticles))
                                @foreach($sidebarArticles as $article)
                                    <a href="{{ $article->link }}" class="flex flex-col group relative block">
                                        <div class="h-32 rounded-xl overflow-hidden mb-3 relative">
                                            <img loading="lazy" src="{{ $article->image }}" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                                        </div>
                                        <div>
                                            <span class="text-brand-green text-xs font-bold uppercase">Tin Nóng</span>
                                            <h3 class="font-serif font-bold text-base text-gray-800 leading-snug group-hover:text-brand-green transition mt-1">{{ $article->title }}</h3>
                                        </div>
                                    </a>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </section>

                {{-- 1.5. CÔNG THỨC NỔI BẬT --}}
                @if((isset($latestPosts) && $latestPosts->count() > 0) || (isset($hotPosts) && $hotPosts->count() > 0))
                    <section id="featured-recipes-slider"
                        class="relative group/slider bg-white/50 backdrop-blur-sm rounded-[2.5rem] p-8 border border-white shadow-soft reveal">

                        {{-- Header với Tabs --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 relative gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-rose-500 to-brand-accent rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 font-serif">
                                        Công Thức Nổi Bật
                                    </h2>
                                    <p class="text-xs text-gray-500">Những món ngon được yêu thích nhất</p>
                                </div>
                            </div>

                            {{-- Tabs + Xem tất cả --}}
                            <div class="flex items-center gap-3">
                                <div class="bg-rose-100 rounded-full p-1 flex text-xs font-bold">
                                    <button onclick="switchRecipeTab('latest')" id="tab-recipe-latest"
                                        class="px-3 sm:px-4 py-1.5 rounded-full transition-all duration-300 bg-white text-rose-600 shadow-sm">
                                        <i class="fas fa-clock mr-1"></i>Mới nhất
                                    </button>
                                    <button onclick="switchRecipeTab('hot')" id="tab-recipe-hot"
                                        class="px-3 sm:px-4 py-1.5 rounded-full transition-all duration-300 text-gray-500 hover:text-rose-500">
                                        <i class="fas fa-fire mr-1"></i>Hot nhất
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Slider cho MỚI NHẤT --}}
                        <div id="recipes-latest-container" class="relative px-2 group/slider transition-all duration-500">
                            <div class="swiper swiperLatestFeatured w-full overflow-hidden pt-4 pb-2">
                                <div class="swiper-wrapper">
                                    @foreach($latestPosts as $recipe)
                                        <div class="swiper-slide !h-auto">
                                            @include('partials.recipe-card', ['recipe' => $recipe])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination !relative !bottom-auto !mt-8 !flex !justify-center !items-center !gap-1"></div>
                            </div>
                        </div>

                        {{-- Slider cho HOT NHẤT (Ẩn mặc định) --}}
                        <div id="recipes-hot-container" class="relative px-2 group/slider hidden opacity-0 transition-all duration-500">
                            <div class="swiper swiperHotFeatured w-full overflow-hidden pt-4 pb-2">
                                <div class="swiper-wrapper">
                                    @foreach($hotPosts as $recipe)
                                        <div class="swiper-slide !h-auto">
                                            @include('partials.recipe-card', ['recipe' => $recipe])
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination !relative !bottom-auto !mt-8 !flex !justify-center !items-center !gap-1"></div>
                            </div>
                        </div>

                        <script>
                            function switchRecipeTab(type) {
                                const latestCont = document.getElementById('recipes-latest-container');
                                const hotCont = document.getElementById('recipes-hot-container');
                                const tabLatest = document.getElementById('tab-recipe-latest');
                                const tabHot = document.getElementById('tab-recipe-hot');

                                if (type === 'latest') {
                                    latestCont.classList.remove('hidden');
                                    setTimeout(() => latestCont.classList.add('opacity-100'), 50);
                                    hotCont.classList.add('hidden', 'opacity-0');
                                    tabLatest.classList.add('bg-white', 'text-rose-600', 'shadow-sm');
                                    tabHot.classList.remove('bg-white', 'text-rose-600', 'shadow-sm');
                                    tabHot.classList.add('text-gray-500');
                                } else {
                                    hotCont.classList.remove('hidden');
                                    setTimeout(() => hotCont.classList.add('opacity-100'), 50);
                                    latestCont.classList.add('hidden', 'opacity-0');
                                    tabHot.classList.add('bg-white', 'text-rose-600', 'shadow-sm');
                                    tabLatest.classList.remove('bg-white', 'text-rose-600', 'shadow-sm');
                                    tabLatest.classList.add('text-gray-500');
                                }
                            }

                            document.addEventListener('DOMContentLoaded', () => {
                                const config = {
                                    grabCursor: true,
                                    slidesPerView: 1,
                                    spaceBetween: 16,
                                    loop: true,
                                    autoplay: { delay: 4000, disableOnInteraction: false },
                                    speed: 800,
                                    breakpoints: {
                                        640: { slidesPerView: 2, spaceBetween: 20 },
                                        1024: { slidesPerView: 3, spaceBetween: 24 },
                                    },
                                    pagination: { el: '.swiper-pagination', clickable: true },
                                };
                                new Swiper('.swiperLatestFeatured', config);
                                new Swiper('.swiperHotFeatured', config);
                            });
                        </script>
                    </section>
                @endif

                {{-- 2. CÔNG THỨC MỚI CẬP NHẬT --}}
                <section id="new-books"
                    class="relative group/slider bg-white/50 backdrop-blur-sm rounded-[2.5rem] p-8 border border-white shadow-soft reveal">
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-brand-green/10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-book-open text-brand-green"></i>
                            </div>
                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 font-serif flex items-center gap-2">
                                    Công Thức Mới Cập Nhật
                                    <span class="text-[10px] bg-red-500 text-white px-2.5 py-1 rounded-full font-black uppercase tracking-wider animate-pulse">MỚI</span>
                                </h2>
                                <p class="text-xs text-brand-muted mt-0.5">Những món ngon vừa được chia sẻ trong cộng đồng</p>
                            </div>
                        </div>
                        <a href="{{ route('recipes.list') }}"
                            class="text-xs font-bold px-4 py-2 bg-brand-green text-white hover:bg-brand-accent rounded-full transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>Khám phá món ngon</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    {{-- Slider Container (SWIPER GRID) --}}
                    <div class="relative px-2 group/slider">
                        {{-- Nút Prev --}}
                        <button id="btnPrevNewBooks"
                            class="absolute left-0 top-1/2 -translate-y-1/2 -ml-2 sm:-ml-5 z-20 w-12 h-12 bg-white rounded-full shadow-xl border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-[#9b2226] hover:border-[#9b2226] hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300 cursor-pointer">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        {{-- Nút Next --}}
                        <button id="btnNextNewBooks"
                            class="absolute right-0 top-1/2 -translate-y-1/2 -mr-2 sm:-mr-5 z-20 w-12 h-12 bg-white rounded-full shadow-xl border border-gray-100 flex items-center justify-center text-gray-600 hover:text-white hover:bg-[#9b2226] hover:border-[#9b2226] hover:scale-110 transition-all opacity-0 group-hover/slider:opacity-100 duration-300 cursor-pointer">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <style>
                            .swiper { opacity: 0; visibility: hidden; transition: opacity 0.3s ease; }
                            .swiper.swiper-initialized { opacity: 1; visibility: visible; }
                            .swiperNewBooks .swiper-pagination-bullet { width: 8px; height: 8px; background-color: #cbd5e1; opacity: 1; transition: all 0.3s; margin: 0 4px !important; }
                            .swiperNewBooks .swiper-pagination-bullet-active { width: 24px; border-radius: 4px; background-color: #2D4539; }
                            .swiperNewBooks .swiper-pagination { position: relative !important; margin-top: 32px !important; bottom: auto !important; }
                        </style>
                        <div class="swiper swiperNewBooks w-full overflow-visible pt-4 pb-2">
                            <div class="swiper-wrapper flex items-stretch">
                                @if(isset($recipes) && $recipes->count() > 0)
                                    @foreach($recipes->take(12) as $recipe)
                                        <div class="swiper-slide !h-auto">
                                            @include('partials.recipe-card', ['recipe' => $recipe])
                                        </div>
                                    @endforeach
                                @else
                                    <div class="swiper-slide w-full py-16 text-center text-gray-400 bg-gray-50/50 rounded-3xl border border-dashed border-gray-200">
                                        <i class="fas fa-pizza-slice text-4xl mb-3"></i>
                                        <p>Chưa có món mới nào hôm nay. Hãy là người đầu tiên chia sẻ!</p>
                                    </div>
                                @endif
                            </div>
                            <div class="swiper-pagination !relative !bottom-auto !mt-8 !flex !justify-center !items-center !gap-1"></div>
                        </div>
                    </div>

                    <!-- Injecting Swiper Styles & Scripts for this section -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
                    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const swiper = new Swiper('.swiperNewBooks', {
                                grabCursor: true,
                                slidesPerView: 1,
                                spaceBetween: 16,
                                loop: true,
                                autoplay: { delay: 3500, disableOnInteraction: false, reverseDirection: true },
                                speed: 800,
                                breakpoints: {
                                    640: { slidesPerView: 2, spaceBetween: 20 },
                                    1024: { slidesPerView: 3, spaceBetween: 24 },
                                    1280: { slidesPerView: 4, spaceBetween: 24 },
                                },
                                pagination: { el: '.swiper-pagination', clickable: true },
                                navigation: { nextEl: '#btnNextNewBooks', prevEl: '#btnPrevNewBooks' },
                            });
                        });
                    </script>


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
                <section id="community-posts" class="mb-16 scroll-mt-24 reveal">
                    <div class="bg-white/70 backdrop-blur-md rounded-[2.5rem] p-8 border border-white shadow-soft">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-1 h-8 bg-brand-accent rounded-full"></div>
                                <div>
                                    <h2
                                        class="text-2xl font-bold text-gray-800 font-serif leading-none flex items-center gap-3">
                                        Bình Luận Món Ăn
                                        {{-- Dữ liệu từ bảng comments --}}
                                        <span
                                            class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold">{{ $communityStats['comments'] ?? 0 }}
                                            bình luận</span>
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">Cảm nhận và bí quyết từ bếp gia đình</p>
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
                                <a href="{{ route('recipes.list') }}"
                                    class="text-xs text-gray-400 hover:text-gray-600 ml-3">Xem tất cả</a>
                            </div>
                        </div>

                        {{-- Container chứa danh công thức --}}
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
                                    Hoàn thành <span class="text-brand-accent font-bold">{{ $activeChallenge->target_count }} thử thách nấu ăn</span> 
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


            <div class="lg:col-span-4 order-2 lg:order-1">
                <div class="space-y-6 sm:space-y-8">
                    {{-- Widget 0: Châm Ngôn Hôm Nay --}}
                    @if(isset($dailyQuote) && $dailyQuote)
                        <div
                            class="glass rounded-3xl p-8 border border-white shadow-soft relative overflow-hidden group hover:shadow-xl transition-all duration-500 reveal">
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
                                    class="w-6 h-6 bg-gradient-to-br from-amber-50 to-orange-100 rounded-full flex items-center justify-center">
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

                    {{-- Widget: Hôm nay nấu gì? --}}
                    @if(isset($randomRecipe) && $randomRecipe)
                        <div
                            class="glass rounded-3xl p-8 border border-white shadow-soft relative overflow-hidden group hover:shadow-xl transition-all duration-500 reveal">
                            {{-- Decorative --}}
                            <div
                                class="absolute -top-8 -right-8 w-28 h-28 bg-red-100/30 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-500">
                            </div>
                            <div
                                class="absolute -bottom-6 -left-6 w-20 h-20 bg-orange-100/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-5 relative">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-11 h-11 bg-gradient-to-br from-brand-green to-brand-accent rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-dice text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-serif font-bold text-gray-800 text-base leading-none">Hôm nay nấu gì?
                                        </h3>
                                        <span class="text-xs text-gray-400">Gợi ý cho bạn</span>
                                    </div>
                                </div>
                                <a href="{{ route('recipes.list') }}"
                                    class="text-xs text-brand-green hover:text-red-800 font-bold flex items-center gap-1"
                                    title="Xem tất cả công thức">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>

                            {{-- Recipe Card --}}
                            <a href="{{ route('recipes.show', $randomRecipe->slug ?? $randomRecipe->id) }}" class="block group/recipe">
                                <div class="flex gap-5">
                                    {{-- Recipe Image --}}
                                    <div
                                        class="w-28 h-28 rounded-2xl overflow-hidden shadow-lg flex-shrink-0 transform group-hover/recipe:scale-105 transition-transform duration-300 border-2 border-white">
                                        <img loading="lazy" src="{{ $randomRecipe->thumbnail }}" alt="{{ $randomRecipe->title }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=400&auto=format&fit=crop'">

                                    </div>

                                    {{-- Recipe Info --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <h4
                                            class="font-extrabold text-gray-800 text-base leading-snug line-clamp-2 group-hover/recipe:text-brand-green transition-colors">
                                            {{ $randomRecipe->title }}
                                        </h4>
                                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                                            <i class="far fa-clock text-xs text-brand-green"></i>
                                            {{ $randomRecipe->cooking_time ?? '30' }} phút
                                        </p>

                                        {{-- Stats --}}
                                        <div class="flex items-center gap-3 mt-3">
                                            <span class="text-[10px] bg-emerald-50 text-brand-green px-2 py-0.5 rounded-full font-bold">
                                                <i class="fas fa-eye mr-1"></i> {{ number_format($randomRecipe->view_count ?? 0) }}
                                            </span>
                                            <span class="text-[10px] bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full font-bold">
                                                <i class="fas fa-comment mr-1"></i> {{ $randomRecipe->comments_count ?? 0 }}
                                            </span>
                                        </div>

                                        {{-- CTA Button --}}
                                        <div class="mt-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-green text-white text-[10px] font-black rounded-full group-hover/recipe:bg-brand-green transition shadow-md uppercase tracking-wider">
                                                NẤU NGAY <i class="fas fa-chevron-right text-[8px] group-hover/recipe:translate-x-1 transition-transform"></i>
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
                                class="absolute -bottom-6 -left-6 w-20 h-20 bg-amber-100/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            {{-- Header --}}
                            <div class="flex items-center justify-between mb-5 relative">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-11 h-11 bg-gradient-to-br from-sky-500 to-brand-green rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-user-pen text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-serif font-bold text-gray-800 text-base leading-none">Tác Giả Hôm Nay
                                        </h3>
                                        <span class="text-xs text-gray-400">Khám phá tác giả</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)"
                                    class="text-xs text-sky-500 hover:text-sky-700 font-bold flex items-center gap-1"
                                    title="Xem tất cả tác giả">
                                </a>
                            </div>

                            {{-- Author Card --}}
                            <a href="{{ isset($dailyAuthor) ? route('public.profile', $dailyAuthor->id) : 'javascript:void(0)' }}" class="block group/author">
                                <div class="flex gap-5">
                                    {{-- Author Photo --}}
                                    <div
                                        class="w-24 h-24 rounded-full overflow-hidden shadow-lg flex-shrink-0 transform group-hover/author:scale-105 transition-transform duration-300 border-3 border-white">
                                        @php
                                            $photoUrl = !empty($dailyAuthor->avatar)
                                                ? (str_starts_with($dailyAuthor->avatar, 'http') ? $dailyAuthor->avatar : asset('storage/' . $dailyAuthor->avatar))
                                                : 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($dailyAuthor->name) . '&background=0284C7&color=fff&size=96';
                                        @endphp
                                        <img loading="lazy" src="{{ $photoUrl }}" alt="{{ $dailyAuthor->name }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    {{-- Author Info --}}
                                    <div class="flex-1 min-w-0 py-1">
                                        <h4
                                            class="font-bold text-gray-800 text-base leading-snug group-hover/author:text-sky-600 transition-colors">
                                            {{ $dailyAuthor->name }}
                                        </h4>

                                        {{-- CTA Button --}}
                                        <div class="mt-3">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 text-white text-xs font-bold rounded-full group-hover/author:bg-sky-700 transition shadow-md">
                                                Xem đầu bếp <i
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

                    {{-- Today's Suggestion Widget --}}
                                <div class="bg-white rounded-[2rem] shadow-[0_10px_30px_rgba(0,0,0,0.05)] p-8 border border-gray-100/50 reveal group/suggest overflow-hidden relative">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-brand-green/5 rounded-full -mr-16 -mt-16 group-hover/suggest:scale-150 transition-transform duration-700"></div>
                                    <h3 class="text-xl font-serif font-black text-gray-800 mb-6 flex items-center justify-between relative z-10">
                                        <span>Gợi ý hôm nay</span>
                                        <i class="fas fa-lightbulb text-[#FFD700] animate-pulse"></i>
                                    </h3>

                                    @if(isset($randomRecipe) && $randomRecipe)
                                    <div class="space-y-6 relative z-10">
                                        <div class="relative aspect-video rounded-2xl overflow-hidden shadow-lg">
                                            <img loading="lazy" src="{{ $randomRecipe->thumbnail }}"
                                                class="w-full h-full object-cover transform group-hover/suggest:scale-110 transition-transform duration-700"
                                                onerror="this.src='https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=1000&auto=format&fit=crop'">

                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                            <div class="absolute bottom-4 left-4">
                                                <span class="px-3 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] font-bold rounded-full border border-white/30">
                                                    {{ $randomRecipe->category->name ?? 'Món ngon' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800 group-hover/suggest:text-brand-green transition-colors leading-tight mb-2">
                                                {{ $randomRecipe->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 line-clamp-2 italic mb-4">
                                                "{{ Str::limit($randomRecipe->description ?? 'Bí quyết nấu món ngon mỗi ngày cùng Góc Bếp.', 100) }}"
                                            </p>
                                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                                <div class="flex gap-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                    <span class="flex items-center gap-1"><i class="far fa-clock"></i> {{ $randomRecipe->cooking_time }}'</span>
                                                    <span class="flex items-center gap-1"><i class="far fa-eye"></i> {{ number_format($randomRecipe->view_count) }}</span>
                                                </div>
                                                <a href="{{ route('recipes.show', $randomRecipe->slug ?? $randomRecipe->id) }}" class="text-brand-green font-black text-[10px] uppercase tracking-widest hover:text-brand-accent transition flex items-center gap-1">
                                                    Xem ngay <i class="fas fa-arrow-right text-[8px]"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="py-12 text-center text-gray-300">
                                        <i class="fas fa-utensils text-4xl mb-4 opacity-20"></i>
                                        <p class="text-sm">Đang chuẩn bị món ngon...</p>
                                    </div>
                                    @endif
                                </div>

                    {{-- Widget 1: Top Thịnh Hành --}}
                    <div
                        class="glass rounded-3xl p-8 border border-white shadow-soft relative overflow-hidden group/widget hover:shadow-xl transition-all duration-500 reveal">
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
                                    class="w-12 h-12 bg-gradient-to-br from-orange-500 via-red-500 to-brand-accent rounded-xl flex items-center justify-center shadow-lg animate-pulse">
                                    <i class="fas fa-fire-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif font-bold text-lg text-gray-800 leading-none">Món Ngon Xu Hướng
                                    </h3>
                                    <span class="text-xs text-orange-600 font-medium">🔥 Được xem nhiều nhất</span>
                                </div>
                            </div>
                            <a href="{{ route('recipes.list') }}"
                                class="text-xs text-orange-500 hover:text-orange-700 font-bold flex items-center gap-1 transition">
                                Xem tất cả <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>


                        {{-- Book List --}}
                        <div class="space-y-3 relative">
                            @if(isset($trendingRecipes) && $trendingRecipes->count() > 0)
                                @foreach($trendingRecipes as $index => $recipe)
                                    @php
                                        $imageUrl = !empty($recipe->image)
                                            ? (str_starts_with($recipe->image, 'http') ? $recipe->image : asset('storage/' . $recipe->image))
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

                                    <a href="{{ route('recipes.show', $recipe->slug ?? $recipe->id) }}"
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
                                            <img loading="lazy" src="{{ $recipe->thumbnail }}" alt="{{ $recipe->title }}" 
                                                class="w-full h-full object-cover" 
                                                onerror="this.src='https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?q=80&w=400&auto=format&fit=crop'">

                                        </div>

                                        {{-- Book Info --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-gray-800 line-clamp-2 group-hover/item:text-orange-600 transition leading-snug"
                                                title="{{ $recipe->title }}">
                                                {{ $recipe->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                Cộng đồng Góc Bếp
                                            </p>

                                            <div class="flex items-center gap-3 text-xs mt-2">
                                                {{-- Rating --}}
                                                <span
                                                    class="flex items-center gap-1 text-orange-500 font-bold bg-orange-50 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-history text-[10px]"></i>
                                                    {{ $recipe->cooking_time }}'
                                                </span>

                                                {{-- Views --}}
                                                <span
                                                    class="flex items-center gap-1 text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-full"
                                                    title="Lượt đọc">
                                                    <i class="far fa-eye text-[10px]"></i>
                                                    {{ number_format($recipe->view_count ?? 0) }}
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
                        class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 rounded-2xl p-6 border border-orange-100 shadow-lg relative overflow-hidden">
                        {{-- Decorative --}}
                        <div
                            class="absolute -top-6 -right-6 w-24 h-24 bg-orange-100/40 rounded-full blur-2xl pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 w-16 h-16 bg-teal-200/30 rounded-full blur-xl pointer-events-none">
                        </div>

                        <div class="flex items-center gap-3 mb-4 relative">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-brand-green to-teal-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-serif font-bold text-gray-800 leading-none">Chủ Đề Ẩm Thực</h3>
                                <span class="text-[10px] text-gray-400">Khám phá theo sở thích</span>
                            </div>
                        </div>


                        <div class="flex flex-wrap gap-2 relative">
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories->take(12) as $category)
                                    <a href="{{ route('recipes.search', ['category_id' => $category->id]) }}"
                                        class="group flex items-center gap-1.5 bg-white/80 backdrop-blur-sm text-gray-600 px-3 py-1.5 rounded-full text-xs font-medium border border-white hover:border-emerald-400 hover:bg-brand-green hover:text-white hover:shadow-lg transition-all duration-300">
                                        <span>{{ $category->name }}</span>
                                        <span
                                            class="bg-orange-100 text-brand-green px-1.5 py-0.5 rounded-full text-[9px] font-bold group-hover:bg-white/30 group-hover:text-white transition">
                                            {{ $category->recipes_count ?? 0 }}
                                        </span>
                                    </a>
                                @endforeach
                            @else
                                <span class="text-sm text-gray-400 italic">Đang cập nhật...</span>
                            @endif
                        </div>

                        @if(isset($categories) && $categories->count() > 12)
                            <div class="mt-4 text-center relative">
                                <a href="{{ route('recipes.list') }}"
                                    class="inline-flex items-center gap-1 text-xs text-brand-green font-bold hover:text-brand-green transition">
                                    Xem tất cả <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Widget 3: Liên Kết Mua Công thức - Redesigned --}}
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
                                <h3 class="font-serif font-bold text-gray-800 leading-none">Dụng Cụ Nhà Bếp</h3>
                                <span class="text-[10px] text-gray-400">Gợi ý từ Góc Bếp</span>
                            </div>
                        </div>


                        <div class="space-y-2 relative">
                            <a href="https://tiki.vn/nha-sach-tiki/c8322" target="_blank"
                                class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-amber-100 hover:bg-blue-50 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/4/43/Logo_Tiki_2023.png"
                                        class="w-8 h-8 object-contain" alt="Tiki">
                                    <div>
                                        <span class="font-bold text-sm text-gray-700 group-hover:text-brand-green block">Đồ Dùng Nhà Bếp</span>
                                        <span class="text-[10px] text-green-600 font-bold">🔥 Giảm tới 35%</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-brand-green group-hover:translate-x-1 transition-all"></i>
                            </a>

                            <a href="https://shopee.vn/nhasachphuongnam" target="_blank"
                                class="flex items-center justify-between p-3 rounded-xl bg-white/80 backdrop-blur-sm border border-white hover:border-orange-300 hover:bg-orange-50 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" src="https://upload.wikimedia.org/wikipedia/commons/f/fe/Shopee.svg"
                                        class="w-8 h-8 object-contain" alt="Shopee">
                                    <div>
                                        <span
                                            class="font-bold text-sm text-gray-700 group-hover:text-orange-600 block">Nguyên Liệu Sạch</span>
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
                                            class="font-bold text-sm text-gray-700 group-hover:text-red-600 block">Combo Sơ Chế</span>
                                        <span class="text-[10px] text-gray-500">✓ Công thức chính hãng</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-300 text-xs group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Widget: Thống Kê Cộng Đồng - Food Style --}}
                    @if(isset($communityStats))
                        <div
                            class="bg-gradient-to-br from-red-50 via-orange-50 to-amber-50 rounded-2xl p-6 border border-red-100 shadow-lg relative overflow-hidden">
                            {{-- Decorative --}}
                            <div
                                class="absolute -top-6 -right-6 w-24 h-24 bg-red-200/40 rounded-full blur-2xl pointer-events-none">
                            </div>
                            <div
                                class="absolute -bottom-4 -left-4 w-16 h-16 bg-orange-200/30 rounded-full blur-xl pointer-events-none">
                            </div>

                            <div class="flex items-center gap-3 mb-4 relative">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-brand-green to-brand-accent rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-chart-pie text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-serif font-bold text-gray-800 leading-none">Thống Kê</h3>
                                    <span class="text-[10px] text-gray-400">Số liệu của Góc Bếp</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 relative">
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-red-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-brand-green transition">
                                        {{ number_format($communityStats['recipes']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Công thức</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-orange-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-brand-accent transition">
                                        {{ number_format($communityStats['members']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Thành viên</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-red-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-brand-green transition">
                                        {{ number_format($communityStats['post_likes'] ?? 0) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Lượt thích</div>
                                </div>
                                <div
                                    class="bg-white/80 backdrop-blur-sm rounded-xl p-3 text-center border border-white hover:border-orange-200 hover:shadow-md transition-all group">
                                    <div class="text-xl font-bold text-gray-800 group-hover:text-brand-accent transition">
                                        {{ number_format($communityStats['comments']) }}
                                    </div>
                                    <div class="text-[10px] text-gray-500 uppercase font-medium">Bình luận</div>
                                </div>
                            </div>

                            {{-- Online Users --}}
                            <div class="mt-3 bg-gradient-to-r from-green-500 to-brand-green rounded-xl p-3 flex items-center justify-between text-white">
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
                                        <i class="fas fa-eye text-brand-green"></i>
                                        Tổng lượt truy cập
                                    </span>
                                    <span class="font-bold text-gray-800">{{ number_format($communityStats['total_visits'] ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div> {{-- END DIV STICKY GROUP --}}

            </div> {{-- END CỘT 4 --}}
        </div> {{-- END GRID --}}
    </main> {{-- END MAIN --}}

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

            // New Books Slider with Autoplay
            const sliderNewBooks = document.getElementById('sliderNewBooks');
            const btnPrevNew = document.getElementById('btnPrevNewBooks');
            const btnNextNew = document.getElementById('btnNextNewBooks');
            if (sliderNewBooks && btnPrevNew && btnNextNew) {
                const scrollAmount = sliderNewBooks.clientWidth > 768 ? sliderNewBooks.clientWidth / 2 : sliderNewBooks.clientWidth;
                btnNextNew.addEventListener('click', () => { sliderNewBooks.scrollBy({ left: scrollAmount, behavior: 'smooth' }); pauseAutoplay(); });
                btnPrevNew.addEventListener('click', () => { sliderNewBooks.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); pauseAutoplay(); });
                
                let playInterval;
                const startAutoplay = () => {
                    playInterval = setInterval(() => {
                        if (sliderNewBooks.scrollLeft + sliderNewBooks.clientWidth >= sliderNewBooks.scrollWidth - 10) {
                            sliderNewBooks.scrollTo({ left: 0, behavior: 'smooth' });
                        } else {
                            sliderNewBooks.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                        }
                    }, 4000);
                };
                const pauseAutoplay = () => {
                    clearInterval(playInterval);
                    setTimeout(startAutoplay, 5000);
                };
                startAutoplay();
                sliderNewBooks.addEventListener('mouseenter', () => clearInterval(playInterval));
                sliderNewBooks.addEventListener('mouseleave', startAutoplay);
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
                url = `{{ url('/') }}/?sort_review=${urlOrSortType}`;
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
            if (!currentUserId) { requireLogin("Bạn cần đăng nhập để thả tim cho bài viết này."); return; }

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

            fetch(`{{ url('/cong-thuc') }}/${postId}/comment`, {
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
                                                                    <img loading="lazy" src="${data.comment.user_frame}" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                                                                    <div class="absolute inset-0 flex items-center justify-center z-0">
                                                                        <img loading="lazy" src="${data.comment.user_avatar}" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                                                                    </div>
                                                                </div>
                                                            `;
                        } else {
                            avatarHtml = `<img loading="lazy" src="${data.comment.user_avatar}" class="w-9 h-9 rounded-full border border-white shadow-sm flex-shrink-0">`;
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
                                                                    <button onclick="toggleReplySection(${data.comment.id})" class="text-[10px] font-bold text-gray-400 hover:text-brand-green transition">
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
            if (!currentUserId) { requireLogin("Vui lòng đăng nhập để có thể bình luận."); return; }
            const btn = document.getElementById(`like-btn-${type}-${id}`);
            const icon = document.getElementById(`like-icon-${type}-${id}`);
            const countSpan = document.getElementById(`like-count-${type}-${id}`);
            if (!btn || !icon || !countSpan) return;

            const isLiked = icon.classList.contains('fas');
            fetch('{{ url('/like') }}', {
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

            fetch(`{{ url('/comment') }}/${commentId}/reply`, {
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
                                                                    <img loading="lazy" src="${data.user_frame}" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                                                                    <div class="absolute inset-0 flex items-center justify-center z-0">
                                                                        <img loading="lazy" src="${data.user_avatar}" class="w-6 h-6 rounded-full object-cover">
                                                                    </div>
                                                                </div>
                                                            `;
                        } else {
                            avatarHtml = `<img loading="lazy" src="${data.user_avatar}" class="w-7 h-7 rounded-full flex-shrink-0">`;
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
                    alert("Lỗi: " + e.message);
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = oldHtml;
                    }
                });
        }

        function editComment(id) {
            const content = document.getElementById(`comment-content-${id}`);
            const form = document.getElementById(`edit-form-${id}`);
            if (content && form) {
                content.classList.add('hidden');
                form.classList.remove('hidden');
                const textarea = document.getElementById(`edit-input-${id}`);
                if (textarea) {
                    textarea.focus();
                    autoResize(textarea);
                }
            }
        }

        function cancelEdit(id) {
            const content = document.getElementById(`comment-content-${id}`);
            const form = document.getElementById(`edit-form-${id}`);
            if (content && form) {
                content.classList.remove('hidden');
                form.classList.add('hidden');
            }
        }

        function saveComment(id, event) {
            const input = document.getElementById(`edit-input-${id}`);
            if (!input) return;

            const content = input.value.trim();
            if (!content) {
                alert("Nội dung không được để trống!");
                return;
            }

            const btn = event.target.closest('button');
            const oldHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch(`{{ url('/cong-thuc') }}/${id}/update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: content })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const contentDiv = document.getElementById(`comment-content-${id}`);
                        // Kiểm tra nếu là reply (p) hay comment (div)
                        const p = contentDiv.querySelector('p');
                        if (p) p.innerText = data.content;
                        else contentDiv.innerText = data.content;

                        cancelEdit(id);
                    } else {
                        alert(data.message || "Có lỗi xảy ra!");
                    }
                })
                .catch(e => alert("Lỗi: " + e.message))
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = oldHtml;
                    }
                });
        }

        async function confirmDelete(id, event) {
            const result = await SwalConfirm('Xác nhận xóa', 'Bạn có chắc chắn muốn xóa bình luận này không?', 'warning', 'Xóa ngay');
            if (result.isConfirmed) {
                const btn = event.target.closest('button');
                const oldHtml = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }

                fetch(`{{ url('/cong-thuc') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                    .then(async r => {
                        const d = await r.json();
                        if (!r.ok) throw new Error(d.message || "Lỗi server");
                        return d;
                    })
                    .then(data => {
                        if (data.success) {
                            // Xóa phần tử khỏi giao diện
                            const replyEl = document.getElementById(`comment-${id}`);
                            if (replyEl) {
                                replyEl.style.opacity = '0';
                                replyEl.style.transform = 'scale(0.95)';
                                replyEl.style.transition = 'all 0.3s ease';
                                setTimeout(() => replyEl.remove(), 300);
                            } else {
                                const commentBtn = document.querySelector(`[onclick*="confirmDelete(${id},"]`);
                                const commentEl = commentBtn ? commentBtn.closest('.bg-white') : null;
                                if (commentEl) {
                                    commentEl.style.opacity = '0';
                                    commentEl.style.transform = 'scale(0.95)';
                                    commentEl.style.transition = 'all 0.3s ease';
                                    setTimeout(() => commentEl.remove(), 300);
                                }
                            }
                        } else {
                            alert(data.message || "Có lỗi xảy ra!");
                        }
                    })
                    .catch(e => alert("Lỗi: " + e.message))
                    .finally(() => {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = oldHtml;
                        }
                    });
            }
        }

        // --- 4. HÀM ĐIỀU KHIỂN BÀI REVIEW CÔNG THỨC ---
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

