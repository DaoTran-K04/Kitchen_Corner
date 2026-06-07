
@php
    $avatarSize = $size ?? 'w-12 h-12';
    $showFrame = $showFrame ?? true;
    $showNameplate = $showNameplate ?? true;
    $equippedFrame = $showFrame ? $user->equippedFrame() : null;
    $avatarUrl = $user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($user->name) . '&background=random&size=128';
    
    $isRoyal = $equippedFrame && $equippedFrame->slug === 'khung-hoang-gia-toi-cao';
    
    // Determine if large display (for nameplate rendering)
    $isLarge = str_contains($avatarSize, 'w-24') || str_contains($avatarSize, 'w-28') || 
               str_contains($avatarSize, 'w-32') || str_contains($avatarSize, 'w-36') || 
               str_contains($avatarSize, 'w-40') || str_contains($avatarSize, 'w-48') || 
               str_contains($avatarSize, 'w-52');
@endphp

<div class="inline-flex flex-col items-center">
    {{-- Avatar + Frame wrapper --}}
    <div class="relative inline-flex items-center justify-center" style="padding: {{ $equippedFrame ? '10%' : '0' }}">

        {{-- User Avatar (always rendered cleanly, never obscured) --}}
        <img src="{{ $avatarUrl }}"
             alt="{{ $user->name }}"
             class="{{ $avatarSize }} rounded-full object-cover flex-shrink-0"
             style="display:block;">

        {{-- Frame ring — rendered as overlay AROUND the avatar using absolute inset negative --}}
        @if($equippedFrame)
            <img src="{{ (Str::startsWith($equippedFrame->frame_image, 'http') || Str::startsWith($equippedFrame->frame_image, 'data:')) ? $equippedFrame->frame_image : asset('storage/' . $equippedFrame->frame_image) }}"
                 alt="Frame"
                 class="absolute inset-0 w-full h-full object-contain pointer-events-none"
                 style="z-index: 1;">
        @endif
    </div>

    {{-- Royal Nameplate — only for Khung Hoàng Gia Tối Cao, only on large displays --}}
    @if($isRoyal && $showNameplate && $isLarge)
        <div class="relative mt-1 px-4 py-1 rounded-lg border-2 border-[#ffd700] bg-gradient-to-b from-[#c62828] to-[#7f0000] shadow-lg overflow-hidden">
            <div class="absolute inset-0 opacity-20" style="background: repeating-linear-gradient(45deg, #ffd700 0px, #ffd700 1px, transparent 1px, transparent 6px);"></div>
            <span class="relative z-10 text-[#ffd700] font-black uppercase tracking-widest text-xs drop-shadow-md whitespace-nowrap"
                  style="text-shadow: 0 0 8px rgba(255,215,0,0.8), 1px 1px 2px rgba(0,0,0,0.9);">
                {{ $user->name }}
            </span>
        </div>
    @endif
</div>
