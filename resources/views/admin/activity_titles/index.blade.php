@extends('layouts.admin')
@section('title', 'Quản Lý Danh Hiệu Hoạt Động')
@section('header', 'Quản Lý Danh Hiệu Hoạt Động')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-900/50">
            <h3 class="font-bold text-gray-700 dark:text-slate-200">Danh sách Tiêu Đề Hoạt Động</h3>
            <a href="{{ route('admin.activity-titles.create') }}" class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Thêm Mới
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-slate-900/50 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 font-bold">Ưu tiên</th>
                        <th class="px-6 py-3 font-bold text-center">Icon</th>
                        <th class="px-6 py-3 font-bold">Tên Danh Hiệu</th>
                        <th class="px-6 py-3 font-bold">Yêu Cầu</th>
                        <th class="px-6 py-3 font-bold text-center">Trạng Thái</th>
                        <th class="px-6 py-3 font-bold text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($titles as $title)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-300 font-bold">
                                {{ $title->priority }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(Str::startsWith($title->icon, 'http') || Str::startsWith($title->icon, '/'))
                                    <img src="{{ Str::startsWith($title->icon, 'http') ? $title->icon : asset('storage/' . $title->icon) }}" class="w-8 h-8 object-contain mx-auto">
                                @else
                                    <span class="text-2xl">{{ $title->icon }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold block" style="color: {{ $title->color }}">{{ $title->name }}</span>
                                <span class="text-xs text-gray-400">{{ $title->color }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-400">
                                <div><i class="fas fa-pen-nib mr-1 text-gray-400"></i> Bài viết: <b>{{ $title->min_posts }}</b></div>
                                <div><i class="fas fa-book mr-1 text-gray-400"></i> Sách: <b>{{ $title->min_books }}</b></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($title->is_active)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-bold rounded-full">Hoạt động</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-xs font-bold rounded-full">Ẩn</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.activity-titles.edit', $title->id) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/50 transition" title="Sửa">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.activity-titles.destroy', $title->id) }}" method="POST"
                                        class="confirm-submit"
                                        data-confirm="Bạn có chắc muốn xóa danh hiệu này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 transition" title="Xóa">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                                Chưa có danh hiệu nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($titles->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                {{ $titles->links() }}
            </div>
        @endif
    </div>
@endsection
