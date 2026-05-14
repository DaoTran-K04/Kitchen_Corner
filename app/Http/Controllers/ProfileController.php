<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Recipe;

class ProfileController extends Controller
{
    /**
     * Cập nhật thông tin hồ sơ cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
        }

        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'avatar_url' => 'nullable|url|max:500',
        ], [
            'name.required' => 'Tên hiển thị là bắt buộc.',
            'name.max' => 'Tên không được quá 100 ký tự.',
            'bio.max' => 'Giới thiệu không được quá 500 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.max' => 'Ảnh không được quá 2MB.',
            'avatar_url.url' => 'Đường dẫn ảnh không hợp lệ.',
        ]);

        /** @var \App\Models\User $user */
        $user->name = $request->input('name');
        $user->bio = $request->input('bio');

        // Xử lý upload avatar (ưu tiên file, sau đó là URL)
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ nếu có và không phải URL bên ngoài
            if ($user->avatar && !Str::startsWith($user->avatar, 'http')) {
                Storage::delete('public/' . str_replace('/storage/', '', $user->avatar));
            }

            // Lưu avatar mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        } elseif ($request->filled('avatar_url')) {
            // Xóa avatar cũ nếu có và không phải URL bên ngoài
            if ($user->avatar && !Str::startsWith($user->avatar, 'http')) {
                Storage::delete('public/' . str_replace('/storage/', '', $user->avatar));
            }
            // Sử dụng URL
            $user->avatar = $request->avatar_url;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hồ sơ thành công!',
            'user' => [
                'name' => $user->name,
                'bio' => $user->bio,
                'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=3E5F4E&color=fff&size=128'
            ]
        ]);
    }

    public function index(Request $request, $id = null)
    {
        // 1. XÁC ĐỊNH USER VÀ LẤY KÈM HUY HIỆU (BADGES)
        if ($id) {
            // [QUAN TRỌNG] Thêm with('activeBadges') để lấy danh hiệu còn hạn
            $user = User::with('activeBadges')->find($id);

            if (!$user)
                return redirect()->route('home')->with('error', 'Người dùng không tồn tại!');
        } else {
            $user = Auth::user();
            if (!$user)
                return redirect()->route('login');

            // Nếu là chính mình, nạp thêm quan hệ badges vào
            $user->load('activeBadges');
        }

        // 2. THỐNG KÊ

        if (Auth::id() == $user->id) {
            $totalReviews = $user->recipes()->count();
        } else {
            $totalReviews = $user->recipes()->where('status', 'published')->count();
        }
        $totalFollowing = $user->followings()->count();
        $totalFollowers = $user->followers()->count();

        // 3. Lấy danh sách bài Review (CÓ PHÂN QUYỀN) - PHÂN TRANG 10 BÀI
        $reviewsQuery = $user->recipes()
            ->with(['user', 'category', 'likes', 'comments']) // Lấy kèm thông tin cần thiết
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'desc');

        // Kiểm tra quyền xem:
        // Nếu người xem KHÔNG PHẢI là chủ profile (Khách) -> Chỉ lấy bài đã duyệt (published)
        if (Auth::id() != $user->id) {
            $reviewsQuery->where('status', 'published');
        }
        // Nếu là chủ nhà -> Xem hết (pending, published, rejected)

        // Phân trang 10 bài/trang
        $reviews = $reviewsQuery->paginate(10, ['*'], 'review_page')->withQueryString();



        // 5. Lấy danh sách công thức đã đăng (do user tạo) - PHÂN TRANG 12 CÔNG THỨC
        // [CẬP NHẬT] Hiển thị cho TẤT CẢ người xem, không chỉ chủ profile
        $isOwnProfile = Auth::id() == $user->id;

        if ($isOwnProfile) {
            // Chủ profile: thấy TẤT CẢ công thức (kể cả chờ duyệt)
            $totalSuggestedRecipes = Recipe::where('user_id', $user->id)->count();
            $suggestedRecipes = Recipe::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(12, ['*'], 'recipe_page')->withQueryString();
        } else {
            // Khách: chỉ thấy công thức ĐÃ DUYỆT
            $totalSuggestedRecipes = Recipe::where('user_id', $user->id)
                ->where('status', 'published')->count();
            $suggestedRecipes = Recipe::where('user_id', $user->id)
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->paginate(12, ['*'], 'recipe_page')->withQueryString();
        }

        // 6. Lấy danh sách bài viết đã lưu (chỉ cho chủ profile)
        $savedPosts = collect();
        $trashedPosts = collect();
        if ($isOwnProfile) {
            // Lấy công thức đã lưu qua Collection model
            $defaultCollection = \App\Models\Collection::where('user_id', $user->id)
                ->where('is_default', true)
                ->first();

            if ($defaultCollection) {
                $savedPosts = $defaultCollection->recipes()
                    ->with(['category', 'user', 'likes', 'comments.user'])
                    ->withCount(['likes', 'comments'])
                    ->latest()
                    ->get();
            }

            // 7. Lấy danh sách bài review đã xóa (thùng rác)
            $trashedPosts = $user->recipes()
                ->onlyTrashed()
                ->orderBy('deleted_at', 'desc')
                ->get();
        }

        // 8. Lấy danh hiệu hoạt động (Activity Title) dựa trên số bài viết và sách đã duyệt
        $activityTitle = $user->getActivityTitle();

        return view('profile', [
            'user' => $user,
            'reviews' => $reviews,
            // 'myBooks' => $myBooks, // Removed
            'suggestedRecipes' => $suggestedRecipes,
            'savedPosts' => $savedPosts,
            'trashedPosts' => $trashedPosts,
            // 'totalBooks' => $totalBooks, // Removed
            'totalReviews' => $totalReviews,
            'totalSuggestedRecipes' => $totalSuggestedRecipes,
            'totalFollowing' => $totalFollowing,
            'totalFollowers' => $totalFollowers,
            'isOwnProfile' => $isOwnProfile,
            'activityTitle' => $activityTitle, // [MỚI] Danh hiệu hoạt động
        ]);
    }

    /**
     * Trang xem tất cả bài review của user
     */
    public function allReviews($id)
    {
        $user = User::with('activeBadges')->findOrFail($id);

        // Lấy danh sách bài Review (CÓ PHÂN QUYỀN)
        $reviewsQuery = $user->recipes()
            ->withCount(['likes', 'comments'])
            ->orderBy('created_at', 'desc');

        // Nếu không phải chủ profile -> Chỉ lấy bài đã duyệt
        if (Auth::id() != $user->id) {
            $reviewsQuery->where('status', 'published');
        }

        $reviews = $reviewsQuery->paginate(10);

        return view('profile-reviews', [
            'user' => $user,
            'reviews' => $reviews,
            'isOwnProfile' => Auth::id() == $user->id,
        ]);
    }

    /**
     * Trang xem tất cả công thức đã đăng của user
     */
    public function allSuggestedRecipes($id)
    {
        $user = User::with('activeBadges')->findOrFail($id);

        // Chỉ cho phép chính chủ xem trang này
        if (Auth::id() != $user->id) {
            return redirect()->route('profile', $id)->with('error', 'Bạn không có quyền xem trang này.');
        }

        $suggestedRecipes = Recipe::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('profile-suggested-recipes', [
            'user' => $user,
            'suggestedRecipes' => $suggestedRecipes,
            'isOwnProfile' => true,
        ]);
    }

    /**
     * Trang bị khung avatar
     */
    public function equipAvatarFrame(Request $request)
    {
        $validated = $request->validate([
            'avatar_frame_id' => 'required|exists:avatar_frames,id'
        ]);

        $user = Auth::user();

        // Kiểm tra user có sở hữu frame này không
        if (!$user->avatarFrames()->where('avatar_frame_id', $validated['avatar_frame_id'])->exists()) {
            return response()->json(['error' => 'Bạn chưa sở hữu khung avatar này!'], 403);
        }

        // Gỡ tất cả khung cũ
        $user->avatarFrames()->updateExistingPivot(
            $user->avatarFrames->pluck('id')->toArray(),
            ['is_equipped' => false]
        );

        // Trang bị khung mới
        $user->avatarFrames()->updateExistingPivot(
            $validated['avatar_frame_id'],
            ['is_equipped' => true]
        );

        return response()->json(['success' => true, 'message' => 'Đã trang bị khung avatar!']);
    }

    /**
     * Gỡ khung avatar
     */
    public function unequipAvatarFrame()
    {
        $user = Auth::user();

        // Gỡ tất cả khung
        $user->avatarFrames()->updateExistingPivot(
            $user->avatarFrames->pluck('id')->toArray(),
            ['is_equipped' => false]
        );

        return response()->json(['success' => true, 'message' => 'Đã gỡ khung avatar!']);
    }

    /**
     * Cập nhật thứ tự hiển thị danh hiệu
     */
    public function updateBadgeOrder(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập!'], 401);
        }

        $request->validate([
            'badge_ids' => 'required|array',
            'badge_ids.*' => 'integer|exists:badges,id'
        ]);

        $badgeIds = $request->input('badge_ids');

        // Cập nhật thứ tự cho từng badge
        foreach ($badgeIds as $order => $badgeId) {
            // Chỉ cập nhật nếu user thực sự sở hữu badge này
            $user->badges()->updateExistingPivot($badgeId, [
                'display_order' => $order
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật thứ tự danh hiệu!'
        ]);
    }
}