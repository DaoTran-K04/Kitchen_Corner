<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Comment;
use App\Models\AdminActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // THÔNG SỐ & BIỂU ĐỒ
        $totalReviews  = Recipe::where('status', 'published')->count();
        $postViews     = Recipe::where('status', 'published')->sum('view_count');
        $bookViews     = 0; // không còn dùng
        $totalViews    = $postViews;
        $pendingReviews = Recipe::where('status', 'pending')->count();
        $totalUsers    = User::where('role', 'user')->count();

        // Lọc theo tháng/năm
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear  = $request->input('year', date('Y'));

        // Biểu đồ (12 tháng của năm được chọn)
        $labels      = [];
        $dataReviews = [];
        $dataViews   = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[]      = "Th " . $month;
            $dataReviews[] = Recipe::whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $month)
                ->count();
            $dataViews[]   = User::whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $month)
                ->count();
        }

        // Bảng (12 tháng của năm được chọn)
        $tableData = [];
        for ($month = 12; $month >= 1; $month--) {
            $tableData[] = [
                'month'   => "T{$month}/{$selectedYear}",
                'reviews' => $dataReviews[$month - 1],
                'users'   => $dataViews[$month - 1],
            ];
        }

        // Danh sách công thức trong tháng được chọn
        $monthlyReviewsList = Recipe::with(['user', 'category'])
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->paginate(5, ['*'], 'page')
            ->withPath(route('admin.dashboard.reviews'))
            ->appends(['month' => $selectedMonth, 'year' => $selectedYear]);

        // Danh sách user đăng ký trong tháng được chọn
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

    /** AJAX: danh sách công thức theo tháng */
    public function dashboardReviews(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear  = $request->input('year', date('Y'));

        $monthlyReviewsList = Recipe::with(['user', 'category'])
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

    /** AJAX: danh sách user theo tháng */
    public function dashboardUsers(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear  = $request->input('year', date('Y'));

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

    /** Xuất Excel (CSV) */
    public function exportExcel(Request $request)
    {
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear  = $request->input('year', date('Y'));

        $recipes = Recipe::with('user')
            ->withCount('comments')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->get();

        $users = User::where('role', 'user')
            ->withCount('recipes')
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->latest()
            ->get();

        // Top 5 công thức được xem nhiều nhất trong tháng
        $topRecipes = Recipe::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        $totalViews    = $recipes->sum('view_count');
        $totalLikes    = \App\Models\Like::whereIn('recipe_id', $recipes->pluck('id'))->count();
        $totalComments = Comment::whereIn('recipe_id', $recipes->pluck('id'))
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->count();

        AdminActivityLog::log(
            'export',
            "Xuất báo cáo Excel tháng {$selectedMonth}/{$selectedYear} ({$recipes->count()} công thức, {$users->count()} users)"
        );

        $filename = "bao-cao-thang-{$selectedMonth}-{$selectedYear}.xls";
        
        return response()->view('admin.exports.statistics', compact(
            'recipes', 'users', 'topRecipes', 'selectedMonth', 'selectedYear', 
            'totalViews', 'totalLikes', 'totalComments'
        ))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
