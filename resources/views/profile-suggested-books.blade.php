@extends('layouts.app')

@section('title', 'Sách đề xuất của ' . $user->name)

@section('content')
    {{-- BREADCRUMB --}}
    <div class="bg-brand-beige/30 py-4 border-b border-brand-beige">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('profile', $user->id) }}" class="hover:text-brand-green transition">Hồ sơ {{ $user->name }}</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold">Tất cả sách đề xuất</span>
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

            {{-- CỘT PHẢI: DANH SÁCH SÁCH ĐỀ XUẤT ĐẦY ĐỦ --}}
            <div class="lg:col-span-3">
                {{-- HEADER --}}
                <div class="bg-white rounded-xl shadow-soft p-4 mb-6 border border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-gray-800 font-serif border-l-4 border-brand-accent pl-3">
                            <i class="fas fa-book-medical text-brand-accent mr-2"></i>Tất cả sách đề xuất
                        </h1>
                        <span class="bg-brand-accent/10 text-brand-accent text-sm px-3 py-1 rounded-full font-bold">
                            {{ $suggestedBooks->total() }} sách
                        </span>
                    </div>

                    <a href="{{ route('books.suggest') }}"
                        class="text-xs font-bold text-white bg-brand-accent hover:bg-[#c29263] px-4 py-2 rounded-full shadow-sm transition flex items-center gap-2 transform hover:-translate-y-0.5">
                        <i class="fas fa-plus-circle"></i> Đề xuất sách mới
                    </a>
                </div>

                {{-- DANH SÁCH SÁCH --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($suggestedBooks as $book)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-card hover:-translate-y-1 transition-all duration-300 flex flex-row h-44 relative group">

                            <div class="w-28 relative flex-shrink-0 bg-gray-200">
                                @if($book->is_approved)
                                    <a href="{{ route('book.show', $book->slug) }}">
                                @endif
                                    @php
                                        $cover = $book->cover_image ?? null;
                                        $coverUrl = $cover
                                            ? (Str::startsWith($cover, 'http') ? $cover : asset('storage/' . $cover))
                                            : 'https://placehold.co/150x225?text=' . urlencode(Str::limit($book->title, 10));
                                    @endphp
                                    <img src="{{ $coverUrl }}" class="w-full h-full object-cover transition group-hover:opacity-90">
                                    @if($book->is_approved)
                                        </a>
                                    @endif
                            </div>

                            <div class="p-3 flex flex-col justify-between flex-grow min-w-0">
                                <div>
                                    <h4 class="font-bold font-serif text-gray-800 text-sm mb-1 leading-tight line-clamp-2">
                                        @if($book->is_approved)
                                            <a href="{{ route('book.show', $book->slug) }}" class="hover:text-brand-green transition">
                                                {{ $book->title }}
                                            </a>
                                        @else
                                            {{ $book->title }}
                                        @endif
                                    </h4>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $book->author_name ?? 'Tác giả' }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        <i class="far fa-calendar-alt mr-1"></i> Gửi: {{ $book->created_at->format('d/m/Y') }}
                                    </p>
                                </div>

                                {{-- BADGE TRẠNG THÁI --}}
                                <div class="mt-auto pt-2">
                                    @if($book->is_approved)
                                        <a href="{{ route('book.show', $book->slug) }}"
                                            class="inline-flex items-center gap-1 text-brand-green border border-brand-green/30 bg-brand-green/5 px-2.5 py-1 rounded text-[10px] font-bold hover:bg-brand-green hover:text-white transition">
                                            <i class="fas fa-check-circle"></i> ĐÃ DUYỆT
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 border border-yellow-200 text-[10px] font-bold px-2.5 py-1 rounded">
                                            <i class="fas fa-clock"></i> CHỜ DUYỆT
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <i class="fas fa-book-medical text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">Bạn chưa đề xuất cuốn sách nào.</p>
                            <p class="text-gray-400 text-xs mt-1 mb-3">Hãy đóng góp sách mới cho cộng đồng nhé!</p>
                            <a href="{{ route('books.suggest') }}" class="text-brand-accent text-sm font-bold hover:underline">
                                + Đề xuất sách ngay
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- PHÂN TRANG --}}
                <div class="mt-8">
                    {{ $suggestedBooks->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection
