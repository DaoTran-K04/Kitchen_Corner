@extends('layouts.app')

@section('title', $pageTitle ?? 'Tất Cả Sách - Góc Sách')

@section('content')
    {{-- Breadcrumb --}}
    <div class="bg-brand-beige/30 py-4 border-b border-brand-beige">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold">Danh Sách Sách</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 flex-grow">
        {{-- FORM FILTER START --}}
        {{-- Form này sẽ gửi dữ liệu về route 'books.list' bằng phương thức GET --}}
        <form id="filterForm" action="{{ route('books.list') }}" method="GET" class="w-full">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
                {{-- MOBILE: FILTER TOGGLE BUTTON --}}
                <div class="lg:hidden">
                    <button type="button" id="mobile-filter-toggle"
                        class="w-full flex items-center justify-center gap-2 bg-white border border-brand-green text-brand-green font-bold py-3 px-4 rounded-xl shadow-sm hover:bg-brand-green hover:text-white transition">
                        <i class="fas fa-filter"></i>
                        <span>Bộ lọc</span>
                        <i class="fas fa-chevron-down text-xs ml-auto transition-transform" id="filter-toggle-icon"></i>
                    </button>
                </div>
            
                {{-- SIDEBAR: BỘ LỌC - Hidden on mobile by default --}}
                <aside id="mobile-filter-panel" class="lg:col-span-3 space-y-8 hidden lg:block">
                    {{-- Widget Thể Loại --}}
<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-soft">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
        <h3 class="font-bold text-brand-green font-serif text-lg">
            <i class="fas fa-filter mr-2 text-brand-accent"></i> Thể Loại
        </h3>
        @if(request('categories') && count(request('categories')) > 0)
            <a href="{{ request()->fullUrlWithQuery(['categories' => null]) }}" 
               class="text-xs text-red-500 hover:text-red-700 hover:underline flex items-center gap-1 transition"
               title="Bỏ lọc thể loại">
                <i class="fas fa-times-circle"></i> Bỏ lọc
            </a>
        @endif
    </div>
    
    {{-- CODE MỚI: Dùng biến $categories --}}
    <div class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
        @if(isset($categories) && $categories->count() > 0)
            @foreach($categories as $cat)
            <label class="flex items-center space-x-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition -mx-2">
                <input type="checkbox" 
                       name="categories[]" 
                       value="{{ $cat->name }}"
                       class="filter-input w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green form-checkbox"
                       {{ in_array($cat->name, request('categories', [])) ? 'checked' : '' }}>
                
                <span class="text-gray-600 group-hover:text-brand-green transition text-sm font-medium flex-1">
                    {{ $cat->name }}
                </span>
            </label>
            @endforeach
        @else
            <p class="text-xs text-gray-400">Chưa có thể loại nào.</p>
        @endif
    </div>
