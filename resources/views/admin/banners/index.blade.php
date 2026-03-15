@extends('layouts.admin')
@section('title', 'Quản Lý Banner')
@section('header', 'Quản Lý Banner')

@section('content')
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex flex-wrap justify-between items-center gap-4">
            <h2 class="font-bold text-gray-800 dark:text-white">Danh sách Trình Chiếu Nội Dung</h2>
            
            <div class="flex items-center gap-2">
                {{-- Reset Filter (only icon) --}}
                <button onclick="resetSort()" id="reset-btn" 
                        class="hidden w-9 h-9 rounded-lg bg-gray-200 dark:bg-slate-500 text-gray-700 dark:text-white hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-600 transition flex items-center justify-center"
                        title="Reset bộ lọc">
                    <i class="fas fa-undo"></i>
                </button>
                
                <a href="{{ route('admin.banners.create') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm shadow-sm transition">
                    <i class="fas fa-plus mr-2"></i> Thêm mới
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="banners-table">
                <thead class="bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-4 w-14 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('order')" data-sort="order">
                            <span class="flex items-center justify-center gap-1">
                                STT
                                <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-order"></i>
                            </span>
                        </th>
                        <th class="px-4 py-4 w-28">Hình ảnh</th>
                        <th class="px-4 py-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('title')" data-sort="title">
                            <span class="flex items-center gap-1">
                                Tiêu đề / Mô tả
                                <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-title"></i>
                            </span>
                        </th>
                        <th class="px-4 py-4 w-32 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-4 w-32 text-center whitespace-nowrap">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="banners-tbody">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition banner-row"
                            data-order="{{ $banner->order }}"
                            data-title="{{ strtolower($banner->title) }}">
                            <td class="px-4 py-4 font-medium text-gray-500 dark:text-slate-400 text-center">{{ $loop->iteration }}</td>
                            <td class="px-4 py-4">
                                @php $bImg = trim($banner->image); @endphp
                                <img src="{{ Str::startsWith($bImg, 'http') ? $bImg : asset('storage/' . $bImg) }}"
                                    class="h-14 w-20 object-cover rounded-lg border border-gray-200 dark:border-slate-600 shadow-sm"
                                    alt="Banner Img">
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800 dark:text-white text-base mb-1">{{ $banner->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400 line-clamp-1">{{ $banner->description }}</div>
                                @if($banner->tag)
                                    <span
                                        class="inline-block mt-2 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 text-[10px] font-bold rounded uppercase">{{ $banner->tag }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($banner->is_active)
                                    <span
                                        class="inline-block px-2.5 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-xs font-bold rounded-full whitespace-nowrap">Hiển Thị</span>
                                @else
                                    <span
                                        class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-300 text-xs font-bold rounded-full whitespace-nowrap">Đang Ẩn</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition"
                                        title="Sửa">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa banner này?');">
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
                        <tr id="empty-row">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500 italic">Chưa có
                                banner nào. Hãy thêm mới!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sort state
        let sortState = {
            order: null,   // null | 'asc' | 'desc'
            title: null    // null | 'asc' | 'desc'
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
            // Order icon
            const orderIcon = document.getElementById('sort-icon-order');
            if (sortState.order === 'asc') {
                orderIcon.className = 'fas fa-sort-up text-blue-600';
            } else if (sortState.order === 'desc') {
                orderIcon.className = 'fas fa-sort-down text-blue-600';
            } else {
                orderIcon.className = 'fas fa-sort text-gray-400';
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
        }

        function applySorting() {
            const tbody = document.getElementById('banners-tbody');
            const rows = Array.from(tbody.querySelectorAll('.banner-row'));
            
            if (rows.length === 0) return;

            rows.sort((a, b) => {
                // Primary sort by order if active
                if (sortState.order !== null) {
                    const orderA = parseInt(a.dataset.order);
                    const orderB = parseInt(b.dataset.order);
                    const orderCompare = sortState.order === 'asc' ? orderA - orderB : orderB - orderA;
                    if (orderCompare !== 0) return orderCompare;
                }

                // Secondary sort by title if active
                if (sortState.title !== null) {
                    const titleA = a.dataset.title;
                    const titleB = b.dataset.title;
                    const titleCompare = titleA.localeCompare(titleB, 'vi');
                    return sortState.title === 'asc' ? titleCompare : -titleCompare;
                }

                // Default: sort by order asc (1, 2, 3...)
                return parseInt(a.dataset.order) - parseInt(b.dataset.order);
            });

            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        function updateResetButton() {
            const resetBtn = document.getElementById('reset-btn');
            if (sortState.order !== null || sortState.title !== null) {
                resetBtn.classList.remove('hidden');
                resetBtn.classList.add('flex');
            } else {
                resetBtn.classList.add('hidden');
                resetBtn.classList.remove('flex');
            }
        }

        function resetSort() {
            sortState.order = null;
            sortState.title = null;
            updateSortIcons();
            applySorting();
            updateResetButton();
        }
    </script>
@endsection