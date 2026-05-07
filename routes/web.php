<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FollowController;
// use App\Http\Controllers\CommentController; // Controller chưa được tạo
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ActivityTitleController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\RecipeController;

// ── CONTROLLERS CHƯA DÙNG (Mở khóa khi cần) ──────────────────────────────────
// use App\Models\Book;
// use App\Http\Controllers\BookController;
// use App\Http\Controllers\PostController;
// use App\Http\Controllers\RankingController;
// use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\AuthorController;
// use App\Http\Controllers\BookSuggestionController;

// ====================================================
// 1. NHÓM PUBLIC (Ai cũng xem được)
// ====================================================

// Route serve file storage (bypass symlink issue on Windows)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
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

// AJAX Live Search – Tìm kiếm công thức theo từ khoá
Route::get('/ajax-search', function (Request $request) {
    $keyword = $request->get('keyword');
    if (!$keyword || strlen($keyword) < 2) return response()->json([]);

    $recipes = App\Models\Recipe::where('status', 'published')
        ->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhereHas('category', function($cat) use ($keyword) {
                  $cat->where('name', 'like', "%{$keyword}%");
              })
              ->orWhereHas('ingredients', function($ing) use ($keyword) {
                  $ing->where('name', 'like', "%{$keyword}%");
              });
        })
        ->select('id', 'title', 'slug', 'image', 'cooking_time', 'difficulty')
        ->orderBy('view_count', 'desc')
        ->limit(8)
        ->get();

    return response()->json($recipes);
})->name('ajax.search');

// Chatbot AI (Gemini API)
Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::get('/api/chatbot/history', [ChatbotController::class, 'getHistory'])->name('chatbot.history');
Route::delete('/api/chatbot/history', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

// Newsletter Subscription
Route::post('/subscribe', [SubscriberController::class, 'store'])->name('subscribe');

// Trang danh sách Tạp Chí (Public)
Route::get('/tap-chi', [ArticleController::class, 'publicIndex'])->name('articles.index');
// Trang chi tiết bài viết Tạp chí
Route::get('/tap-chi/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// ── ROUTES CÔNG THỨC (ĐÃ MỞ KHÓA) ──────────────────────────────────────────
Route::get('/cong-thuc', [RecipeController::class, 'index'])->name('recipes.list');
Route::get('/cong-thuc/dang-bai', [RecipeController::class, 'create'])->middleware(['auth', 'email.verified'])->name('recipes.create');
Route::get('/cong-thuc/{slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/tim-kiem', [RecipeController::class, 'search'])->name('recipes.search');
Route::get('/danh-sach', [RecipeController::class, 'index'])->name('books.list'); // alias

// ── ROUTES TÁC GIẢ / THÀNH VIÊN ───────────────────────────────────────
Route::get('/tac-gia', [AuthorController::class, 'index'])->name('authors.index');

// ── ROUTES CŨ ĐÃ KHOÁ ─────────────────────────────────────────────────────────
// Route::get('/danh-sach', [BookController::class, 'list'])->name('books.list');
// Route::get('/review-search', [BookController::class, 'search'])->name('books.search');
// Route::get('/tac-gia/{slug}', [AuthorController::class, 'show'])->name('authors.show');
// Route::get('/chi-tiet/{slug}', [BookController::class, 'show'])->name('detail');
// Route::get('/chi-tiet/{slug}/danh-gia', [BookController::class, 'showReviews'])->name('book.reviews');
// Route::get('/book/{slug}', [BookController::class, 'show'])->name('book.show');
// Route::get('/ranking/top-liked', [RankingController::class, 'topLikedPosts']);

// API Public: Follow
Route::get('/api/user/{id}/followers', [FollowController::class, 'getFollowers']);
Route::get('/api/user/{id}/following', [FollowController::class, 'getFollowing']);

// Public Profile (không cần đăng nhập)
Route::get('/thanh-vien/{id}', [ProfileController::class, 'index'])->name('public.profile');

// Thử Thách
Route::get('/thu-thach', [ChallengeController::class, 'index'])->name('challenges.index');

// Theme Switcher Client
Route::post('/set-site-theme', function (Request $request) {
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Unauthorized']);
    }
    if (in_array($request->theme, ['auto', 'default', 'christmas', 'tet', 'valentine', 'halloween'])) {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'active_theme'],
            ['value' => $request->theme]
        );
        \Illuminate\Support\Facades\Cache::forget('active_theme');
    }
    return response()->json(['success' => true]);
})->name('theme.switch');


