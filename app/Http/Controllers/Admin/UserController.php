<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::withCount('posts');

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
}
