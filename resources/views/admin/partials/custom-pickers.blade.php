{{--
Custom Picker Components for Admin Panel

Usage:
1. Year Picker (calendar-style dropdown):
@include('admin.partials.custom-pickers', ['type' => 'year', 'name' => 'published_year', 'value' =>
old('published_year'), 'placeholder' => 'Chọn năm', 'min' => 1900, 'max' => 2030])

2. Number Scroll Picker (alarm clock style):
@include('admin.partials.custom-pickers', ['type' => 'scroll', 'name' => 'order', 'value' => old('order', 0), 'min' =>
0, 'max' => 100])
--}}

@if($type === 'year')
    {{-- Year Picker - Hybrid: Input + Dropdown --}}
    <div class="year-picker-container relative" data-name="{{ $name }}">
        <input type="hidden" name="{{ $name }}" id="{{ $name }}-input" value="{{ $value ?? '' }}">
        
        <div class="flex items-center border dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all focus-within:ring-2 focus-within:ring-blue-500 overflow-hidden">
            {{-- Input để nhập tay --}}
            <input type="text" 
                   id="{{ $name }}-text-input"
                   class="year-text-input flex-1 px-4 py-2 bg-transparent text-gray-800 dark:text-white outline-none placeholder-gray-400 dark:placeholder-slate-500"
                   placeholder="{{ $placeholder ?? 'Nhập hoặc chọn năm...' }}"
                   value="{{ $value ?? '' }}"
                   maxlength="4"
                   pattern="[0-9]*"
                   inputmode="numeric"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4); document.getElementById('{{ $name }}-input').value = this.value;">
            
            {{-- Nút mở dropdown --}}
            <button type="button" id="{{ $name }}-trigger"
                class="year-picker-trigger px-3 py-2 text-gray-400 dark:text-slate-500 hover:text-blue-500 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-slate-600 transition border-l dark:border-slate-600">
                <i class="fas fa-calendar-alt"></i>
            </button>
        </div>

        <div
            class="year-picker-dropdown hidden absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 rounded-xl shadow-xl overflow-hidden">
            {{-- Navigation --}}
            <div
                class="flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-slate-700 border-b border-gray-200 dark:border-slate-600">
                <button type="button"
                    class="year-nav-btn p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                    data-direction="prev">
                    <i class="fas fa-chevron-left text-gray-600 dark:text-slate-300"></i>
                </button>
                <span class="year-range-display font-bold text-gray-700 dark:text-slate-200"></span>
                <button type="button"
                    class="year-nav-btn p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition"
                    data-direction="next">
                    <i class="fas fa-chevron-right text-gray-600 dark:text-slate-300"></i>
                </button>
            </div>

            {{-- Years Grid --}}
            <div class="year-grid grid grid-cols-4 gap-1 p-2 max-h-48 overflow-y-auto custom-scrollbar">
                {{-- Years will be populated by JS --}}
            </div>

            {{-- Clear Button --}}
            <div class="px-2 pb-2">
                <button type="button"
                    class="year-clear-btn w-full py-1.5 text-xs text-gray-500 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                    <i class="fas fa-times mr-1"></i> Xóa lựa chọn
                </button>
            </div>
        </div>
    </div>

@elseif($type === 'scroll')
    {{-- Number Scroll Picker --}}
    @php
        $currentVal = $value ?? $min ?? 0;
        $showAuto = isset($autoText) && $currentVal == 0;
    @endphp
    <div class="scroll-picker-container relative inline-flex" data-name="{{ $name }}" data-min="{{ $min ?? 0 }}"
        data-max="{{ $max ?? 100 }}" data-auto-text="{{ $autoText ?? '' }}">
        <input type="hidden" name="{{ $name }}" id="{{ $name }}-input" value="{{ $currentVal }}">

        <div
            class="flex items-center bg-white dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-slate-500 shadow-sm overflow-hidden">
            {{-- Decrease Button --}}
            <button type="button"
                class="scroll-btn scroll-decrease w-10 h-10 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-slate-600 text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white transition-all">
                <i class="fas fa-minus text-sm"></i>
            </button>

            {{-- Number Display --}}
            <div class="scroll-display-wrapper min-w-[48px] h-10 flex items-center justify-center select-none cursor-ns-resize border-x border-gray-200 dark:border-slate-600 px-2"
                title="Cuộn để thay đổi">
                <span
                    class="scroll-current-value text-sm font-bold text-gray-800 dark:text-white">{{ $showAuto ? $autoText : $currentVal }}</span>
            </div>

            {{-- Increase Button --}}
            <button type="button"
                class="scroll-btn scroll-increase w-10 h-10 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-slate-600 text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-white transition-all">
                <i class="fas fa-plus text-sm"></i>
            </button>
        </div>
    </div>

@elseif($type === 'rating')
    {{-- Rating Picker - Interactive Star Style (1.0 - 5.0) --}}
    @php
        $numericValue = $value ? (float) $value : 0;
        $currentVal = $value ? number_format($numericValue, 1) : '';
        $hasValue = !empty($currentVal);
    @endphp
    <div class="rating-picker-container" data-name="{{ $name }}" data-initial="{{ $currentVal }}">
        <input type="hidden" name="{{ $name }}" id="{{ $name }}-input" value="{{ $currentVal }}">

        {{-- Interactive Stars --}}
        <div class="rating-stars-interactive inline-flex" style="gap: 4px;">
            @for($i = 1; $i <= 5; $i++)
                <div class="rating-star-wrapper relative cursor-pointer" data-star="{{ $i }}"
                    style="width: 32px; height: 32px;">
                    {{-- Background star (empty) --}}
                    <i class="fas fa-star text-3xl text-gray-200 dark:text-slate-600 absolute inset-0"></i>
                    {{-- Filled star (clipped based on value) --}}
                    <div class="rating-star-fill absolute inset-0 overflow-hidden"
                        style="width: {{ $hasValue && $numericValue >= $i ? '100' : ($hasValue && $numericValue > $i - 1 ? (($numericValue - ($i - 1)) * 100) : '0') }}%;">
                        <i class="fas fa-star text-3xl text-amber-400"></i>
                    </div>
                </div>
            @endfor
        </div>
    </div>
@endif