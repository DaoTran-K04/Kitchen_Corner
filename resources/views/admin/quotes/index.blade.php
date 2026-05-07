@extends('layouts.admin')
@section('title', 'Quản Lý Châm Ngôn')
@section('header', 'Quản Lý Châm Ngôn')

@section('content')
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center gap-4">
            <div>
                <h2 class="font-bold text-gray-800 dark:text-white text-lg">Quản lý Châm Ngôn</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Các câu châm ngôn hiển thị ngẫu nhiên theo ngày
                    trên trang chủ</p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Reset Filter (only icon) --}}
                <button onclick="resetSort()" id="reset-btn"
                    class="hidden w-9 h-9 rounded-lg bg-gray-200 dark:bg-slate-500 text-gray-700 dark:text-white hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-600 transition flex items-center justify-center"
                    title="Reset bộ lọc">
                    <i class="fas fa-undo"></i>
                </button>

                <a href="{{ route('admin.quotes.create') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm shadow-sm transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Thêm Mới
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="quotes-table">
                <thead class="bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 font-semibold uppercase">
                    <tr>
                        <th class="px-4 py-4 w-14 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none whitespace-nowrap"
                            onclick="toggleSort('order')">
                            STT
                            <i class="fas fa-sort ml-1 text-gray-400" id="sort-icon-order"></i>
                        </th>
                        <th class="px-4 py-4">Nội dung</th>
                        <th class="px-4 py-4 w-40 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                            onclick="toggleSort('author')">
                            Tác giả
                            <i class="fas fa-sort ml-1 text-gray-400" id="sort-icon-author"></i>
                        </th>
                        <th class="px-4 py-4 w-32">Nguồn</th>
                        <th class="px-4 py-4 w-28 text-center whitespace-nowrap">Trạng thái</th>
                        <th class="px-4 py-4 w-28 text-center whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="quotes-tbody">
                    @forelse($quotes as $quote)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition" data-order="{{ $quote->order }}"
                            data-author="{{ strtolower($quote->author) }}">
                            <td class="px-4 py-4 text-center font-medium text-gray-500 dark:text-slate-400">
                                {{ ($quotes->currentPage() - 1) * $quotes->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-gray-800 dark:text-white line-clamp-2 italic">
                                    "{{ $quote->content }}"
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-gray-800 dark:text-white">{{ $quote->author }}</div>
                            </td>
                            <td class="px-4 py-4 text-gray-500 dark:text-slate-400">
                                {{ $quote->source ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if ($quote->is_active)
                                    <span
                                        class="inline-block px-2.5 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-xs font-bold rounded-full">
                                        Hiển thị
                                    </span>
                                @else
                                    <span
                                        class="inline-block px-2.5 py-1 bg-gray-100 dark:bg-slate-600 text-gray-500 dark:text-slate-300 text-xs font-bold rounded-full">
                                        Đang ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.quotes.edit', $quote->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition"
                                        title="Sửa">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.quotes.destroy', $quote->id) }}" method="POST"
                                        class="confirm-submit"
                                        data-confirm="Bạn có chắc muốn xóa châm ngôn này?">
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 dark:text-slate-500 italic">
                                <i class="fas fa-quote-left text-4xl mb-3 block opacity-30"></i>
                                Chưa có châm ngôn nào. Hãy thêm mới!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($quotes->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                {{ $quotes->links() }}
            </div>
        @endif
    </div>

    <script>
        // Sort state
        let sortState = {
            order: null,
            author: null
        };

        function toggleSort(field) {
            // Reset other fields
            Object.keys(sortState).forEach(key => {
                if (key !== field) sortState[key] = null;
            });

            // Toggle current field: null -> asc -> desc -> null
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
            Object.keys(sortState).forEach(field => {
                const icon = document.getElementById(`sort-icon-${field}`);
                if (!icon) return;

                icon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down', 'text-gray-400', 'text-blue-600',
                    'dark:text-blue-400');

                if (sortState[field] === 'asc') {
                    icon.classList.add('fa-sort-up', 'text-blue-600', 'dark:text-blue-400');
                } else if (sortState[field] === 'desc') {
                    icon.classList.add('fa-sort-down', 'text-blue-600', 'dark:text-blue-400');
                } else {
                    icon.classList.add('fa-sort', 'text-gray-400');
                }
            });
        }

        function applySorting() {
            const tbody = document.getElementById('quotes-tbody');
            const rows = Array.from(tbody.querySelectorAll('tr[data-order]'));

            if (rows.length === 0) return;

            const activeField = Object.keys(sortState).find(key => sortState[key] !== null);

            if (!activeField) {
                // Default sort by order ascending
                rows.sort((a, b) => parseInt(a.dataset.order) - parseInt(b.dataset.order));
            } else {
                rows.sort((a, b) => {
                    let valA, valB;

                    if (activeField === 'order') {
                        valA = parseInt(a.dataset.order);
                        valB = parseInt(b.dataset.order);
                    } else if (activeField === 'author') {
                        valA = a.dataset.author;
                        valB = b.dataset.author;
                    }

                    let comparison = 0;
                    if (typeof valA === 'number') {
                        comparison = valA - valB;
                    } else {
                        comparison = valA.localeCompare(valB, 'vi');
                    }

                    return sortState[activeField] === 'desc' ? -comparison : comparison;
                });
            }

            rows.forEach(row => tbody.appendChild(row));
        }

        function updateResetButton() {
            const hasActiveSort = Object.values(sortState).some(v => v !== null);
            const resetBtn = document.getElementById('reset-btn');
            if (resetBtn) {
                resetBtn.classList.toggle('hidden', !hasActiveSort);
            }
        }

        function resetSort() {
            Object.keys(sortState).forEach(key => sortState[key] = null);
            updateSortIcons();
            applySorting();
            updateResetButton();
        }

        // Initialize default sort
        document.addEventListener('DOMContentLoaded', function () { applySorting(); });
    </script>
@endsection
