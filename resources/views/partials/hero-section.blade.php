{{-- HERO SECTION: Full-viewport theo tiêu chí lý thuyết (Tasty-style) --}}
{{-- Phân cấp hình ảnh: Ảnh là nền → Tiêu đề lớn → Mô tả → CTA --}}
<section id="hero-carousel" class="relative text-white overflow-hidden group" style="height:90vh;min-height:580px;max-height:900px;">

    {{-- Slider Wrapper (chiều ngang) --}}
    <div class="hero-slider-wrapper flex w-full h-full" id="sliderWrapper">
        @foreach($heroSlides as $index => $slide)
        @php
            $imagePath  = is_object($slide) ? $slide->image       : $slide['image'];
            $imgSrc     = Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath);
            $slideTag   = is_object($slide) ? ($slide->tag         ?? 'Nổi Bật')        : ($slide['tag'] ?? 'Nổi Bật');
            $slideTitle = is_object($slide) ? $slide->title                              : $slide['title'];
            $slideDesc  = is_object($slide) ? ($slide->description ?? $slide->desc ?? '') : ($slide['desc'] ?? '');
            $rawLink    = is_object($slide) ? ($slide->link ?? '') : ($slide['link'] ?? '');
            $bannerLink = !empty($rawLink) && $rawLink !== '#' ? $rawLink : route('recipes.list');
        @endphp
        <div class="w-full h-full flex-shrink-0 relative group/slide">

            {{-- [MỨC 1] Ảnh nền toàn màn hình (60%+ diện tích) --}}
            <div class="absolute inset-0">
                <img src="{{ $imgSrc }}" alt="{{ $slideTitle }}"
                    class="w-full h-full object-cover transition-transform duration-[8s] ease-in-out group-hover/slide:scale-105">

                {{-- Gradient overlay: tối ở trái (nội dung), trong suốt ở phải (ảnh thở) --}}
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/10"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            </div>

            {{-- [Xóa] Admin Tool đã chuyển vào trang Quản trị --}}

            {{-- [MỨC 2–4] Nội dung văn bản - canh giữa chiều dọc --}}
            <div class="absolute inset-0 flex items-center">
                <div class="container mx-auto px-6 md:px-10 lg:px-16 pt-24 lg:pt-32">
                    <div class="max-w-2xl xl:max-w-3xl">

                        {{-- [MỨC 2] Badge/Tag --}}
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#E85D04]/50 bg-[#E85D04]/15 backdrop-blur-sm mb-6">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#E85D04] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#E85D04]"></span>
                            </span>
                            <span class="text-[11px] font-black uppercase tracking-[0.3em] text-[#E85D04]">{{ $slideTag }}</span>
                        </div>

                        {{-- [MỨC 1] Tiêu đề lớn nhất - Phân cấp hình ảnh tối cao --}}
                        <h1 class="text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-serif font-black leading-[1.05] tracking-tight text-white drop-shadow-2xl mb-6">
                            {!! $slideTitle !!}
                        </h1>

                        {{-- [MỨC 3] Mô tả phụ --}}
                        @if($slideDesc)
                        <p class="text-white/70 text-lg md:text-xl leading-relaxed mb-8 border-l-4 border-[#E85D04]/60 pl-5 italic font-light max-w-xl">
                            "{{ $slideDesc }}"
                        </p>
                        @endif

                        {{-- [MỨC 4] CTA Buttons --}}
                        <div class="flex flex-wrap gap-4 items-center">
                            {{-- Nút chính - Màu cam đất (kích thích vị giác theo lý thuyết) --}}
                            <a href="{{ $bannerLink }}"
                                class="inline-flex items-center gap-3 bg-[#E85D04] text-white font-black px-10 py-4 rounded-full shadow-[0_12px_30px_rgba(232,93,4,0.5)] hover:bg-white hover:text-[#E85D04] transition-all duration-300 transform hover:-translate-y-1 uppercase tracking-widest text-sm group/cta">
                                <span>Khám Phá Ngay</span>
                                <i class="fas fa-arrow-right text-xs group-hover/cta:translate-x-1.5 transition-transform"></i>
                            </a>

                            {{-- Nút phụ - Glassmorphism --}}
                            @auth
                            <a href="{{ route('recipes.create') }}"
                                class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md text-white font-semibold px-8 py-4 rounded-full border border-white/25 hover:bg-white/20 hover:border-white/40 transition-all duration-300 text-sm">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Chia sẻ công thức</span>
                            </a>
                            @else
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md text-white font-semibold px-8 py-4 rounded-full border border-white/25 hover:bg-white/20 transition-all duration-300 text-sm">
                                <i class="fas fa-user-plus text-xs"></i>
                                <span>Tham gia miễn phí</span>
                            </a>
                            @endauth
                        </div>

                    </div>
                </div>
            </div>

            {{-- Số thứ tự slide (góc phải dưới) --}}
            <div class="absolute bottom-10 right-10 text-white/40 font-black font-serif text-sm tracking-widest hidden md:block">
                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad(count($heroSlides), 2, '0', STR_PAD_LEFT) }}
            </div>

        </div>
        @endforeach
    </div>

    {{-- Nút Điều Hướng --}}
    <button id="heroPrevBtn"
        class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/30 hover:bg-[#E85D04] text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 hover:scale-110 z-20 cursor-pointer border border-white/20">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button id="heroNextBtn"
        class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-black/30 hover:bg-[#E85D04] text-white flex items-center justify-center backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100 hover:scale-110 z-20 cursor-pointer border border-white/20">
        <i class="fas fa-chevron-right"></i>
    </button>

    {{-- Dots indicator - Đường kẻ mỏng theo style Tasty --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
        @foreach($heroSlides as $index => $slide)
        <button
            class="indicator-dot h-[3px] rounded-full transition-all duration-500 {{ $index === 0 ? 'bg-[#E85D04] w-10' : 'bg-white/40 w-5 hover:bg-white/70' }}"
            data-index="{{ $index }}"></button>
        @endforeach
    </div>

    {{-- Scroll indicator (góc phải) --}}
    <div class="absolute bottom-6 right-8 hidden md:flex flex-col items-center gap-2 opacity-40">
        <span class="text-[10px] uppercase tracking-[0.25em] text-white font-bold">Cuộn</span>
        <div class="w-px h-8 bg-white animate-pulse"></div>
        <i class="fas fa-chevron-down text-[9px] text-white mt-1"></i>
    </div>

</section>
