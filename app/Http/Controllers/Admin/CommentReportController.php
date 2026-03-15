<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommentReport;
use App\Models\AdminActivityLog;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentReportController extends Controller
{
    /**
     * Hiển thị danh sách báo cáo bình luận
     */
    public function index(Request $request)
    {
        $query = CommentReport::with(['comment.user', 'comment.post', 'user', 'resolvedBy']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo lý do
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Sắp xếp: pending trước, mới nhất trước
        $reports = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.comment-reports.index', compact('reports'));
    }

    /**
     * Xem chi tiết báo cáo
     */
    public function show(CommentReport $commentReport)
    {
        $commentReport->load(['comment.user', 'comment.post.book', 'user', 'resolvedBy']);
        return view('admin.comment-reports.show', compact('commentReport'));
    }

    /**
     * Chấp thuận báo cáo - Xóa bình luận vi phạm
     */
    public function approve(Request $request, CommentReport $commentReport)
    {
        // Kiểm tra nếu đã xử lý
        if ($commentReport->status !== 'pending') {
            return back()->with('error', 'Báo cáo này đã được xử lý trước đó.');
        }

        $comment = $commentReport->comment;
        $commentContent = $comment ? $comment->content : 'Bình luận đã bị xóa';
        $commentUser = $comment && $comment->user ? $comment->user->name : 'Người dùng ẩn';

        // Cập nhật trạng thái báo cáo
        $commentReport->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Xóa bình luận vi phạm
        if ($comment) {
            $comment->delete();
        }

        // Ghi log
        AdminActivityLog::log(
            'approve',
            "Chấp thuận báo cáo và xóa bình luận của {$commentUser}",
            CommentReport::class,
            $commentReport->id,
            ['comment_content' => $commentContent],
            ['status' => 'approved']
        );

        // Gửi thông báo cho người báo cáo
        if ($commentReport->user) {
            $commentReport->user->notify(new ReportResolvedNotification([
                'status' => 'approved',
                'message' => "Báo cáo bình luận của bạn đã được chấp thuận. Bình luận vi phạm đã bị xóa.",
                'admin_note' => $request->admin_note,
                'post_title' => 'Bình luận',
                'link' => route('home'),
            ]));
        }

        return back()->with('success', 'Đã chấp thuận báo cáo và xóa bình luận vi phạm.');
    }

    /**
     * Từ chối báo cáo
     */
    public function reject(Request $request, CommentReport $commentReport)
    {
        // Kiểm tra nếu đã xử lý
        if ($commentReport->status !== 'pending') {
            return back()->with('error', 'Báo cáo này đã được xử lý trước đó.');
        }

        $commentReport->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Ghi log
        AdminActivityLog::log(
            'reject',
            "Từ chối báo cáo bình luận #" . $commentReport->comment_id,
            CommentReport::class,
            $commentReport->id,
            null,
            ['status' => 'rejected']
        );

        // Gửi thông báo cho người báo cáo
        if ($commentReport->user) {
            $commentReport->user->notify(new ReportResolvedNotification([
                'status' => 'rejected',
                'message' => "Báo cáo bình luận của bạn đã bị từ chối.",
                'admin_note' => $request->admin_note,
                'post_title' => 'Bình luận',
                'link' => route('home'),
            ]));
        }

        return back()->with('success', 'Đã từ chối báo cáo.');
    }

    /**
     * Xóa báo cáo
     */
    public function destroy(CommentReport $commentReport)
    {
        $reportId = $commentReport->id;
        $commentReport->delete();

        // Ghi log
        AdminActivityLog::log(
            'delete',
            "Xóa báo cáo bình luận #{$reportId}",
            CommentReport::class,
            $reportId,
            null,
            null
        );

        return back()->with('success', 'Đã xóa báo cáo.');
    }
}
