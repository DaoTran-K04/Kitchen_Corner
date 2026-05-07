<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;
use App\Models\AvatarFrame;
use Illuminate\Support\Str;

class GamificationSeeder extends Seeder
{
    public function run()
    {
        // Vô hiệu hóa kiểm tra khóa ngoại để có thể truncate
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Badge::truncate();
        AvatarFrame::truncate();

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed Badges (Huy hiệu)
        $badges = [
            [
                'name' => 'Vua Đầu Bếp',
                'description' => 'Dành cho người đóng góp trên 50 công thức nấu ăn chất lượng.',
                'icon' => '👑',
            ],
            [
                'name' => 'Người Truyền Cảm Hứng',
                'description' => 'Công thức của bạn nhận được tổng cộng hơn 1000 lượt yêu thích.',
                'icon' => '🌟',
            ],
            [
                'name' => 'Thành Viên Tích Cực',
                'description' => 'Đăng nhập liên tục trong 7 ngày để nhận huy hiệu này.',
                'icon' => '🔥',
            ],
            [
                'name' => 'Chuyên Gia Bình Luận',
                'description' => 'Có hơn 100 bình luận đóng góp ý kiến cho các đầu bếp khác.',
                'icon' => '💬',
            ],
            [
                'name' => 'Đầu Bếp Mới (Newbie)',
                'description' => 'Huy hiệu chào mừng bạn gia nhập cộng đồng Góc Bếp.',
                'icon' => '🌱',
            ],
            [
                'name' => 'Bậc Thầy Gia Vị',
                'description' => 'Sử dụng thành thạo và chia sẻ các bí quyết nêm nếm độc đáo.',
                'icon' => '🧂',
            ],
        ];

        foreach ($badges as $b) {
            Badge::create([
                'name' => $b['name'],
                'slug' => Str::slug($b['name']),
                'description' => $b['description'],
                'icon' => $b['icon'],
                'is_active' => true,
            ]);
        }

        // 2. Seed Avatar Frames (Khung ảnh)
        $frames = [
            [
                'name' => 'Khung Vàng Hoàng Gia',
                'description' => 'Khung dành cho các thành viên VIP và đóng góp xuất sắc.',
                'image' => 'https://img.icons8.com/color/200/crown.png',
            ],
            [
                'name' => 'Khung Mùa Xuân Rực Rỡ',
                'description' => 'Khung phiên bản giới hạn mừng ngày hội ẩm thực mùa xuân.',
                'image' => 'https://img.icons8.com/color/200/spring.png',
            ],
            [
                'name' => 'Khung Đầu Bếp Chuyên Nghiệp',
                'description' => 'Khung đặc biệt dành cho các đầu bếp có chứng chỉ xác minh.',
                'image' => 'https://img.icons8.com/color/200/chef-hat.png',
            ],
            [
                'name' => 'Khung Kim Cương Lấp Lánh',
                'description' => 'Phần thưởng dành cho Top 1 bảng xếp hạng tháng.',
                'image' => 'https://img.icons8.com/color/200/diamond.png',
            ],
            [
                'name' => 'Khung Gỗ Mộc Mạc',
                'description' => 'Dành cho những người yêu thích phong cách nấu ăn truyền thống.',
                'image' => 'https://img.icons8.com/color/200/wood.png',
            ],
        ];

        foreach ($frames as $f) {
            AvatarFrame::create([
                'name' => $f['name'],
                'slug' => Str::slug($f['name']),
                'description' => $f['description'],
                'frame_image' => $f['image'],
                'is_active' => true,
                'order' => 0,
            ]);
        }
    }
}
