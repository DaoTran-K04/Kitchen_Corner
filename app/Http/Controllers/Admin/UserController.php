<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::withCount(['recipes', 'comments', 'likes']);

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->appends($request->query());

        // AJAX request - return JSON with rendered HTML
        if ($request->ajax() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($users->hasPages()) {
                $paginationHtml = '<div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700">'
                    . $users->links('vendor.pagination.admin')->toHtml()
                    . '</div>';
            }
            return response()->json([
                'table' => view('admin.users._table', compact('users'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle active status (vô hiệu hóa/kích hoạt tài khoản)
     */
    public function toggleActive(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể vô hiệu hóa Admin');
        }

        $user->is_active = !$user->is_active;
        if (!$user->is_active) {
            $request->validate(['ban_reason' => 'required|string|max:500']);
            $user->ban_reason = $request->ban_reason;
        } else {
            $user->ban_reason = null; // Clear reason when unbanned
        }

        $user->save();

        $action = $user->is_active ? 'Kích hoạt' : 'Khóa';

        // Ghi log
        AdminActivityLog::log(
            'update',
            "{$action} thành viên: {$user->name} ({$user->email})",
            User::class,
            $user->id,
            ['is_active' => !$user->is_active],
            ['is_active' => $user->is_active]
        );

        return back()->with('success', "Đã {$action} thành viên!");
    }

    /**
     * Hiển thị form sửa thành viên
     */
    public function edit(User $user)
    {
        $user->load(['challenges' => function($q) {
            $q->orderBy('user_challenges.completed_at', 'desc');
        }, 'badges', 'avatarFrames']);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin thành viên
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,user',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $request->validate($rules);

        $oldData = $user->only(['name', 'email', 'role']);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        AdminActivityLog::log(
            'update',
            "Cập nhật thành viên: {$user->name} ({$user->email})",
            User::class,
            $user->id,
            $oldData,
            $user->only(['name', 'email', 'role'])
        );

        return redirect()->route('admin.users.index')
            ->with('success', "Đã cập nhật thông tin thành viên \"{$user->name}\"!");
    }

    /**
     * Cấp/hạ quyền Admin
     */
    public function toggleRole(User $user)
    {
        // Không tự hạ quyền chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi quyền của chính mình!');
        }

        $oldRole = $user->role;
        $user->role = ($user->role === 'admin') ? 'user' : 'admin';
        $user->save();

        AdminActivityLog::log(
            'update',
            "Thay đổi quyền {$user->name}: {$oldRole} → {$user->role}",
            User::class,
            $user->id,
            ['role' => $oldRole],
            ['role' => $user->role]
        );

        $msg = $user->role === 'admin' ? "Đã cấp quyền Admin cho \"{$user->name}\"!" : "Đã hạ quyền \"{$user->name}\" về Thành viên!";
        return back()->with('success', $msg);
    }

    /**
     * Xóa vĩnh viễn thành viên
     */
    public function destroy(User $user)
    {
        // Không tự xóa chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự xóa tài khoản của chính mình!');
        }

        $name = $user->name;
        
        AdminActivityLog::log(
            'delete',
            "Xóa thành viên: {$name} ({$user->email})",
            User::class,
            $user->id,
            $user->toArray(),
            []
        );

        $user->forceDelete(); // Hoặc delete() nếu dùng SoftDeletes

        return back()->with('success', "Đã xóa vĩnh viễn tài khoản \"{$name}\"!");
    }

    /**
     * Thu hồi danh hiệu (Badge) từ người dùng
     */
    public function revokeBadge(User $user, $badgeId)
    {
        $user->badges()->detach($badgeId);
        
        AdminActivityLog::log(
            'update',
            "Thu hồi danh hiệu từ {$user->name}: ID Badge {$badgeId}",
            User::class,
            $user->id,
            ['badge_id' => $badgeId],
            []
        );

        return back()->with('success', 'Đã thu hồi danh hiệu thành công!');
    }

    /**
     * Khởi động lại thử thách cho người dùng
     */
    public function resetChallenge(User $user, $challengeId)
    {
        $user->challenges()->detach($challengeId);
        
        AdminActivityLog::log(
            'update',
            "Xóa tiến trình thử thách của {$user->name}: ID Challenge {$challengeId}",
            User::class,
            $user->id,
            ['challenge_id' => $challengeId],
            []
        );

        return back()->with('success', 'Đã đặt lại tiến trình thử thách thành công!');
    }
}
