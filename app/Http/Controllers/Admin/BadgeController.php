<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BadgeController extends Controller
{
    /**
     * Hiển thị danh sách badges
     */
    public function index()
    {
        return redirect()->route('admin.game.index', ['tab' => 'badges']);
    }

    /**
     * Xem chi tiết badge - redirect về trang chính
     */
    public function show(Badge $badge)
    {
        return redirect()->route('admin.game.index', ['tab' => 'badges']);
    }

    /**
     * Form tạo badge mới
     */
    public function create()
    {
        return redirect()->route('admin.game.index', ['tab' => 'badges']);
    }

    /**
     * Lưu badge mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('badges', 'name')->whereNull('deleted_at')],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:500',
            'icon_file' => 'nullable|image|mimes:gif,png,jpg,jpeg,webp,svg|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên biểu tượng.',
            'name.unique' => 'Biểu tượng này đã tồn tại. Vui lòng chọn tên khác.',
            'name.max' => 'Tên biểu tượng không được quá 255 ký tự.',
        ]);

        // Xử lý icon: ưu tiên file upload > URL/emoji
        if ($request->hasFile('icon_file')) {
            $validated['icon'] = $request->file('icon_file')->store('badge-icons', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = $request->has('is_active');

        $badge = Badge::create($validated);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Tạo biểu tượng mới: ' . $badge->name,
            'model_type' => Badge::class,
            'model_id' => $badge->id,
            'new_values' => $badge->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo biểu tượng thành công!',
                'badge' => $badge
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'badges'])
            ->with('success', 'Tạo biểu tượng thành công!');
    }

    /**
     * Form chỉnh sửa badge
     */
    public function edit(Request $request, Badge $badge)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'badge' => $badge
            ]);
        }
        return view('admin.game.badges.edit', compact('badge'));
    }

    /**
     * Cập nhật badge
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('badges', 'name')->ignore($badge->id)->whereNull('deleted_at')],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:500',
            'icon_file' => 'nullable|image|mimes:gif,png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $oldValues = $badge->toArray();

        // Xử lý icon: ưu tiên file upload > URL/emoji
        if ($request->hasFile('icon_file')) {
            // Xóa icon cũ nếu là file local
            if ($badge->icon && Str::startsWith($badge->icon, 'badge-icons/')) {
                Storage::delete('public/' . $badge->icon);
            }
            $validated['icon'] = $request->file('icon_file')->store('badge-icons', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $badge->update($validated);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Cập nhật biểu tượng: ' . $badge->name,
            'model_type' => Badge::class,
            'model_id' => $badge->id,
            'old_values' => $oldValues,
            'new_values' => $badge->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật biểu tượng thành công!',
                'badge' => $badge
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'badges'])
            ->with('success', 'Cập nhật biểu tượng thành công!');
    }

    /**
     * Xóa badge
     */
    public function destroy(Request $request, Badge $badge)
    {
        $badgeName = $badge->name;
        $oldValues = $badge->toArray();

        $badge->delete();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Xóa biểu tượng: ' . $badgeName,
            'model_type' => Badge::class,
            'model_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa biểu tượng thành công!'
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'badges'])
            ->with('success', 'Xóa biểu tượng thành công!');
    }
}
