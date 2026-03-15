@extends('layouts.admin')
@section('title', 'Quản lý Sách')
@section('header', 'Danh sách đầu sách')

@section('content')
{{-- Thêm style đổ bóng mềm mại --}}
<style>
    .shadow-soft {
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 3px rgba(0,0,0,0.02);
    }
    .shadow-soft:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 0 5px rgba(0,0,0,0.02);
    }
</style>

<div class="flex flex-col gap-4">
    
    {{-- Header Toolbar --}}
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-soft border border-gray-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4 flex-none z-50 transition-colors duration-300">
        
        <h2 class="text-lg font-bold text-gray-700 dark:text-white whitespace-nowrap flex items-center gap-2">
            Kho sách <span class="px-2 py-0.5 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-slate-200 rounded-full text-xs border border-gray-300 dark:border-slate-500">{{ $totalBooks ?? $books->total() }}</span>
        </h2>

        <div class="flex items-center gap-2 w-full md:w-auto">
            
            {{-- Form Tìm kiếm & Lọc (Thêm ID để JS bắt sự kiện) --}}
            <form id="book-search-form" action="{{ route('admin.books.index') }}" method="GET" class="flex items-center gap-2">
                
                {{-- Category Filter --}}
                <div class="relative" id="category-filter-wrapper">
                    <button type="button" 
                        onclick="toggleCategoryDropdown()"
                        class="h-10 pl-4 pr-10 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm text-gray-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm flex items-center gap-2 min-w-[250px] relative">
                        <span id="current-category-label" class="truncate max-w-[210px] font-medium block text-left">
                            @if(request('category_id') && request('category_id') != 'all')
                                {{ $categories->firstWhere('id', request('category_id'))->name ?? 'Tất cả thể loại' }}
                            @else
                                <span class="text-gray-500 dark:text-slate-400">Tất cả thể loại</span>
                            @endif
                        </span>
                        
                        <span class="absolute right-0 inset-y-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="category-dropdown" 
                        class="hidden absolute top-[calc(100%+8px)] left-0 w-full bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 overflow-hidden">
                        
                        {{-- Search box inside dropdown --}}
                        <div class="p-2 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="category-search" 
                                    onkeyup="filterCategories()"
                                    placeholder="Tìm thể loại..." 
                                    class="w-full h-8 pl-8 pr-3 text-xs bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-md focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition">
                            </div>
                        </div>

                        <div class="max-h-[280px] overflow-y-auto custom-scrollbar p-1.5 space-y-0.5">
                            {{-- Option: All --}}
                            <label class="cursor-pointer group block">
                                <input type="radio" name="category_id" value="all" class="hidden peer"
                                    {{ !request('category_id') || request('category_id') == 'all' ? 'checked' : '' }}>
                                <div class="px-3 py-2.5 rounded-lg text-sm text-gray-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 peer-checked:font-semibold hover:bg-gray-50 dark:hover:bg-slate-700/80 transition flex items-center justify-between">
                                    <span>Tất cả</span>
                                    <i class="fas fa-check text-blue-600 dark:text-blue-400 text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </label>

                            {{-- Options: Categories --}}
                            @foreach($categories as $cat)
                            <label class="cursor-pointer group block category-item" data-name="{{ strtolower($cat->name) }}">
                                <input type="radio" name="category_id" value="{{ $cat->id }}" class="hidden peer"
                                    {{ request('category_id') == $cat->id ? 'checked' : '' }}>
                                <div class="px-3 py-2.5 rounded-lg text-sm text-gray-700 dark:text-slate-300 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 peer-checked:font-semibold hover:bg-gray-50 dark:hover:bg-slate-700/80 transition flex items-center justify-between">
                                    <span>{{ $cat->name }}</span>
                                    <i class="fas fa-check text-blue-600 dark:text-blue-400 text-xs opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </label>
                            @endforeach
                            
                            <div id="no-category-found" class="hidden px-3 py-4 text-center text-xs text-gray-400 dark:text-slate-500 italic">
                                Không tìm thấy thể loại
                            </div>
                        </div>
                    </div>

                    {{-- Overlay to close dropdown --}}
                    <div id="dropdown-overlay" onclick="toggleCategoryDropdown()" class="hidden fixed inset-0 z-40 bg-transparent"></div>
                </div>

                {{-- Search Input --}}
                {{-- Search Input (Flex container to fix alignment) --}}
                <div class="relative w-full md:w-80 group flex">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-20">
                        <i class="fas fa-search text-gray-400 text-xs group-focus-within:text-blue-500 transition"></i>
                    </div>
                    
                    <input type="text" name="keyword" placeholder="Nhập tên sách, tác giả..." value="{{ request('keyword') }}"
                        class="h-10 w-full pl-9 pr-3 border border-gray-300 dark:border-slate-600 rounded-l-lg border-r-0 text-sm focus:outline-none focus:ring-0 focus:border-blue-500 transition shadow-sm bg-white dark:bg-slate-700 text-gray-800 dark:text-white dark:placeholder-slate-400 z-10">
                    
                    <button type="submit" class="h-10 px-4 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-r-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-slate-600 transition cursor-pointer flex items-center justify-center flex-none z-10" title="Tìm kiếm">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                {{-- Button Xóa lọc (Ẩn hiện bằng JS) --}}
                <button type="button" 
                    id="clear-filter-btn"
                    onclick="clearFilters()" 
                    class="{{ (request('keyword') || (request('category_id') && request('category_id') != 'all')) ? '' : 'hidden' }} h-10 w-10 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition shadow-sm flex items-center justify-center flex-none" 
                    title="Xóa bộ lọc (Reset)">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </form>

            <div class="h-8 w-px bg-gray-300 dark:bg-slate-600 mx-1 hidden md:block"></div>

            {{-- Nút thêm mới --}}
            <a href="{{ route('admin.books.create') }}" class="h-10 bg-blue-600 text-white px-4 rounded-lg text-sm hover:bg-blue-700 flex items-center justify-center shadow-soft transition whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i> Thêm mới
            </a>
        </div>
    </div>

    {{-- Table Container --}}
    <div id="books-table-container" class="bg-white dark:bg-slate-800 rounded-xl shadow-soft border border-gray-100 dark:border-slate-700 flex-1 flex flex-col z-0 transition-colors duration-300">
        @include('admin.books.table')
    </div>

