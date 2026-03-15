@extends('layouts.admin')
@section('title', 'Quản Lý Danh Mục')
@section('header', 'Quản Lý Danh Mục')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form thêm mới --}}
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-5 sticky top-6">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-500"></i>Thêm danh mục
                </h3>
                <form id="add-category-form">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Tên danh mục <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" id="category-name" required
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                placeholder="VD: Trinh thám">
                            <p id="name-error" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Mô tả</label>
                            <textarea name="description" id="category-desc" rows="3"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-none placeholder:italic"
                                placeholder="Mô tả ngắn (không bắt buộc)..."></textarea>
                        </div>
                        <button type="submit" id="submit-btn"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-lg font-medium transition flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i>Tạo mới
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danh sách --}}
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div
                    class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-between items-center">
                    <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                        <i class="fas fa-list text-blue-500"></i>Tất cả danh mục
                        <span id="total-count"
                            class="text-sm font-normal text-gray-500 dark:text-slate-400">({{ $categories->total() }})</span>
                    </span>
                </div>

                <div id="categories-table-container">
                    <table class="w-full text-left">
                        <thead
                            class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800 border-b dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-3 w-12 text-center">#</th>
                                <th class="px-5 py-3">Tên</th>
                                <th class="px-5 py-3">Mô tả</th>
                                <th class="px-5 py-3">Slug</th>
                                <th class="px-5 py-3 text-center w-24">Số sách</th>
                                <th class="px-5 py-3 text-center w-20"></th>
                            </tr>
                        </thead>
                        <tbody id="categories-tbody" class="divide-y divide-gray-100 dark:divide-slate-700">
                            @foreach($categories as $index => $cat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition category-row"
                                    data-id="{{ $cat->id }}">
                                    <td class="px-5 py-3 text-center text-gray-400 dark:text-slate-500 text-sm">
                                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $cat->name }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-slate-400 text-sm max-w-[200px]">
                                        @if($cat->description)
                                            <span class="truncate block"
                                                title="{{ $cat->description }}">{{ Str::limit($cat->description, 50) }}</span>
                                        @else
                                            <span class="text-gray-300 dark:text-slate-600 italic">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-slate-400 text-sm font-mono italic">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span
                                            class="inline-flex items-center justify-center px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 text-xs font-bold rounded-full min-w-[40px]">
                                            {{ $cat->books_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <button type="button" onclick="deleteCategory({{ $cat->id }}, '{{ $cat->name }}')"
                                            class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white transition opacity-0 group-hover:opacity-100"
                                            title="Xóa">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($categories->isEmpty())
                        <div class="p-8 text-center text-gray-400 dark:text-slate-500">
                            <i class="fas fa-folder-open text-4xl mb-3"></i>
                            <p>Chưa có danh mục nào</p>
                        </div>
                    @endif
                </div>

                @if($categories->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                        {{ $categories->links('vendor.pagination.admin') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Track tổng số danh mục từ server
        let totalCategories = {{ $categories->total() }};

        // AJAX Add Category
        document.getElementById('add-category-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            const nameInput = document.getElementById('category-name');
            const descInput = document.getElementById('category-desc');
            const errorEl = document.getElementById('name-error');

            // Reset
            errorEl.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';

            try {
                const response = await fetch('{{ route("admin.categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: nameInput.value,
                        description: descInput.value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors?.name) {
                        errorEl.textContent = data.errors.name[0];
                        errorEl.classList.remove('hidden');
                    } else {
                        throw new Error(data.message || 'Có lỗi xảy ra');
                    }
                } else {
                    // Success - reload to show new category
                    showToast('Thêm danh mục thành công!', 'success');
                    nameInput.value = '';
                    descInput.value = '';
                    setTimeout(() => location.reload(), 500);
                }
            } catch (err) {
                showToast(err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i> Tạo mới';
            }
        });

        // AJAX Delete Category
        async function deleteCategory(id, name) {
            if (!confirm(`Bạn có chắc muốn xóa danh mục "${name}"?`)) return;

            const row = document.querySelector(`tr[data-id="${id}"]`);
            row.style.opacity = '0.5';

            try {
                const response = await fetch(`/admin/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.transform = 'translateX(20px)';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        updateCount(-1);
                    }, 300);
                    showToast('Đã xóa danh mục!', 'success');
                } else {
                    throw new Error('Không thể xóa');
                }
            } catch (err) {
                row.style.opacity = '1';
                showToast(err.message, 'error');
            }
        }

        function updateCount(delta = 0) {
            totalCategories += delta;
            document.getElementById('total-count').textContent = `(${totalCategories})`;
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg text-white font-medium shadow-lg z-50 transition-all duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }
    </script>
@endsection