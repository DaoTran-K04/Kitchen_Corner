@extends('layouts.app')

@section('title', 'Bài Review của ' . $user->name)

@section('content')
    {{-- BREADCRUMB --}}
    <div class="bg-brand-beige/30 py-4 border-b border-brand-beige">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('profile', $user->id) }}" class="hover:text-brand-green transition">Hồ sơ {{ $user->name }}</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold">Tất cả bài review</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 flex-grow min-h-screen">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- CỘT TRÁI: SIDEBAR USER --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-soft p-6 text-center border border-gray-100 sticky top-24">
                    @php
                        $equippedFrame = $user->equippedFrame();
                    @endphp

                    <div class="relative w-32 h-32 mx-auto mb-4 group">
                        @if($equippedFrame)
                            <img src="{{ Str::startsWith($equippedFrame->frame_image, 'http') ? $equippedFrame->frame_image : asset('storage/' . $equippedFrame->frame_image) }}"
                                alt="Frame" class="absolute inset-0 w-full h-full object-contain pointer-events-none z-10">
                        @endif

                        <div class="absolute inset-0 flex items-center justify-center z-0">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128' }}"
                                class="w-20 h-20 rounded-full border-2 border-brand-beige shadow-md object-cover">
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 font-serif">{{ $user->name }}</h2>
                    <p class="text-gray-600 text-sm italic mb-4 px-2 bg-gray-50 py-2 rounded-lg border border-gray-100 mt-2">
                        {{ $user->bio ?? 'Thành viên tích cực của Góc Sách.' }}
                    </p>

                    <a href="{{ route('profile', $user->id) }}" 
                        class="block w-full border border-gray-300 text-gray-600 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại hồ sơ
                    </a>
                </div>
            </div>

            {{-- CỘT PHẢI: DANH SÁCH REVIEW ĐẦY ĐỦ --}}
            <div class="lg:col-span-3">
                {{-- HEADER --}}
                <div class="bg-white rounded-xl shadow-soft p-4 mb-6 border border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-gray-800 font-serif border-l-4 border-brand-green pl-3">
                            <i class="fas fa-pen-nib text-brand-green mr-2"></i>Tất cả bài Review
                        </h1>
                        <span class="bg-brand-green/10 text-brand-green text-sm px-3 py-1 rounded-full font-bold">
                            {{ $reviews->total() }} bài
                        </span>
                    </div>

                    @if($isOwnProfile)
                        <a href="{{ route('reviews.create') }}"
                            class="text-xs font-bold text-white bg-brand-accent hover:bg-[#c29263] px-4 py-2 rounded-full shadow-sm transition flex items-center gap-2 transform hover:-translate-y-0.5">
                            <i class="fas fa-pen-nib"></i> Viết Review mới
                        </a>
                    @endif
                </div>

                {{-- DANH SÁCH REVIEW --}}
                <div class="space-y-6">
                    @forelse($reviews as $post)
                        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition group relative">

                            {{-- BADGE TRẠNG THÁI --}}
                            @if($isOwnProfile)
                                <div class="absolute top-4 right-4 z-10">
                                    @if($post->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200 shadow-sm animate-pulse">
                                            <i class="fas fa-clock"></i> Đang chờ duyệt
                                        </span>
                                    @elseif($post->status == 'rejected')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-200 shadow-sm">
                                            <i class="fas fa-times-circle"></i> Bị từ chối
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- THÔNG TIN SÁCH --}}
                            <div class="flex justify-between items-start mb-4 pr-20">
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('book.show', $post->book_id ?? 0) }}" class="block shrink-0">
                                        @php
                                            $cover = $post->book->cover_image ?? null;
                                            $coverUrl = $cover
                                                ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                                : 'https://placehold.co/50';
                                        @endphp
                                        <img src="{{ $coverUrl }}" class="w-12 h-16 object-cover rounded shadow-sm border border-gray-200">
                                    </a>

                                    <div>
                                        <h4 class="font-bold text-gray-800 text-base mb-1">
                                            <a href="{{ route('book.show', $post->book_id ?? 0) }}" class="hover:text-brand-green transition">
                                                {{ $post->book->title ?? 'Sách đã xóa' }}
                                            </a>
                                        </h4>
                                        <div class="flex text-yellow-400 text-xs items-center gap-2">
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= ($post->rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-gray-400 text-[11px]">• {{ $post->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TIÊU ĐỀ & NỘI DUNG --}}
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-brand-green transition">
                                {{ $post->title }}
                            </h3>

                            <div class="text-gray-500 text-sm line-clamp-3 prose prose-sm max-w-none">
                                {!! $post->content !!}
                            </div>

                            {{-- FOOTER --}}
                            <div class="flex items-center justify-between mt-4 text-xs text-gray-400 border-t border-gray-50 pt-3">
                                <span class="flex items-center gap-2">
                                    <i class="far fa-clock"></i> {{ $post->created_at->diffForHumans() }}
                                </span>

                                <div class="flex items-center gap-3">
                                    @if($isOwnProfile)
                                        <a href="{{ route('reviews.edit', $post->id) }}"
                                            class="text-blue-500 hover:text-blue-700 font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1 transition">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                    @endif

                                    <a href="{{ route('book.reviews', $post->book->slug ?? $post->book_id) }}"
                                        class="text-brand-green font-bold hover:underline text-xs uppercase tracking-wide flex items-center gap-1">
                                        Xem chi tiết <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <i class="fas fa-pen-nib text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Chưa có bài viết nào.</p>
                            @if($isOwnProfile)
                                <a href="{{ route('reviews.create') }}" class="text-brand-accent font-bold hover:underline text-sm mt-2 inline-block">
                                    Viết bài đầu tiên ngay
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- PHÂN TRANG --}}
                <div class="mt-8">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection
