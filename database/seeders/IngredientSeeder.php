<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            // [Tên, Đơn vị, Calo, Protein, Carbs, Fat] (giá trị xấp xỉ trên mỗi đơn vị)
            
            // --- CƠM / GẠO / BỘT / ĐỒ KHÔ ---
            ['Cơm', '100g', 130, 2.7, 28, 0.3],
            ['Cơm trắng', '100g', 130, 2.7, 28, 0.3],
            ['Gạo tẻ', '100g', 360, 7.5, 78, 1.2],
            ['Bún tươi', '100g', 110, 1.7, 25, 0.1],
            ['Bánh phở tươi', '100g', 140, 2.2, 32, 0.1],
            ['Bánh mì', '100g', 265, 9, 49, 3.2],
            ['Bánh mì baguette', '100g', 270, 9.5, 52, 3.0],
            ['Mì Spaghetti', '100g', 158, 5.8, 31, 0.9],
            ['Bột mì đa dụng', '100g', 364, 10, 76, 1.0],
            ['Bột nếp ngon', '100g', 348, 6.5, 78, 0.8],
            ['Đậu xanh cà vỏ', '100g', 347, 23, 62, 1.2],
            ['Bột nở (Baking powder)', 'g', 1, 0, 0.5, 0],

            // --- THỊT (MEATS) ---
            ['Thịt bò', '100g', 250, 26, 0, 15],
            ['Thịt lợn (nạc)', '100g', 242, 27, 0, 14],
            ['Ức gà', '100g', 165, 31, 0, 3.6],
            ['Đùi gà rút xương', '100g', 211, 17, 0, 15],
            ['Sườn non', '100g', 277, 16, 0, 23],
            ['Thịt ba rọi heo', '100g', 518, 9, 0, 53],
            ['Thịt ba vị heo', '100g', 518, 9, 0, 53],
            ['Xương ống bò', '100g', 240, 15, 0, 20],
            ['Thịt bò phi lê', '100g', 215, 22, 0, 14],
            ['Thịt nạc vai heo', '100g', 236, 18, 0, 18],
            ['Thịt bò băm', '100g', 250, 26, 0, 15],
            ['Thịt bò thăn ngoại (Striploin)', '100g', 220, 25, 0, 13],
            ['Bò rọi thái lát dập', '100g', 290, 20, 0, 23],
            ['Ức hoặc đùi gà xay', '100g', 180, 22, 0, 10],
            ['Ếch đồng ngon', '100g', 73, 16.4, 0, 0.3],

            // --- HẢI SẢN (SEAFOOD) ---
            ['Cá hồi', '100g', 208, 20, 0, 13],
            ['Phi lê cá hồi', '100g', 208, 20, 0, 13],
            ['Tôm tươi', '100g', 99, 24, 0.2, 0.3],

            // --- TRỨNG / SỮA / BƠ / ĐẬU (DAIRY / EGGS / SOY) ---
            ['Trứng gà', 'quả', 70, 6, 0.6, 5],
            ['Đậu phụ', '100g', 76, 8, 1.9, 4.8],
            ['Đậu phụ chiên', '100g', 270, 17, 2.5, 20],
            ['Sữa tươi', '100ml', 62, 3.3, 4.8, 3.3],
            ['Sữa tươi không đường', '100ml', 62, 3.3, 4.8, 3.3],
            ['Kem béo (Whipping cream)', '100ml', 340, 2.1, 2.8, 36],
            ['Bơ nhạt', '10g', 72, 0.1, 0.1, 8.1],
            ['Bơ phết', '10g', 72, 0.1, 0.1, 8.1],
            ['Bơ lạt', '10g', 72, 0.1, 0.1, 8.1],
            ['Phô mai Parmesan bào', '10g', 43, 3.8, 0.4, 2.9],
            ['Pate gan heo', '100g', 320, 14, 2.5, 28],
            ['Xúc xích xông khói', '100g', 300, 12, 2.0, 27],

            // --- RAU CỦ / QUẢ (VEGETABLES & FRUITS) ---
            ['Khoai tây', '100g', 77, 2, 17, 0.1],
            ['Súp lơ xanh', '100g', 34, 2.8, 7, 0.4],
            ['Cà rốt', '100g', 41, 0.9, 10, 0.2],
            ['Cà chua', '100g', 18, 0.9, 3.9, 0.2],
            ['Cà chua bi', '100g', 18, 0.9, 3.9, 0.2],
            ['Cà chua chín', '100g', 18, 0.9, 3.9, 0.2],
            ['Hành tây', '100g', 40, 1.1, 9, 0.1],
            ['Hành tây đỏ', '100g', 40, 1.1, 9, 0.1],
            ['Rau muống', '100g', 19, 2, 3.1, 0.3],
            ['Cải thìa', '100g', 13, 1.5, 2.2, 0.2],
            ['Nấm hương', '100g', 34, 2.2, 7, 0.5],
            ['Măng tây', '100g', 20, 2.2, 3.9, 0.1],
            ['Dâu tây tươi', '100g', 32, 0.7, 7.7, 0.3],
            ['Chuối xanh', '100g', 89, 1.1, 22.8, 0.3],
            ['Quả bơ (Avocado)', '100g', 160, 2, 8.5, 14.7],
            ['Dưa chuột', '100g', 15, 0.7, 3.6, 0.1],
            ['Rau xà lách lolo xanh', '100g', 15, 1.3, 2.8, 0.2],
            ['Cà tím Thái hoặc VN', '100g', 25, 1.0, 6.0, 0.2],
            ['Đậu que', '100g', 31, 1.8, 7.0, 0.2],
            ['Rau sống (Xà lách, tía tô..)', '100g', 16, 1.5, 3.0, 0.2],
            ['Đu đủ dưa chuột xanh', '100g', 32, 0.5, 8.0, 0.1],

            // --- DẦU ĂN / SỐT / GIA VỊ LỎNG (OILS, SAUCES, LIQUIDS) ---
            ['Dầu ăn', '10ml', 88, 0, 0, 10],
            ['Dầu oliu', '10ml', 88, 0, 0, 10],
            ['Nước mắm', '10ml', 6, 1.3, 0, 0],
            ['Đường trắng', '10g', 39, 0, 10, 0],
            ['Đường', '10g', 39, 0, 10, 0],
            ['Đường thốt nốt', '10g', 38, 0.1, 9.5, 0],
            ['Mật ong', '10g', 30, 0, 8.2, 0],
            ['Siro phong (Maple syrup)', '10g', 26, 0, 6.7, 0],
            ['Giấm gạo', '10ml', 2, 0, 0.1, 0],
            ['Giấm táo', '10ml', 2, 0, 0.1, 0],
            ['Nước cốt dừa', '100ml', 230, 2.3, 5.5, 24],
            ['Nước cốt dừa đặc', '100ml', 230, 2.3, 5.5, 24],
            ['Sốt cà chua (Tomato paste)', '10g', 8, 0.4, 1.9, 0.1],
            ['Sốt cà ri xanh Thái', '10g', 15, 0.2, 1.2, 1.0],
            ['Mẻ chua', '10g', 5, 0.1, 1.0, 0],
            ['Nước hàng (nước màu)', '10ml', 35, 0, 9, 0],
            ['Ruợu vang trắng', '10ml', 8, 0, 0.3, 0],
            ['Nước dùng bò (Beef stock)', '100ml', 15, 1.2, 0.5, 0.8],
            ['Nước dùng gà', '100ml', 16, 1.5, 0.4, 0.8],

            // --- GIA VỊ KHÔ / THẢO MỘC (SPICES & HERBS) ---
            ['Tỏi', '10g', 15, 0.6, 3.3, 0.1],
            ['Tỏi nguyên củ', '10g', 15, 0.6, 3.3, 0.1],
            ['Hành khô', '10g', 7, 0.25, 1.6, 0],
            ['Hành tím', '10g', 7, 0.25, 1.6, 0],
            ['Hành tím, tỏi băm', '10g', 11, 0.4, 2.4, 0.1],
            ['Gừng tươi', '10g', 8, 0.2, 1.8, 0.1],
            ['Gừng nướng', '10g', 8, 0.2, 1.8, 0.1],
            ['Ớt tươi', '10g', 4, 0.2, 0.9, 0],
            ['Ớt sừng đỏ', '10g', 4, 0.2, 0.9, 0],
            ['Muối', 'g', 0, 0, 0, 0],
            ['Muối hạt', 'g', 0, 0, 0, 0],
            ['Hạt tiêu', 'g', 2, 0.1, 0.6, 0],
            ['Tiêu đen xay', 'g', 2, 0.1, 0.6, 0],
            ['Tiêu xanh nguyên hạt', 'g', 2, 0.1, 0.6, 0],
            ['Hành lá, ngò gai', '10g', 3, 0.2, 0.6, 0],
            ['Hành lá', '10g', 3, 0.2, 0.6, 0],
            ['Rau mùi (Coriander)', '10g', 2, 0.2, 0.4, 0],
            ['Quế, hoa hồi, thảo quả', '10g', 25, 0.4, 5.2, 0.6],
            ['Lá Oregano sấy khô', '1g', 3, 0.1, 0.7, 0.1],
            ['Cỏ xạ hương tươi (Thyme)', '1g', 1, 0, 0.2, 0],
            ['Lá chanh Kaffir, Húng quế', '10g', 3, 0.3, 0.5, 0],
            ['Tía tô, Lá lốt, Hành hoa', '10g', 3, 0.2, 0.5, 0],
            ['Nghệ tươi', '10g', 3, 0.1, 0.8, 0],
            ['Mè rang (Vừng)', '10g', 57, 1.8, 2.3, 5.0],
            ['Sả băm', '10g', 10, 0.2, 2.5, 0.1],
            ['Bột gia vị Taco', '10g', 30, 1.0, 5.0, 0.5],
            ['Lá chanh Kaffir', '10g', 3, 0.3, 0.5, 0],
            ['Ngò tây (Parsley)', '10g', 3, 0.3, 0.6, 0.1],
            ['Bánh Tacos cứng (Taco shells)', 'vỏ', 50, 1.0, 8.0, 2.5],
            ['Cà chua, ngò rí', '10g', 5, 0.2, 1.0, 0.1],
            ['Nước lá dứa, gấc, lá cẩm', '10ml', 5, 0.1, 1.2, 0],
            ['Muối, tiêu đen', 'g', 1, 0, 0.2, 0],
            ['Muối hạt, tiêu đen', 'g', 1, 0, 0.2, 0],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::firstOrCreate(
                ['name' => $ing[0]],
                [
                    'slug' => Str::slug($ing[0]) . '-' . Str::random(4),
                    'unit' => $ing[1],
                    'calories_per_unit' => $ing[2],
                    'protein_per_unit' => $ing[3],
                    'carbs_per_unit' => $ing[4],
                    'fat_per_unit' => $ing[5],
                    'icon' => 'fas fa-leaf', // Mặc định
                ]
            );
        }
    }
}
