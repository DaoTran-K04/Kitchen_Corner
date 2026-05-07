@extends('layouts.admin')
@section('title', 'Quản Lý Công Thức')
@section('header', 'Quản Lý Công Thức')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex flex-wrap justify-between items-center gap-4">
        <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
            <i class="fas fa-utensils text-green-500"></i>Danh sách công thức
            <span class="text-sm font-normal text-gray-500 dark:text-slate-400">({{ $recipes->total() }})</span>
        </span>

        <form method="GET" action="{{ route('admin.recipes.index') }}" class="flex items-center gap-3">
            <select name="status" class="px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-800 text-gray-800 dark:text-slate-200 text-sm" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp / Ẩn</option>
            </select>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên công thức..." 
                    class="px-4 py-2 pl-10 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-800 text-gray-800 dark:text-slate-200 text-sm">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="m-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    <div id="recipe-table-container" class="overflow-x-auto">
        @include('admin.recipes.table')
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('recipe-table-container');
    const searchForm = document.querySelector('form');
    const searchInput = document.querySelector('input[name="search"]');
    const statusSelect = document.querySelector('select[name="status"]');
    let timeout = null;

    function fetchRecipes(url = null) {
        const status = statusSelect ? statusSelect.value : '';
        const search = searchInput ? searchInput.value : '';
        const fetchUrl = url || `{{ route('admin.recipes.index') }}?status=${status}&search=${encodeURIComponent(search)}`;
        
        tableContainer.style.opacity = '0.5';
        
        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            tableContainer.style.opacity = '1';
            attachPagination();
        })
        .catch(err => {
            console.error('Error fetching recipes:', err);
            tableContainer.style.opacity = '1';
        });
    }

    function attachPagination() {
        const paginationLinks = tableContainer.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchRecipes(this.href);
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchRecipes();
            }, 500);
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function(e) {
            e.preventDefault();
            fetchRecipes();
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchRecipes();
        });
    }

    attachPagination();
});
</script>
@endsection
