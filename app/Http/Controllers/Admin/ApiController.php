<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Book;
use App\Models\PostReport;
use App\Models\CommentReport;

class ApiController extends Controller
{
    /**
     * Get pending counts for real-time polling
     */
    public function pendingCounts()
    {
        $postsPending = Post::whereNotNull('book_id')->where('status', 'pending')->count();
        $postsPendingDelete = Post::whereNotNull('book_id')->where('status', 'pending_delete')->count();
        $booksPending = Book::where('is_approved', false)->count();
        $postReports = PostReport::where('status', 'pending')->count();
        $commentReports = CommentReport::where('status', 'pending')->count();

        return response()->json([
            'posts_pending' => $postsPending,
            'posts_pending_delete' => $postsPendingDelete,
            'books_pending' => $booksPending,
            'post_reports' => $postReports,
            'comment_reports' => $commentReports,
            'total_pending' => $postsPending + $postsPendingDelete + $booksPending + $postReports + $commentReports,
        ]);
    }
}

