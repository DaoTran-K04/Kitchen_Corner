<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\Book;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ActivityTitleController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookSuggestionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SubscriberController;

// ====================================================
// 1. NHÓM PUBLIC (Ai cũng xem được)
// ====================================================

// Route serve file storage (bypass symlink issue on Windows)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mimeType]);
})->where('path', '.*');

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang tĩnh (Footer)
Route::view('/ve-chung-toi', 'pages.about')->name('page.about');
Route::view('/dieu-khoan-su-dung', 'pages.terms')->name('page.terms');
Route::view('/chinh-sach-bao-mat', 'pages.privacy')->name('page.privacy');
Route::view('/lien-he', 'pages.contact')->name('page.contact');
// AJAX Live Search (cho Header)
Route::get('/ajax-search', function (Illuminate\Http\Request $request) {
    $keyword = $request->get('keyword');

    if (!$keyword || strlen($keyword) < 2) {
        return response()->json([]);
    }

    $books = App\Models\Book::where('is_approved', true)
        ->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('author_name', 'like', "%{$keyword}%");
        })
        ->select('id', 'title', 'slug', 'author_name', 'cover_image', 'avg_rating')
        ->orderBy('view_count', 'desc')
        ->limit(8)
        ->get();

    return response()->json($books);
})->name('ajax.search');

