@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-24 lg:pt-32 pb-10">
    <div class="container mx-auto px-4 lg:px-12 relative z-10">
        {{-- Banner & Tiêu đề --}}
        <div class="bg-gradient-to-r from-brand-accent to-brand-green p-8 sm:p-12 rounded-[2rem] shadow-xl text-white mb-10 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20 mix-blend-overlay"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-left">
                    <h1 class="font-serif text-4xl sm:text-5xl font-bold mb-4 drop-shadow-md">Khám Phá Tác Giả</h1>
                    <p class="text-white/90 text-sm sm:text-base max-w-xl leading-relaxed font-light">
                        Tuyển tập những đầu bếp và thành viên xuất sắc nhất cộng đồng Góc Bếp. Khám phá hàng ngàn công thức tuyệt đỉnh từ tủ bếp của họ.
                    </p>
                </div>
                {{-- Search Box --}}
                <div class="w-full md:w-auto relative">
                    <form action="{{ route('authors.index') }}" method="GET" class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm tác giả..."
                            class="w-full md:w-80 lg:w-96 pl-12 pr-4 py-4 rounded-full text-gray-800 bg-white shadow-lg focus:outline-none focus:ring-4 focus:ring-white/40 transition-all font-medium">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-brand-accent text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-orange-600 transition shadow-sm">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Decorative circles --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 left-20 w-32 h-32 bg-brand-green/30 rounded-full blur-xl"></div>
        </div>

        {{-- Lọc & Sắp xếp --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <p class="text-gray-600 font-medium">Tìm thấy <span class="font-bold text-brand-green">{{ $authors->total() }}</span> tác giả</p>
            <div class="flex gap-2">
                <a href="{{ route('authors.index', ['q' => request('q'), 'sort' => 'popular']) }}" 
                   class="px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all 
                   {{ $sort == 'popular' ? 'border-brand-accent bg-brand-accent/10 text-brand-accent' : 'border-gray-200 text-gray-500 hover:border-brand-accent hover:text-brand-accent' }}">
                   Nhiều công thức nhất
                </a>
                <a href="{{ route('authors.index', ['q' => request('q'), 'sort' => 'name']) }}" 
                   class="px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all 
                   {{ $sort == 'name' ? 'border-brand-accent bg-brand-accent/10 text-brand-accent' : 'border-gray-200 text-gray-500 hover:border-brand-accent hover:text-brand-accent' }}">
                   Tên A-Z
                </a>
            </div>
        </div>

        {{-- Grid Danh Sách Tác Giả --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
            @forelse($authors as $author)
                <div class="bg-white rounded-3xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col items-center text-center group cursor-pointer"
                     onclick="window.location='{{ route('public.profile', $author->id) }}'">
                    
                    {{-- Avatar & Khung --}}
                    <div class="relative mb-4 transform group-hover:scale-105 transition-transform duration-300">
                        @include('partials.user-avatar-with-frame', [
                            'user' => $author,
                            'size' => 'w-24 h-24',
                            'avatarSize' => 'w-20 h-20'
                        ])
                        
                        {{-- Huy hiệu Admin/Mod --}}
                        @if($author->role == 'admin')
                            <div class="absolute -bottom-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-md" title="Admin">
                                <i class="fas fa-crown text-xs"></i>
                            </div>
                        @elseif($author->role == 'moderator')
                            <div class="absolute -bottom-2 -right-2 bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-md" title="Moderator">
                                <i class="fas fa-shield-alt text-xs"></i>
                            </div>
                        @else
                            <div class="absolute -bottom-2 -right-2 bg-brand-green text-white w-8 h-8 rounded-full flex items-center justify-center border-2 border-white shadow-md" title="Thành viên nổi bật">
                                <i class="fas fa-star text-xs"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Thông tin --}}
                    <a href="{{ route('public.profile', $author->id) }}" class="font-bold text-xl text-gray-800 hover:text-brand-green transition mb-1 leading-tight line-clamp-1">
                        {{ $author->name }}
                    </a>
                    
                    {{-- Badges --}}
                    <div class="flex flex-wrap justify-center gap-1 my-2">
                        @include('partials.user-badges', ['user' => $author, 'size' => 'sm'])
                    </div>
                    
                    {{-- Bio ngắn --}}
                    @if($author->bio)
                        <p class="text-xs text-gray-500 line-clamp-2 mt-2 mb-4 leading-relaxed px-2">
                            {{ $author->bio }}
                        </p>
                    @else
                        <p class="text-xs text-gray-400 italic mb-4 mt-2">Đầu bếp giấu mặt tài năng</p>
                    @endif

                    <div class="mt-auto w-full pt-4 border-t border-gray-50 grid grid-cols-2 text-center text-sm">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-lg">{{ number_format($author->recipes_count ?? 0) }}</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Công thức</span>
                        </div>
                        <div class="flex flex-col border-l border-gray-100">
                            <span class="font-bold text-brand-green text-lg">{{ number_format($author->followers_count ?? 0) }}</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Người theo dõi</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-white rounded-3xl shadow-sm border border-dashed border-gray-200 text-center">
                    <div class="inline-flex w-20 h-20 bg-gray-50 rounded-full items-center justify-center mb-4">
                        <i class="fas fa-search text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Chưa tìm thấy tác giả nào</h3>
                    <p class="text-gray-500 text-sm">Rất tiếc chúng tôi không tìm thấy đầu bếp nào khớp với từ khóa của bạn.</p>
                </div>
            @endforelse
        </div>

        {{-- Phân trang --}}
        <div class="mt-12 flex justify-center custom-pagination">
            {{ $authors->links() }}
        </div>
    </div>
</div>

<style>
.custom-pagination nav {
    background: white;
    padding: 0.5rem;
    border-radius: 9999px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    display: inline-flex;
    gap: 0.25rem;
}
.custom-pagination span[aria-current="page"] > span {
    background: var(--brand-green);
    color: white;
    border-color: var(--brand-green);
    font-weight: 700;
}
.custom-pagination a, .custom-pagination span {
    border-radius: 9999px !important;
}
</style>
@endsection
