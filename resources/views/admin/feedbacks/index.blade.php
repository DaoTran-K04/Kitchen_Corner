@extends('layouts.admin')
@section('title', 'Quản Lý Góp Ý')
@section('header', 'Quản Lý Góp Ý')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex justify-between items-center">
        <span class="font-bold text-gray-700 dark:text-slate-200 flex items-center gap-2">
            <i class="fas fa-envelope-open-text text-orange-500"></i> Danh sách góp ý
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-600 border-b border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800 border-b dark:border-slate-700">
                <tr>
                    <th class="px-5 py-3 w-16 text-center">ID</th>
                    <th class="px-5 py-3">Người gửi</th>
                    <th class="px-5 py-3">Tiêu đề</th>
                    <th class="px-5 py-3 text-center">Ngày gửi</th>
                    <th class="px-5 py-3 text-center">Trạng thái</th>
                    <th class="px-5 py-3 text-center w-32">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($feedbacks as $fb)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition {{ $fb->status == 'pending' ? 'bg-orange-50/30' : '' }}">
                        <td class="px-5 py-4 text-center text-gray-500 text-sm">#{{ $fb->id }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-800 dark:text-white">{{ $fb->name }}</div>
                            <div class="text-xs text-gray-500">{{ $fb->email }}</div>
                        </td>
                        <td class="px-5 py-4 text-gray-700 dark:text-slate-300">
                            {{ Str::limit($fb->subject, 50) }}
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-500">
                            {{ $fb->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($fb->status == 'pending')
                                <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-lg font-medium">Chờ xử lý</span>
                            @elseif($fb->status == 'read')
                                <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-lg font-medium">Đã xem</span>
                            @else
                                <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-lg font-medium">Đã giải quyết</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.feedbacks.show', $fb->id) }}" class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition" title="Xem chi tiết">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <form action="{{ route('admin.feedbacks.destroy', $fb->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa góp ý này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Xóa">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Không có góp ý nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($feedbacks->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-slate-700">
            {{ $feedbacks->links() }}
        </div>
    @endif
</div>
@endsection
