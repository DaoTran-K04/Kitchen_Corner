@extends('layouts.admin')
@section('title', 'Sửa Châm Ngôn')
@section('header', 'Chỉnh Sửa Châm Ngôn')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-slate-700">
                <h2 class="font-bold text-gray-800 dark:text-white text-lg flex items-center gap-2">
                    <i class="fas fa-edit text-amber-500"></i>
                    Chỉnh Sửa Châm Ngôn
                </h2>
            </div>

            <form action="{{ route('admin.quotes.update', $quote->id) }}" method="POST" class="p-4 space-y-4">
                @csrf
                @method('PUT')

                {{-- Nội dung --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Nội dung <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" rows="5" required
                        class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white transition resize-y min-h-[120px] placeholder:italic"
                        placeholder="Nhập nội dung câu châm ngôn...">{{ old('content', $quote->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tác giả --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Tác giả <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="author" value="{{ old('author', $quote->author) }}" required
                        class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white transition placeholder:italic"
                        placeholder="Ví dụ: Albert Einstein">
                    @error('author')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nguồn (optional) --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Nguồn <span class="text-gray-400 font-normal">(Tùy chọn)</span>
                    </label>
                    <input type="text" name="source" value="{{ old('source', $quote->source) }}"
                        class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white transition placeholder:italic"
                        placeholder="Ví dụ: Tên công thức, bài viết...">
                </div>

                {{-- Thứ tự hiển thị --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                        Thứ tự hiển thị
                    </label>
                    @include('admin.partials.custom-pickers', [
                        'type' => 'scroll',
                        'name' => 'order',
                        'value' => old('order', $quote->order),
                        'min' => 0,
                        'max' => 99,
                        'autoText' => 'Auto'
                    ])
                </div>

                {{-- Trạng thái Hiển thị --}}
                <div>
                    <label
                        class="flex items-center justify-between p-3 border dark:border-slate-600 rounded-lg bg-gradient-to-r from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 cursor-pointer hover:shadow-md transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                                <i class="fas fa-eye text-green-600 dark:text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <span class="font-bold text-gray-800 dark:text-white block text-sm">Hiển thị châm
                                    ngôn</span>
                                <span class="text-xs text-gray-500 dark:text-slate-400">Chỉnh chế độ hiển thị trên trang
                                    chủ</span>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" {{ $quote->is_active ? 'checked' : '' }}
                                class="sr-only peer">
                            <div
                                class="w-12 h-6 bg-gray-300 dark:bg-slate-500 rounded-full peer peer-checked:bg-green-500 transition-colors duration-300">
                            </div>
                            <div
                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-6">
                            </div>
                        </div>
                    </label>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('admin.quotes.index') }}"
                        class="px-5 py-2.5 bg-gray-100 dark:bg-slate-600 text-gray-700 dark:text-slate-300 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-slate-500 transition">
                        Hủy
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
