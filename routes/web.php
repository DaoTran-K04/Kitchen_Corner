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
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ActivityTitleController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\RecipeController;

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\FollowController;

// ====================================================
// 1. NHÓM PUBLIC (Ai cũng xem được)
// ====================================================

Route::get('/test-nutrition', function () {
    return view('test-nutrition');
});


// ── ROUTES CÔNG THỨC (Ưu tiên hàng đầu để tránh lỗi 404) ───────────────────────
// Removed {slug} routes from here to place them after specific routes

// Route setup hosting (fix storage symlink and cache)
Route::get('/setup-hosting', function () {
    try {
        // 1. Xử lý Symlink
        $storagePath = public_path('storage');
        if (file_exists($storagePath) || is_link($storagePath)) {
            if (PHP_OS_FAMILY === 'Windows') {
                exec('rmdir /s /q "' . $storagePath . '"');
            } else {
                @unlink($storagePath);
                @rmdir($storagePath);
            }
        }
        \Illuminate\Support\Facades\Artisan::call('storage:link');

        // 2. Clear Cache Sâu
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        
        // Bắt buộc xóa key cache của Slide Banner
        \Illuminate\Support\Facades\Cache::forget('hero_slides');
        
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }


        // 3. Đổ dữ liệu thật (KitchenCornerRecipeSeeder)
        // Lưu ý: Lệnh này sẽ xóa các công thức cũ và thay bằng bộ dữ liệu chuẩn
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'KitchenCornerRecipeSeeder']);

        // 4. ÉP BUỘC CẬP NHẬT ĐỦ 4 BANNER CHO HOSTING BẤT CHẤP CACHE/OPCACHE
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('banners')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $banners = [
                [
                    'title' => 'Lễ hội Ẩm thực Mùa Hè 2026',
                    'tag' => 'Sự kiện Hot',
                    'description' => 'Tham gia ngay chuỗi sự kiện ẩm thực đường phố và nhận hàng ngàn voucher quà tặng hấp dẫn từ các đầu bếp hàng đầu.',
                    'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974&auto=format&fit=crop',
                    'link' => '#',
                    'is_active' => true,
                    'order' => 1
                ],
                [
                    'title' => 'Cuộc thi: Đầu bếp tại gia xuất sắc',
                    'tag' => 'Khám phá',
                    'description' => 'Trổ tài nấu nướng đỉnh cao, chia sẻ công thức độc quyền và nhận cúp vàng danh giá cùng phần thưởng 50.000.000đ.',
                    'image' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2070&auto=format&fit=crop',
                    'link' => '#',
                    'is_active' => true,
                    'order' => 2
                ],
                [
                    'title' => 'Bí quyết nấu ăn chay thanh lọc cơ thể',
                    'tag' => 'Mẹo hay',
                    'description' => 'Khám phá các công thức chay độc đáo, đủ dưỡng chất từ đầu bếp 5 sao giúp thanh mát cơ thể và tràn đầy năng lượng mỗi ngày.',
                    'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=2070&auto=format&fit=crop',
                    'link' => '#',
                    'is_active' => true,
                    'order' => 3
                ],
                [
                    'title' => 'Hương vị Việt Nam: Đậm đà bản sắc',
                    'tag' => 'Ẩm thực Việt',
                    'description' => 'Tổng hợp hàng trăm công thức món Việt truyền thống chuẩn vị ba miền từ phở, bún chả đến cơm tấm sườn bì chả.',
                    'image' => 'https://images.unsplash.com/photo-1596797038530-2c107229654b?q=80&w=2070&auto=format&fit=crop',
                    'link' => '#',
                    'is_active' => true,
                    'order' => 4
                ]
            ];

            foreach ($banners as $b) {
                \App\Models\Banner::create($b);
            }
        } catch (\Exception $ex) {
            // Im lặng hoặc log lỗi nếu có
        }

        return 'Hosting setup successfully! <br> - Storage link recreated <br> - Cache cleared <br> - <b>Real Recipes & 4 Banners Imported Successfully!</b> <br><br> Go back to <a href="'.url('/').'">Home</a>';
    } catch (\Exception $e) {
        return 'Failed to setup hosting: ' . $e->getMessage();
    }
});