// Vietnamese URL aliases (người dùng Việt thân thiện)
Route::get('/dang-nhap', fn() => redirect()->route('login'))->name('dang-nhap');
Route::get('/dang-ky', fn() => redirect()->route('register'))->name('dang-ky');
Route::get('/admin', fn() => auth()->check() && auth()->user()->role === 'admin'
    ? redirect()->route('admin.dashboard')
    : redirect()->route('login')
)->name('admin.root');

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
// 2.1 QUÊN MẬT KHẨU
// ====================================================
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');
Route::get('/verify-code', [AuthController::class, 'showVerifyCodeForm'])->name('password.verify.form');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('password.verify');
Route::post('/resend-code', [AuthController::class, 'resendCode'])->name('password.resend');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ====================================================
// 2.5 XÁC THỰC EMAIL (OTP)
// ====================================================
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verify', [AuthController::class, 'verifyRegistrationCode'])
    ->middleware('auth')->name('verification.verify');

Route::post('/email/resend-code', [AuthController::class, 'resendRegistrationCode'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ====================================================
// 3. NHÓM THÀNH VIÊN (AUTH + EMAIL VERIFIED)
// ====================================================
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    // --- LIKE & COMMENT (AJAX) ---
    Route::post('/like', [HomeController::class, 'toggleLike'])->name('handle.like');
    Route::post('/comment/{id}/reply', [HomeController::class, 'storeReply'])->name('comment.reply');
    Route::put('/comment/{id}', [HomeController::class, 'updateComment'])->name('comment.update');
    Route::delete('/comment/{id}', [HomeController::class, 'deleteComment'])->name('comment.destroy');
    
    // --- REPORTING ---
    Route::post('/report/post/{id}', [\App\Http\Controllers\ReportController::class, 'reportPost'])->name('report.post');
    Route::post('/report/comment/{id}', [\App\Http\Controllers\ReportController::class, 'reportComment'])->name('report.comment');
});

