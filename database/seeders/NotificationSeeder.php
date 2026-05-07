<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'admin@gmail.com')->first();
        if (!$user) return;

        // Clear existing notifications to avoid clutter
        DB::table('notifications')->where('notifiable_id', $user->id)->delete();

        $notifications = [
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\CommentLikedNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'user_name' => 'Bếp Trưởng Vua Đầu Bếp',
                    'avatar' => 'https://i.pravatar.cc/150?u=chef',
                    'message' => 'đã thích bình luận của bạn trong bài viết',
                    'post_title' => 'Bí quyết làm món Phở Bò truyền thống đậm đà',
                    'type' => 'comment_like'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subMinutes(5),
                'updated_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\CommentRepliedNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'user_name' => 'Cô Ba Trà Vinh',
                    'avatar' => 'https://i.pravatar.cc/150?u=coba',
                    'message' => 'đã trả lời bình luận của bạn:',
                    'post_title' => '"Cảm ơn cháu, món này ngon quá!"',
                    'type' => 'comment_reply'
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\AdminNewPostNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'admin_new_post',
                    'message' => 'Góc Bếp vừa đăng tải bài viết mới: "Top 10 mẹo vặt nhà bếp bạn không nên bỏ qua". Khám phá ngay!',
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            [
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\BookApprovedNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'type' => 'book_approved',
                    'message' => 'Yêu cầu gợi ý cuốn sách "Hương vị Miền Tây" của bạn đã được quản trị viên phê duyệt. Chúc bạn có những phút giây nấu nướng vui vẻ!',
                ]),
                'read_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}
