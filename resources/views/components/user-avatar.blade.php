<div {{ $attributes->merge(['class' => 'relative inline-block']) }}>
    @php
        $equippedFrame = $user->equippedFrame();
        $avatarUrl = $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128';
    @endphp
    
    <!-- Avatar Frame (if equipped) -->
    @if($equippedFrame)
        <img src="{{ Str::startsWith($equippedFrame->frame_image, 'http') ? $equippedFrame->frame_image : asset('storage/' . $equippedFrame->frame_image) }}"
             alt="Frame"
             class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
    @endif
    
    <!-- User Avatar -->
    <div class="absolute inset-0 flex items-center justify-center z-0">
        <img src="{{ $avatarUrl }}" 
             alt="{{ $user->name }}"
             {{ $attributes->merge(['class' => 'rounded-full object-cover']) }}>
    </div>
</div>
