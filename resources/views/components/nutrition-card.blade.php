@props(['calories', 'protein', 'carbs', 'fat'])

@php
    $totalMacros = $protein + $carbs + $fat;
    $proteinPercent = $totalMacros > 0 ? round(($protein / $totalMacros) * 100) : 0;
    $carbsPercent = $totalMacros > 0 ? round(($carbs / $totalMacros) * 100) : 0;
    $fatPercent = $totalMacros > 0 ? round(($fat / $totalMacros) * 100) : 0;
@endphp

<div class="bg-white rounded-3xl shadow-lg p-6 max-w-sm font-sans mx-auto border border-gray-100">
    <!-- Header -->
    <div class="flex items-center space-x-2 mb-6">
        <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.5 10a4.5 4.5 0 018.665-1.5H10V4.335A4.5 4.5 0 015.5 10zM10 14.5a4.5 4.5 0 01-4.335-3h8.67A4.5 4.5 0 0110 14.5z" clip-rule="evenodd" />
        </svg>
        <h2 class="text-xl font-bold text-gray-800">Thông tin dinh dưỡng</h2>
    </div>

    <!-- Calories -->
    <div class="text-center mb-6">
        <div class="text-5xl font-black text-orange-500 tracking-tight">{{ number_format($calories) }}</div>
        <div class="text-gray-400 text-sm mt-1">kcal / khẩu phần</div>
    </div>

    <!-- Macros Cards -->
    <div class="grid grid-cols-3 gap-3 mb-8">
        <!-- Protein -->
        <div class="bg-blue-50 rounded-2xl p-3 text-center flex flex-col justify-center">
            <div class="text-xl font-bold text-blue-700">{{ $protein }}g</div>
            <div class="text-xs text-gray-500 mt-1">Protein</div>
        </div>
        <!-- Carbs -->
        <div class="bg-yellow-50 rounded-2xl p-3 text-center flex flex-col justify-center">
            <div class="text-xl font-bold text-yellow-700">{{ $carbs }}g</div>
            <div class="text-xs text-gray-500 mt-1">Carbs</div>
        </div>
        <!-- Fat -->
        <div class="bg-red-50 rounded-2xl p-3 text-center flex flex-col justify-center">
            <div class="text-xl font-bold text-red-600">{{ $fat }}g</div>
            <div class="text-xs text-gray-500 mt-1">Chất béo</div>
        </div>
    </div>

    <!-- Progress Bars -->
    <div class="space-y-4">
        <!-- Protein Bar -->
        <div>
            <div class="flex justify-between text-sm mb-1 text-gray-600">
                <span>Protein</span>
                <span>{{ $proteinPercent }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $proteinPercent }}%"></div>
            </div>
        </div>

        <!-- Carbs Bar -->
        <div>
            <div class="flex justify-between text-sm mb-1 text-gray-600">
                <span>Carbs</span>
                <span>{{ $carbsPercent }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $carbsPercent }}%"></div>
            </div>
        </div>

        <!-- Fat Bar -->
        <div>
            <div class="flex justify-between text-sm mb-1 text-gray-600">
                <span>Chất béo</span>
                <span>{{ $fatPercent }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-red-400 h-2 rounded-full" style="width: {{ $fatPercent }}%"></div>
            </div>
        </div>
    </div>
</div>
