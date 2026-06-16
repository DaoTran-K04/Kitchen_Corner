<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Challenge;
use App\Models\Like;
use App\Models\CommentLike;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\CommentLikedNotification;
use App\Notifications\CommentRepliedNotification;

// đề xuất công thức cá nhân hóa
class HomeController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. CỘNG ĐỒNG – BÌNH LUẬN CÔNG THỨC ---
        $sortReview = $request->get('sort_review', 'latest');

        // Lấy comment cha có liên kết với recipe (thay vì post->book cũ)
        $reviewQuery = Comment::with([
            'user.activeBadges',
            'recipe',        // ← quan hệ mới (thay 'post.book')
            'likes',
            'replies' => function ($query) {
                $query->with(['user.activeBadges', 'likes'])->latest();
            }
        ])
            ->whereNull('parent_id')
            ->whereHas('recipe')       // ← chỉ lấy comment có recipe
            ->withCount(['likes', 'replies']);

        if ($sortReview == 'popular') {
            // Sắp xếp theo tổng lượt tương tác (Like + Phản hồi)
            $reviewQuery->orderByRaw('(likes_count + replies_count) DESC')
                        ->orderByDesc('created_at');
        } else {
            $reviewQuery->latest();
        }

        $latestReviews = $reviewQuery->paginate(5)->withQueryString();

        if ($request->ajax()) {
            return view('partials.home_comments', compact('latestReviews'))->render();
        }

        // --- 2. BANNER SLIDE (CACHE 30 phút) ---
        $heroSlides = \Illuminate\Support\Facades\Cache::remember('hero_slides', 1800, function() {
            return Banner::where('is_active', true)->orderBy('order', 'asc')->latest()->get();
        });

        // --- 3. CÔNG THỨC THEO CHỦ ĐỀ MỚI NHẤT ---
        $siteTheme = \Illuminate\Support\Facades\Cache::rememberForever('active_theme', function () {
            $setting = \App\Models\Setting::where('key', 'active_theme')->first();
            return $setting ? $setting->value : 'auto';
        });

        if ($siteTheme === 'auto') {
            $month = now()->month; $day = now()->day;
            if ($month == 12 && $day >= 20 && $day <= 26) $siteTheme = 'christmas';
            elseif (($month == 1 && $day >= 15) || ($month == 2 && $day <= 15)) $siteTheme = 'tet';
            elseif ($month == 2 && $day >= 12 && $day <= 20) $siteTheme = 'valentine';
            elseif (($month == 10 && $day >= 25) || ($month == 11 && $day <= 2)) $siteTheme = 'halloween';
        }

        $recipesQuery = Recipe::with(['user', 'category'])->withCount(['likes', 'comments'])->where('status', 'published');

        $themeKeywords = [];
        if ($siteTheme === 'valentine') {
            $themeKeywords = ['socola', 'ngọt', 'bánh', 'kem', 'bít tết', 'dâu', 'rượu', 'tình yêu', 'tráng miệng'];
        } elseif ($siteTheme === 'tet') {
            $themeKeywords = ['tết', 'thịt kho', 'bánh chưng', 'bánh tét', 'gà luộc', 'dưa hành', 'chả lụa', 'giò', 'canh măng', 'mứt', 'xôi', 'chè'];
        } elseif ($siteTheme === 'christmas') {
            $themeKeywords = ['gà tây', 'bánh kem', 'nướng', 'bò', 'rượu vang', 'súp', 'salad', 'bánh quy', 'giáng sinh', 'noel'];
        } elseif ($siteTheme === 'halloween') {
            $themeKeywords = ['bí đỏ', 'súp', 'kẹo', 'huyết', 'máu', 'quỷ', 'ma', 'socola', 'bánh quy', 'halloween'];
        } elseif ($siteTheme === 'default' || $siteTheme === 'auto') {
            $m = now()->month;
            if ($m >= 3 && $m <= 4) { // Mùa Xuân -> mát mẻ, chay, nhẹ bụng
                $themeKeywords = ['gỏi', 'salad', 'hấp', 'luộc', 'thanh mát', 'chay', 'đậu hũ', 'nước ép', 'thảo mộc', 'canh ngao'];
            } elseif ($m >= 5 && $m <= 7) { // Mùa Hè -> nóng bức, chè, kem, sinh tố 
                $themeKeywords = ['giải nhiệt', 'kem', 'chè', 'sinh tố', 'trái cây', 'lạnh', 'rau câu', 'nước ép', 'sữa chua', 'gỏi', 'cuốn'];
            } elseif ($m >= 8 && $m <= 10) { // Mùa Thu / Trung Thu -> chè ấm, bánh ngọt
                $themeKeywords = ['trung thu', 'bánh nướng', 'bánh dẻo', 'cốm', 'chè', 'súp', 'hầm', 'canh', 'gà hầm', 'táo đỏ'];
            } else { // Mùa Đông -> cay nóng
                $themeKeywords = ['lẩu', 'nướng', 'cay', 'nóng', 'chiên', 'gừng', 'tiêu', 'bò sốt', 'cà ri', 'hầm xương'];
            }
        }

        if (!empty($themeKeywords)) {
            $cases = [];
            $bindings = [];
            foreach ($themeKeywords as $keyword) {
                // Priority by Title
                $cases[] = "title LIKE ?";
                $bindings[] = "%{$keyword}%";
            }
            
            // Build the CASE statement to bring matched items to the top
            $sql = "CASE WHEN (" . implode(" OR ", $cases) . ") THEN 1 ELSE 2 END";
            $recipesQuery->orderByRaw($sql, $bindings);
        }

        // Secondary sorting by newest updates
        $recipesQuery->orderBy('created_at', 'desc');

        $recipes = \Illuminate\Support\Facades\Cache::remember('home_themed_recipes_' . $siteTheme, 600, function() use ($recipesQuery) {
            return $recipesQuery->take(12)->get();
        });

        $trendingRecipes = \Illuminate\Support\Facades\Cache::remember('home_trending_recipes', 600, function() {
            return Recipe::with(['user', 'category'])
                ->withCount(['likes', 'comments'])
                ->where('status', 'published')
                ->orderBy('view_count', 'desc')
                ->take(5)
                ->get();
        });

        // --- 4. BÀI VIẾT TẠP CHÍ (Ưu tiên từ Database) ---
        $formattedArticles = \Illuminate\Support\Facades\Cache::remember('home_articles', 1200, function() {
            $dbArticles = Article::with('user')->where('is_active', true)->latest()->take(3)->get();
            return $dbArticles->map(function($article) {
                return (object)[
                    'title' => $article->title,
                    'link' => route('articles.show', $article->slug),
                    'description' => $article->excerpt,
                    'image' => $article->thumbnail,
                    'date' => $article->created_at->format('d/m/Y'),
                    'author_name' => $article->user->name ?? 'Admin'
                ];
            });
        });



        $featuredArticle = $formattedArticles->first();
        $sidebarArticles = $formattedArticles->slice(1, 2);

        $categories = \Illuminate\Support\Facades\Cache::remember('home_categories', 60, function() {
            return Category::withCount('recipes')->orderBy('name', 'asc')->get();
        });

        // --- 5. CHÂM NGÔN ẨM THỰC NGẪU NHIÊN (CACHE 12h) ---
        $dailyQuote = \Illuminate\Support\Facades\Cache::remember('home_daily_quote', 43200, function() {
            return \App\Models\Quote::where('is_active', true)->inRandomOrder()->first();
        });

        // --- 6. THỐNG KÊ CỘNG ĐỒNG (CACHE 60 phút) ---
        $communityStats = \Illuminate\Support\Facades\Cache::remember('community_stats', 60, function () {
            return [
                'recipes'       => Recipe::where('status', 'published')->count(),
                'members'       => \App\Models\User::count(),
                'comments'      => Comment::count(),
                'categories'    => Category::count(),
                'recipe_views'  => Recipe::where('status', 'published')->sum('view_count'),
                'comment_likes' => CommentLike::count(),
                'online_users'  => \App\Models\SiteVisit::getOnlineCount(),
                'total_visits'  => \App\Models\SiteStatistic::getTotalPageViews(),
                'post_likes'    => Like::count(),
            ];
        });

        // Cập nhật lượt người dùng online (không cache)
        $communityStats['online_users'] = \App\Models\SiteVisit::getOnlineCount();
        $communityStats['total_visits'] = \App\Models\SiteStatistic::getTotalPageViews();

        // --- 7. CÔNG THỨC "HÔM NAY NẤU GÌ?" (TỐI ƯU HÓA) ---
        $totalPublishedRecipes = $communityStats['recipes'];
        $randomRecipe = null;
        if ($totalPublishedRecipes > 0) {
            $dayOfYear    = now()->dayOfYear + now()->year;
            $offsetIndex  = $dayOfYear % $totalPublishedRecipes;
            $randomRecipe = Recipe::with(['category'])->withCount(['likes', 'comments'])->where('status', 'published')->offset($offsetIndex)->limit(1)->first();
        }

        // --- 8. TÁC GIẢ TIÊU BIỂU ---
        $dailyAuthor = \App\Models\User::whereHas('recipes', function($q) {
            $q->where('status', 'published');
        })->inRandomOrder()->first();

        // Lấy 8 công thức nổi bật để hiển thị ở section slider
        // Lấy 8 công thức nổi bật (CACHE 30p)
        $latestPosts = \Illuminate\Support\Facades\Cache::remember('home_featured_posts', 1800, function() {
            $featured = Recipe::with(['user', 'category'])->withCount(['likes', 'comments'])->where('status', 'published')->where('is_featured', true)->latest()->take(8)->get();
            if ($featured->count() < 8) {
                $more = Recipe::with(['user', 'category'])
                    ->withCount(['likes', 'comments'])
                    ->where('status', 'published')
                    ->whereNotIn('id', $featured->pluck('id')->toArray()) // tránh trùng lặp
                    ->latest()
                    ->take(8 - $featured->count())
                    ->get();
                return $featured->merge($more);
            }
            return $featured;
        });
        
        $hotPosts = \Illuminate\Support\Facades\Cache::remember('home_hot_posts', 1800, function() {
            return Recipe::with(['user', 'category'])->withCount(['likes', 'comments'])->where('status', 'published')->orderBy('view_count', 'desc')->take(8)->get();
        });

        // --- 9. THỬ THÁCH (CACHE 1h) ---
        $today = now()->toDateString();
        $activeChallenge = \Illuminate\Support\Facades\Cache::remember('home_active_challenge', 3600, function() use ($today) {
            return Challenge::with('badge')
                ->where('is_active', true)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->inRandomOrder()
                ->first();
        });

        // --- 10. ĐỀ XUẤT CÁ NHÂN HÓA (CHO NGƯỜI DÙNG ĐĂNG NHẬP) --- //
        $personalizedRecipes = collect();
        if (Auth::check()) {
            $user = Auth::user();
            // Lấy ID các công thức người dùng đã thích hoặc lưu (nếu có table collections/bookmarks thì thêm vào)
            $likedRecipeIds = DB::table('likes')->where('user_id', $user->id)->pluck('recipe_id')->toArray();
            $collectedRecipeIds = DB::table('collection_recipes')
                ->join('collections', 'collections.id', '=', 'collection_recipes.collection_id')
                ->where('collections.user_id', $user->id)
                ->pluck('collection_recipes.recipe_id')
                ->toArray();
            $interactedRecipeIds = array_unique(array_merge($likedRecipeIds, $collectedRecipeIds));

            if (count($interactedRecipeIds) > 0) {
                // Lấy các danh mục của những công thức đã tương tác
                $categoryIds = DB::table('recipes')
                    ->whereIn('id', $interactedRecipeIds)
                    ->pluck('category_id')
                    ->unique()
                    ->toArray();

                // Truy vấn các công thức cùng danh mục, loại trừ những công thức đã tương tác
                $personalizedRecipes = Recipe::with(['user', 'category'])
                    ->withCount(['likes', 'comments'])
                    ->where('status', 'published')
                    ->whereIn('category_id', $categoryIds)
                    ->whereNotIn('id', $interactedRecipeIds)
                    ->inRandomOrder()
                    ->take(8)
                    ->get();
            }
            
            // Nếu không đủ dữ liệu cá nhân hóa (chưa tương tác hoặc đã xem hết trong danh mục)
            if ($personalizedRecipes->count() < 4) {
                $additionalRecipes = Recipe::with(['user', 'category'])
                    ->withCount(['likes', 'comments'])
                    ->where('status', 'published')
                    ->whereNotIn('id', $interactedRecipeIds)
                    ->orderBy('view_count', 'desc')
                    ->take(8 - $personalizedRecipes->count())
                    ->get();
                $personalizedRecipes = $personalizedRecipes->merge($additionalRecipes);
            }
        }

        return view('home', compact(
            'heroSlides',
            'recipes',
            'latestReviews',
            'categories',
            'featuredArticle',
            'sidebarArticles',
            'dailyQuote',
            'communityStats',
            'randomRecipe',
            'trendingRecipes',
            'dailyAuthor',
            'latestPosts',
            'hotPosts',
            'activeChallenge',
            'personalizedRecipes'
        ));
    }

    // --- LOGIC LIKE ---
    public function toggleLike(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate(['id' => 'required|integer', 'type' => 'required|in:post,comment']);
        $userId = Auth::id();
        $id     = $request->id;
        $type   = $request->type;
        $liked  = false;
        $count  = 0;

        if ($type === 'post') {
            $existingLike = Like::where('user_id', $userId)->where('recipe_id', $id)->first();
            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                Like::create(['user_id' => $userId, 'recipe_id' => $id]);
                $liked = true;
                
                // Gửi thông báo cho tác giả công thức
                $recipe = Recipe::find($id);
                if ($recipe && $recipe->user_id != $userId) {
                    try {
                        $recipe->user->notify(new \App\Notifications\RecipeLikedNotification(Auth::user(), $recipe));
                    } catch (\Exception $e) {
                        \Log::error("Lỗi gửi thông báo thích công thức: " . $e->getMessage());
                    }
                }
            }
            $count = Like::where('recipe_id', $id)->count();
        } else {
            $existingLike = CommentLike::where('user_id', $userId)->where('comment_id', $id)->first();
            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                CommentLike::create(['user_id' => $userId, 'comment_id' => $id, 'is_like' => 1]);
                $liked = true;
                $comment = Comment::find($id);
                if ($comment && $comment->user_id != $userId) {
                    try {
                        $comment->user->notify(new CommentLikedNotification(Auth::user(), $comment));
                    } catch (\Exception $e) {
                    }
                }
            }
            $count = CommentLike::where('comment_id', $id)->count();
        }
        return response()->json(['success' => true, 'liked' => $liked, 'count' => $count, 'type' => $type]);
    }

    // --- LOGIC REPLY ---
    public function storeReply(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
        $request->validate(['content' => 'required|max:500']);

        $parentComment = Comment::findOrFail($id);
        $user          = Auth::user();

        $reply              = new Comment();
        $reply->user_id     = $user->id;
        $reply->parent_id   = $id;
        $reply->content     = $request->input('content');
        $reply->recipe_id   = $parentComment->recipe_id; // ← dùng recipe_id thay post_id
        $reply->save();

        // Cập nhật tiến độ thử thách loại 'bình luận'
        $user->updateChallengeProgress();

        $parentComment = Comment::with('user')->find($id);

        if ($parentComment && $parentComment->user_id != Auth::id()) {
            try {
                $parentComment->user->notify(new CommentRepliedNotification(Auth::user(), $reply));
            } catch (\Exception $e) {
                \Log::error("Lỗi gửi thông báo: " . $e->getMessage());
            }
        }

        $equippedFrame = $user->equippedFrame();
        $frameUrl      = null;
        if ($equippedFrame) {
            $frameUrl = \Illuminate\Support\Str::startsWith($equippedFrame->frame_image, 'http')
                ? $equippedFrame->frame_image
                : asset('storage/' . $equippedFrame->frame_image);
        }

        return response()->json([
            'success'     => true,
            'reply_id'    => $reply->id,
            'user_name'   => $user->name,
            'user_avatar' => $user->avatar ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($user->name) . '&background=random',
            'user_frame'  => $frameUrl,
            'content'     => $reply->content,
            'time'        => $reply->created_at->diffForHumans(),
        ]);
    }

    public function updateComment(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
        $request->validate(['content' => 'required|max:500']);

        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa bình luận này!'], 403);
        }

        $comment->content = $request->input('content');
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bình luận!',
            'content' => $comment->content
        ]);
    }

    public function deleteComment($id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này!'], 403);
        }

        try {
            DB::beginTransaction();
            
            // 1. Xóa các lượt thích của bình luận này
            DB::table('comment_likes')->where('comment_id', $id)->delete();
            
            // 2. Xóa các báo cáo liên quan
            DB::table('comment_reports')->where('comment_id', $id)->delete();
            
            // 3. Xóa các phản hồi con (và các lượt thích của chúng)
            $replyIds = $comment->replies()->pluck('id');
            if ($replyIds->count() > 0) {
                DB::table('comment_likes')->whereIn('comment_id', $replyIds)->delete();
                DB::table('comment_reports')->whereIn('comment_id', $replyIds)->delete();
                $comment->replies()->delete();
            }
            
            // 4. Xóa chính bình luận đó
            $comment->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bình luận!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lỗi xóa bình luận: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống khi xóa: ' . $e->getMessage()], 500);
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            $link = $notification->data['link'] ?? null;
            if ($link) return redirect($link);
        }
        return redirect()->back();
    }

    // API lấy thông báo realtime
    public function getNotifications()
    {
        $user          = Auth::user();
        $notifications = $user->notifications()->take(20)->get();
        $unreadCount   = $user->unreadNotifications->count();

        $formattedNotifications = $notifications->map(function ($notification) {
            $dbType   = $notification->type;
            $dataType = $notification->data['type'] ?? '';

            $systemClasses = [
                'App\Notifications\NewReportNotification',
                'App\Notifications\NewBookRequestNotification',
                'App\Notifications\BookApprovedNotification',
                'App\Notifications\RecipeApprovedNotification',
                'App\Notifications\RecipeRejectedNotification',
                'App\Notifications\AdminNewPostNotification',
                'App\Notifications\ReportResolvedNotification'
            ];
            $systemTypes = ['new_report', 'book_request', 'book_approved', 'recipe_approved', 'recipe_rejected', 'admin_new_post', 'report_resolved'];

            $isSystemNotification = in_array($dbType, $systemClasses) || in_array($dataType, $systemTypes) || isset($notification->data['icon']);

            $type  = $dataType ?: match ($dbType) {
                'App\Notifications\NewReportNotification'    => 'new_report',
                'App\Notifications\NewBookRequestNotification' => 'book_request',
                'App\Notifications\BookApprovedNotification' => 'book_approved',
                'App\Notifications\RecipeApprovedNotification' => 'recipe_approved',
                'App\Notifications\RecipeRejectedNotification' => 'recipe_rejected',
                'App\Notifications\RecipeLikedNotification'  => 'recipe_liked',
                'App\Notifications\AdminNewPostNotification' => 'admin_new_post',
                'App\Notifications\ReportResolvedNotification' => 'report_resolved',
                default => ''
            };

            $title = $notification->data['title'] ?? '';
            $icon  = $notification->data['icon']  ?? null;
            $color = $notification->data['color'] ?? 'text-green-600';

            if ($isSystemNotification) {
                switch ($type) {
                    case 'new_report':    $icon = 'fas fa-flag';          $title = 'Báo cáo mới';           $color = 'text-yellow-600'; break;
                    case 'book_request':  $icon = 'fas fa-utensils';      $title = 'Công thức mới';         $color = 'text-yellow-600'; break;
                    case 'book_approved': $icon = 'fas fa-check-circle';  $title = 'Công thức được duyệt';  $color = 'text-green-600';  break;
                    case 'recipe_rejected': 
                        $icon = 'fas fa-ban'; 
                        $title = ($notification->data['is_violation'] ?? false) ? 'Cảnh báo vi phạm!' : 'Công thức bị từ chối'; 
                        $color = 'text-red-600'; 
                        break;
                    case 'admin_new_post':$icon = 'fas fa-file-contract'; $title = 'Bài đăng mới';          $color = 'text-red-600';    break;
                    case 'report_resolved':
                        $status = $notification->data['status'] ?? 'resolved';
                        if ($status === 'approved') { $icon = 'fas fa-check-circle'; $title = 'Báo cáo được chấp thuận'; $color = 'text-green-600'; }
                        else                        { $icon = 'fas fa-times-circle'; $title = 'Báo cáo bị từ chối';      $color = 'text-red-600';   }
                        break;
                }
            }

            return [
                'id'          => $notification->id,
                'is_system'   => $isSystemNotification,
                'title'       => $title,
                'icon'        => $icon,
                'color'       => $color,
                'user_avatar' => $notification->data['user_avatar'] ?? 'https://api.dicebear.com/7.x/initials/svg?seed=User',
                'user_name'   => $notification->data['user_name']   ?? '',
                'message'     => $notification->data['message']     ?? 'đã tương tác với bạn',
                'post_title'  => \Str::limit($notification->data['post_title'] ?? ($notification->data['recipe_title'] ?? ''), 50),
                'time'        => $notification->created_at->diffForHumans(),
                'read_at'     => $notification->read_at,
                'link'        => route('notification.read', $notification->id)
            ];
        });

        return response()->json([
            'unread_count'  => $unreadCount,
            'notifications' => $formattedNotifications
        ]);
    }
}
