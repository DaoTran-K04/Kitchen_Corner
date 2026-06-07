@extends('layouts.admin')
@section('title', 'Chi tiết Góp ý')
@section('header', 'Chi tiết Góp ý')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.feedbacks.index') }}" class="text-sm font-medium text-gray-500 hover:text-orange-500 transition flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">{{ $feedback->subject }}</h2>
                
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-6 pb-6 border-b dark:border-slate-700">
                    <span class="flex items-center gap-1"><i class="fas fa-user text-gray-400"></i> {{ $feedback->name }}</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><i class="fas fa-envelope text-gray-400"></i> <a href="mailto:{{ $feedback->email }}" class="text-blue-500 hover:underline">{{ $feedback->email }}</a></span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><i class="far fa-clock text-gray-400"></i> {{ $feedback->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-slate-300 whitespace-pre-wrap">
                    {{ $feedback->message }}
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden sticky top-24">
            <div class="p-4 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">
                <span class="font-bold text-gray-700 dark:text-slate-200">Trạng thái xử lý</span>
            </div>
            
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-50 text-green-600 p-3 rounded-lg text-sm mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('admin.feedbacks.updateStatus', $feedback->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-4">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-slate-700 {{ $feedback->status == 'pending' ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-gray-200 dark:border-slate-600' }}">
                            <input type="radio" name="status" value="pending" class="text-orange-500 focus:ring-orange-500" {{ $feedback->status == 'pending' ? 'checked' : '' }}>
                            <span class="ml-3 font-medium text-gray-700 dark:text-slate-300">Chờ xử lý</span>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-slate-700 {{ $feedback->status == 'read' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-slate-600' }}">
                            <input type="radio" name="status" value="read" class="text-blue-500 focus:ring-blue-500" {{ $feedback->status == 'read' ? 'checked' : '' }}>
                            <span class="ml-3 font-medium text-gray-700 dark:text-slate-300">Đã xem</span>
                        </label>

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-slate-700 {{ $feedback->status == 'resolved' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-slate-600' }}">
                            <input type="radio" name="status" value="resolved" class="text-green-500 focus:ring-green-500" {{ $feedback->status == 'resolved' ? 'checked' : '' }}>
                            <span class="ml-3 font-medium text-gray-700 dark:text-slate-300">Đã giải quyết</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-[#9b2226] hover:bg-red-800 text-white py-2.5 rounded-lg font-medium transition">
                        Cập nhật trạng thái
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
