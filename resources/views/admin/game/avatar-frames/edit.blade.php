@extends('layouts.admin')
@section('title', 'Chỉnh Sửa Khung Avatar')
@section('header', 'Chỉnh Sửa Khung Avatar')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <form action="{{ route('admin.avatar-frames.update', $frame) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    {{-- Tên --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Tên khung <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="name-count">{{ strlen($frame->name) }}</span>/50)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $frame->name) }}" maxlength="50"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                            required
                            oninput="document.getElementById('name-count').textContent = this.value.length">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            Mô tả
                            <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                    id="desc-count">{{ strlen($frame->description ?? '') }}</span>/150)</span>
                        </label>
                        <textarea name="description" rows="5" maxlength="150"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white resize-y min-h-[120px]"
                            placeholder="Mô tả khung avatar..."
                            oninput="document.getElementById('desc-count').textContent = this.value.length">{{ old('description', $frame->description) }}</textarea>
                    </div>

                    {{-- Hình ảnh hiện tại --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Hình ảnh hiện
                            tại</label>
                        <div
                            class="mb-3 rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 w-32 h-32 bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                            <img loading="lazy" src="{{ (Str::startsWith($frame->frame_image, 'http') || Str::startsWith($frame->frame_image, 'data:')) ? $frame->frame_image : asset('storage/' . $frame->frame_image) }}"
                                class="max-h-full max-w-full object-contain">
                        </div>
                    </div>

                    {{-- Tải ảnh mới --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-upload mr-1"></i> Tải ảnh mới từ máy
                        </label>
                        <input type="file" name="frame_image" accept=".gif,.png,.jpg,.jpeg,.webp,.svg"
                            class="w-full text-sm text-gray-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100">
                        <p class="text-xs text-gray-400 mt-1">Hỗ trợ: GIF, PNG, JPG, WebP</p>
                    </div>

                    {{-- URL ảnh --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                            <i class="fas fa-link mr-1"></i> Hoặc dán URL ảnh
                        </label>
                        <input type="url" name="frame_image_url"
                            class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white text-sm"
                            placeholder="https://example.com/frame.gif">
                    </div>

                    {{-- Thứ tự --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Thứ tự</label>
                            @include('admin.partials.custom-pickers', [
                                'type' => 'scroll',
                                'name' => 'order',
                                'value' => old('order', $frame->order),
                                'min' => 0,
                                'max' => 99,
                                'autoText' => 'Auto'
                            ])
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" {{ $frame->is_active ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500 mr-2">
                                <span class="text-sm text-gray-700 dark:text-slate-300">Kích hoạt</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('admin.game.index', ['tab' => 'frames']) }}"
                        class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                        Hủy
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
