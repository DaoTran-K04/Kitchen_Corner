@extends('layouts.admin')
@section('title', 'Chi tiết báo cáo bình luận')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.comment-reports.index') }}" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b dark:border-slate-700 flex justify-between items-center bg-gray-50 dark:bg-slate-700">
            <h2 class="text-xl font-bold dark:text-white">Báo cáo #{{ $commentReport->id }}</h2>
            <span class="px-3 py-1 rounded-full text-sm font-bold 
                @if($commentReport->status == 'pending') bg-yellow-100 text-yellow-700 
                @elseif($commentReport->status == 'approved') bg-green-100 text-green-700 
                @else bg-red-100 text-red-700 @endif">
                {{ $commentReport->status == 'pending' ? 'Chờ xử lý' : ($commentReport->status == 'approved' ? 'Đã chấp thuận' : 'Đã từ chối') }}
            </span>
        </div>

        <div class="p-6 space-y-8">
            {{-- Thông tin bình luận --}}
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-l-4 border-blue-500 pl-2">Nội dung bị báo cáo</h3>
                <div class="bg-red-50 dark:bg-red-900/10 p-4 rounded-lg border border-red-100 dark:border-red-800">
                    @if($commentReport->comment)
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $commentReport->comment->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($commentReport->comment->user->name) }}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white">{{ $commentReport->comment->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $commentReport->comment->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-slate-300 italic">"{{ $commentReport->comment->content }}"</p>
                        @if($commentReport->comment->recipe)
                        <p class="mt-3 text-sm text-gray-500">
                            Tại công thức: <a href="{{ route('recipes.show', $commentReport->comment->recipe->slug) }}" target="_blank" class="text-blue-600 hover:underline font-bold">{{ $commentReport->comment->recipe->title }}</a>
                        </p>
                        @endif
                    @else
                        <p class="text-gray-500 italic">Bình luận này đã bị xóa khỏi hệ thống.</p>
                    @endif
                </div>
            </div>

            {{-- Thông tin người báo cáo --}}
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-l-4 border-orange-500 pl-2">Người báo cáo</h3>
                    <div class="flex items-center gap-3">
                        <img src="{{ $commentReport->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($commentReport->user->name) }}" class="w-12 h-12 rounded-full border">
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white">{{ $commentReport->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $commentReport->user->email }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-l-4 border-orange-500 pl-2">Chi tiết báo cáo</h3>
                    <p class="text-sm mb-1"><span class="font-bold">Lý do:</span> <span class="text-orange-600">{{ $commentReport->reason_label }}</span></p>
                    <p class="text-sm"><span class="font-bold">Mô tả:</span> {{ $commentReport->description ?? 'Không có mô tả chi tiết' }}</p>
                    <p class="text-xs text-gray-400 mt-2">Gửi lúc: {{ $commentReport->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Kết quả xử lý --}}
            @if($commentReport->status != 'pending')
            <div class="bg-gray-50 dark:bg-slate-700 p-6 rounded-xl border dark:border-slate-600">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-l-4 border-green-500 pl-2">Kết quả xử lý</h3>
                <div class="space-y-3">
                    <p class="text-sm"><span class="font-bold">Người xử lý:</span> {{ $commentReport->resolvedBy->name }}</p>
                    <p class="text-sm"><span class="font-bold">Thời gian:</span> {{ $commentReport->resolved_at->format('d/m/Y H:i') }}</p>
                    <div>
                        <p class="text-sm font-bold mb-1">Ghi chú quản trị:</p>
                        <p class="text-sm text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-800 p-3 rounded border dark:border-slate-600">{{ $commentReport->admin_note ?? 'Không có ghi chú' }}</p>
                    </div>
                </div>
            </div>
            @else
            {{-- Form xử lý --}}
            <div class="pt-6 border-t dark:border-slate-700">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-l-4 border-blue-500 pl-2">Thực hiện xử lý</h3>
                <div class="flex gap-4">
                    <button type="button" onclick="openApproveModal({{ $commentReport->id }}, '{{ addslashes($commentReport->comment->user->name ?? 'Người dùng') }}')"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                        Chấp thuận (Xóa bình luận)
                    </button>
                    <button type="button" onclick="openRejectModal({{ $commentReport->id }})"
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg font-bold hover:bg-gray-600 transition">
                        Từ chối báo cáo
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Tận dụng lại các modal từ trang index --}}
@include('admin.comment-reports.modals') {{-- Giả sử mình tách modal ra --}}
@endsection