</div>

{{-- JAVASCRIPT XỬ LÝ ENAX (VANILLA JS CHO ỔN ĐỊNH) --}}
<script>
    // 1. Dropdown Toggle & Animation
    function toggleCategoryDropdown() {
        const dropdown = document.getElementById('category-dropdown');
        const overlay = document.getElementById('dropdown-overlay');
        
        if (dropdown.classList.contains('hidden')) {
            // OPEN
            dropdown.classList.remove('hidden');
            overlay.classList.remove('hidden');
            
            // Slide Down Animation
            dropdown.animate([
                { opacity: 0, transform: 'translateY(-10px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ], {
                duration: 200, easing: 'ease-out', fill: 'forwards'
            });

            setTimeout(() => {
                const searchInput = document.getElementById('category-search');
                if(searchInput) searchInput.focus();
            }, 100);

        } else {
            // CLOSE
            const animation = dropdown.animate([
                { opacity: 1, transform: 'translateY(0)' },
                { opacity: 0, transform: 'translateY(-10px)' }
            ], {
                duration: 150, easing: 'ease-in', fill: 'forwards'
            });
            
            animation.onfinish = () => {
                dropdown.classList.add('hidden');
                overlay.classList.add('hidden');
            };
        }
    }

    // 2. Filter Client-side inside Dropdown (Improved Logic)
    function filterCategories() {
        const input = document.getElementById('category-search');
        // Chuẩn hóa chuỗi tìm kiếm: bỏ dấu, về chữ thường
        const filter = input.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        const items = document.querySelectorAll('.category-item');
        const noResults = document.getElementById('no-category-found');
        let hasVisible = false;

        items.forEach(item => {
            // Lấy data-name gốc và cũng chuẩn hóa tương tự
            const originalName = item.getAttribute('data-name').toLowerCase();
            const normalizedName = originalName.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            
            // Tìm kiếm tương đối (cho phép gõ không dấu tìm có dấu)
            if (normalizedName.includes(filter)) {
                item.style.display = "";
                hasVisible = true;
            } else {
                item.style.display = "none";
            }
        });
        
        if (!hasVisible) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    // 3. MAIN AJAX LOGIC (Vanilla JS)
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('book-search-form');
        const tableContainer = document.getElementById('books-table-container');

        // A. Handle Form Submit (Search)
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop page reload
                fetchData(getFullUrl());
            });
        }

        // B. Handle Category Radio Change
        const radios = document.querySelectorAll('input[name="category_id"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Update Label immediately
                updateCategoryLabel(this);
                // Close Dropdown
                toggleCategoryDropdown();
                // Fetch Data
                fetchData(getFullUrl());
            });
        });

        // C. Handle Pagination Clicks (Event Delegation)
        // Vì pagination inner HTML thay đổi sau khi AJAX, nên phải dùng delegation từ container cha
        if (tableContainer) {
            tableContainer.addEventListener('click', function(e) {
                // Cập nhật selector chính xác cho Laravel Pagination (Tailwind)
                // Thường là thẻ 'a' nằm trong 'nav' hoặc thẻ 'a' có class page-link (tùy view)
                // Selector này bao quát cả nav > a và .pagination a
                const link = e.target.closest('nav[role="navigation"] a') || e.target.closest('.pagination a'); 
                
                if (link && link.getAttribute('href')) {
                    e.preventDefault();
                    // Lấy URL trực tiếp từ href
                    fetchData(link.getAttribute('href'));
                }
            });
        }

        // Helper: Get Current URL with Params
        function getFullUrl(page = 1) {
            // Lấy URL form action
            const baseUrl = searchForm.getAttribute('action');
            // Lấy FormData
            const formData = new FormData(searchForm);
            // Convert to Query String
            const params = new URLSearchParams(formData);
            // Reset page param if generic fetch (or keep existing logic)
            // Note: Laravel pagination links already contain full query params sometimes.
            // But here we build from form.
            return `${baseUrl}?${params.toString()}`;
        }

        // Core Fetch Function
        function fetchData(url) {
            // Visual Loading Feedback
            tableContainer.style.opacity = '0.5';
            tableContainer.style.pointerEvents = 'none';

            // Check Visibility of Clear Button
            checkClearButtonVisibility();

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Important for Laravel $request->ajax()
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(html => {
                tableContainer.innerHTML = html;
                
                // Update Browser URL (History)
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                console.error('Error loading books:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.');
            })
            .finally(() => {
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            });
        }

        // Helper: Update UI Label
        function updateCategoryLabel(radioInput) {
            const labelSpan = document.getElementById('current-category-label');
            
            if (radioInput.value === 'all') {
                labelSpan.innerHTML = '<span class="text-gray-500 dark:text-slate-400">Tất cả thể loại</span>';
            } else {
                // Find sibling text
                // Structure: label > input ~ div > span
                const wrapperDiv = radioInput.nextElementSibling; // the div after input
                const nameSpan = wrapperDiv.querySelector('span'); // first span
                if (nameSpan) {
                    labelSpan.innerText = nameSpan.innerText;
                }
            }
        }

        // Helper: Check Clear Button
        function checkClearButtonVisibility() {
            const btn = document.getElementById('clear-filter-btn');
            const keyword = searchForm.querySelector('input[name="keyword"]').value;
            const category = searchForm.querySelector('input[name="category_id"]:checked')?.value;

            if (keyword || (category && category !== 'all')) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
            }
        }

        // Make global for existing onclick="clearFilters()"
        window.clearFilters = function() {
            // Reset Inputs
            searchForm.querySelector('input[name="keyword"]').value = '';
            
            const allRadio = searchForm.querySelector('input[name="category_id"][value="all"]');
            if (allRadio) {
                allRadio.checked = true;
                updateCategoryLabel(allRadio);
            }

            // Fetch
            fetchData(getFullUrl());
        };
    });
</script>
@endsection