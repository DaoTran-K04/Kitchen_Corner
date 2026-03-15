<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Post;
use App\Models\AdminActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // THÔNG SỐ & BIỂU ĐỒ
        $totalReviews = Post::whereNotNull('book_id')->count();
        $postViews = Post::sum('view_count');
        $bookViews = Book::sum('view_count');
        $totalViews = $postViews + $bookViews;
        $pendingReviews = Post::where('status', 'pending')->whereNotNull('book_id')->count();
        $totalUsers = User::where('role', 'user')->count();

        // Lọc theo tháng/năm - định nghĩa trước để sử dụng cho biểu đồ
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        // Biểu đồ xử lý (12 tháng của năm được chọn)
        $labels = [];
        $dataReviews = [];
        $dataViews = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = "Th " . $month;
            $dataReviews[] = Post::whereYear('created_at', $selectedYear)->whereMonth('created_at', $month)->whereNotNull('book_id')->count();
            $dataViews[] = User::whereYear('created_at', $selectedYear)->whereMonth('created_at', $month)->count();
        }

        // Bảng xử lý (12 tháng của năm được chọn)
        $tableData = [];
        for ($month = 12; $month >= 1; $month--) {
            $tableData[] = [
                'month' => "T{$month}/{$selectedYear}",
                'reviews' => $dataReviews[$month - 1],
                'users' => $dataViews[$month - 1]
            ];
        }

        // Lấy danh sách Review trong tháng đã chọn
        $monthlyReviewsList = Post::with(['user', 'book'])
            ->whereNotNull('book_id')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(5, ['*'], 'page')
            ->withPath(route('admin.dashboard.reviews'))
            ->appends(['month' => $selectedMonth, 'year' => $selectedYear]);

        // Lấy danh sách User đăng ký trong tháng đã chọn
        $monthlyUsersList = User::where('role', 'user')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(5, ['*'], 'page')
            ->withPath(route('admin.dashboard.users'))
            ->appends(['month' => $selectedMonth, 'year' => $selectedYear]);

        return view('admin.dashboard', compact(
            'totalReviews',
            'totalViews',
            'postViews',
            'bookViews',
            'pendingReviews',
            'totalUsers',
            'labels',
            'dataReviews',
            'dataViews',
            'tableData',
            'selectedMonth',
            'selectedYear',
            'monthlyReviewsList',
            'monthlyUsersList'
        ));
    }

    /**
     * AJAX endpoint for loading reviews list
     */
    public function dashboardReviews(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        $monthlyReviewsList = Post::with(['user', 'book'])
            ->whereNotNull('book_id')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(5, ['*'], 'page')
            ->withPath(route('admin.dashboard.reviews'))
            ->appends(['month' => $selectedMonth, 'year' => $selectedYear]);

        return view('admin.partials.dashboard-reviews', compact(
            'monthlyReviewsList',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * AJAX endpoint for loading users list
     */
    public function dashboardUsers(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        $monthlyUsersList = User::where('role', 'user')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(5, ['*'], 'page')
            ->withPath(route('admin.dashboard.users'))
            ->appends(['month' => $selectedMonth, 'year' => $selectedYear]);

        return view('admin.partials.dashboard-users', compact(
            'monthlyUsersList',
            'selectedMonth',
            'selectedYear'
        ));
    }

    /**
     * Export dashboard data to Excel (CSV format compatible with Excel)
     */
    public function exportExcel(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));

        // Lấy dữ liệu Reviews của tháng
        $reviews = Post::with(['user', 'book'])
            ->withCount('likes')
            ->whereNotNull('book_id')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->get();

        // Lấy dữ liệu Users đăng ký trong tháng
        $users = User::where('role', 'user')
            ->withCount([
                'posts' => function ($query) {
                    $query->whereNotNull('book_id');
                }
            ])
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->get();

        // Top 5 sách được review nhiều nhất trong tháng
        $topBooks = Post::whereNotNull('book_id')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->select('book_id', DB::raw('COUNT(*) as review_count'))
            ->groupBy('book_id')
            ->orderByDesc('review_count')
            ->limit(5)
            ->with('book')
            ->get();

        // Thống kê bổ sung
        $totalViews = $reviews->sum('view_count');
        $totalLikes = $reviews->sum('likes_count');
        $totalComments = \App\Models\Comment::whereIn('post_id', $reviews->pluck('id'))
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        // Ghi log
        AdminActivityLog::log(
            'export',
            "Xuất báo cáo Excel tháng {$selectedMonth}/{$selectedYear} ({$reviews->count()} reviews, {$users->count()} users)"
        );

        // Tạo tên file
        $filename = "bao-cao-thang-{$selectedMonth}-{$selectedYear}.csv";

        // Tạo CSV content với BOM để Excel hiển thị tiếng Việt đúng
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reviews, $users, $topBooks, $selectedMonth, $selectedYear, $totalViews, $totalLikes, $totalComments) {
            $file = fopen('php://output', 'w');

            // BOM cho UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ======== TIÊU ĐỀ BÁO CÁO ========
            fputcsv($file, ["BÁO CÁO THỐNG KÊ THÁNG {$selectedMonth}/{$selectedYear}"]);
            fputcsv($file, ["Ngày xuất: " . date('d/m/Y H:i')]);
            fputcsv($file, []);

            // ======== PHẦN REVIEWS ========
            fputcsv($file, ["=== DANH SÁCH BÀI REVIEW ({$reviews->count()} bài) ==="]);
            fputcsv($file, ['STT', 'Tên sách', 'Người viết', 'Email', 'Đánh giá (sao)', 'Lượt xem', 'Lượt thích', 'Trạng thái', 'Ngày tạo']);

            foreach ($reviews as $index => $review) {
                fputcsv($file, [
                    $index + 1,
                    $review->book->title ?? 'Sách đã xóa',
                    $review->user->name ?? 'N/A',
                    $review->user->email ?? 'N/A',
                    $review->rating ? $review->rating . '/5' : 'Chưa đánh giá',
                    number_format($review->view_count ?? 0),
                    number_format($review->likes_count ?? 0),
                    $review->status == 'published' ? 'Đã duyệt' : 'Chờ duyệt',
                    $review->created_at->format('d/m/Y H:i'),
                ]);
            }

            // ======== PHẦN USERS ========
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ["=== THÀNH VIÊN MỚI ĐĂNG KÝ ({$users->count()} người) ==="]);
            fputcsv($file, ['STT', 'Họ tên', 'Email', 'Số bài viết', 'Ngày đăng ký']);

            foreach ($users as $index => $user) {
                fputcsv($file, [
                    $index + 1,
                    $user->name,
                    $user->email,
                    $user->posts_count ?? 0,
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            // ======== TOP 5 SÁCH ĐƯỢC REVIEW NHIỀU NHẤT ========
            if ($topBooks->count() > 0) {
                fputcsv($file, []);
                fputcsv($file, []);
                fputcsv($file, ["=== TOP 5 SÁCH ĐƯỢC REVIEW NHIỀU NHẤT ==="]);
                fputcsv($file, ['Hạng', 'Tên sách', 'Tác giả', 'Số lượt review']);

                foreach ($topBooks as $index => $item) {
                    if ($item->book) {
                        fputcsv($file, [
                            $index + 1,
                            $item->book->title,
                            $item->book->author_name ?? 'Không rõ',
                            $item->review_count,
                        ]);
                    }
                }
            }

            // ======== THỐNG KÊ TỔNG HỢP ========
            fputcsv($file, []);
            fputcsv($file, []);
            fputcsv($file, ["=== THỐNG KÊ TỔNG HỢP ==="]);
            fputcsv($file, ['Chỉ số', 'Số lượng']);
            fputcsv($file, ['Tổng bài review trong tháng', $reviews->count()]);
            fputcsv($file, ['Review đã duyệt', $reviews->where('status', 'published')->count()]);
            fputcsv($file, ['Review chờ duyệt', $reviews->where('status', 'pending')->count()]);
            fputcsv($file, ['Tổng lượt xem bài viết', number_format($totalViews)]);
            fputcsv($file, ['Tổng lượt thích', number_format($totalLikes)]);
            fputcsv($file, ['Tổng bình luận mới', number_format($totalComments)]);
            fputcsv($file, ['Thành viên mới đăng ký', $users->count()]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
