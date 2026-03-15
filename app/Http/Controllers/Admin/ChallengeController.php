<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Badge;
use App\Models\UserChallenge;
use App\Models\UserBadge;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChallengeController extends Controller
{
    /**
     * Hiển thị danh sách challenges
     */
    public function index()
    {
        return redirect()->route('admin.game.index', ['tab' => 'challenges']);
    }

    /**
     * Form tạo challenge mới
     */
    public function create()
    {
        return redirect()->route('admin.game.index', ['tab' => 'challenges']);
    }

    /**
     * Lưu challenge mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'avatar_frame_id' => 'nullable|exists:avatar_frames,id',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'target_count' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'badge_id.required' => 'Vui lòng chọn danh hiệu.',
            'badge_id.exists' => 'Danh hiệu không tồn tại.',
            'name.required' => 'Vui lòng nhập tên thử thách.',
            'target_count.required' => 'Vui lòng nhập số bài review cần viết.',
            'target_count.min' => 'Số bài review phải ít nhất là 1.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'name.max' => 'Tên thử thách tối đa 50 ký tự.',
            'description.max' => 'Mô tả tối đa 150 ký tự.',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = $request->has('is_active');
        // Xử lý avatar_frame_id nếu rỗng
        if (empty($validated['avatar_frame_id'])) {
            $validated['avatar_frame_id'] = null;
        }

        $challenge = Challenge::create($validated);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create',
            'description' => 'Tạo thử thách mới: ' . $challenge->name,
            'model_type' => Challenge::class,
            'model_id' => $challenge->id,
            'new_values' => $challenge->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo thử thách thành công!',
                'challenge' => $challenge->load('badge')
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'challenges'])
            ->with('success', 'Tạo thử thách thành công!');
    }

    /**
     * Xem chi tiết challenge và tiến độ users
     */
    public function show(Challenge $challenge)
    {
        $challenge->load('badge', 'avatarFrame');

        $userChallenges = UserChallenge::where('challenge_id', $challenge->id)
            ->with('user')
            ->orderBy('is_completed', 'desc')
            ->orderBy('current_count', 'desc')
            ->paginate(20);

        // Cho modal edit
        $badges = Badge::where('is_active', true)->get();
        $frames = \App\Models\AvatarFrame::where('is_active', true)->orderBy('order')->get();

        return view('admin.game.challenges.show', compact('challenge', 'userChallenges', 'badges', 'frames'));
    }

    /**
     * Form chỉnh sửa challenge
     */
    public function edit(Request $request, Challenge $challenge)
    {
        $badges = Badge::where('is_active', true)->get();
        $frames = \App\Models\AvatarFrame::where('is_active', true)->orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'challenge' => $challenge->load('badge', 'avatarFrame'),
                'badges' => $badges,
                'frames' => $frames
            ]);
        }

        return view('admin.game.challenges.edit', compact('challenge', 'badges', 'frames'));
    }

    /**
     * Cập nhật challenge
     */
    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'avatar_frame_id' => 'nullable|exists:avatar_frames,id',
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
            'target_count' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'badge_id.required' => 'Vui lòng chọn danh hiệu.',
            'name.required' => 'Vui lòng nhập tên thử thách.',
            'target_count.required' => 'Vui lòng nhập số bài review cần viết.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'name.max' => 'Tên thử thách tối đa 50 ký tự.',
            'description.max' => 'Mô tả tối đa 150 ký tự.',
        ]);

        $oldValues = $challenge->toArray();
        $validated['is_active'] = $request->has('is_active');
        // Xử lý avatar_frame_id nếu rỗng
        if (empty($validated['avatar_frame_id'])) {
            $validated['avatar_frame_id'] = null;
        }

        $challenge->update($validated);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update',
            'description' => 'Cập nhật thử thách: ' . $challenge->name,
            'model_type' => Challenge::class,
            'model_id' => $challenge->id,
            'old_values' => $oldValues,
            'new_values' => $challenge->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thử thách thành công!',
                'challenge' => $challenge->fresh()->load('badge', 'avatarFrame')
            ]);
        }

        return redirect()->route('admin.challenges.show', $challenge)
            ->with('success', 'Cập nhật thử thách thành công!');
    }

    /**
     * Xóa challenge
     */
    public function destroy(Request $request, Challenge $challenge)
    {
        $challengeName = $challenge->name;
        $oldValues = $challenge->toArray();

        $challenge->delete();

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete',
            'description' => 'Xóa thử thách: ' . $challengeName,
            'model_type' => Challenge::class,
            'model_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa thử thách thành công!'
            ]);
        }

        return redirect()->route('admin.game.index', ['tab' => 'challenges'])
            ->with('success', 'Xóa thử thách thành công!');
    }

    /**
     * Cấp badge cho user đã hoàn thành challenge
     */
    public function awardBadge(Request $request, Challenge $challenge, $userId)
    {
        $userChallenge = UserChallenge::where('challenge_id', $challenge->id)
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->firstOrFail();

        // Kiểm tra đã có badge chưa
        $existingBadge = UserBadge::where('user_id', $userId)
            ->where('badge_id', $challenge->badge_id)
            ->first();

        if ($existingBadge) {
            return back()->with('error', 'User đã có danh hiệu này!');
        }

        // Cấp badge
        $userBadge = UserBadge::create([
            'user_id' => $userId,
            'badge_id' => $challenge->badge_id,
            'earned_at' => now(),
            'expires_at' => $request->input('expires_at'),
        ]);

        // Log activity
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'award_badge',
            'description' => 'Cấp danh hiệu "' . $challenge->badge->name . '" cho user #' . $userId,
            'model_type' => UserBadge::class,
            'model_id' => $userBadge->id,
            'new_values' => $userBadge->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Đã cấp danh hiệu thành công!');
    }
}
