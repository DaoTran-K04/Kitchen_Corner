<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Recipe;
use App\Models\User;

class HolidayRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        if (!$admin) {
            $this->command->error('No users found.');
            return;
        }

        $recipes = [
            // Giáng Sinh
            [
                'title' => 'Gà Tây Quay Sốt Cam Mật Ong',
                'description' => 'Món gà tây nướng nguyên con vàng rộm, thấm đẫm vị ngọt ngào của mật ong và chút chua thanh của cam tươi. Đỉnh cao của nghệ thuật nướng lễ hội. Không thể thiếu cho một đêm Giáng sinh diệu kỳ.',
                'image' => 'https://images.unsplash.com/photo-1574672280600-4accfa5b6f98?auto=format&fit=crop&q=80&w=900',
                'category_id' => 8, // Món chính
                'status' => 'published',
                'view_count' => rand(200, 800)
            ],
            // Tết
            [
                'title' => 'Thịt Kho Tàu Nước Dừa Truyền Thống',
                'description' => 'Miếng thịt ba chỉ mềm mọng, trứng cút ngấm đều nước dừa xiêm ngọt lịm. Màu cánh gián óng ánh biểu tượng cho sự sung túc và may mắn trong mâm cơm ngày Tết cổ truyền.',
                'image' => 'https://images.unsplash.com/photo-1628268909376-e8c44bb39cb4?auto=format&fit=crop&q=80&w=900',
                'category_id' => 8, 
                'status' => 'published',
                'view_count' => rand(200, 800)
            ],
            // Valentine
            [
                'title' => 'Lava Mousse Socola Trái Tim',
                'description' => 'Món bánh kem ngọt ngào với nhân socola tan chảy quyến rũ. Một món tráng miệng lớp lang, tinh tế, ngập tràn lãng mạn tình yêu cho ngày Valentine đáng nhớ.',
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&q=80&w=900',
                'category_id' => 9, // Tráng miệng
                'status' => 'published',
                'view_count' => rand(200, 800)
            ],
            // Halloween
            [
                'title' => 'Súp Bí Đỏ Nguyên Quỷ Xúc Xích',
                'description' => 'Món súp bí đỏ tươi mát, bốc khói nóng hổi nhâm nhi trong đêm Halloween sợ hãi. Trang trí cùng các mảnh xúc xích xông khói tỉa hình ma quỷ.',
                'image' => 'https://images.unsplash.com/photo-1509358271058-acd22cc93898?auto=format&fit=crop&q=80&w=900',
                'category_id' => 7, // Soup/Khai vị
                'status' => 'published',
                'view_count' => rand(200, 800)
            ],
            // Thu / Hè (Chè)
            [
                'title' => 'Chè Dưỡng Nhan Tuyết Yến Táo Đỏ',
                'description' => 'Chè dưỡng nhan thanh tao bao gồm nhựa đào, tuyết yến và táo đỏ quý hiếm. Mang lại công dụng làm đẹp da, thanh nhiệt giải độc cực kỳ hiệu quả trong những ngày oi bức.',
                'image' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&q=80&w=900',
                'category_id' => 9, // Tráng miệng/chè
                'status' => 'published',
                'view_count' => rand(200, 800)
            ]
        ];

        foreach ($recipes as $r) {
            $recipe = Recipe::create([
                'user_id'      => $admin->id,
                'category_id'  => $r['category_id'],
                'title'        => $r['title'],
                'slug'         => Str::slug($r['title']) . '-' . rand(1000, 9999),
                'description'  => $r['description'],
                'cooking_time' => 45,
                'difficulty'   => 'medium',
                'image'        => $r['image'],
                'status'       => $r['status'],
                'view_count'   => $r['view_count']
            ]);

            // Add some targeted ingredients so the deep search hits perfectly
            if (strpos($r['title'], 'Socola') !== false) {
                $ing1 = \App\Models\Ingredient::firstOrCreate(['name' => 'Bột Socola nguyên chất', 'slug' => \Illuminate\Support\Str::slug('Bột Socola nguyên chất'), 'unit' => 'g']);
                $ing2 = \App\Models\Ingredient::firstOrCreate(['name' => 'Kem tươi whipping', 'slug' => \Illuminate\Support\Str::slug('Kem tươi whipping'), 'unit' => 'ml']);
                $recipe->ingredients()->attach($ing1->id, ['quantity' => 100, 'notes' => 'Tùy chỉnh']);
                $recipe->ingredients()->attach($ing2->id, ['quantity' => 50, 'notes' => 'Đánh bông']);
            }
            if (strpos($r['title'], 'Gà Tây') !== false) {
                $ing = \App\Models\Ingredient::firstOrCreate(['name' => 'Gà Tây cao cấp', 'slug' => \Illuminate\Support\Str::slug('Gà Tây cao cấp'), 'unit' => 'con']);
                $recipe->ingredients()->attach($ing->id, ['quantity' => 1, 'notes' => '']);
            }
            if (strpos($r['title'], 'Táo Đỏ') !== false) {
                $ing = \App\Models\Ingredient::firstOrCreate(['name' => 'Táo đỏ tân cương', 'slug' => \Illuminate\Support\Str::slug('Táo đỏ tân cương'), 'unit' => 'g']);
                $recipe->ingredients()->attach($ing->id, ['quantity' => 20, 'notes' => 'Ngâm mềm']);
            }
        }
        
        $this->command->info('Seeded ' . count($recipes) . ' premium thematic recipes!');
    }
}
