<a href="{{ route('recipes.show', $recipe->slug) }}"
    class="group bg-white rounded-2xl shadow hover:shadow-xl overflow-hidden transition-all duration-300 flex flex-col h-full recipe-card-item">
    {{-- Ảnh --}}
    <div class="relative overflow-hidden aspect-[4/3]">
        <img loading="lazy" src="{{ $recipe->thumbnail }}"
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
        <div class="flex items-center gap-1.5 text-sm text-gray-500 mb-2 font-medium">
            @if($recipe->category)
            <span class="text-green-600 font-medium truncate min-w-0">{{ $recipe->category->name }}</span>
            <span class="flex-shrink-0">•</span>
            @endif
            @if($recipe->cooking_time)
            <span class="flex-shrink-0 whitespace-nowrap"><i class="fas fa-clock text-gray-400"></i> {{ $recipe->cooking_time }} phút</span>
            @endif
        </div>
        <h3 class="font-bold text-gray-900 group-hover:text-teal-600 transition line-clamp-2 mb-2 leading-snug text-lg">
            {{ $recipe->title }}
        </h3>
        <div class="flex items-center justify-between text-sm text-gray-500 mt-auto pt-3 border-t border-gray-100 font-medium gap-2">
            <span class="flex items-center gap-1.5 min-w-0 flex-1">
                <img loading="lazy" src="{{ $recipe->user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed='.urlencode($recipe->user->name).'&size=20' }}"
                    class="w-5 h-5 rounded-full object-cover flex-shrink-0" alt="">
                <span class="truncate">{{ $recipe->user->name }}</span>
            </span>
            <span class="flex items-center gap-1.5 flex-shrink-0 whitespace-nowrap">
                <i class="fas fa-eye"></i> {{ number_format($recipe->view_count) }}
            </span>
        </div>
    </div>
</a>
