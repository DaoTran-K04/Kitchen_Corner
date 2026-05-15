{{--
    Partial view for displaying user avatar with optional frame
    Usage: @include('partials.user-avatar-with-frame', ['user' => $user, 'size' => 'w-12 h-12', 'avatarSize' => 'w-10 h-10'])
    
    Parameters:
    - $user: User object (required)
    - $size: Container size class (optional, default: w-12 h-12)
    - $avatarSize: Avatar size class (optional, default: w-10 h-10)
    - $showFrame: Whether to show frame (optional, default: true)
--}}

@php
    $containerSize = $size ?? 'w-12 h-12';
    $avatarSize = $avatarSize ?? 'w-10 h-10';
    $showFrame = $showFrame ?? true;
    $equippedFrame = $showFrame ? $user->equippedFrame() : null;
    $avatarUrl = $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=128';
@endphp

<div class="relative {{ $containerSize }} inline-block flex-shrink-0">
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
             class="{{ $avatarSize }} rounded-full object-cover border-2 border-gray-200">
    </div>
</div>
