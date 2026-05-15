<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvatarFrame;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AvatarFrameController extends Controller
{
    /**
     * Redirect về trang game index
     */
    public function index()
    {
        return redirect()->route('admin.game.index', ['tab' => 'frames']);
    }

    /**
     * Lưu frame mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('avatar_frames', 'name')->whereNull('deleted_at')],
            'description' => 'nullable|string',
            'frame_image' => 'nullable|image|mimes:gif,png,jpg,jpeg,webp,svg|max:2048',
            'frame_image_url' => 'nullable|url',
        ], [
            'name.required' => 'Vui lòng nhập tên khung avatar.',
            'name.unique' => 'Khung avatar này đã tồn tại.',
        ]);

        // Xử lý hình ảnh
        $frameImage = null;
        if ($request->hasFile('frame_image')) {
            $frameImage = $request->file('frame_image')->store('avatar-frames', 'public');
        } elseif ($request->filled('frame_image_url')) {
            $frameImage = $request->input('frame_image_url');
        }

        if (!$frameImage) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['frame_image_url' => ['Vui lòng nhập URL hình ảnh!']]], 422);
            }
            return back()->withInput()->with('error', 'Vui lòng tải ảnh hoặc nhập URL hình ảnh!');
        }

        $frame = AvatarFrame::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'description' => $validated['description'] ?? null,
            'frame_image' => $frameImage,
            'is_active' => $request->has('is_active'),
            'order' => $request->input('order', 0),
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Tạo khung avatar: ' . $frame->name,
            'model_type' => AvatarFrame::class,
            'model_id' => $frame->id,
            'new_values' => $frame->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo khung avatar thành công!',
                'frame' => $frame
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'frames'])
            ->with('success', 'Tạo khung avatar thành công!');
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(Request $request, AvatarFrame $avatarFrame)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'frame' => $avatarFrame
            ]);
        }
        return view('admin.game.avatar-frames.edit', ['frame' => $avatarFrame]);
    }

    /**
     * Cập nhật frame
     */
    public function update(Request $request, AvatarFrame $avatarFrame)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('avatar_frames', 'name')->ignore($avatarFrame->id)->whereNull('deleted_at')],
            'description' => 'nullable|string',
            'frame_image' => 'nullable|image|mimes:gif,png,jpg,jpeg,webp,svg|max:2048',
            'frame_image_url' => 'nullable|url',
        ]);

        $oldValues = $avatarFrame->toArray();

        // Xử lý hình ảnh mới
        if ($request->hasFile('frame_image')) {
            // Xóa ảnh cũ nếu là file local
            if ($avatarFrame->frame_image && !Str::startsWith($avatarFrame->frame_image, 'http')) {
                Storage::delete('public/' . $avatarFrame->frame_image);
            }
            $validated['frame_image'] = $request->file('frame_image')->store('avatar-frames', 'public');
        } elseif ($request->filled('frame_image_url')) {
            // Xóa ảnh cũ nếu là file local
            if ($avatarFrame->frame_image && !Str::startsWith($avatarFrame->frame_image, 'http')) {
                Storage::delete('public/' . $avatarFrame->frame_image);
            }
            $validated['frame_image'] = $request->input('frame_image_url');
        } else {
            unset($validated['frame_image']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $request->input('order', $avatarFrame->order);

        $avatarFrame->update($validated);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Cập nhật khung avatar: ' . $avatarFrame->name,
            'model_type' => AvatarFrame::class,
            'model_id' => $avatarFrame->id,
            'old_values' => $oldValues,
            'new_values' => $avatarFrame->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khung avatar thành công!',
                'frame' => $avatarFrame
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'frames'])
            ->with('success', 'Cập nhật khung avatar thành công!');
    }

    /**
     * Xóa frame
     */
    public function destroy(Request $request, AvatarFrame $avatarFrame)
    {
        $frameName = $avatarFrame->name;
        $oldValues = $avatarFrame->toArray();

        // Xóa file ảnh nếu là local
        if ($avatarFrame->frame_image && !Str::startsWith($avatarFrame->frame_image, 'http')) {
            Storage::delete('public/' . $avatarFrame->frame_image);
        }

        $avatarFrame->delete();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Xóa khung avatar: ' . $frameName,
            'model_type' => AvatarFrame::class,
            'model_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa khung avatar thành công!'
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'frames'])
            ->with('success', 'Xóa khung avatar thành công!');
    }
}
