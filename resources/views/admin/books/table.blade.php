<div class="flex-1 overflow-auto" id="books-table-wrapper">
    <table class="w-full text-left border-collapse" id="books-table">
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
                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                    onclick="toggleSort('title')">
                    <span class="flex items-center gap-1">
                        Sách
                        <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-title"></i>
                    </span>
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-slate-700 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-600 transition select-none"
                    onclick="toggleSort('author')">
                    <span class="flex items-center gap-1">
                        Tác giả
                        <i class="fas fa-sort text-gray-400 sort-icon" id="sort-icon-author"></i>
                    </span>
                </th>
                <th class="px-6 py-3 w-64 bg-gray-50 dark:bg-slate-700">Danh mục</th>
                <th class="px-6 py-3 text-center bg-gray-50 dark:bg-slate-700 w-32">Trạng thái</th>
                <th class="px-6 py-3 text-center bg-gray-50 dark:bg-slate-700 w-[150px] whitespace-nowrap">Hành động
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700" id="books-tbody">
            @forelse($books as $book)
                @php
                    $stt = ($books->currentPage() - 1) * $books->perPage() + $loop->iteration;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 group transition duration-150 book-row"
                    data-stt="{{ $stt }}" data-title="{{ strtolower($book->title) }}"
                    data-author="{{ strtolower($book->author_name) }}">
                    <td class="px-4 py-4 text-center font-medium text-gray-500 dark:text-slate-400">{{ $stt }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="relative w-10 h-14 flex-shrink-0">
                                @php
                                    $coverImage = $book->cover_image;
                                    if ($coverImage) {
                                        $coverImage = trim($coverImage); // Trim whitespace
                                        if (!str_starts_with($coverImage, 'http')) {
                                            // Use asset() to handle XAMPP/Subdirectory paths correctly
                                            $coverImage = asset('storage/' . $coverImage);
                                        }
                                    }
                                    $coverSrc = $coverImage ?: 'https://placehold.co/50';
                                @endphp
                                <img src="{{ $coverSrc }}"
                                    class="w-full h-full object-cover rounded border border-gray-200 dark:border-slate-600 bg-gray-100 dark:bg-slate-600 shadow-sm"
                                    alt="{{ $book->title }}"
                                    onerror="this.onerror=null; this.src='https://placehold.co/50?text=No+Img';">
                            </div>
                            <div>
                                <span class="font-bold text-gray-800 dark:text-white block text-sm leading-tight mb-1"
                                    title="{{ $book->title }}">
                                    {{ Str::limit($book->title, 35) }}
                                </span>
                                <span class="text-[10px] text-gray-400 dark:text-slate-500 flex items-center gap-1">
                                    <i class="far fa-eye"></i> {{ number_format($book->view_count) }} lượt xem
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-gray-600 dark:text-slate-300 text-sm font-medium">
                        {{ $book->author_name }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @forelse($book->categories as $cat)
                                <span
                                    class="px-2 py-1 bg-blue-50 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 rounded-md text-[10px] font-bold border border-blue-100 dark:border-blue-800 whitespace-nowrap">
                                    {{ $cat->name }}
                                </span>
                            @empty
                                <span class="text-gray-400 dark:text-slate-500 text-xs italic">Chưa phân loại</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($book->is_approved)
                            <span
                                class="inline-flex items-center justify-center gap-1 text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50 px-2 py-1 rounded-full text-[10px] font-bold border border-green-200 dark:border-green-800 w-24 whitespace-nowrap">
                                <i class="fas fa-check-circle"></i> Đã duyệt
                            </span>
                        @else
                            <span
                                class="inline-flex items-center justify-center gap-1 text-yellow-700 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/50 px-2 py-1 rounded-full text-[10px] font-bold border border-yellow-200 dark:border-yellow-800 w-24 whitespace-nowrap">
                                <i class="fas fa-clock"></i> Chờ duyệt
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex justify-center items-center gap-2">
                            @if(!$book->is_approved)
                                <form action="{{ route('admin.books.approve', $book) }}" method="POST" class="inline">
                                    @csrf
                                    <button
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 dark:hover:bg-green-600 hover:text-white transition"
                                        title="Duyệt sách này">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.books.edit', $book) }}"
                                class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white transition flex items-center justify-center"
                                title="Sửa">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <a href="{{ route('reviews.create', ['book_id' => $book->id]) }}"
                                class="h-8 w-8 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 hover:bg-purple-500 dark:hover:bg-purple-600 hover:text-white transition flex items-center justify-center"
                                title="Viết Review" target="_blank">
                                <i class="fas fa-feather-alt text-xs"></i>
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa cuốn sách này không?');">
                                @csrf @method('DELETE')
                                <button
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
                    <td colspan="6" class="text-center py-12 text-gray-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-search text-4xl text-gray-300 dark:text-slate-600 mb-3"></i>
                            <p>Không tìm thấy cuốn sách nào phù hợp.</p>
                            @if(request('keyword') || request('category_id'))
                                <button onclick="clearFilters()"
                                    class="text-blue-600 dark:text-blue-400 text-sm hover:underline mt-1 font-medium bg-transparent border-0 cursor-pointer">Xóa
                                    bộ lọc</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($books->hasPages())
    <div
        class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-center flex-none">
        {{ $books->links('vendor.pagination.admin') }}
    </div>
@endif

{{-- Sorting JavaScript --}}
<script>
    // Sort state for books table
    var bookSortState = {
        stt: null,    // null | 'asc' | 'desc'
        title: null,
        author: null
    };

    function toggleSort(field) {
        // Reset other fields
        Object.keys(bookSortState).forEach(key => {
            if (key !== field) bookSortState[key] = null;
        });

        // Cycle: null -> asc -> desc -> null
        if (bookSortState[field] === null) {
            bookSortState[field] = 'asc';
        } else if (bookSortState[field] === 'asc') {
            bookSortState[field] = 'desc';
        } else {
            bookSortState[field] = null;
        }

        updateBookSortIcons();
        applyBookSorting();
    }

    function updateBookSortIcons() {
        Object.keys(bookSortState).forEach(field => {
            const icon = document.getElementById(`sort-icon-${field}`);
            if (!icon) return;

            icon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down', 'text-gray-400', 'text-blue-600', 'dark:text-blue-400');

            if (bookSortState[field] === 'asc') {
                icon.classList.add('fa-sort-up', 'text-blue-600');
            } else if (bookSortState[field] === 'desc') {
                icon.classList.add('fa-sort-down', 'text-blue-600');
            } else {
                icon.classList.add('fa-sort', 'text-gray-400');
            }
        });
    }

    function applyBookSorting() {
        const tbody = document.getElementById('books-tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('.book-row'));
        if (rows.length === 0) return;

        const activeField = Object.keys(bookSortState).find(key => bookSortState[key] !== null);

        if (!activeField) {
            // Default sort by STT asc
            rows.sort((a, b) => parseInt(a.dataset.stt) - parseInt(b.dataset.stt));
        } else {
            rows.sort((a, b) => {
                let valA, valB;

                if (activeField === 'stt') {
                    valA = parseInt(a.dataset.stt);
                    valB = parseInt(b.dataset.stt);
                } else if (activeField === 'title') {
                    valA = a.dataset.title;
                    valB = b.dataset.title;
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

                return bookSortState[activeField] === 'desc' ? -comparison : comparison;
            });
        }

        rows.forEach(row => tbody.appendChild(row));
    }
</script>