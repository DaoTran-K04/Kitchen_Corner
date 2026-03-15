@extends('layouts.app')

@section('title', 'Tìm Sách - Góc Sách')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-brand-beige/30 py-4 border-b border-brand-beige">
        <div class="container mx-auto px-4">
            <div class="flex items-center text-sm text-gray-500 font-medium">
                <a href="{{ route('home') }}" class="hover:text-brand-green transition">Trang chủ</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-brand-green font-bold">Tìm Kiếm</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-4 py-12 flex-grow min-h-screen">
        
        <!-- Header Tìm Kiếm -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-brand-green font-serif mb-3">Tìm Sách Để Review</h1>
            <p class="text-gray-500 text-lg">Chọn tiêu chí, nhập thông tin và nhấn nút để lọc sách từ thư viện</p>
        </div>

        <!-- KHU VỰC THÔNG BÁO LỖI -->
        <div class="max-w-3xl mx-auto mb-6">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-center animate-pulse">
                    <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                    <div>
                        <p class="font-bold">Lỗi nhập liệu!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Tìm Kiếm -->
        <div class="max-w-3xl mx-auto mb-12 relative z-10">
            <div class="bg-white p-1.5 rounded-full shadow-card hover:shadow-glow border border-gray-200 transition-all focus-within:border-brand-green focus-within:ring-4 focus-within:ring-brand-green/10 flex items-center">
                
                <!-- Form gửi về Route 'books.search' -->
                <form id="searchForm" action="{{ route('books.search') }}" method="GET" class="flex items-center w-full">
                    
                    <!-- Hidden Input lưu loại Filter -->
                    <input type="hidden" name="filter_type" id="filterTypeInput" value="{{ request('filter_type', 'title') }}">

                    <!-- Dropdown Button -->
                    <div class="relative group border-r border-gray-100 pr-1 mr-1">
                        <button type="button" id="filterBtn" onclick="toggleDropdown()" class="flex items-center gap-2 px-4 py-3 text-gray-700 font-bold hover:text-brand-green transition bg-white hover:bg-gray-50 rounded-l-full whitespace-nowrap min-w-[140px] justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-filter text-brand-green text-sm"></i>
                                <span id="currentFilterLabel" class="text-sm">
                                    @switch(request('filter_type'))
                                        @case('view_count') Lượt xem @break
                                        @case('avg_rating') Điểm đánh giá @break
                                        @case('total_reviews') Số lượng Review @break
                                        @default Mặc định
                                    @endswitch
                                </span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] ml-1 text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="filterMenu" class="hidden absolute top-full left-0 mt-4 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-1 z-50">
                            <!-- Options -->
                            <div onclick="selectFilter('view_count', 'Lượt xem', 'Nhập số lượt xem tối thiểu...', 'number')" class="cursor-pointer block px-4 py-3 text-sm text-gray-700 hover:bg-brand-green/10 hover:text-brand-green font-medium border-b border-gray-50">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-3"><i class="fas fa-eye"></i></span>
                                    <div><div class="font-bold">Lượt xem</div><div class="text-xs text-gray-400">Lọc theo độ phổ biến</div></div>
                                </div>
                            </div>
                            
                            <div onclick="selectFilter('avg_rating', 'Điểm đánh giá', 'Nhập điểm tối thiểu (1-5)...', 'number')" class="cursor-pointer block px-4 py-3 text-sm text-gray-700 hover:bg-brand-green/10 hover:text-brand-green font-medium border-b border-gray-50">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mr-3"><i class="fas fa-star"></i></span>
                                    <div><div class="font-bold">Điểm đánh giá</div><div class="text-xs text-gray-400">Lọc theo chất lượng</div></div>
                                </div>
                            </div>
                            
                            <div onclick="selectFilter('total_reviews', 'Số lượng Review', 'Nhập số bài review tối thiểu...', 'number')" class="cursor-pointer block px-4 py-3 text-sm text-gray-700 hover:bg-brand-green/10 hover:text-brand-green font-medium border-b border-gray-50">
                                <div class="flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center mr-3"><i class="fas fa-comments"></i></span>
                                    <div><div class="font-bold">Số lượng Review</div><div class="text-xs text-gray-400">Lọc theo độ thảo luận</div></div>
                                </div>
                            </div>

                            <div onclick="resetFilter()" class="cursor-pointer block px-4 py-2 text-xs text-gray-500 hover:bg-gray-50 text-center font-bold bg-gray-50/50">
                                <i class="fas fa-times-circle mr-1"></i> Tắt bộ lọc (Tìm tên sách)
                            </div>
                        </div>
                    </div>

                    <!-- Input -->
                    <input type="text" id="searchInput" name="keyword" value="{{ request('keyword') }}"
                           class="flex-1 px-2 py-2 bg-transparent outline-none text-gray-700 placeholder-gray-400 w-full font-medium"
                           placeholder="Nhập tên sách, tác giả..." autocomplete="off">
                    
                    <!-- Submit Button -->
                    <button type="submit" class="bg-brand-green hover:bg-brand-green-light text-white px-6 py-2.5 rounded-full font-bold transition transform active:scale-95 shadow-md flex items-center gap-2 flex-shrink-0 ml-1">
                        <i class="fas fa-search"></i> <span class="hidden sm:inline">Tìm kiếm</span>
                    </button>
                </form>
            </div>
            
            <!-- Quick Suggestions (ĐÃ CẬP NHẬT NGẪU NHIÊN TỪ DB) -->
            <div class="mt-4 text-center text-sm text-gray-500 flex flex-wrap justify-center gap-3">
                <span class="text-gray-400 flex items-center">Gợi ý nhanh:</span>
                
                @php
                    // Mảng dữ liệu ngẫu nhiên cho số liệu
                    $randomViews = [1000, 2000, 3000, 5000, 10000];
                    $view = $randomViews[array_rand($randomViews)];

                    $randomRatings = [3.5, 4.0, 4.2, 4.5, 4.8];
                    $rating = $randomRatings[array_rand($randomRatings)];

                    // Lấy tên tác giả ngẫu nhiên trực tiếp từ Database
                    try {
                        $author = \App\Models\Book::inRandomOrder()->value('author_name');
                    } catch (\Exception $e) {
                        $author = null;
                    }

                    // Fallback nếu chưa có dữ liệu hoặc lỗi
                    if (!$author) {
                        $author = 'Nguyễn Nhật Ánh';
                    }
                @endphp

                {{-- Button 1: Lượt xem Ngẫu nhiên --}}
                <button type="button" onclick="quickFilter('view_count', {{ $view }})" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-xs font-medium hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all shadow-sm">
                    <i class="fas fa-eye text-blue-500"></i> > {{ $view >= 1000 ? ($view/1000) . 'k' : $view }} Xem
                </button>

                {{-- Button 2: Đánh giá Ngẫu nhiên --}}
                <button type="button" onclick="quickFilter('avg_rating', {{ $rating }})" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-xs font-medium hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all shadow-sm">
                    <i class="fas fa-star text-yellow-500"></i> > {{ $rating }} Sao
                </button>

                {{-- Button 3: Tác giả Ngẫu nhiên từ DB --}}
                <button type="button" onclick="resetFilter('{{ $author }}')" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-xs font-medium hover:border-brand-green hover:text-brand-green hover:bg-green-50 transition-all shadow-sm">
                    <i class="fas fa-book text-brand-green"></i> {{ $author }}
                </button>
            </div>
        </div>

        <!-- Thông báo kết quả -->
        @if(request('keyword') && !session('error'))
            <div class="text-center mb-6 text-gray-500 text-sm">
                <span class="font-bold text-gray-800">Kết quả:</span>
                @if(request('filter_type') == 'title' || !request('filter_type'))
                    tìm kiếm cho từ khóa "<span class="text-brand-green font-bold">{{ request('keyword') }}</span>"
                @else
                    sách có 
                    @switch(request('filter_type'))
                        @case('view_count') lượt xem @break
                        @case('avg_rating') điểm đánh giá @break
                        @case('total_reviews') số review @break
                    @endswitch
                    từ <span class="text-brand-green font-bold">{{ request('keyword') }}</span> trở lên
                @endif
            </div>
        @endif
        
        <!-- Grid Hiển thị Sách -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($books as $book)
                <div class="group bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-card hover:-translate-y-1 transition-all duration-300 flex flex-col h-full relative">
                    <div class="relative w-full aspect-[2/3] bg-gray-100 overflow-hidden">
                        <!-- Ảnh bìa sách -->
                        <img src="{{ $book->image ?? $book->cover_image ?? 'https://placehold.co/300x450' }}" alt="{{ $book->title }}" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110" 
                             onerror="this.src='https://placehold.co/300x450?text=No+Image'">
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[1px]">
                            <!-- Link đến trang Review sách -->
                            <a href="{{ route('book.show', $book->slug ?? $book->id) }}" class="bg-yellow-400 text-brand-green px-5 py-2.5 rounded-full font-bold shadow-xl hover:scale-105 transform transition flex items-center gap-2">
                                <i class="fas fa-pen"></i> Viết Review
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-serif font-bold text-gray-800 text-base leading-snug mb-1 line-clamp-2 group-hover:text-brand-green transition h-10" title="{{ $book->title }}">
                            {{ $book->title }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-3 font-medium truncate">{{ $book->author_name ?? ($book->author ?? 'Tác giả ẩn danh') }}</p>
                        
                        <!-- Chỉ số thống kê -->
                        <div class="grid grid-cols-3 gap-1 pt-3 border-t border-gray-50 text-[10px] text-gray-500 text-center">
                            <div class="{{ request('filter_type') == 'view_count' ? 'text-brand-green font-bold bg-green-50 rounded py-1' : '' }}">
                                <i class="fas fa-eye mb-1 block"></i> {{ number_format($book->view_count ?? 0) }}
                            </div>
                            <div class="{{ request('filter_type') == 'avg_rating' ? 'text-yellow-500 font-bold bg-yellow-50 rounded py-1' : '' }}">
                                <i class="fas fa-star mb-1 block"></i> {{ $book->avg_rating ?? 0 }}
                            </div>
                            <div class="{{ request('filter_type') == 'total_reviews' ? 'text-blue-500 font-bold bg-blue-50 rounded py-1' : '' }}">
                                <i class="fas fa-comments mb-1 block"></i> {{ number_format($book->total_reviews ?? 0) }}
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('book.show', $book->slug ?? $book->id) }}" class="block w-full text-center bg-gray-50 border border-gray-200 text-gray-600 py-1.5 rounded-lg text-xs font-bold hover:bg-brand-green hover:text-white hover:border-brand-green transition">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                @if(!session('error'))
                <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200 mt-8">
                    <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 animate-pulse">
                        <i class="fas fa-search text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Không tìm thấy kết quả</h3>
                    <p class="text-gray-500 mb-6">Không có cuốn sách nào khớp với tiêu chí của bạn.</p>
                    <a href="{{ route('books.search') }}" class="text-brand-green font-bold hover:underline">Xem tất cả sách</a>
                </div>
                @endif
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            @if(method_exists($books, 'links'))
                {{ $books->withQueryString()->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>

    <script>
        function toggleDropdown() {
            document.getElementById('filterMenu').classList.toggle('hidden');
        }

        window.addEventListener('click', function(e) {
            const btn = document.getElementById('filterBtn');
            const menu = document.getElementById('filterMenu');
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Hàm helper để cập nhật input
        function updateInputAttributes(inputType, placeholder) {
            const input = document.getElementById('searchInput');
            input.placeholder = placeholder;
            input.type = inputType;
            
            if(inputType === 'number') {
                input.setAttribute('min', '0');
                input.setAttribute('step', 'any');
            } else {
                input.removeAttribute('min');
                input.removeAttribute('step');
            }
        }

        function selectFilter(filterKey, label, placeholder, inputType) {
            document.getElementById('filterTypeInput').value = filterKey;
            document.getElementById('currentFilterLabel').textContent = label;
            document.getElementById('filterMenu').classList.add('hidden');
            
            const input = document.getElementById('searchInput');
            input.value = ''; 
            
            updateInputAttributes(inputType, placeholder);
            input.focus();
        }

        function resetFilter(prefill = '') {
            selectFilter('title', 'Mặc định', 'Nhập tên sách, tác giả...', 'text');
            if(prefill) {
                document.getElementById('searchInput').value = prefill;
                document.getElementById('searchForm').submit();
            }
        }

        function quickFilter(type, value) {
            let label = 'Lượt xem';
            if(type === 'avg_rating') label = 'Điểm đánh giá';
            if(type === 'total_reviews') label = 'Số lượng Review';
            
            selectFilter(type, label, '...', 'number');
            document.getElementById('searchInput').value = value;
            document.getElementById('searchForm').submit();
        }

        // Khôi phục trạng thái input khi tải lại trang
        document.addEventListener('DOMContentLoaded', function() {
            const currentType = document.getElementById('filterTypeInput').value;
            let placeholder = 'Nhập tên sách, tác giả...';
            let inputType = 'text';

            switch(currentType) {
                case 'view_count':
                    placeholder = 'Nhập số lượt xem tối thiểu...';
                    inputType = 'number';
                    break;
                case 'avg_rating':
                    placeholder = 'Nhập điểm tối thiểu (1-5)...';
                    inputType = 'number';
                    break;
                case 'total_reviews':
                    placeholder = 'Nhập số bài review tối thiểu...';
                    inputType = 'number';
                    break;
            }
            
            updateInputAttributes(inputType, placeholder);
        });
    </script>
@endsection