</div>

                    {{-- Widget Đánh Giá --}}
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-soft">
                        <h3 class="font-bold text-brand-green font-serif text-lg mb-4 pb-2 border-b border-gray-100">
                            <i class="fas fa-star mr-2 text-brand-accent"></i> Đánh Giá
                        </h3>
                        <div class="space-y-2">
                            @for($i = 5; $i >= 3; $i--)
                            <label class="flex items-center space-x-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition -mx-2">
                                <input type="radio" 
                                       name="rating" 
                                       value="{{ $i }}"
                                       class="filter-input text-brand-green focus:ring-brand-green w-4 h-4"
                                       {{ request('rating') == $i ? 'checked' : '' }}>
                                <div class="flex items-center text-xs flex-1">
                                    <div class="text-yellow-400 flex gap-1 mr-2">
                                        @for($j = 1; $j <= 5; $j++)
                                            @if($j <= $i) <i class="fas fa-star"></i>
                                            @else <i class="far fa-star text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 font-medium group-hover:text-brand-green">Trở lên</span>
                                </div>
                            </label>
                            @endfor
                            
                            {{-- Nút xóa lọc đánh giá --}}
                            @if(request('rating'))
                                <div class="pt-2 text-center border-t border-gray-50 mt-2">
                                    <a href="{{ request()->fullUrlWithQuery(['rating' => null]) }}" class="text-xs text-red-500 hover:text-red-700 hover:underline">
                                        <i class="fas fa-times-circle"></i> Bỏ lọc đánh giá
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </aside>

                {{-- MAIN CONTENT: DANH SÁCH SÁCH --}}
                <div class="lg:col-span-9">
                    
                    {{-- Toolbar: Sắp xếp & Số lượng --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-soft border border-gray-100">
                        <p class="text-sm text-gray-500 font-medium mb-2 sm:mb-0">
                            @if(isset($books) && $books->count() > 0)
                                Hiển thị <span class="font-bold text-brand-green">{{ $books->count() }}</span> kết quả trên trang này
                            @else
                                Chưa tìm thấy sách phù hợp
                            @endif
                        </p>
                        <div class="flex items-center gap-3">
    <span class="text-sm text-gray-500 font-medium">Sắp xếp:</span>
    <div class="relative group">
        {{-- Lưu ý: class 'filter-input' rất quan trọng để JS tự động submit form --}}
        <select name="sort" class="filter-input appearance-none bg-gray-50 border border-gray-200 text-sm rounded-lg pl-4 pr-10 py-2 text-gray-700 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green cursor-pointer hover:bg-white transition shadow-sm w-48">
            
            {{-- Option 1: Mới nhất --}}
            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                Mới nhất
            </option>

            {{-- Option 2: Xem nhiều nhất --}}
            <option value="view_desc" {{ request('sort') == 'view_desc' ? 'selected' : '' }}>
                Xem nhiều nhất
            </option>

            {{-- Option 3: Đánh giá cao --}}
            <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>
                Đánh giá cao
            </option>
            
            {{-- Option 4: Tên A-Z (Thêm mới cho đủ bộ) --}}
            <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                Tên A-Z
            </option>

        </select>
        
        {{-- Icon mũi tên --}}
        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400 group-hover:text-brand-green transition">
            <i class="fas fa-chevron-down text-xs"></i>
        </div>
    </div>
</div>
                    </div>

                    {{-- Grid Sách --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                        @if(isset($books) && $books->count() > 0)
                            @foreach($books as $book)
                                @php
                                    $cover = $book->cover_image;
                                    if (!$cover) {
                                        $coverUrl = 'https://placehold.co/300x450?text=No+Image';
                                    } elseif (str_starts_with($cover, 'http')) {
                                        $coverUrl = $cover;
                                    } else {
                                        $coverUrl = asset('storage/' . $cover);
                                    }
                                @endphp

                                <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-card hover:-translate-y-1 transition-all duration-300 flex flex-col h-full relative">
                                    {{-- Ảnh bìa --}}
                                    <div class="relative w-full aspect-[2/3] bg-gray-100 overflow-hidden">
                                        <a href="{{ route('detail', $book->slug ?? $book->id) }}">
                                            <img src="{{ $coverUrl }}" 
                                                 alt="{{ $book->title }}"
                                                 class="w-full h-full object-cover transition duration-700 group-hover:scale-110"
                                                 onerror="this.src='https://placehold.co/300x450?text=No+Image'">
                                        </a>
                                        
                                        @if($book->view_count > 1000)
                                            <div class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md animate-pulse">
                                                HOT
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Nội dung --}}
                                    <div class="p-4 flex flex-col flex-1">
                                        <div class="text-[10px] text-brand-accent uppercase font-bold tracking-wider mb-1.5 truncate">
                                            @if(isset($book->categories) && $book->categories->isNotEmpty())
                                                {{ $book->categories->first()->name }}
                                            @else
                                                Sách
                                            @endif
                                        </div>
                                        
                                        <h3 class="font-serif font-bold text-gray-800 text-base leading-snug mb-1 line-clamp-2 group-hover:text-brand-green transition h-10" title="{{ $book->title }}">
                                            <a href="{{ route('detail', $book->slug ?? $book->id) }}">
                                                {{ $book->title }}
                                            </a>
                                        </h3>
                                        
                                        <p class="text-xs text-gray-500 mb-3 font-medium truncate">
                                            {{ $book->author_name ?? 'Tác giả ẩn danh' }}
                                        </p>
                                        
                                        <div class="mt-auto pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                                            <div class="flex items-center text-yellow-400 gap-1 bg-yellow-50 px-2 py-0.5 rounded-md">
                                                <i class="fas fa-star"></i> 
                                                <span class="text-gray-600 font-bold">{{ $book->avg_rating ?? '0' }}</span>
                                            </div>
                                            <div class="text-gray-400 flex items-center gap-1" title="Lượt xem">
                                                <i class="far fa-eye"></i> {{ number_format($book->view_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Empty State --}}
                            <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
                                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-book-open text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Không tìm thấy cuốn sách nào phù hợp với bộ lọc.</p>
                                <a href="{{ route('books.list') }}" class="inline-block mt-4 text-brand-green font-bold text-sm hover:underline">Xóa bộ lọc</a>
                            </div>
                        @endif
                    </div>

                    {{-- Phân trang --}}
                    <div class="mt-12 flex justify-center">
                        @if(isset($books) && method_exists($books, 'links'))
                            {{ $books->links('vendor.pagination.custom') }}
                        @endif
                    </div>

                </div>
            </div>
        </form>
    </main>

    {{-- SCRIPT TỰ ĐỘNG SUBMIT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy form
            const filterForm = document.getElementById('filterForm');
            // Lấy tất cả input có class 'filter-input' (đã thêm vào html ở trên)
            const inputs = document.querySelectorAll('.filter-input');

            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Hiệu ứng mờ nhẹ để user biết đang load
                    document.querySelector('main').style.opacity = '0.5';
                    filterForm.submit();
                });
            });
            
            // Mobile Filter Toggle
            const filterToggle = document.getElementById('mobile-filter-toggle');
            const filterPanel = document.getElementById('mobile-filter-panel');
            const filterIcon = document.getElementById('filter-toggle-icon');
            
            if (filterToggle && filterPanel) {
                filterToggle.addEventListener('click', function() {
                    filterPanel.classList.toggle('hidden');
                    filterIcon.classList.toggle('rotate-180');
                });
            }
        });
    </script>
@endsection