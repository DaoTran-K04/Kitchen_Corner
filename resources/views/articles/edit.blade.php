@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Bài Viết')
@section('header', 'Sửa Bài Tạp Chí')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-4xl mx-auto">
    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Cột Trái --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề bài viết</label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nhãn (Tag)</label>
                    <input type="text" name="tag" value="{{ old('tag', $article->tag) }}" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="VD: Mẹo Đọc, Cảm Hứng">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả ngắn</label>
                    <textarea name="excerpt" rows="4" 
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('excerpt', $article->excerpt) }}</textarea>
                </div>
            </div>

            {{-- Cột Phải --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh bìa hiện tại</label>
                    @if($article->thumbnail)
                        <img src="{{ Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset('storage/' . $article->thumbnail) }}" 
                             class="w-full h-48 object-cover rounded-lg border border-gray-200 mb-2">
                    @endif
                    <input type="file" name="thumbnail" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <input type="checkbox" name="is_featured" id="is_featured" {{ $article->is_featured ? 'checked' : '' }} 
                           class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                    <label for="is_featured" class="font-medium text-gray-700 select-none cursor-pointer">
                        Đây là bài Tiêu Điểm (Hiện to nhất)
                    </label>
                </div>
            </div>
        </div>

        {{-- Nội dung chi tiết (Full width) --}}
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung chi tiết</label>
            <textarea name="content" id="editor" rows="10" 
                      class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('content', $article->content) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 font-bold transition">Hủy</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fas fa-save mr-2"></i> Lưu Thay Đổi
            </button>
        </div>
    </form>
</div>

{{-- Tích hợp CKEditor đơn giản (nếu cần) --}}
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endsection