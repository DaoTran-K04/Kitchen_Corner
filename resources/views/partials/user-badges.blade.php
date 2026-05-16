

@php
    $badges = $user->activeBadges ?? collect();
    $size = $size ?? 'sm';
    $max = $max ?? 5;

    // Size classes
    $sizeClasses = [
        'xs' => 'w-4 h-4 text-[10px]',
        'sm' => 'w-5 h-5 text-xs',
        'md' => 'w-6 h-6 text-sm',
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

@if($badges->count() > 0)
    <div class="inline-flex items-center gap-0.5 ml-1">
        @foreach($badges->take($max) as $badge)
            @php
                $icon = $badge->icon;
                $isUrl = $icon && (Str::startsWith($icon, 'http') || Str::startsWith($icon, '/'));
                $iconUrl = $isUrl
                    ? (Str::startsWith($icon, 'http') ? $icon : asset('storage/' . $icon))
                    : null;
            @endphp

            <div class="group relative cursor-help" title="{{ $badge->name }}">
                @if($iconUrl)
                    <img src="{{ $iconUrl }}" alt="{{ $badge->name }}" class="{{ $sizeClass }} object-contain rounded-full"
                        referrerpolicy="no-referrer"
                        onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22gold%22><circle cx=%2212%22 cy=%2212%22 r=%2210%22/><text x=%2212%22 y=%2216%22 text-anchor=%22middle%22 font-size=%2212%22>🏆</text></svg>';">
                @elseif($icon && mb_strlen($icon) <= 4)
                    {{-- Emoji --}}
                    <span
                        class="{{ $sizeClass }} inline-flex items-center justify-center bg-gradient-to-br from-purple-400 to-pink-400 rounded-full text-white">
                        {{ $icon }}
                    </span>
                @else
                    {{-- Fallback medal icon --}}
                    <span
                        class="{{ $sizeClass }} inline-flex items-center justify-center bg-gradient-to-br from-yellow-400 to-orange-400 rounded-full text-white">
                        <i class="fas fa-medal" style="font-size: 60%;"></i>
                    </span>
                @endif

                {{-- Tooltip --}}
                <div
                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 hidden group-hover:block z-50 w-max pointer-events-none">
                    <div class="bg-gray-800 text-white text-[10px] rounded py-0.5 px-2 shadow-lg whitespace-nowrap">
                        {{ $badge->name }}
                    </div>
                    <div class="w-1.5 h-1.5 bg-gray-800 transform rotate-45 absolute -bottom-0.5 left-1/2 -translate-x-1/2">
                    </div>
                </div>
            </div>
        @endforeach

        @if($badges->count() > $max)
            <span class="text-[10px] text-gray-400 ml-0.5">+{{ $badges->count() - $max }}</span>
        @endif
    </div>
@endif
