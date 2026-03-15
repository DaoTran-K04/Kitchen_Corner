@extends('layouts.admin')
@section('title', 'Chỉnh Sửa Danh Hiệu')
@section('header', 'Chỉnh Sửa: ' . $activityTitle->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div
                class="p-6 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white">Thông Tin Danh Hiệu</h3>
            </div>

            <form action="{{ route('admin.activity-titles.update', $activityTitle->id) }}" method="POST"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Tên & Priority --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Tên Danh Hiệu <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $activityTitle->name) }}" required
                            class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Độ Ưu Tiên <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="priority" value="{{ old('priority', $activityTitle->priority) }}"
                            required min="0"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                </div>

                {{-- Icon & Color --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Biểu tượng
                            (Emoji/URL)</label>
                        <input type="text" name="icon" value="{{ old('icon', $activityTitle->icon) }}"
                            class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Màu sắc (Hex) <span
                                class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" value="{{ old('color', $activityTitle->color) }}"
                                class="h-10 w-14 rounded cursor-pointer border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 p-1">
                            <input type="text" name="color" value="{{ old('color', $activityTitle->color) }}" required
                                class="flex-1 px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500 uppercase">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-slate-700 pt-4">
                    <h4 class="font-bold text-gray-800 dark:text-white mb-4">Điều Kiện Đạt Được</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Số bài review tối
                                thiểu</label>
                            <input type="number" name="min_posts" value="{{ old('min_posts', $activityTitle->min_posts) }}"
                                required min="0"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Số sách đề xuất
                                tối thiểu</label>
                            <input type="number" name="min_books" value="{{ old('min_books', $activityTitle->min_books) }}"
                                required min="0"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $activityTitle->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-slate-300">Kích hoạt danh hiệu
                        này</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-slate-700 mt-2">
                    <a href="{{ route('admin.activity-titles.index') }}"
                        class="px-5 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 font-bold rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition">Hủy</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg shadow-green-500/30">
                        <i class="fas fa-save mr-2"></i>Cập Nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection