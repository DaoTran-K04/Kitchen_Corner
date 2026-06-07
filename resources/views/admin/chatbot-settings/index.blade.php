@extends('layouts.admin')
@section('title', 'Cấu Hình Trợ Lý AI Chatbot')
@section('header', 'Cấu Hình Trợ Lý AI Chatbot')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 transition-colors duration-300">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Huấn luyện Chatbot (System Prompt)</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400">
            Cấu hình bộ não, luật lệ và tính cách cho trợ lý AI Góc Bếp. Hệ thống sử dụng mô hình Google Gemini.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.chatbot-settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- System Prompt --}}
        <div>
            <label for="system_prompt" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                Bộ Quy Tắc Huấn Luyện (System Instructions)
            </label>
            <textarea id="system_prompt" name="system_prompt" rows="18" required
                class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-slate-600 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 dark:bg-slate-700 dark:text-white font-mono text-sm leading-relaxed transition-colors">{{ old('system_prompt', $systemPrompt) }}</textarea>
            @error('system_prompt')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">Gợi ý: Trình bày rõ ràng các vùng cấm, giới hạn kiến thức và phong cách giao tiếp.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Temperature --}}
            <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                    Sự Sáng Tạo (Temperature: 0.0 - 1.0)
                </label>
                <input type="number" step="0.1" min="0" max="1" id="temperature" name="temperature" value="{{ old('temperature', $temperature) }}" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-slate-600 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 dark:bg-slate-700 dark:text-white transition-colors">
                @error('temperature')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">0.0 là cứng nhắc, 1.0 là rất sáng tạo.</p>
            </div>

            {{-- Max Tokens --}}
            <div>
                <label for="max_tokens" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                    Độ Dài Tối Đa (Max Output Tokens)
                </label>
                <input type="number" step="50" min="100" max="2048" id="max_tokens" name="max_tokens" value="{{ old('max_tokens', $maxTokens) }}" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 dark:border-slate-600 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 dark:bg-slate-700 dark:text-white transition-colors">
                @error('max_tokens')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Giới hạn số lượng từ AI có thể trả lời trong một tin nhắn (ví dụ: 600).</p>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-brand-green text-white rounded-lg font-medium hover:bg-emerald-700 focus:ring-4 focus:ring-brand-green/30 transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Lưu Cấu Hình Chatbot
            </button>
        </div>
    </form>
</div>
@endsection
