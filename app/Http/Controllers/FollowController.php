<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FollowController extends Controller
{
    public function toggleFollow(Request $request)
    {
        $user = Auth::user();
        // Kiểm tra đăng nhập
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Bạn cần đăng nhập để theo dõi!']);
        }

        $targetUserId = $request->input('user_id');

        // Không được tự follow chính mình
        if ($user->id == $targetUserId) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không thể tự theo dõi chính mình!']);
        }

        // Kiểm tra xem đã follow chưa
        if ($user->isFollowing($targetUserId)) {
            // Đã follow -> Hủy follow (Unfollow)
            $user->followings()->detach($targetUserId);
            $action = 'unfollowed';
        } else {
            // Chưa follow -> Thêm follow
            $user->followings()->attach($targetUserId);
            $action = 'followed';
        }

        // Đếm lại số người theo dõi của đối phương để cập nhật giao diện
        $targetUser = User::find($targetUserId);
        $followerCount = $targetUser->followers()->count();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'follower_count' => $followerCount
        ]);
    }
    public function getFollowers($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json([]);

        // Lấy danh sách followers
        $followers = $user->followers()->get(['users.id', 'name', 'avatar']);
        
        return response()->json($followers);
    }

    // 2. Lấy danh sách những người mình đang theo dõi (Following)
    public function getFollowing($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json([]);

        // Lấy danh sách followings
        $following = $user->followings()->get(['users.id', 'name', 'avatar']);

        return response()->json($following);
    }
}