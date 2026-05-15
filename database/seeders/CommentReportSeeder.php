<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommentReport;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommentReportSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CommentReport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $comments = Comment::all();

        if ($users->count() < 2 || $comments->count() < 5) {
            return;
        }

        // 1. Một số báo cáo đang chờ xử lý (Pending)
        $pendingReports = [
            ['reason' => 'spam', 'desc' => 'Bình luận này quảng cáo trang web cá độ bóng đá.'],
            ['reason' => 'offensive', 'desc' => 'Dùng từ ngữ thô tục xúc phạm người đăng công thức.'],
            ['reason' => 'harassment', 'desc' => 'Liên tục làm phiền và công kích cá nhân.'],
        ];

        foreach ($pendingReports as $report) {
            CommentReport::create([
                'comment_id' => $comments->random()->id,
                'user_id' => $users->random()->id,
                'reason' => $report['reason'],
                'description' => $report['desc'],
                'status' => 'pending',
            ]);
        }

        // 2. Một số báo cáo đã chấp thuận (Approved)
        CommentReport::create([
            'comment_id' => $comments->random()->id,
            'user_id' => $users->random()->id,
            'reason' => 'inappropriate',
            'description' => 'Ảnh đại diện và bình luận chứa nội dung không phù hợp với trẻ em.',
            'status' => 'approved',
            'admin_note' => 'Đã xác minh và xóa bình luận vi phạm.',
            'resolved_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            'resolved_at' => now(),
        ]);

        // 3. Một số báo cáo đã từ chối (Rejected)
        CommentReport::create([
            'comment_id' => $comments->random()->id,
            'user_id' => $users->random()->id,
            'reason' => 'other',
            'description' => 'Tôi không thích cách người này nói chuyện.',
            'status' => 'rejected',
            'admin_note' => 'Lý do báo cáo không vi phạm quy chuẩn cộng đồng. Chỉ là bất đồng quan điểm cá nhân.',
            'resolved_by' => $users->where('role', 'admin')->first()->id ?? $users->first()->id,
            'resolved_at' => now(),
        ]);
    }
}
