{{--
    Recipe Card Component - Update: 2026-05-15 20:46
    - Ảnh chiếm ≥ 60% diện tích card (aspect-[4/5])
--}}
@php
    $imageUrl = $recipe->image;
    if (!empty($imageUrl)) {
        if (!str_starts_with($imageUrl, 'http')) {
            $imageUrl = asset('storage/' . $imageUrl);
        }
    } else {
        $imageUrl = 'https://placehold.co/600x750/9b2226/white?text=Góc+Bếp';
    }

    $difficultyMap = [
        'easy'   => ['label' => 'Dễ',        'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
        'medium' => ['label' => 'Trung bình', 'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
        'hard'   => ['label' => 'Khó',        'color' => 'text-rose-600',   'bg' => 'bg-rose-50'],
    ];
    $diff = $difficultyMap[$recipe->difficulty] ?? ['label' => 'N/A', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50'];
@endphp

<div class="group/card relative bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-[0_20px_40px_rgba(0,0,0,0.12)] transition-all duration-400 transform hover:-translate-y-2 border border-gray-100/80">

    {{-- [PHÂN CẤP 1] Ảnh chiếm ≥ 60% - quan trọng nhất --}}
    <div class="relative overflow-hidden" style="aspect-ratio:4/5;">
        <img src="{{ $recipe->thumbnail }}" alt="{{ $recipe->title }}"
            loading="lazy"
            class="w-full h-full object-cover transform group-hover/card:scale-108 transition-transform duration-700 ease-out"
            onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1000&auto=format&fit=crop'">


        {{-- Gradient overlay từ dưới lên (đọc thông tin) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent opacity-80 group-hover/card:opacity-90 transition-opacity duration-500"></div>

        {{-- Badge góc trên trái: Danh mục --}}
        <div class="absolute top-3 left-3 flex gap-2 flex-wrap">
            @if(isset($recipe->category) && $recipe->category)
            <span class="px-2.5 py-1 bg-[#9b2226]/80 backdrop-blur-sm text-white text-[10px] font-bold rounded-full border border-white/20 leading-none">
                {{ $recipe->category->name }}
            </span>
            @endif
            @if($recipe->is_featured ?? false)
            <span class="px-2.5 py-1 bg-[#f4a261] text-white text-[10px] font-black rounded-full leading-none flex items-center gap-1 animate-pulse">
                <i class="fas fa-fire text-[8px]"></i> HOT
            </span>
            @endif
            @if(isset($recipe->is_premium) && $recipe->is_premium)
            <span class="px-2.5 py-1 bg-gradient-to-r from-gray-700 to-black/80 shadow text-white text-[10px] font-black rounded-full leading-none flex items-center gap-1">
                <i class="fas fa-lock text-[8px]"></i> Thành viên
            </span>
            @endif
        </div>

        {{-- Nút Yêu thích góc trên phải --}}
        <button class="absolute top-3 right-3 w-8 h-8 bg-white/20 backdrop-blur-sm border border-white/30 text-white rounded-full flex items-center justify-center hover:bg-rose-500 hover:border-rose-400 transition-all duration-300 opacity-0 group-hover/card:opacity-100"
            title="Lưu công thức">
            <i class="far fa-heart text-xs"></i>
        </button>

        {{-- [PHÂN CẤP 2] Tên công thức - nằm trên ảnh ở dưới --}}
        <div class="absolute bottom-0 left-0 right-0 p-4">
            {{-- Metadata hàng 1: Thời gian + Độ khó --}}
            <div class="flex items-center gap-3 text-[11px] font-semibold text-white/80 mb-2 translate-y-2 group-hover/card:translate-y-0 transition-transform duration-400">
                <span class="flex items-center gap-1.5">
                    <i class="far fa-clock text-[#f4a261]"></i>
                    {{ $recipe->cooking_time ?? 30 }}'
                </span>
                <span class="w-1 h-1 bg-white/40 rounded-full"></span>
                <span class="flex items-center gap-1">
                    <i class="fas fa-signal text-[#f4a261]"></i>
                    @php
                        $diffLabels = [
                            'easy' => 'Dễ',
                            'medium' => 'T.Bình',
                            'hard' => 'Khó'
                        ];
                        echo $diffLabels[$recipe->difficulty] ?? 'Dễ';
                    @endphp
                </span>

            </div>

            {{-- Tên công thức --}}
            <h3 class="text-white font-bold text-sm md:text-base leading-snug line-clamp-2 group-hover/card:text-[#f4a261] transition-colors duration-300">
                {{ $recipe->title }}
            </h3>
        </div>
    </div>

    {{-- [PHÂN CẤP 3] Footer: Tác giả + Thống kê --}}
    <div class="px-4 py-3 flex items-center justify-between bg-white">

        {{-- Tác giả --}}
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
                <img src="{{ $recipe->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($recipe->user->name ?? 'GB').'&background=9b2226&color=fff&size=56' }}"
                    class="w-full h-full object-cover"
                    onerror="this.src='https://ui-avatars.com/api/?name=GB&background=9b2226&color=fff'">
            </div>
            <span class="text-xs font-semibold text-gray-600 truncate max-w-[80px]">{{ $recipe->user->name ?? 'Thành viên' }}</span>
        </div>

        {{-- Thống kê --}}
        <div class="flex items-center gap-3 text-gray-400 flex-shrink-0">
            <span class="text-[11px] flex items-center gap-1">
                <i class="far fa-eye text-[10px]"></i>
                {{ $recipe->view_count > 999 ? round($recipe->view_count/1000, 1).'K' : $recipe->view_count }}
            </span>
            <span class="text-[11px] flex items-center gap-1">
                <i class="far fa-comment text-[10px]"></i>
                {{ $recipe->comments_count ?? 0 }}
            </span>
        </div>
    </div>

    {{-- Overlay link toàn card --}}
    <a href="{{ route('recipes.show', $recipe->slug ?? $recipe->id) }}" class="absolute inset-0 z-10" title="{{ $recipe->title }}"></a>
</div>
