@extends('layouts.admin')
@section('title', 'Quản Lý Nguyên Liệu')
@section('header', 'Quản Lý Nguyên Liệu')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
                <i class="fas fa-carrot text-orange-500"></i>Danh sách nguyên liệu
                <span class="text-sm font-normal text-gray-500 dark:text-slate-400">({{ $ingredients->total() }})</span>
            </span>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <form action="{{ route('admin.ingredients.index') }}" method="GET" class="relative w-full md:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm nguyên liệu..." 
                        class="w-full pl-10 pr-4 py-2 text-sm border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-800 text-gray-800 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </form>
                
                <a href="{{ route('admin.ingredients.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800 border-b dark:border-slate-700">
                    <tr>
                        <th class="px-5 py-3 w-12 text-center">#</th>
                        <th class="px-5 py-3">Tên</th>
                        <th class="px-5 py-3 text-center">Đơn vị</th>
                        <th class="px-5 py-3 text-center text-orange-500"><i class="fas fa-fire mr-1"></i>Calo</th>
                        <th class="px-5 py-3 text-center text-blue-500">Protein</th>
                        <th class="px-5 py-3 text-center text-yellow-600">Carbs</th>
                        <th class="px-5 py-3 text-center text-red-500">Fat</th>
                        <th class="px-5 py-3 text-center w-24">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($ingredients as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-5 py-3 text-center text-gray-400 dark:text-slate-500 text-sm">
                                {{ ($ingredients->currentPage() - 1) * $ingredients->perPage() + $index + 1 }}
                            </td>
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-white">
                                {{ $item->name }}
                            </td>
                            <td class="px-5 py-3 text-center text-gray-600 dark:text-slate-300">
                                {{ $item->unit }}
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-orange-500">
                                {{ $item->calories_per_unit }}
                            </td>
                            <td class="px-5 py-3 text-center text-blue-600 font-medium">
                                {{ $item->protein_per_unit }}g
                            </td>
                            <td class="px-5 py-3 text-center text-yellow-600 font-medium">
                                {{ $item->carbs_per_unit }}g
                            </td>
                            <td class="px-5 py-3 text-center text-red-500 font-medium">
                                {{ $item->fat_per_unit }}g
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.ingredients.edit', $item->id) }}" 
                                       class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition" title="Sửa">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.ingredients.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa nguyên liệu này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/40 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Xóa">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400 dark:text-slate-500">
                                <i class="fas fa-carrot text-4xl mb-3 opacity-30"></i>
                                <p>Không tìm thấy nguyên liệu nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ingredients->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                {{ $ingredients->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>
@endsection