Route::middleware(['auth', 'email.verified'])->group(function () {

    // --- ĐỔI MẬT KHẨU ---
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change.password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password.post');

    // --- PROFILE & FOLLOW ---
    Route::get('/profile/{id?}', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar-frame/equip', [ProfileController::class, 'equipAvatarFrame'])->name('profile.avatar-frame.equip');
    Route::post('/profile/avatar-frame/unequip', [ProfileController::class, 'unequipAvatarFrame'])->name('profile.avatar-frame.unequip');
    Route::post('/profile/badges/order', [ProfileController::class, 'updateBadgeOrder'])->name('profile.badges.order');
    Route::post('/follow/toggle', [FollowController::class, 'toggleFollow'])->name('follow.toggle');

    // --- THÔNG BÁO ---
    Route::get('/notifications/read-all', [HomeController::class, 'markAllAsRead'])->name('notification.readAll');
    Route::get('/notifications/{id}', [HomeController::class, 'markAsRead'])->name('notification.read');
    Route::get('/api/notifications', [HomeController::class, 'getNotifications'])->name('api.notifications');



    // --- RECIPE COMMENTS (khi RecipeController sẵn sàng) ---
    // Route::post('/recipes/{id}/comment', [CommentController::class, 'store'])->name('recipe.comment');

    // --- CHALLENGES ---
    Route::post('/challenge/join/{id}', [ChallengeController::class, 'join'])->name('challenge.join');

    // ── ROUTES CÔNG THỨC (ĐÃ MỞ KHÓA) ────────────────────────────────────

    Route::post('/cong-thuc/store', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/cong-thuc/{id}/chinh-sua', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/cong-thuc/{id}/update', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/cong-thuc/{id}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::post('/cong-thuc/{id}/comment', [RecipeController::class, 'storeComment'])->name('recipes.comment');
    Route::post('/bo-suu-tap/toggle', [RecipeController::class, 'toggleBookmark'])->name('recipes.bookmark');
    Route::get('/tim-kiem-nguyen-lieu', [RecipeController::class, 'smartSearch'])->name('recipes.smart-search'); // 🔒 Chỉ thành viên đã xác thực

    // ── ROUTES CŨ ĐÃ KHOÁ ──────────────────────────────────────────────────
    // Route::get('/sach/de-xuat', [BookSuggestionController::class, 'create'])->name('books.suggest');
    // Route::post('/sach/de-xuat', [BookSuggestionController::class, 'store'])->name('books.suggest.store');
    // Route::post('/post/save', [HomeController::class, 'toggleSavePost'])->name('post.save');
    // Route::post('/posts/{id}/comment', [PostController::class, 'postComment'])->name('posts.comment');
    // Route::post('/post/{post_id}/comment', [CommentController::class, 'store'])->name('post.comment');

    // Route::get('/reviews/viet-bai', ...)->name('reviews.create');
    // Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');
    // Route::get('/reviews/{id}/chinh-sua', [PostController::class, 'edit'])->name('reviews.edit');
    // Route::put('/reviews/{id}/update', [PostController::class, 'update'])->name('reviews.update');
    // Route::post('/reviews/{id}/request-delete', [PostController::class, 'requestDelete'])->name('reviews.request-delete');
    // Route::get('/profile/{id}/reviews', [ProfileController::class, 'allReviews'])->name('profile.reviews');
    // Route::get('/profile/{id}/suggested-books', [ProfileController::class, 'allSuggestedBooks'])->name('profile.suggested-books');
});


// ====================================================
// 4. NHÓM ADMIN (AUTH + ADMIN REQUIRED)
// ====================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/reviews', [AdminController::class, 'dashboardReviews'])->name('dashboard.reviews');
    Route::get('/dashboard/users', [AdminController::class, 'dashboardUsers'])->name('dashboard.users');
    Route::get('/dashboard/export-excel', [AdminController::class, 'exportExcel'])->name('dashboard.export');

    // Theme
    Route::post('/set-theme', function (Request $request) {
        $theme = $request->input('theme');
        $validThemes = ['auto', 'default', 'christmas', 'tet', 'valentine', 'halloween'];
        if (in_array($theme, $validThemes)) {
            \App\Models\Setting::updateOrCreate(['key' => 'active_theme'], ['value' => $theme]);
            \Illuminate\Support\Facades\Cache::forget('active_theme');
            return response()->json(['success' => true, 'theme' => $theme]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid theme'], 400);
    })->name('set-theme');

    Route::get('/theme', function () {
        return view('admin.theme.index');
    })->name('theme.index');

    Route::post('/theme/save-settings', function (Request $request) {
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

    // Resource Controllers (đang hoạt động)
    Route::resource('articles', ArticleController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('badges', App\Http\Controllers\Admin\BadgeController::class);
    Route::resource('activity-titles', ActivityTitleController::class);
    Route::resource('challenges', \App\Http\Controllers\Admin\ChallengeController::class);
    Route::resource('avatar-frames', \App\Http\Controllers\Admin\AvatarFrameController::class);
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/toggle-role', [\App\Http\Controllers\Admin\UserController::class, 'toggleRole'])->name('users.toggle-role');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::post('/activity-logs/{activityLog}/restore', [\App\Http\Controllers\Admin\ActivityLogController::class, 'restore'])->name('activity-logs.restore');

    // Gamification
    Route::get('/game', [\App\Http\Controllers\Admin\GameController::class, 'index'])->name('game.index');
    Route::post('/challenges/{challenge}/award-badge/{userId}', [\App\Http\Controllers\Admin\ChallengeController::class, 'awardBadge'])->name('challenges.award-badge');

    // Comment Reports
    Route::get('/comment-reports', [\App\Http\Controllers\Admin\CommentReportController::class, 'index'])->name('comment-reports.index');
    Route::get('/comment-reports/{commentReport}', [\App\Http\Controllers\Admin\CommentReportController::class, 'show'])->name('comment-reports.show');
    Route::post('/comment-reports/{commentReport}/approve', [\App\Http\Controllers\Admin\CommentReportController::class, 'approve'])->name('comment-reports.approve');
    Route::post('/comment-reports/{commentReport}/reject', [\App\Http\Controllers\Admin\CommentReportController::class, 'reject'])->name('comment-reports.reject');
    Route::delete('/comment-reports/{commentReport}', [\App\Http\Controllers\Admin\CommentReportController::class, 'destroy'])->name('comment-reports.destroy');

    // Subscribers
    Route::get('subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('subscribers/{subscriber}/toggle-active', [\App\Http\Controllers\Admin\SubscriberController::class, 'toggleActive'])->name('subscribers.toggle-active');
    Route::delete('subscribers/{subscriber}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::get('subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');

    // API Real-time
    Route::get('/api/pending-counts', [\App\Http\Controllers\Admin\ApiController::class, 'pendingCounts'])->name('api.pending-counts');

    // ── ADMIN RECIPES (ĐÃ MỞ KHÓA) ─────────────────────────────────────────
    Route::resource('recipes', \App\Http\Controllers\Admin\RecipeController::class)->only(['index', 'show', 'destroy']);
    Route::post('recipes/{recipe}/approve', [\App\Http\Controllers\Admin\RecipeController::class, 'approve'])->name('recipes.approve');
    Route::post('recipes/{recipe}/feature', [\App\Http\Controllers\Admin\RecipeController::class, 'feature'])->name('recipes.feature');


    // Route::resource('books', \App\Http\Controllers\Admin\BookController::class);   // Chưa có table books
    // Route::post('books/{book}/approve', [\App\Http\Controllers\Admin\BookController::class, 'approve'])->name('books.approve');
    Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);
    Route::post('recipes/{recipe}/reject', [\App\Http\Controllers\Admin\RecipeController::class, 'reject'])->name('recipes.reject');
    // Route::get('authors', [\App\Http\Controllers\Admin\AuthorController::class, 'adminIndex'])->name('authors.index'); // Chưa có controller


});
