<table class="w-full text-left border-collapse">
    <thead
        class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
        <tr>
            <th class="px-4 py-3 w-12 text-center">#</th>
            <th class="px-4 py-3">Thành viên</th>
            <th class="px-4 py-3 w-56">Email</th>
            <th class="px-4 py-3 text-center w-28 whitespace-nowrap">Vai trò</th>
            <th class="px-4 py-3 text-center w-20 whitespace-nowrap">Bài viết</th>
            <th class="px-4 py-3 text-center w-20 whitespace-nowrap">Lượt thích</th>
            <th class="px-4 py-3 text-center w-20 whitespace-nowrap">Bình luận</th>
            <th class="px-4 py-3 text-center w-28 whitespace-nowrap">Ngày tham gia</th>
            <th class="px-4 py-3 text-center w-16"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
        @forelse($users as $index => $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 group transition">
                <td class="px-5 py-4 text-center text-gray-400 dark:text-slate-500 text-sm">
                    {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                            class="w-10 h-10 rounded-full border dark:border-slate-600 object-cover {{ !$user->is_active ? 'opacity-50 grayscale' : '' }}">
                        <div>
                            <span class="font-bold text-gray-800 dark:text-white {{ !$user->is_active ? 'line-through opacity-60' : '' }}">{{ $user->name }}</span>
                            @if(!$user->is_active)
                                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 rounded text-xs font-bold">
                                    <i class="fas fa-ban mr-1 text-[10px]"></i>Đã khóa
                                </span>
                            @endif
                            @if($user->violation_count >= 2)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 bg-orange-100 dark:bg-orange-900/50 text-orange-600 dark:text-orange-300 rounded text-[10px] font-bold" title="Vi phạm nhiều lần ({{ $user->violation_count }} lần)">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Vi phạm
                                </span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600 dark:text-slate-300 max-w-[220px] truncate"
                    title="{{ $user->email }}">{{ $user->email }}</td>
                <td class="px-4 py-4 text-center">
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-shield-alt mr-1"></i>Admin
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-user mr-1"></i>Thành viên
                        </span>
                    @endif
                </td>
                <td class="px-5 py-4 text-center">
                    <a href="{{ route('admin.recipes.index', ['user_id' => $user->id]) }}" class="inline-flex items-center justify-center px-2 py-0.5 bg-green-100 dark:bg-green-900/40 hover:bg-green-200 dark:hover:bg-green-900/60 text-green-600 dark:text-green-300 text-xs font-bold rounded-full min-w-[40px] transition" title="Xem bài viết của tài khoản này">
                        {{ $user->recipes_count }}
                    </a>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="inline-flex items-center justify-center px-2 py-0.5 bg-red-50 dark:bg-red-900/20 text-red-500 text-xs font-bold rounded-full min-w-[40px]">
                        {{ $user->likes_count }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="inline-flex items-center justify-center px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-500 text-xs font-bold rounded-full min-w-[40px]">
                        {{ $user->comments_count }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-slate-400 italic">
                    {{ $user->created_at->format('d/m/Y') }}
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        {{-- Nút Sửa --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 hover:text-white transition" title="Chỉnh sửa">
                            <i class="fas fa-pen text-xs"></i>
                        </a>

                        {{-- Nút cấp/hạ quyền Admin đã bị gỡ theo yêu cầu --}}

                        {{-- Nút khoá/mở khoá --}}
                        @if($user->role !== 'admin')
                            @if($user->is_active)
                                <button type="button" onclick="openBanModal('{{ route('admin.users.toggle-active', $user->id) }}', true, '{{ addslashes($user->name) }}')" class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 hover:bg-orange-500 hover:text-white transition" title="Khóa tài khoản">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            @else
                                <button type="button" onclick="openBanModal('{{ route('admin.users.toggle-active', $user->id) }}', false, '{{ addslashes($user->name) }}')" class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 hover:text-white transition" title="Mở khóa tài khoản">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            @endif
                            
                            {{-- Nút xóa vĩnh viễn --}}
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="confirm-submit" data-confirm="XÓA VĨNH VIỄN tài khoản {{ $user->name }} cùng toàn bộ công thức và dữ liệu liên quan? Không thể hoàn tác!" data-confirm-type="warning">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-600 hover:text-white transition" title="Xóa tài khoản">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        @else
                            <span class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-slate-600 text-gray-400 dark:text-slate-500" title="Admin">
                                <i class="fas fa-lock text-xs"></i>
                            </span>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">
                    <i class="fas fa-search text-4xl mb-3"></i>
                    <p>Không tìm thấy thành viên nào</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
