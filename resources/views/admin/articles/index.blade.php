@extends('layouts.admin')
@section('title', 'Tạp Chí Đọc')
@section('header', 'Tạp Chí Đọc')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white text-lg">Danh sách Bài viết Tạp Chí</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Các bài viết trong mục Tạp Chí Đọc trên trang chủ</p>
            </div>
            
            <div class="flex items-center gap-2">
                {{-- Reset Filter (only icon) --}}
                <button onclick="resetSort()" id="reset-btn" 
                        class="hidden w-9 h-9 rounded-lg bg-gray-200 dark:bg-slate-500 text-gray-700 dark:text-white hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-600 transition flex items-center justify-center"
                        title="Reset bộ lọc">
                    <i class="fas fa-undo"></i>
                </button>
                
                <a href="{{ route('admin.articles.create') }}" 
                   class="h-10 bg-blue-600 text-white px-4 rounded-lg text-sm hover:bg-blue-700 flex items-center justify-center shadow-md hover:shadow-lg transition transform hover:scale-105 whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i> Thêm mới
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="articles-table">
                <thead class="bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 font-semibold uppercase text-xs">
                    <tr>
                        <th class="px-4 py-4 w-14 text-center whitespace-nowrap cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('id')" data-sort="id">
                            <span class="flex items-center justify-center gap-1">
                                STT
                                <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-id"></i>
                            </span>
                        </th>
                        <th class="px-4 py-4 w-24 whitespace-nowrap">Hình ảnh</th>
                        <th class="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('title')" data-sort="title">
                            <span class="flex items-center gap-1">
                                Tiêu đề
                                <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-title"></i>
                            </span>
                        </th>
                        <th class="px-4 py-4 w-28 whitespace-nowrap">Tag</th>
                        <th class="px-4 py-4 w-24 text-center whitespace-nowrap">Nổi bật</th>
                        <th class="px-4 py-4 w-24 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-4 w-36 whitespace-nowrap cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('date')" data-sort="date">
                            <span class="flex items-center gap-1">
                                Ngày tạo
                                <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-date"></i>
                            </span>
                        </th>
                        <th class="px-4 py-4 w-20 text-center whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="articles-tbody">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition article-row"
                            data-id="{{ $article->id }}"
                            data-title="{{ strtolower($article->title) }}"
                            data-date="{{ $article->created_at->timestamp }}">
                            <td class="px-4 py-4 font-medium text-gray-500 dark:text-slate-400 text-center">{{ ($articles->currentPage() - 1) * $articles->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-4">
                                @if($article->thumbnail)
                                    @php
                                        $thumb = trim($article->thumbnail);
                                        $imgUrl = str_starts_with($thumb, 'http') 
                                            ? $thumb 
                                            : asset('storage/' . $thumb);
                                    @endphp
                                    <img src="{{ $imgUrl }}" alt="{{ $article->title }}" class="w-16 h-12 object-cover rounded-lg shadow-sm">
                                @else
                                    <div class="w-16 h-12 bg-gray-100 dark:bg-slate-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800 dark:text-white line-clamp-2">{{ $article->title }}</div>
                                @if($article->excerpt)
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1 line-clamp-1">{{ $article->excerpt }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($article->tag)
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full whitespace-nowrap">
                                        {{ $article->tag }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($article->is_featured)
                                    <span class="inline-flex items-center px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-full">
                                        <i class="fas fa-star mr-1"></i>Nổi bật
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($article->is_active)
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-xs font-bold rounded-full">
                                        <i class="fas fa-eye mr-1"></i>Hiển thị
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-slate-400 text-xs font-bold rounded-full">
                                        <i class="fas fa-eye-slash mr-1"></i>Đang ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-500 dark:text-slate-400 text-xs whitespace-nowrap">
                                {{ $article->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.articles.edit', $article->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition"
                                        title="Sửa">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST"
                                        class="confirm-submit"
                                        data-confirm="Bạn có chắc muốn xóa bài viết này?">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition"
                                            title="Xóa">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400 dark:text-slate-500 italic">
                                <i class="fas fa-newspaper text-4xl mb-3 block opacity-30"></i>
                                Chưa có bài viết nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    <script>
        // Sort state
        let sortState = {
            id: null,     // null | 'asc' | 'desc'
            title: null,  // null | 'asc' | 'desc'
            date: null    // null | 'asc' | 'desc'
        };

        function toggleSort(field) {
            // Cycle through: null -> asc -> desc -> null
            if (sortState[field] === null) {
                sortState[field] = 'asc';
            } else if (sortState[field] === 'asc') {
                sortState[field] = 'desc';
            } else {
                sortState[field] = null;
            }

            updateSortIcons();
            applySorting();
            updateResetButton();
        }

        function updateSortIcons() {
            // ID icon
            const idIcon = document.getElementById('sort-icon-id');
            if (sortState.id === 'asc') {
                idIcon.className = 'fas fa-sort-up text-blue-600';
            } else if (sortState.id === 'desc') {
                idIcon.className = 'fas fa-sort-down text-blue-600';
            } else {
                idIcon.className = 'fas fa-sort text-gray-400';
            }

            // Title icon
            const titleIcon = document.getElementById('sort-icon-title');
            if (sortState.title === 'asc') {
                titleIcon.className = 'fas fa-sort-up text-blue-600';
            } else if (sortState.title === 'desc') {
                titleIcon.className = 'fas fa-sort-down text-blue-600';
            } else {
                titleIcon.className = 'fas fa-sort text-gray-400';
            }

            // Date icon
            const dateIcon = document.getElementById('sort-icon-date');
            if (sortState.date === 'asc') {
                dateIcon.className = 'fas fa-sort-up text-blue-600';
            } else if (sortState.date === 'desc') {
                dateIcon.className = 'fas fa-sort-down text-blue-600';
            } else {
                dateIcon.className = 'fas fa-sort text-gray-400';
            }
        }

        function applySorting() {
            const tbody = document.getElementById('articles-tbody');
            const rows = Array.from(tbody.querySelectorAll('.article-row'));
            
            if (rows.length === 0) return;

            rows.sort((a, b) => {
                // Sort by ID if active
                if (sortState.id !== null) {
                    const idA = parseInt(a.dataset.id);
                    const idB = parseInt(b.dataset.id);
                    const idCompare = idA - idB;
                    if (idCompare !== 0) {
                        return sortState.id === 'asc' ? idCompare : -idCompare;
                    }
                }

                // Sort by title if active
                if (sortState.title !== null) {
                    const titleA = a.dataset.title;
                    const titleB = b.dataset.title;
                    const titleCompare = titleA.localeCompare(titleB, 'vi');
                    if (titleCompare !== 0) {
                        return sortState.title === 'asc' ? titleCompare : -titleCompare;
                    }
                }

                // Sort by date if active
                if (sortState.date !== null) {
                    const dateA = parseInt(a.dataset.date);
                    const dateB = parseInt(b.dataset.date);
                    const dateCompare = dateA - dateB;
                    return sortState.date === 'asc' ? dateCompare : -dateCompare;
                }

                // Default: keep original order (by date desc)
                return parseInt(b.dataset.date) - parseInt(a.dataset.date);
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        function updateResetButton() {
            const resetBtn = document.getElementById('reset-btn');
            if (sortState.id !== null || sortState.title !== null || sortState.date !== null) {
                resetBtn.classList.remove('hidden');
                resetBtn.classList.add('flex');
            } else {
                resetBtn.classList.add('hidden');
                resetBtn.classList.remove('flex');
            }
        }

        function resetSort() {
            sortState.id = null;
            sortState.title = null;
            sortState.date = null;
            updateSortIcons();
            applySorting();
            updateResetButton();
        }
    </script>
@endsection
