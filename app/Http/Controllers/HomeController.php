<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Post;     // <--- Đã thêm Post
use App\Models\Banner;
use App\Models\Quote;
use App\Models\Challenge;
use App\Models\Like;
use App\Models\CommentLike;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CommentLikedNotification;
use App\Notifications\CommentRepliedNotification;
use App\Notifications\PostLikedNotification;
use App\Notifications\PostCommentedNotification;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. XỬ LÝ PHẦN CỘNG ĐỒNG REVIEW (HIỂN THỊ COMMENTS) ---
        $sortReview = $request->get('sort_review', 'latest');

        // Query từ bảng COMMENTS - chỉ lấy comment cha (không phải reply)
        $reviewQuery = Comment::with([
            'user.activeBadges', // Load user cùng với badges
            'post.book', // Lấy thông tin sách qua bài post
            'likes',
            'replies' => function ($query) {
                $query->with(['user.activeBadges', 'likes'])->latest(); // Load badges cho cả replies
            }
        ])
            ->whereNull('parent_id') // Chỉ lấy comment gốc, không phải reply
            ->whereHas('post.book') // Chỉ lấy comment có liên kết với sách
            ->withCount('likes');

        if ($sortReview == 'popular') {
            $reviewQuery->orderByDesc('likes_count');
        } else {
            $reviewQuery->latest();
        }

        $latestReviews = $reviewQuery->paginate(5)->withQueryString();

        // Trả về Partial View nếu là Ajax (khi bấm phân trang)
        if ($request->ajax()) {
            return view('partials.home_comments', compact('latestReviews'))->render();
        }

        // --- 2. CÁC PHẦN DỮ LIỆU KHÁC (GIỮ NGUYÊN) ---
        $heroSlides = Banner::where('is_active', true)->orderBy('order', 'asc')->latest()->get();
        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([
                (object) [
                    'id' => null,
                    'title' => 'Cây Cam Ngọt Của Tôi',
                    'tag' => 'Sách Kinh Điển',
                    'description' => '"Vị chua chát của cái nghèo hòa trộn..."',
                    'image' => 'https://library.hust.edu.vn/sites/default/files/C%C3%A2y%20cam%20ng%E1%BB%8Dt%20c%E1%BB%A7a%20t%C3%B4i%20-%20%E1%BA%A2nh%20b%C3%ACa.jpg',
                    'rating' => '4.9/5.0',
                    'link' => '#'
                ]
            ]);
        }

        $bookQuery = Book::where('is_approved', true)->with('categories')->withAvg(['posts'], 'rating')->orderBy('created_at', 'desc')->take(10);
        $books = $bookQuery->get();
        foreach ($books as $book) {
            $book->avg_rating = round($book->posts_avg_rating ?? 0, 1);
        }

        $featuredArticle = Article::with('user')->where('is_featured', true)->where('is_active', true)->latest()->first();
        $sidebarArticles = Article::with('user')->where('is_featured', false)->where('is_active', true)->latest()->take(2)->get();
        $categories = Category::withCount('books')->orderBy('name', 'asc')->get();

        // --- LẤY QUOTE NGẪU NHIÊN THEO NGÀY ---
        $quotes = Quote::where('is_active', true)->get();
        $dailyQuote = null;
        if ($quotes->count() > 0) {
            // Dùng ngày hiện tại làm seed để cùng ngày luôn hiển thị cùng quote
            $dayOfYear = now()->dayOfYear + now()->year;
            $dailyQuote = $quotes[$dayOfYear % $quotes->count()];
        }

        // --- THỐNG KÊ CỘNG ĐỒNG ---
        $communityStats = [
            'books' => Book::where('is_approved', true)->count(),
            'members' => \App\Models\User::count(),
            'reviews' => Post::where('status', 'published')->count(),
            'comments' => Comment::count(),
            'book_views' => Book::where('is_approved', true)->sum('view_count'), // Tổng lượt đọc sách
            'post_views' => Post::where('status', 'published')->sum('view_count'), // Tổng lượt đọc bài
            'authors' => \App\Models\Author::count(), // Số tác giả
            'categories' => Category::count(), // Số thể loại
            'post_likes' => Like::count(), // Lượt thích bài review
            'comment_likes' => CommentLike::count(), // Lượt thích bình luận
            'online_users' => \App\Models\SiteVisit::getOnlineCount(), // Số người đang online
            'total_visits' => \App\Models\SiteStatistic::getTotalPageViews(), // Tổng lượt truy cập
        ];

        // --- SÁCH NGẪU NHIÊN "HÔM NAY ĐỌC GÌ?" ---
        $allApprovedBooks = Book::where('is_approved', true)
            ->withAvg(['posts'], 'rating')
            ->get();
        $randomBook = null;
        if ($allApprovedBooks->count() > 0) {
            // Dùng ngày làm seed để cùng ngày hiển thị cùng sách
            $dayOfYear = now()->dayOfYear + now()->year;
            $randomBook = $allApprovedBooks[$dayOfYear % $allApprovedBooks->count()];
            // Gán avg_rating từ posts
            $randomBook->avg_rating = round($randomBook->posts_avg_rating ?? 0, 1);
        }

        // --- TÁC GIẢ NGÀY HÔM NAY ---
        $allAuthors = \App\Models\Author::all();
        $dailyAuthor = null;
        if ($allAuthors->count() > 0) {
            // Dùng ngày làm seed để cùng ngày hiển thị cùng tác giả (offset +1 để khác sách)
            $dayOfYear = now()->dayOfYear + now()->year + 1;
            $dailyAuthor = $allAuthors[$dayOfYear % $allAuthors->count()];
        }

        // --- BÀI REVIEW NỔI BẬT (FEATURED POSTS) ---
        // Mới nhất - sắp xếp theo ngày tạo
        $latestPosts = Post::with(['user', 'book'])
            ->where('status', 'published')
            ->whereNotNull('thumbnail')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Hot nhất - sắp xếp theo lượt xem
        $hotPosts = Post::with(['user', 'book'])
            ->where('status', 'published')
            ->whereNotNull('thumbnail')
            ->orderByDesc('view_count')
            ->take(8)
            ->get();

        // --- THỬ THÁCH NGẪU NHIÊN ĐANG HOẠT ĐỘNG ---
        $today = now()->toDateString();
        $activeChallenge = Challenge::with('badge')
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->inRandomOrder()
            ->first();

        // Truyền biến $latestReviews vào view
        return view('home', compact(
            'heroSlides',
            'books',
            'latestReviews',
            'categories',
            'featuredArticle',
            'sidebarArticles',
            'dailyQuote',
            'communityStats',
            'randomBook',
            'dailyAuthor',
            'latestPosts',
            'hotPosts',
            'activeChallenge'
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
        $id = $request->id;
        $type = $request->type;
        $liked = false;
        $count = 0;

        if ($type === 'post') {
            $existingLike = Like::where('user_id', $userId)->where('post_id', $id)->first();
            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                Like::create(['user_id' => $userId, 'post_id' => $id]);
                $liked = true;

                // --- BỔ SUNG GỬI THÔNG BÁO CHO CHỦ BÀI VIẾT ---
                $post = Post::find($id);
                if ($post && $post->user_id != $userId) {
                    try {
                        $post->user->notify(new PostLikedNotification(Auth::user(), $post));
                    } catch (\Exception $e) {
                        \Log::error("Lỗi gửi thông báo Like Post: " . $e->getMessage());
                    }
                }
                // --------------------------------------------
            }
            $count = Like::where('post_id', $id)->count();
        } else {
            $existingLike = CommentLike::where('user_id', $userId)->where('comment_id', $id)->first();
            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                CommentLike::create(['user_id' => $userId, 'comment_id' => $id, 'is_like' => 1]);
                $liked = true;
                // Notify
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

    // --- LOGIC LƯU BÀI VIẾT (SAVE POST) ---
    public function toggleSavePost(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate(['post_id' => 'required|integer|exists:posts,id']);
        $userId = Auth::id();
        $postId = $request->post_id;

        $user = Auth::user();
        $isSaved = $user->savedPosts()->where('post_id', $postId)->exists();

        if ($isSaved) {
            // Bỏ lưu
            $user->savedPosts()->detach($postId);
            $saved = false;
        } else {
            // Lưu bài viết
            $user->savedPosts()->attach($postId);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved,
            'message' => $saved ? 'Đã lưu bài viết!' : 'Đã bỏ lưu bài viết!'
        ]);
    }

    // --- LOGIC REPLY (ĐÃ SỬA LỖI DATABASE) ---
    public function storeReply(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
        $request->validate(['content' => 'required|max:500']);

        $parentComment = Comment::findOrFail($id);
        $user = Auth::user();

        $reply = new Comment();
        $reply->user_id = $user->id;
        $reply->parent_id = $id;
        $reply->content = $request->input('content');

        // CHỈ LƯU POST_ID (Bỏ book_id và is_active vì không có trong DB)
        $reply->post_id = $parentComment->post_id;

        $reply->save();

        // LẤY THÔNG TIN NGƯỜI DÙNG CỦA BÌNH LUẬN CHA
        $parentComment = Comment::with('user')->find($id);

        // KIỂM TRA: Nếu người trả lời KHÔNG PHẢI là chủ nhân của bình luận gốc
        if ($parentComment && $parentComment->user_id != Auth::id()) {
            try {
                // Gửi thông báo cho chủ nhân của bình luận cha
                $parentComment->user->notify(new CommentRepliedNotification(Auth::user(), $reply));
            } catch (\Exception $e) {
                \Log::error("Lỗi gửi thông báo: " . $e->getMessage());
            }
        }

        $equippedFrame = $user->equippedFrame();
        $frameUrl = null;
        if ($equippedFrame) {
            $frameUrl = \Illuminate\Support\Str::startsWith($equippedFrame->frame_image, 'http')
                ? $equippedFrame->frame_image
                : asset('storage/' . $equippedFrame->frame_image);
        }

        return response()->json([
            'success' => true,
            'reply_id' => $reply->id,
            'user_name' => $user->name,
            'user_avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
            'user_frame' => $frameUrl,
            'content' => $reply->content,
            'time' => 'Vừa xong'
        ]);
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
            // 1. Đánh dấu đã đọc
            $notification->markAsRead();

            // 2. Lấy link từ data của thông báo
            // Link này phải có dạng: /chi-tiet/ten-sach-slug#post-123
            $link = $notification->data['link'] ?? null;

            if ($link) {
                return redirect($link);
            }
        }

        return redirect()->back();
    }

    // API lấy thông báo realtime
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->take(20)->get();
        $unreadCount = $user->unreadNotifications->count();

        $formattedNotifications = $notifications->map(function ($notification) {
            $dbType = $notification->type;
            $dataType = $notification->data['type'] ?? '';

            // Danh sách các class Notification là System
            $systemClasses = [
                'App\Notifications\NewReportNotification',
                'App\Notifications\NewBookRequestNotification',
                'App\Notifications\BookApprovedNotification',
                'App\Notifications\AdminNewPostNotification',
                'App\Notifications\ReportResolvedNotification'
            ];

            $systemTypes = ['new_report', 'book_request', 'book_approved', 'admin_new_post', 'report_resolved'];

            // Check nếu là system notification
            $isSystemNotification = in_array($dbType, $systemClasses) || in_array($dataType, $systemTypes) || isset($notification->data['icon']);

            // Xác định Type chuẩn
            $type = $dataType ?: match ($dbType) {
                'App\Notifications\NewReportNotification' => 'new_report',
                'App\Notifications\NewBookRequestNotification' => 'book_request',
                'App\Notifications\BookApprovedNotification' => 'book_approved',
                'App\Notifications\AdminNewPostNotification' => 'admin_new_post',
                'App\Notifications\ReportResolvedNotification' => 'report_resolved',
                default => ''
            };

            // Mặc định cho User notification
            $title = $notification->data['title'] ?? '';
            $icon = $notification->data['icon'] ?? null;
            $color = $notification->data['color'] ?? 'text-green-600';

            // Nếu là System thì set cứng Icon/Title theo Type
            if ($isSystemNotification) {
                switch ($type) {
                    case 'new_report':
                        $icon = 'fas fa-flag';
                        $title = 'Báo cáo mới';
                        $color = 'text-yellow-600';
                        break;
                    case 'book_request':
                        $icon = 'fas fa-book';
                        $title = 'Gợi ý sách mới';
                        $color = 'text-yellow-600';
                        break;
                    case 'book_approved':
                        $icon = 'fas fa-check-circle';
                        $title = 'Sách được duyệt';
                        $color = 'text-green-600';
                        break;
                    case 'admin_new_post':
                        $icon = 'fas fa-file-contract';
                        $title = 'Bài đăng mới ';
                        $color = 'text-red-600';
                        break;
                    case 'report_resolved':
                        $status = $notification->data['status'] ?? 'resolved';
                        if ($status === 'approved') {
                            $icon = 'fas fa-check-circle';
                            $title = 'Báo cáo được chấp thuận';
                            $color = 'text-green-600';
                        } else {
                            $icon = 'fas fa-times-circle';
                            $title = 'Báo cáo bị từ chối';
                            $color = 'text-red-600';
                        }
                        break;
                }
            }

            return [
                'id' => $notification->id,
                'is_system' => $isSystemNotification,
                'title' => $title,
                'icon' => $icon,
                'color' => $color,
                'user_avatar' => $notification->data['user_avatar'] ?? 'https://ui-avatars.com/api/?name=User',
                'user_name' => $notification->data['user_name'] ?? '', // Bỏ default "Ai đó" để handle ở fontend nếu cần, hoặc để rỗng
                'message' => $notification->data['message'] ?? 'đã tương tác với bạn',
                'post_title' => \Str::limit($notification->data['post_title'] ?? ($notification->data['book_title'] ?? ''), 50),
                'time' => $notification->created_at->diffForHumans(),
                'read_at' => $notification->read_at,
                'link' => route('notification.read', $notification->id)
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $formattedNotifications
        ]);
    }
}