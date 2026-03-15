<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostReport;
use App\Models\AdminActivityLog;
use App\Notifications\ReportResolvedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReportController extends Controller
{
    /**
     * Hiển thị danh sách báo cáo bài viết
     */
    public function index(Request $request)
    {
        $query = PostReport::with(['post.user', 'post.book', 'user', 'resolvedBy']);

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

        return view('admin.post-reports.index', compact('reports'));
    }

    /**
     * Xem chi tiết báo cáo
     */
    public function show(PostReport $postReport)
    {
        $postReport->load(['post.user', 'post.book', 'user', 'resolvedBy']);
        return view('admin.post-reports.show', compact('postReport'));
    }

    /**
     * Chấp thuận báo cáo - Ẩn bài viết vi phạm
     */
    public function approve(Request $request, PostReport $postReport)
    {
        // Kiểm tra nếu đã xử lý
        if ($postReport->status !== 'pending') {
            return back()->with('error', 'Báo cáo này đã được xử lý trước đó.');
        }

        $post = $postReport->post;
        $postTitle = $post ? $post->title : 'Bài viết đã bị xóa';
        $postUser = $post && $post->user ? $post->user->name : 'Người dùng ẩn';

        // Cập nhật trạng thái báo cáo
        $postReport->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Ẩn bài viết vi phạm (đổi status thành hidden)
        if ($post) {
            $post->update(['status' => 'hidden']);
        }

        // Ghi log
        AdminActivityLog::log(
            'approve',
            "Chấp thuận báo cáo và ẩn bài viết \"{$postTitle}\" của {$postUser}",
            PostReport::class,
            $postReport->id,
            ['post_title' => $postTitle],
            ['status' => 'approved']
        );

        // Gửi thông báo cho người báo cáo
        if ($postReport->user) {
            $postReport->user->notify(new ReportResolvedNotification([
                'status' => 'approved',
                'message' => "Báo cáo của bạn về bài viết \"{$postTitle}\" đã được chấp thuận. Bài viết đã bị ẩn.",
                'admin_note' => $request->admin_note,
                'post_title' => $postTitle,
                'link' => route('home'),
            ]));
        }

        return back()->with('success', 'Đã chấp thuận báo cáo và ẩn bài viết vi phạm.');
    }

    /**
     * Từ chối báo cáo
     */
    public function reject(Request $request, PostReport $postReport)
    {
        // Kiểm tra nếu đã xử lý
        if ($postReport->status !== 'pending') {
            return back()->with('error', 'Báo cáo này đã được xử lý trước đó.');
        }

        $postReport->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Ghi log
        AdminActivityLog::log(
            'reject',
            "Từ chối báo cáo bài viết #" . $postReport->post_id,
            PostReport::class,
            $postReport->id,
            null,
            ['status' => 'rejected']
        );

        // Gửi thông báo cho người báo cáo
        if ($postReport->user) {
            $postTitle = $postReport->post ? $postReport->post->title : 'Bài viết';
            $postReport->user->notify(new ReportResolvedNotification([
                'status' => 'rejected',
                'message' => "Báo cáo của bạn về bài viết \"{$postTitle}\" đã bị từ chối.",
                'admin_note' => $request->admin_note,
                'post_title' => $postTitle,
                'link' => route('home'),
            ]));
        }

        return back()->with('success', 'Đã từ chối báo cáo.');
    }

    /**
     * Xóa báo cáo
     */
    public function destroy(PostReport $postReport)
    {
        $reportId = $postReport->id;
        $postReport->delete();

        // Ghi log
        AdminActivityLog::log(
            'delete',
            "Xóa báo cáo bài viết #{$reportId}",
            PostReport::class,
            $reportId,
            null,
            null
        );

        return back()->with('success', 'Đã xóa báo cáo.');
    }

    /**
     * Xóa vĩnh viễn bài viết vi phạm
     */
    public function deletePost(Request $request, PostReport $postReport)
    {
        if ($postReport->status !== 'pending') {
            return back()->with('error', 'Báo cáo này đã được xử lý trước đó.');
        }

        $post = $postReport->post;
        $postTitle = $post ? $post->title : 'Bài viết đã bị xóa';
        $postUser = $post && $post->user ? $post->user->name : 'Người dùng ẩn';

        // Cập nhật trạng thái báo cáo
        $postReport->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note ?? 'Đã xóa bài viết vi phạm',
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Xóa bài viết (soft delete vì Post có SoftDeletes)
        if ($post) {
            $post->delete();
        }

        // Ghi log
        AdminActivityLog::log(
            'delete',
            "Xóa bài viết vi phạm \"{$postTitle}\" của {$postUser}",
            PostReport::class,
            $postReport->id,
            ['post_title' => $postTitle],
            ['action' => 'deleted_post']
        );

        // Gửi thông báo cho người báo cáo
        if ($postReport->user) {
            $postReport->user->notify(new ReportResolvedNotification([
                'status' => 'approved',
                'message' => "Báo cáo của bạn về bài viết \"{$postTitle}\" đã được chấp thuận. Bài viết đã bị xóa.",
                'admin_note' => $request->admin_note ?? 'Đã xóa bài viết vi phạm',
                'post_title' => $postTitle,
                'link' => route('home'),
            ]));
        }

        return back()->with('success', 'Đã xóa bài viết vi phạm.');
    }
}
