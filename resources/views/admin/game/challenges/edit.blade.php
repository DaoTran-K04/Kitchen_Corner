@extends('layouts.admin')
@section('title', 'Sửa Thử Thách')
@section('header', 'Sửa Thử Thách')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div
            class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 bg-amber-500">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        <span>Chỉnh sửa: {{ $challenge->name }}</span>
                    </h3>
                    <a href="{{ route('admin.challenges.show', $challenge) }}"
                        class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.challenges.update', $challenge) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Cột trái --}}
                        <div class="space-y-4">
                            {{-- Tên --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Tên thử thách <span class="text-red-500">*</span>
                                    <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                            id="name-count">{{ strlen($challenge->name) }}</span>/50)</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $challenge->name) }}" maxlength="50"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic"
                                    placeholder="VD: Season Mùa Đông 2025" required
                                    oninput="document.getElementById('name-count').textContent = this.value.length">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Biểu tượng --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Biểu tượng nhận được <span class="text-red-500">*</span>
                                </label>
                                <select name="badge_id"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer"
                                    required>
                                    <option value="">-- Chọn biểu tượng --</option>
                                    @foreach($badges as $badge)
                                        <option value="{{ $badge->id }}" {{ old('badge_id', $challenge->badge_id) == $badge->id ? 'selected' : '' }}>
                                            {{ $badge->icon ?? '🏅' }} {{ $badge->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('badge_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Khung hoạt ảnh --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Khung hoạt ảnh (tùy chọn)
                                </label>
                                <select name="avatar_frame_id"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white cursor-pointer">
                                    <option value="">-- Không tặng khung --</option>
                                    @foreach($frames as $frame)
                                        <option value="{{ $frame->id }}" {{ old('avatar_frame_id', $challenge->avatar_frame_id) == $frame->id ? 'selected' : '' }}>
                                            {{ $frame->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Số bài review --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Số bài review cần viết <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="target_count" min="1" max="100"
                                    value="{{ old('target_count', $challenge->target_count) }}"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
                                @error('target_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Cột phải --}}
                        <div class="space-y-4">
                            {{-- Ngày bắt đầu --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Ngày bắt đầu <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date"
                                    value="{{ old('start_date', $challenge->start_date->format('Y-m-d')) }}"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Ngày kết thúc --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Ngày kết thúc <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date"
                                    value="{{ old('end_date', $challenge->end_date->format('Y-m-d')) }}"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                    required>
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Mô tả --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">
                                    Mô tả
                                    <span class="text-xs text-gray-400 font-normal ml-1">(<span
                                            id="desc-count">{{ strlen($challenge->description ?? '') }}</span>/150)</span>
                                </label>
                                <textarea name="description" rows="3" maxlength="150"
                                    class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white placeholder:italic resize-none"
                                    placeholder="Mô tả ngắn gọn về thử thách..."
                                    oninput="document.getElementById('desc-count').textContent = this.value.length">{{ old('description', $challenge->description) }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kích hoạt Toggle --}}
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700 dark:text-slate-300">
                                    Kích hoạt thử thách
                                </span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" {{ old('is_active', $challenge->is_active) ? 'checked' : '' }} class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-300 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 flex justify-end gap-3">
                    <a href="{{ route('admin.challenges.show', $challenge) }}"
                        class="px-6 py-2.5 rounded-lg text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 font-bold transition">
                        Hủy
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