// Route dọn dẹp dung lượng hosting (Xóa file nén cũ, log cũ) để giải phóng bộ nhớ
Route::get('/clean-hosting', function () {
    $results = [];
    $totalFreed = 0;
    $basePath = base_path();
    
    // 1. Tìm và xóa các file zip/rar/tar.gz thừa ở thư mục gốc và public
    $directories = [$basePath, $basePath . '/public', $basePath . '/public/assets'];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) continue;
        $files = scandir($dir);
        foreach ($files as $file) {
            $filePath = $dir . '/' . $file;
            if (is_file($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if (in_array($ext, ['zip', 'rar', 'tar', 'gz', 'tgz', '7z'])) {
                    // Tránh xóa file zip mới nếu nó hợp lệ
                    if ($file === 'recipes.zip' && filesize($filePath) > 10 * 1024 * 1024) {
                        continue;
                    }
                    $size = filesize($filePath);
                    if (@unlink($filePath)) {
                        $results[] = "Đã xóa file nén thừa: {$file} (" . round($size / 1024 / 1024, 2) . " MB)";
                        $totalFreed += $size;
                    }
                }
            }
        }
    }
    
    // 2. Xóa các file log của Laravel
    $logDir = storage_path('logs');
    if (is_dir($logDir)) {
        $files = scandir($logDir);
        foreach ($files as $file) {
            $filePath = $logDir . '/' . $file;
            if (is_file($filePath) && str_ends_with($file, '.log')) {
                $size = filesize($filePath);
                if (@unlink($filePath)) {
                    $results[] = "Đã xóa file log: {$file} (" . round($size / 1024 / 1024, 2) . " MB)";
                    $totalFreed += $size;
                }
            }
        }
    }

    // 3. Xóa các cache tạm
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Cache::forget('hero_slides');

    // ÉP BUỘC CẬP NHẬT ĐỦ 4 BANNER CHO HOSTING BẤT CHẤP CACHE/OPCACHE KHI DỌN DẸP
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('banners')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $banners = [
            [
                'title' => 'Lễ hội Ẩm thực Mùa Hè 2026',
                'tag' => 'Sự kiện Hot',
                'description' => 'Tham gia ngay chuỗi sự kiện ẩm thực đường phố và nhận hàng ngàn voucher quà tặng hấp dẫn từ các đầu bếp hàng đầu.',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974&auto=format&fit=crop',
                'link' => '#',
                'is_active' => true,
                'order' => 1
            ],
            [
                'title' => 'Cuộc thi: Đầu bếp tại gia xuất sắc',
                'tag' => 'Khám phá',
                'description' => 'Trổ tài nấu nướng đỉnh cao, chia sẻ công thức độc quyền và nhận cúp vàng danh giá cùng phần thưởng 50.000.000đ.',
                'image' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?q=80&w=2070&auto=format&fit=crop',
                'link' => '#',
                'is_active' => true,
                'order' => 2
            ],
            [
                'title' => 'Bí quyết nấu ăn chay thanh lọc cơ thể',
                'tag' => 'Mẹo hay',
                'description' => 'Khám phá các công thức chay độc đáo, đủ dưỡng chất từ đầu bếp 5 sao giúp thanh mát cơ thể và tràn đầy năng lượng mỗi ngày.',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=2070&auto=format&fit=crop',
                'link' => '#',
                'is_active' => true,
                'order' => 3
            ],
            [
                'title' => 'Hương vị Việt Nam: Đậm đà bản sắc',
                'tag' => 'Ẩm thực Việt',
                'description' => 'Tổng hợp hàng trăm công thức món Việt truyền thống chuẩn vị ba miền từ phở, bún chả đến cơm tấm sườn bì chả.',
                'image' => 'https://images.unsplash.com/photo-1596797038530-2c107229654b?q=80&w=2070&auto=format&fit=crop',
                'link' => '#',
                'is_active' => true,
                'order' => 4
            ]
        ];

        foreach ($banners as $b) {
            \App\Models\Banner::create($b);
        }
    } catch (\Exception $ex) {
        // Im lặng
    }
    
    // 4. CHẨN ĐOÁN HỆ THỐNG BANNER (DIAGNOSTICS)
    $bannerCount = \App\Models\Banner::count();
    $seederPath = base_path('database/seeders/KitchenCornerRecipeSeeder.php');
    $seederStatus = "Không tìm thấy file seeder!";
    $hasNewBannersCode = "KHÔNG";
    
    if (file_exists($seederPath)) {
        $content = file_get_contents($seederPath);
        if (str_contains($content, 'Bí quyết nấu ăn chay thanh lọc cơ thể') && str_contains($content, 'Hương vị Việt Nam: Đậm đà bản sắc')) {
            $hasNewBannersCode = "CÓ (Đã chứa logic 4 Banner mới)";
        } else {
            $hasNewBannersCode = "KHÔNG (File seeder trên host vẫn là bản cũ chỉ có 2 Banner)";
        }
        $seederStatus = "File seeder tồn tại tại: {$seederPath}";
    }

    $freedMb = round($totalFreed / 1024 / 1024, 2);
    $output = "<div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>";
    $output .= "<h2 style='color: #2e7d32;'>🧹 Dọn dẹp dung lượng hosting thành công!</h2>";
    if (empty($results)) {
        $output .= "<p>Không tìm thấy file rác, file nén cũ hoặc log cũ nào chiếm dụng bộ nhớ.</p>";
    } else {
        $output .= "<ul style='padding-left: 20px;'><li>" . implode("</li><li>", $results) . "</li></ul>";
        $output .= "<p><strong>Tổng dung lượng đã giải phóng: <span style='color: #d32f2f;'>{$freedMb} MB</span></strong></p>";
    }
    
    // Hộp chẩn đoán lỗi hiển thị
    $output .= "<div style='background: #f5f5f5; padding: 15px; border: 1px solid #ccc; border-radius: 6px; margin: 20px 0;'>";
    $output .= "<h3 style='margin-top: 0; color: #1976d2;'>🔍 Chẩn đoán hệ thống Banner Slider:</h3>";
    $output .= "<p>• Số lượng Banner trong CSDL trên host: <b>{$bannerCount}</b></p>";
    $output .= "<p>• Trạng thái file seeder trên host: <i>{$seederStatus}</i></p>";
    $output .= "<p>• File seeder trên host đã chứa code 4 Banner mới chưa: <b style='color: " . ($hasNewBannersCode[0] === 'C' ? 'green' : 'red') . ";'>{$hasNewBannersCode}</b></p>";
    $output .= "</div>";
    
    $output .= "<p style='background: #e8f5e9; padding: 10px; border-left: 4px solid #2e7d32; border-radius: 4px;'><b>Bây giờ bạn hãy thử:</b><br>1. Tải lên lại file <b>recipes.zip</b> đã được tối ưu siêu nhỏ (12.2MB).<br>2. Tiến hành giải nén (Extract) trực tiếp trên hosting!</p>";
    $output .= '<br><a href="'.url('/').'" style="display: inline-block; background: #2e7d32; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Về trang chủ</a>';
    $output .= "</div>";
    
    return $output;
});

