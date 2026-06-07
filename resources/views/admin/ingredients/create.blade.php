@extends('layouts.admin')
@section('title', 'Thêm Nguyên Liệu')
@section('header', 'Thêm Nguyên Liệu')

@section('content')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-lg text-gray-800 dark:text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-500"></i> Thêm mới nguyên liệu
            </h3>
            <a href="{{ route('admin.ingredients.index') }}" class="text-gray-500 hover:text-blue-500 transition text-sm flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.ingredients.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Thông tin cơ bản -->
                <div class="col-span-1 md:col-span-2">
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 border-b pb-2 mb-4">Thông tin cơ bản</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Tên nguyên liệu <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                placeholder="VD: Thịt bò, Trứng gà...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1">Đơn vị (để quy chuẩn) <span class="text-red-500">*</span></label>
                            <input type="text" name="unit" value="{{ old('unit', '100g') }}" required
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white"
                                placeholder="VD: 100g, 1 quả, 1 muỗng...">
                        </div>
                    </div>
                </div>

                <!-- Số liệu dinh dưỡng -->
                <div class="col-span-1 md:col-span-2">
                    <h4 class="font-semibold text-gray-700 dark:text-slate-300 border-b pb-2 mb-4 mt-2">Giá trị dinh dưỡng (trên 1 đơn vị)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1 text-orange-500">Calo (kcal)</label>
                            <input type="number" name="calories_per_unit" value="{{ old('calories_per_unit', 0) }}" min="0"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1 text-blue-500">Protein (g)</label>
                            <input type="number" name="protein_per_unit" value="{{ old('protein_per_unit', 0) }}" min="0"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1 text-yellow-600">Carbs (g)</label>
                            <input type="number" name="carbs_per_unit" value="{{ old('carbs_per_unit', 0) }}" min="0"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-1 text-red-500">Fat (g)</label>
                            <input type="number" name="fat_per_unit" value="{{ old('fat_per_unit', 0) }}" min="0"
                                class="w-full px-4 py-2 border dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-slate-700 flex justify-end gap-3">
                <a href="{{ route('admin.ingredients.index') }}" class="px-5 py-2 text-gray-600 hover:text-gray-900 font-medium bg-gray-100 hover:bg-gray-200 rounded-lg transition">Hủy</a>
                <button type="submit" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Lưu thông tin
                </button>
            </div>
        </form>
    </div>
@endsection
