<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\CommentReport;

class ApiController extends Controller
{
    /** Lấy số lượng badge chờ xử lý (real-time polling) */
    public function pendingCounts()
    {
        $postsPending       = Recipe::where('status', 'pending')->count();
        $postsPendingDelete = 0; // không còn pending_delete trong Recipe
        $booksPending       = 0; // Book model không còn tồn tại
        $postReports        = 0; // PostReport model không còn tồn tại
        $commentReports     = CommentReport::where('status', 'pending')->count();

        return response()->json([
            'posts_pending'        => $postsPending,
            'posts_pending_delete' => $postsPendingDelete,
            'books_pending'        => $booksPending,
            'post_reports'         => $postReports,
            'comment_reports'      => $commentReports,
            'total_pending'        => $postsPending + $commentReports,
        ]);
    }
}