// Route serve file storage (bypass symlink issue on Windows)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mimeType]);
})->where('path', '.*');

// Trang bị khóa
Route::get('/banned', function () {
    return view('errors.banned');
})->name('banned');

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Trang tĩnh (Footer)
Route::view('/ve-chung-toi', 'pages.about')->name('page.about');
Route::view('/dieu-khoan-su-dung', 'pages.terms')->name('page.terms');
Route::view('/chinh-sach-bao-mat', 'pages.privacy')->name('page.privacy');
Route::view('/lien-he', 'pages.contact')->name('page.contact');
Route::post('/lien-he', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');

// AJAX Live Search – Tìm kiếm công thức theo từ khoá
Route::get('/ajax-search', function (Request $request) {
    $keyword = $request->get('keyword');
    if (!$keyword || mb_strlen($keyword, 'UTF-8') < 2) return response()->json([]);

    $lowerKeyword = mb_strtolower($keyword, 'UTF-8');

    $recipes = App\Models\Recipe::where('status', 'published')
        ->where(function ($q) use ($lowerKeyword) {
            $q->whereRaw('LOWER(title) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"])
              ->orWhereRaw('LOWER(description) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"])
              ->orWhereHas('category', function($cat) use ($lowerKeyword) {
                  $cat->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
              })
              ->orWhereHas('ingredients', function($ing) use ($lowerKeyword) {
                  $ing->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerKeyword}%"]);
              });
        })
        ->select('id', 'title', 'slug', 'image', 'cooking_time', 'difficulty')
        ->orderBy('view_count', 'desc')
        ->limit(8)
        ->get();

    $recipes->each->append('thumbnail');

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
Route::get('/tim-kiem', [RecipeController::class, 'search'])->name('recipes.search');
Route::get('/danh-sach', [RecipeController::class, 'index'])->name('books.list'); // alias

// ── ROUTES CÔNG THỨC CHI TIẾT (Phải đặt sau dang-bai để tránh nuốt route) ──
Route::get('/cong-thuc/{slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/cong thuc/{slug}', [RecipeController::class, 'show']); 
Route::get('/cong%20thuc/{slug}', [RecipeController::class, 'show']);
// ── ROUTES TÁC GIẢ / THÀNH VIÊN ───────────────────────────────────────
Route::get('/tac-gia', [AuthorController::class, 'index'])->name('authors.index');

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

    // ── ROUTES ADMIN CŨ ──────────────────────────────────────────────────
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
    // Banner Management
    Route::resource('banners', BannerController::class);

    // Feedback Management
    Route::resource('feedbacks', \App\Http\Controllers\Admin\FeedbackController::class)->except(['create', 'store', 'edit', 'update']);
    Route::patch('feedbacks/{feedback}/status', [\App\Http\Controllers\Admin\FeedbackController::class, 'updateStatus'])->name('feedbacks.updateStatus');
    Route::resource('badges', App\Http\Controllers\Admin\BadgeController::class);
    Route::resource('activity-titles', ActivityTitleController::class);
    Route::resource('challenges', \App\Http\Controllers\Admin\ChallengeController::class);
    Route::resource('avatar-frames', \App\Http\Controllers\Admin\AvatarFrameController::class);
    Route::resource('ingredients', \App\Http\Controllers\Admin\IngredientController::class);
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/toggle-role', [\App\Http\Controllers\Admin\UserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::delete('/users/{user}/badges/{badge}', [\App\Http\Controllers\Admin\UserController::class, 'revokeBadge'])->name('users.revoke-badge');
    Route::delete('/users/{user}/challenges/{challenge}', [\App\Http\Controllers\Admin\UserController::class, 'resetChallenge'])->name('users.reset-challenge');

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

    // AI Moderation Logs
    Route::get('/ai-moderation', [\App\Http\Controllers\Admin\AiModerationLogController::class, 'index'])->name('ai-moderation.index');

    // Chatbot Settings
    Route::get('/chatbot-settings', [\App\Http\Controllers\Admin\ChatbotSettingController::class, 'index'])->name('chatbot-settings.index');
    Route::post('/chatbot-settings', [\App\Http\Controllers\Admin\ChatbotSettingController::class, 'update'])->name('chatbot-settings.update');

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