// Chatbot API
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::get('/api/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
Route::delete('/api/chatbot/history', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

// Newsletter Subscription
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');

// Trang chi tiết bài viết Tạp chí
Route::get('/tap-chi/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Trang Danh sách sách
Route::get('/danh-sach', [BookController::class, 'list'])->name('books.list');

// Tìm kiếm Review/Sách
Route::get('/review-search', [BookController::class, 'search'])->name('books.search');

// Tác giả
Route::get('/tac-gia', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/tac-gia/{slug}', [AuthorController::class, 'show'])->name('authors.show');

// Xem chi tiết sách & Đánh giá
Route::get('/chi-tiet/{slug}', [BookController::class, 'show'])->name('detail');
Route::get('/chi-tiet/{slug}/danh-gia', [BookController::class, 'showReviews'])->name('book.reviews');
// Route dự phòng cho link cũ (nếu có)
Route::get('/book/{slug}', [BookController::class, 'show'])->name('book.show');

// API Public lấy danh sách Follow (Cho trang Profile)
Route::get('/api/user/{id}/followers', [FollowController::class, 'getFollowers']);
Route::get('/api/user/{id}/following', [FollowController::class, 'getFollowing']);

// Ranking
Route::get('/ranking/top-liked', [RankingController::class, 'topLikedPosts']);

// Public Profile - Xem profile thành viên (không cần đăng nhập)
Route::get('/thanh-vien/{id}', [ProfileController::class, 'index'])->name('public.profile');


// ====================================================
// 2. NHÓM KHÁCH (GUEST ONLY)
// ====================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ====================================================
// 2.1 QUÊN MẬT KHẨU (Cả guest và auth đều dùng được)
// ====================================================
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');
Route::get('/verify-code', [AuthController::class, 'showVerifyCodeForm'])->name('password.verify.form');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('password.verify');
Route::post('/resend-code', [AuthController::class, 'resendCode'])->name('password.resend');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ====================================================
// 2.5 NHÓM XÁC THỰC EMAIL (EMAIL VERIFICATION - OTP)
// ====================================================

// 1. Giao diện nhập mã OTP xác thực
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. Xử lý xác thực mã OTP
Route::post('/email/verify', [AuthController::class, 'verifyRegistrationCode'])
    ->middleware('auth')->name('verification.verify');

// 3. Gửi lại mã OTP xác thực
Route::post('/email/resend-code', [AuthController::class, 'resendRegistrationCode'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Route cho trang Thử Thách
// Code này chạy qua Controller để lấy dữ liệu rồi mới trả về View
Route::get('/thu-thach', [ChallengeController::class, 'index'])->name('challenges.index');


// ====================================================
// 3. NHÓM THÀNH VIÊN (AUTH + EMAIL VERIFIED REQUIRED)
// ====================================================

// Route logout - cho phép cả user chưa verify
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'email.verified'])->group(function () {

    // --- CHANGE PASSWORD ---
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change.password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password.post');

    // --- PROFILE & FOLLOW ---
    Route::get('/profile/{id?}', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/{id}/reviews', [ProfileController::class, 'allReviews'])->name('profile.reviews');
    Route::get('/profile/{id}/suggested-books', [ProfileController::class, 'allSuggestedBooks'])->name('profile.suggested-books');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar-frame/equip', [ProfileController::class, 'equipAvatarFrame'])->name('profile.avatar-frame.equip');
    Route::post('/profile/avatar-frame/unequip', [ProfileController::class, 'unequipAvatarFrame'])->name('profile.avatar-frame.unequip');
    Route::post('/profile/badges/order', [ProfileController::class, 'updateBadgeOrder'])->name('profile.badges.order');
    Route::post('/follow/toggle', [FollowController::class, 'toggleFollow'])->name('follow.toggle');

    // --- ĐỀ XUẤT SÁCH ---
    Route::get('/sach/de-xuat', [BookSuggestionController::class, 'create'])->name('books.suggest');
    Route::post('/sach/de-xuat', [BookSuggestionController::class, 'store'])->name('books.suggest.store');

    // --- LIKE & COMMENT (AJAX) ---
    // Route xử lý Like chung (cho cả Post và Comment)
    Route::post('/like', [HomeController::class, 'toggleLike'])->name('handle.like');

    // Route lưu bài viết (Save Post)
    Route::post('/post/save', [HomeController::class, 'toggleSavePost'])->name('post.save');

    // Route gửi Reply (Bình luận trả lời)
    Route::post('/comment/{id}/reply', [HomeController::class, 'storeReply'])->name('comment.reply');

    // Route comment bài viết (nếu dùng PostController riêng)
    Route::post('/posts/{id}/comment', [PostController::class, 'postComment'])->name('posts.comment');

    // Route comment cho chi tiết bài viết
    Route::post('/post/{post_id}/comment', [CommentController::class, 'store'])->name('post.comment');

    // --- REPORT (BÁO CÁO VI PHẠM) ---
    Route::post('/report/post/{id}', [\App\Http\Controllers\ReportController::class, 'reportPost'])->name('report.post');
    Route::post('/report/comment/{id}', [\App\Http\Controllers\ReportController::class, 'reportComment'])->name('report.comment');

    // --- THÔNG BÁO (NOTIFICATION) ---
    // Đánh dấu tất cả là đã đọc
    Route::get('/notifications/read-all', [HomeController::class, 'markAllAsRead'])->name('notification.readAll');

    // Đọc 1 thông báo cụ thể -> Chuyển hướng
    Route::get('/notifications/{id}', [HomeController::class, 'markAsRead'])->name('notification.read');

    // API lấy thông báo realtime (cho polling)
    Route::get('/api/notifications', [HomeController::class, 'getNotifications'])->name('api.notifications');

    // --- REVIEW / POST ---
    Route::get('/reviews/viet-bai', function (Illuminate\Http\Request $request) {
        $user = Auth::user();
        $preselectedBook = null;

        // Nếu có book_id, lấy thông tin sách để tự động chọn
        if ($request->has('book_id')) {
            $preselectedBook = Book::find($request->book_id);
        }

        return view('create-review', compact('user', 'preselectedBook'));
    })->name('reviews.create');

    // Lưu bài viết mới
    Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');

    // Chỉnh sửa bài review
    Route::get('/reviews/{id}/chinh-sua', [PostController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{id}/update', [PostController::class, 'update'])->name('reviews.update');

    // Yêu cầu xóa bài review (chờ admin duyệt)
    Route::post('/reviews/{id}/request-delete', [PostController::class, 'requestDelete'])->name('reviews.request-delete');

    // Hủy yêu cầu xóa bài review
    Route::post('/reviews/{id}/cancel-delete', [PostController::class, 'cancelDelete'])->name('reviews.cancel-delete');

    // Khôi phục bài review từ thùng rác
    Route::post('/reviews/{id}/restore', [PostController::class, 'restorePost'])->name('reviews.restore');

    // Xóa vĩnh viễn bài review
    Route::delete('/reviews/{id}/force-delete', [PostController::class, 'forceDeletePost'])->name('reviews.force-delete');

    // --- API NỘI BỘ (Cho JS tìm sách khi viết review) ---
    Route::get('/api/books/search', function (Illuminate\Http\Request $request) {
        $query = $request->get('q');
        $books = Illuminate\Support\Facades\DB::table('books')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('author_name', 'like', "%{$query}%")
            ->select('id', 'title', 'author_name', 'published_year', 'cover_image', 'slug')
            ->limit(10)
            ->get();
        return response()->json($books);
    });

    // API lấy sách phổ biến (Cho trang viết review)
    Route::get('/api/books/popular', function () {
        // Lấy top 20 sách phổ biến nhất, sau đó random 6 cuốn
        $books = App\Models\Book::where('is_approved', true)
            ->orderBy('view_count', 'desc')
            ->select('id', 'title', 'author_name', 'published_year', 'cover_image', 'slug', 'avg_rating')
            ->limit(20)
            ->get()
            ->shuffle()
            ->take(6);
        return response()->json($books->values());
    });
    // chalenges
    Route::post('/challenge/join/{id}', [ChallengeController::class, 'join'])->name('challenge.join');
});


// ====================================================
// 4. NHÓM ADMIN (AUTH + ADMIN REQUIRED)
// ====================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/reviews', [AdminController::class, 'dashboardReviews'])->name('dashboard.reviews');
    Route::get('/dashboard/users', [AdminController::class, 'dashboardUsers'])->name('dashboard.users');
    Route::get('/dashboard/export-excel', [AdminController::class, 'exportExcel'])->name('dashboard.export');

    // Set Theme Decoration
    Route::post('/set-theme', function (Illuminate\Http\Request $request) {
        $theme = $request->input('theme');
        $validThemes = ['auto', 'default', 'christmas', 'tet', 'valentine', 'halloween'];
        
        if (in_array($theme, $validThemes)) {
            session(['admin_theme_override' => $theme]);
            return response()->json(['success' => true, 'theme' => $theme]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid theme'], 400);
    })->name('set-theme');

    // Theme Management Page
    Route::get('/theme', function () {
        return view('admin.theme.index');
    })->name('theme.index');

    // Save Theme Settings
    Route::post('/theme/save-settings', function (Illuminate\Http\Request $request) {
        $theme = $request->input('theme');
        $settings = $request->input('settings');
        
        $validThemes = ['christmas', 'tet', 'valentine', 'halloween'];
        
        if (in_array($theme, $validThemes) && is_array($settings)) {
            $allSettings = session('theme_settings', []);
            $allSettings[$theme] = $settings;
            session(['theme_settings' => $allSettings]);
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
    })->name('theme.save-settings');

    // Resource Controllers
    Route::resource('books', AdminBookController::class);
    Route::post('books/{book}/approve', [AdminBookController::class, 'approve'])->name('books.approve');
    Route::resource('articles', ArticleController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::post('posts/{id}/approve-delete', [\App\Http\Controllers\Admin\PostController::class, 'approveDelete'])->name('posts.approve-delete');
    Route::post('posts/{id}/reject-delete', [\App\Http\Controllers\Admin\PostController::class, 'rejectDelete'])->name('posts.reject-delete');
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('banners', BannerController::class);
    Route::resource('badges', App\Http\Controllers\Admin\BadgeController::class);
    Route::resource('activity-titles', ActivityTitleController::class);
    Route::resource('challenges', \App\Http\Controllers\Admin\ChallengeController::class);
    Route::resource('avatar-frames', \App\Http\Controllers\Admin\AvatarFrameController::class);
    // Authors - dùng adminIndex() thay vì index() cho trang admin
    Route::get('authors', [AuthorController::class, 'adminIndex'])->name('authors.index');
    Route::get('authors/proxy-image', [AuthorController::class, 'proxyImage'])->name('authors.proxy-image');
    Route::get('authors/create', [AuthorController::class, 'create'])->name('authors.create');
    Route::post('authors', [AuthorController::class, 'store'])->name('authors.store');
    Route::get('authors/{author}/edit', [AuthorController::class, 'edit'])->name('authors.edit');
    Route::put('authors/{author}', [AuthorController::class, 'update'])->name('authors.update');
    Route::delete('authors/{author}', [AuthorController::class, 'destroy'])->name('authors.destroy');
    Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);

    // Subscribers Management
    Route::get('subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('subscribers/{subscriber}/toggle-active', [\App\Http\Controllers\Admin\SubscriberController::class, 'toggleActive'])->name('subscribers.toggle-active');
    Route::delete('subscribers/{subscriber}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::get('subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');

    // Activity Logs (xem + khôi phục)
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::post('/activity-logs/{activityLog}/restore', [\App\Http\Controllers\Admin\ActivityLogController::class, 'restore'])->name('activity-logs.restore');

    // Game / Gamification
    Route::get('/game', [\App\Http\Controllers\Admin\GameController::class, 'index'])->name('game.index');
    Route::post('/challenges/{challenge}/award-badge/{userId}', [\App\Http\Controllers\Admin\ChallengeController::class, 'awardBadge'])->name('challenges.award-badge');

    // Comment Reports
    Route::get('/comment-reports', [\App\Http\Controllers\Admin\CommentReportController::class, 'index'])->name('comment-reports.index');
    Route::get('/comment-reports/{commentReport}', [\App\Http\Controllers\Admin\CommentReportController::class, 'show'])->name('comment-reports.show');
    Route::post('/comment-reports/{commentReport}/approve', [\App\Http\Controllers\Admin\CommentReportController::class, 'approve'])->name('comment-reports.approve');
    Route::post('/comment-reports/{commentReport}/reject', [\App\Http\Controllers\Admin\CommentReportController::class, 'reject'])->name('comment-reports.reject');
    Route::delete('/comment-reports/{commentReport}', [\App\Http\Controllers\Admin\CommentReportController::class, 'destroy'])->name('comment-reports.destroy');

    // Post Reports (Báo cáo bài viết)
    Route::get('/post-reports', [\App\Http\Controllers\Admin\PostReportController::class, 'index'])->name('post-reports.index');
    Route::get('/post-reports/{postReport}', [\App\Http\Controllers\Admin\PostReportController::class, 'show'])->name('post-reports.show');
    Route::post('/post-reports/{postReport}/approve', [\App\Http\Controllers\Admin\PostReportController::class, 'approve'])->name('post-reports.approve');
    Route::post('/post-reports/{postReport}/reject', [\App\Http\Controllers\Admin\PostReportController::class, 'reject'])->name('post-reports.reject');
    Route::post('/post-reports/{postReport}/delete-post', [\App\Http\Controllers\Admin\PostReportController::class, 'deletePost'])->name('post-reports.delete-post');
    Route::delete('/post-reports/{postReport}', [\App\Http\Controllers\Admin\PostReportController::class, 'destroy'])->name('post-reports.destroy');

    // API Routes cho Polling Real-time
    Route::get('/api/pending-counts', [\App\Http\Controllers\Admin\ApiController::class, 'pendingCounts'])->name('api.pending-counts');
});
