@extends('layouts.admin')
@section('title', 'Chỉnh sửa thành viên: ' . $user->name)
@section('header', 'Chỉnh sửa thành viên')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:underline text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex items-center gap-3">
            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=40' }}"
                 class="w-10 h-10 rounded-full object-cover" alt="{{ $user->name }}">
            <div>
                <h3 class="font-bold text-gray-800 dark:text-white">{{ $user->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $user->email }}</p>
            </div>
            <span class="ml-auto px-2 py-1 rounded-full text-xs font-bold
                {{ $user->role === 'admin' ? 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300' : 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300' }}">
                {{ $user->role === 'admin' ? '🛡️ Admin' : '👤 Thành viên' }}
            </span>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <ul class="list-disc list-inside text-red-600 dark:text-red-300 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Họ tên <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-1">Vai trò</label>
                <select name="role"
                    class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="user"  {{ old('role', $user->role) === 'user'  ? 'selected' : '' }}>👤 Thành viên</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                </select>
            </div>

            <div class="border-t border-gray-100 dark:border-slate-700 pt-5">
                <p class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3">
                    Đổi mật khẩu <span class="text-gray-400 font-normal">(để trống nếu không muốn thay đổi)</span>
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Mật khẩu mới</label>
                        <input type="password" name="password"
                            class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-200 dark:border-slate-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-slate-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Nhập lại mật khẩu mới">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition text-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-2.5 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition text-sm">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
