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
        $query = User::withCount('recipes');

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
    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể vô hiệu hóa Admin');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'Kích hoạt' : 'Vô hiệu hóa';

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
}

