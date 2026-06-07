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
            <img loading="lazy" src="{{ $user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($user->name) . '&background=random&size=40' }}"
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
    
    <!-- Lịch sử thành tựu & thử thách -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden mt-6">
        <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700 flex items-center gap-3">
            <h3 class="font-bold text-gray-800 dark:text-white"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Lịch Sử Thành Tựu & Thử Thách</h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3 border-b pb-2">Danh Hiệu Đang Sở Hữu</h4>
                <div class="flex flex-wrap gap-2">
                    @forelse($user->badges as $badge)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200 group" title="{{ $badge->description }}">
                            <span class="mr-1">{{ $badge->icon }}</span> {{ $badge->name }}
                            <form action="{{ route('admin.users.revoke-badge', [$user, $badge]) }}" method="POST" class="ml-2" onsubmit="return confirm('Bạn có chắc muốn thu hồi danh hiệu này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-amber-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                        </span>
                    @empty
                        <span class="text-sm text-gray-500 italic">Thành viên chưa có danh hiệu nào.</span>
                    @endforelse
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-3 border-b pb-2">Lịch Sử Tham Gia Thử Thách</h4>
                @if($user->challenges->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Tên Thử Thách</th>
                                    <th class="px-4 py-3 font-semibold text-center">Tiến Độ</th>
                                    <th class="px-4 py-3 font-semibold text-center">Trạng Thái</th>
                                    <th class="px-4 py-3 font-semibold text-right">Ngày Hoàn Thành</th>
                                    <th class="px-4 py-3 font-semibold text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach($user->challenges as $challenge)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ $challenge->name }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded dark:bg-slate-600">{{ $challenge->pivot->current_count }} / {{ $challenge->target_count }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($challenge->pivot->is_completed)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Đã hoàn thành
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    Đang thực hiện
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-gray-500 text-xs">
                                            {{ $challenge->pivot->completed_at ? \Carbon\Carbon::parse($challenge->pivot->completed_at)->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('admin.users.reset-challenge', [$user, $challenge]) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tiến trình của thử thách này không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Xóa tiến trình">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Thành viên chưa tham gia hoặc chưa được ghi nhận thử thách nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
