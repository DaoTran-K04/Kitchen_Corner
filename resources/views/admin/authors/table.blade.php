<div class="flex-1 overflow-auto" id="authors-table-wrapper">
    <table class="w-full text-left border-collapse" id="authors-table">
        <thead
            class="bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 text-xs uppercase sticky top-0 z-10 shadow-sm">
            <tr>
                <th class="px-4 py-3 w-14 bg-gray-50 dark:bg-slate-700 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                    onclick="toggleSort('stt')">
                    <span class="flex items-center justify-center gap-1">
                        STT
                        <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-stt"></i>
                    </span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none min-w-[280px]"
                    onclick="toggleSort('name')">
                    <span class="flex items-center gap-1">
                        Tác giả
                        <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-name"></i>
                    </span>
                </th>
                <th class="px-4 py-3 w-28 bg-gray-50 dark:bg-slate-700 text-center cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                    onclick="toggleSort('books')">
                    <span class="flex items-center justify-center gap-1">
                        Số sách
                        <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-books"></i>
                    </span>
                </th>
                <th class="px-6 py-3 w-36 bg-gray-50 dark:bg-slate-700 text-center">Quốc tịch</th>
                <th class="px-6 py-3 w-32 bg-gray-50 dark:bg-slate-700 text-center whitespace-nowrap">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="authors-tbody">
            @forelse($authors as $author)
                @php
                    $stt = ($authors->currentPage() - 1) * $authors->perPage() + $loop->iteration;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition duration-150 author-row"
                    data-stt="{{ $stt }}" data-name="{{ strtolower($author->name) }}"
                    data-books="{{ $author->books_count ?? 0 }}">
                    <td class="px-4 py-4 text-center font-medium text-gray-500 dark:text-slate-400">{{ $stt }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php
                                $photoUrl = $author->photo ? trim($author->photo) : null;
                                if ($photoUrl && !str_starts_with($photoUrl, 'http')) {
                                    $photoUrl = asset('storage/' . $photoUrl);
                                }
                            @endphp
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="{{ $author->name }}"
                                    class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-600 flex-shrink-0">
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-sm flex-shrink-0">
                                    {{ mb_substr($author->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <span
                                    class="font-bold text-gray-800 dark:text-white block text-sm leading-tight mb-0.5 truncate"
                                    title="{{ $author->name }}">
                                    {{ $author->name }}
                                </span>
                                @if($author->birth_year || $author->death_year)
                                    <span class="text-[10px] text-gray-400 dark:text-slate-500 italic">
                                        {{ $author->birth_year ?? '?' }} - {{ $author->death_year ?? 'nay' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span
                            class="inline-flex items-center justify-center px-2 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 text-xs font-bold rounded-full min-w-[60px]">
                            {{ $author->books_count ?? 0 }} sách
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600 dark:text-slate-400 text-sm">
                        {{ $author->nationality ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex justify-center items-center gap-2">
                            {{-- Edit --}}
                            <a href="{{ route('admin.authors.edit', $author->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition"
                                title="Sửa">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('admin.authors.destroy', $author->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa tác giả {{ $author->name }}?');"
                                class="inline">
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
                    <td colspan="5" class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-user-edit text-4xl text-gray-300 dark:text-slate-600 mb-3"></i>
                            <p>Không có tác giả nào.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($authors->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-center flex-none"
        id="pagination-container">
        {{ $authors->links('vendor.pagination.admin') }}
    </div>
@endif

{{-- Sorting & Pagination JavaScript --}}
<script>
    var authorSortState = { stt: null, name: null, books: null };

    function toggleSort(field) {
        Object.keys(authorSortState).forEach(key => {
            if (key !== field) authorSortState[key] = null;
        });
        if (authorSortState[field] === null) authorSortState[field] = 'asc';
        else if (authorSortState[field] === 'asc') authorSortState[field] = 'desc';
        else authorSortState[field] = null;
        updateAuthorSortIcons();
        applyAuthorSorting();
        updateResetButton();
    }

    function updateAuthorSortIcons() {
        Object.keys(authorSortState).forEach(field => {
            const icon = document.getElementById(`sort-icon-${field}`);
            if (!icon) return;
            icon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down', 'text-gray-400', 'text-blue-600');
            if (authorSortState[field] === 'asc') icon.classList.add('fa-sort-up', 'text-blue-600');
            else if (authorSortState[field] === 'desc') icon.classList.add('fa-sort-down', 'text-blue-600');
            else icon.classList.add('fa-sort', 'text-gray-400');
        });
    }

    function applyAuthorSorting() {
        const tbody = document.getElementById('authors-tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('.author-row'));
        if (rows.length === 0) return;
        const activeField = Object.keys(authorSortState).find(key => authorSortState[key] !== null);
        if (!activeField) {
            rows.sort((a, b) => parseInt(a.dataset.stt) - parseInt(b.dataset.stt));
        } else {
            rows.sort((a, b) => {
                let valA, valB;
                if (activeField === 'stt') { valA = parseInt(a.dataset.stt); valB = parseInt(b.dataset.stt); }
                else if (activeField === 'name') { valA = a.dataset.name; valB = b.dataset.name; }
                else if (activeField === 'books') { valA = parseInt(a.dataset.books); valB = parseInt(b.dataset.books); }
                let comparison = typeof valA === 'number' ? valA - valB : valA.localeCompare(valB, 'vi');
                return authorSortState[activeField] === 'desc' ? -comparison : comparison;
            });
        }
        rows.forEach(row => tbody.appendChild(row));
    }

    function updateResetButton() {
        const hasActiveSort = Object.values(authorSortState).some(v => v !== null);
        const resetBtn = document.getElementById('reset-btn');
        if (resetBtn) {
            resetBtn.classList.toggle('hidden', !hasActiveSort);
            resetBtn.classList.toggle('flex', hasActiveSort);
        }
    }

    function resetSort() {
        Object.keys(authorSortState).forEach(key => authorSortState[key] = null);
        updateAuthorSortIcons();
        applyAuthorSorting();
        updateResetButton();
    }

    // AJAX Pagination
    document.addEventListener('DOMContentLoaded', function () {
        const tableContainer = document.getElementById('authors-table-container');
        if (tableContainer) {
            tableContainer.addEventListener('click', function (e) {
                const link = e.target.closest('nav[role="navigation"] a') || e.target.closest('.pagination a');
                if (link && link.getAttribute('href')) {
                    e.preventDefault();
                    fetchAuthorsData(link.getAttribute('href'));
                }
            });
        }
    });

    function fetchAuthorsData(url) {
        const container = document.getElementById('authors-table-container');
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(r => r.ok ? r.text() : Promise.reject('Error'))
            .then(html => { container.innerHTML = html; window.history.pushState({}, '', url); })
            .catch(e => { console.error(e); alert('Lỗi tải dữ liệu.'); })
            .finally(() => { container.style.opacity = '1'; container.style.pointerEvents = 'auto'; });
    }
</script>