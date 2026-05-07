<table class="w-full text-left border-collapse">
    <thead
        class="bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-slate-400 text-xs uppercase border-b dark:border-slate-700">
        <tr>
            <th class="px-4 py-3 w-12 text-center">#</th>
            <th class="px-4 py-3">Thành viên</th>
            <th class="px-4 py-3 w-56">Email</th>
            <th class="px-4 py-3 text-center w-28 whitespace-nowrap">Vai trò</th>
            <th class="px-4 py-3 text-center w-20 whitespace-nowrap">Bài viết</th>
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
                            <span
                                class="font-bold text-gray-800 dark:text-white {{ !$user->is_active ? 'line-through opacity-60' : '' }}">{{ $user->name }}</span>
                            @if(!$user->is_active)
                                <span
                                    class="ml-2 inline-flex items-center px-1.5 py-0.5 bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-300 rounded text-xs font-bold">
                                    <i class="fas fa-ban mr-1 text-[10px]"></i>Đã khóa
                                </span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600 dark:text-slate-300 max-w-[220px] truncate"
                    title="{{ $user->email }}">{{ $user->email }}</td>
                <td class="px-4 py-4 text-center">
                    @if($user->role === 'admin')
                        <span
                            class="inline-flex items-center px-2.5 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-shield-alt mr-1"></i>Admin
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 rounded-full text-xs font-bold whitespace-nowrap">
                            <i class="fas fa-user mr-1"></i>Thành viên
                        </span>
                    @endif
                </td>
                <td class="px-5 py-4 text-center">
                    <span
                        class="inline-flex items-center justify-center px-2 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 text-xs font-bold rounded-full min-w-[40px]">
                        {{ $user->recipes_count }}
                    </span>
                </td>
                <td class="px-5 py-4 text-center text-sm text-gray-500 dark:text-slate-400 italic">
                    {{ $user->created_at->format('d/m/Y') }}
                </td>
                <td class="px-5 py-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        {{-- Nút Sửa --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 hover:bg-blue-500 hover:text-white transition"
                            title="Chỉnh sửa">
                            <i class="fas fa-pen text-xs"></i>
                        </a>

                        {{-- Nút cấp/hạ quyền Admin --}}
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST"
                            class="confirm-submit"
                            data-confirm="{{ $user->role === 'admin' ? 'Hạ quyền' : 'Cấp quyền Admin' }} cho {{ $user->name }}?"
                            data-confirm-type="question">
                            @csrf
                            <button type="submit"
                                class="w-8 h-8 flex items-center justify-center rounded-full {{ $user->role === 'admin' ? 'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-300 hover:bg-red-500 hover:text-white' : 'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 hover:bg-purple-500 hover:text-white' }} transition"
                                title="{{ $user->role === 'admin' ? 'Hạ về Thành viên' : 'Cấp quyền Admin' }}">
                                <i class="fas fa-shield-alt text-xs"></i>
                            </button>
                        </form>
                        @endif

                        {{-- Nút khoá/mở khoá --}}
                        @if($user->role !== 'admin')
                        <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST"
                            class="confirm-submit"
                            data-confirm="{{ $user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }} tài khoản {{ $user->name }}?"
                            data-confirm-type="{{ $user->is_active ? 'warning' : 'info' }}">
                            @csrf
                            @if($user->is_active)
                                <button class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-300 hover:bg-orange-500 hover:text-white transition opacity-0 group-hover:opacity-100" title="Vô hiệu hóa">
                                    <i class="fas fa-ban text-xs"></i>
                                </button>
                            @else
                                <button class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-300 hover:bg-green-500 hover:text-white transition" title="Kích hoạt lại">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            @endif
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
