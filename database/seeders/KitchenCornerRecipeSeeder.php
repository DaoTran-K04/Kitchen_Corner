<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use App\Models\Article;


class KitchenCornerRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminList = User::where('role', 'admin')->get();
        if ($adminList->isEmpty()) {
            $adminUser = User::first();
        } else {
            $adminUser = $adminList->first();
        }

        if (!$adminUser) {
            $this->command->error('No users found. Please seed users first.');
            return;
        }

        // Wipe old recipes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Recipe::truncate();
        DB::table('recipe_ingredients')->truncate();
        DB::table('recipe_steps')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $recipes = [
            [
                'title' => 'Sườn Xào Chua Ngọt Đậm Đà',
                'description' => 'Món sườn xào chua ngọt truyền thống với lớp sốt bóng bẩy, thịt sườn mềm thơm quyện cùng vị chua dôn dốt của cà chua và chút cay nồng của tiêu đỏ. Tuyệt hảo khi dùng kèm cơm trắng nóng hổi.',
                'image' => 'https://images.unsplash.com/photo-1544025162-811cffd92ef7?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 8, // Món chính
                'cooking_time' => 45,
                'difficulty' => 'medium',
                'total_calories' => 550,
                'total_protein' => 30,
                'total_carbs' => 45,
                'total_fat' => 25,
                'ingredients' => [
                    ['name' => 'Sườn non', 'quantity' => '500', 'unit' => 'g', 'notes' => 'Chặt miếng vừa ăn'],
                    ['name' => 'Cà chua', 'quantity' => '2', 'unit' => 'quả', 'notes' => 'Thái hạt lựu'],
                    ['name' => 'Hành khô', 'quantity' => '1', 'unit' => 'củ', 'notes' => 'Băm nhuyễn'],
                    ['name' => 'Tỏi', 'quantity' => '3', 'unit' => 'tép', 'notes' => 'Băm nhuyễn'],
                    ['name' => 'Giấm gạo', 'quantity' => '3', 'unit' => 'muỗng canh', 'notes' => ''],
                    ['name' => 'Đường', 'quantity' => '2', 'unit' => 'muỗng canh', 'notes' => ''],
                    ['name' => 'Nước mắm', 'quantity' => '2', 'unit' => 'muỗng canh', 'notes' => '']
                ],
                'steps' => [
                    'Rửa sạch sườn, chần sơ qua nước sôi để khử mùi hôi. Vớt ra để ráo nước.',
                    'Chiên sườn vàng đều các mặt trên chảo dầu nóng, vớt ra đĩa có giấy thấm dầu.',
                    'Phi thơm hành tỏi băm, cho cà chua xào nát tạo màu. Thêm giấm, đường, nước mắm, xì dầu và nửa chén nước lọc đun sôi.',
                    'Trút sườn vào chảo sốt, đảo đều tay trên lửa nhỏ riu riu cho đến khi sốt sánh lại, bám đều quanh miếng sườn.',
                    'Tắt bếp, rắc hành lá và tiêu đen. Bày ra đĩa thưởng thức.'
                ]
            ],
            [
                'title' => 'Phở Bò Gia Truyền Hà Nội',
                'description' => 'Tinh hoa ẩm thực Việt Nam gói gọn trong bát phở bò nóng hổi. Nước dùng thanh ngọt hầm từ xương bò nguyên chất trong 24 giờ, kết hợp cùng sợi phở dai mềm và những thớ thịt bò tái tươi rói.',
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cb431?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 6, // Mì - Bún - Phở
                'cooking_time' => 120,
                'difficulty' => 'hard',
                'total_calories' => 450,
                'total_protein' => 35,
                'total_carbs' => 60,
                'total_fat' => 12,
                'ingredients' => [
                    ['name' => 'Xương ống bò', 'quantity' => '1', 'unit' => 'kg', 'notes' => 'Khử mùi kỹ'],
                    ['name' => 'Thịt bò phi lê', 'quantity' => '300', 'unit' => 'g', 'notes' => 'Thái lát mỏng'],
                    ['name' => 'Bánh phở tươi', 'quantity' => '500', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Gừng nướng', 'quantity' => '1', 'unit' => 'củ', 'notes' => ''],
                    ['name' => 'Hành tây nướng', 'quantity' => '1', 'unit' => 'củ', 'notes' => ''],
                    ['name' => 'Quế, hoa hồi, thảo quả', 'quantity' => '10', 'unit' => 'g', 'notes' => 'Rang thơm'],
                    ['name' => 'Hành lá, ngò gai', 'quantity' => '50', 'unit' => 'g', 'notes' => 'Thái nhỏ']
                ],
                'steps' => [
                    'Nướng gừng, hành tây chín thơm. Rang các loại gia vị khô (hoa hồi, quế, thảo quả) cho dậy mùi.',
                    'Chần xương bò bằng nước sôi, rửa sạch. Ninh xương bò với 3-4 lít nước cùng gừng, hành tây nướng trong ít nhất 4 giờ. Nhớ hớt bọt liên tục để nước trong.',
                    'Sau khi ninh 2 giờ, cho gói gia vị khô vào nồi hầm chung. Nêm nếm bột canh, nước mắm, đường phèn cho vừa ăn.',
                    'Chần bánh phở qua nước sôi, xếp vào bát. Đặt thịt bò tái lên trên bánh phở.',
                    'Chan nước dùng sôi sùng sục vào bát để làm chín thịt bò tái. Rắc hành lá, tiêu, ăn nóng cùng quẩy và chanh ớt.'
                ]
            ],
            [
                'title' => 'Salad Ức Gà Áp Chảo Healthy',
                'description' => 'Món salad tràn ngập màu sắc và dinh dưỡng. Thích hợp cho chế độ ăn kiêng Eat Clean, giữ trọn hương vị tươi mát của rau củ kết hợp với ức gà áp chảo mọng nước, đậm đà.',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 9, // Tráng miệng (Reused as Healthy/Salad usually but we put 9 or 8)
                'cooking_time' => 20,
                'difficulty' => 'easy',
                'total_calories' => 320,
                'total_protein' => 45,
                'total_carbs' => 15,
                'total_fat' => 10,
                'ingredients' => [
                    ['name' => 'Ức gà', 'quantity' => '250', 'unit' => 'g', 'notes' => 'Lọc bỏ da'],
                    ['name' => 'Rau xà lách lolo xanh', 'quantity' => '100', 'unit' => 'g', 'notes' => 'Rửa sạch, thái nhỏ'],
                    ['name' => 'Cà chua bi', 'quantity' => '10', 'unit' => 'quả', 'notes' => 'Cắt đôi'],
                    ['name' => 'Dưa chuột', 'quantity' => '1', 'unit' => 'quả', 'notes' => 'Thái lát chéo'],
                    ['name' => 'Dầu oliu', 'quantity' => '2', 'unit' => 'muỗng canh', 'notes' => ''],
                    ['name' => 'Mật ong', 'quantity' => '1', 'unit' => 'muỗng cà phê', 'notes' => 'Dùng làm sốt'],
                    ['name' => 'Giấm táo', 'quantity' => '1', 'unit' => 'muỗng canh', 'notes' => 'Dùng làm sốt'],
                    ['name' => 'Tiêu đen xay', 'quantity' => '1', 'unit' => 'nhúm', 'notes' => '']
                ],
                'steps' => [
                    'Sơ chế ức gà, khía các đường chéo để ngấm gia vị. Ướp ức gà với xíu muối, tiêu đen, 1 muỗng dầu oliu trong 10 phút.',
                    'Làm nóng chảo, áp chảo ức gà lửa vừa mỗi mặt khoảng 4-5 phút cho xém vàng và chín tới bên trong. Vớt ra thái lát.',
                    'Pha nước sốt: Trộn đều dầu oliu còn lại, mật ong, giấm táo, chút muối và tiêu thành hỗn hợp đồng nhất.',
                    'Xếp xà lách, cà chua bi, dưa chuột ra đĩa. Trải ức gà lên trên.',
                    'Rưới nước sốt đều lên đĩa salad trước khi ăn và trộn nhẹ nhàng.'
                ]
            ],
            [
                'title' => 'Spaghetti Bolognese (Sốt Bò Bằm)',
                'description' => 'Tuyệt tác của nền ẩm thực Ý mang đến hương vị quyến rũ không thể cưỡng lại. Sốt thịt bò bằm với cà chua tươi hòa quyện cùng mì Ý sợi dai dai, phủ một lớp phô mai Parmesan bào sợi thơm lừng.',
                'image' => 'https://images.unsplash.com/photo-1598866594230-a7c12756260f?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 6, // Mì pasta
                'cooking_time' => 30,
                'difficulty' => 'easy',
                'total_calories' => 600,
                'total_protein' => 25,
                'total_carbs' => 70,
                'total_fat' => 20,
                'ingredients' => [
                    ['name' => 'Mì Spaghetti', 'quantity' => '200', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Thịt bò băm', 'quantity' => '200', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Cà chua', 'quantity' => '4', 'unit' => 'quả', 'notes' => 'Xay nhuyễn hoặc băm nhỏ'],
                    ['name' => 'Hành tây', 'quantity' => '0.5', 'unit' => 'củ', 'notes' => 'Hạt lựu'],
                    ['name' => 'Tỏi', 'quantity' => '2', 'unit' => 'tép', 'notes' => 'Băm nhuyễn'],
                    ['name' => 'Sốt cà chua (Tomato paste)', 'quantity' => '2', 'unit' => 'muỗng canh', 'notes' => ''],
                    ['name' => 'Lá Oregano sấy khô', 'quantity' => '1', 'unit' => 'muỗng cà phê', 'notes' => ''],
                    ['name' => 'Phô mai Parmesan bào', 'quantity' => '2', 'unit' => 'muỗng canh', 'notes' => '']
                ],
                'steps' => [
                    'Luộc mì với 1 muỗng muối và chút dầu ăn trong 8 - 10 phút. Vớt ra để ráo, xóc qua xíu dầu ăn cho các sợi không dính nhau.',
                    'Phi thơm tỏi và hành tây, cho thịt bò băm vào xào săn lại lửa lớn.',
                    'Đổ cà chua xay nhuyễn và sốt tomato paste vào chảo, đảo đều. Nêm chút muối, đường, hạt tiêu.',
                    'Thêm nửa bát nước, đun liu riu cho sốt sền sệt lại khoảng 15 phút. Rắc lá oregano nếm lại vừa miệng rồi tắt bếp.',
                    'Bày mì ra đĩa, rưới đẫm sốt bò băm lên trên và rắc phô mai Parmesan bào sợi. Ăn ngay khi nóng.'
                ]
            ],
            [
                'title' => 'Bánh Pancake Dâu Tây Mềm Xốp',
                'description' => 'Món tráng miệng hoặc bữa sáng kiểu Tây cực kỳ dễ làm. Những lớp bánh pancake mềm mịn tơi xốp, thơm mùi bơ sữa và trứng gà, rưới thêm siro phong và kết hợp với dâu tây tươi chua ngọt.',
                'image' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7bb7445?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 9, // Tráng miệng
                'cooking_time' => 20,
                'difficulty' => 'easy',
                'total_calories' => 380,
                'total_protein' => 10,
                'total_carbs' => 55,
                'total_fat' => 12,
                'ingredients' => [
                    ['name' => 'Bột mì đa dụng', 'quantity' => '150', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Sữa tươi không đường', 'quantity' => '120', 'unit' => 'ml', 'notes' => ''],
                    ['name' => 'Trứng gà', 'quantity' => '2', 'unit' => 'quả', 'notes' => 'Tách lòng'],
                    ['name' => 'Bơ nhạt', 'quantity' => '30', 'unit' => 'g', 'notes' => 'Đun chảy'],
                    ['name' => 'Đường', 'quantity' => '30', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Bột nở (Baking powder)', 'quantity' => '5', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Dâu tây tươi', 'quantity' => '100', 'unit' => 'g', 'notes' => 'Cắt vát'],
                    ['name' => 'Siro phong (Maple syrup)', 'quantity' => '3', 'unit' => 'muỗng', 'notes' => '']
                ],
                'steps' => [
                    'Trộn bột mì, nửa lượng đường, bột nở với nhau. Đánh tan lòng đỏ trứng với bơ đun chảy và sữa tươi, đổ vào hỗn hợp bột khuấy nhẹ tay.',
                    'Dùng máy đánh trứng đánh bông lòng trắng với nửa lượng đường còn lại đến khi chóp mềm.',
                    'Chia lòng trắng đánh bông làm 3 phần, nhẹ nhàng trộn fold vào âu bột ở bước 1.',
                    'Làm nóng chảo chống dính, phết một lớp bơ siêu mỏng. Múc 1 muôi bột đổ thẳng vào giữa chảo để bột tự dàn tròn.',
                    'Rán nhỏ lửa khoảng 2 phút đến khi mặt bánh nổi bọt khí, lật bánh rán tiếp 1 phút.',
                    'Xếp các miếng bánh pancake xếp chồng lên nhau, rải dâu tây và rưới siro phong lên đỉnh bánh. Có thể rắc thêm xíu đường bột.'
                ]
            ],
            [
                'title' => 'Cá Hồi Nướng Măng Tây Sốt Chanh Bơ',
                'description' => 'Món ăn Âu mang đậm phong cách nhà hàng 5 sao nay có thể thực hiện tại nhà siêu nhanh. Cá hồi Na Uy chứa nhiều Omega-3 áp chảo giữ được độ ẩm mềm béo, ăn kèm măng tây giòn ngọt và sốt chanh bơ tan chảy đầu lưỡi.',
                'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 8, // Món chính
                'cooking_time' => 25,
                'difficulty' => 'medium',
                'total_calories' => 450,
                'total_protein' => 40,
                'total_carbs' => 8,
                'total_fat' => 28,
                'ingredients' => [
                    ['name' => 'Phi lê cá hồi', 'quantity' => '300', 'unit' => 'g', 'notes' => 'Giữ da'],
                    ['name' => 'Măng tây', 'quantity' => '200', 'unit' => 'g', 'notes' => 'Bỏ gốc già'],
                    ['name' => 'Bơ nhạt', 'quantity' => '40', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Chanh vàng (Lemon)', 'quantity' => '1', 'unit' => 'quả', 'notes' => 'Vắt lấy nước và bào vỏ'],
                    ['name' => 'Tỏi', 'quantity' => '2', 'unit' => 'tép', 'notes' => 'Băm nhuyễn'],
                    ['name' => 'Ruợu vang trắng', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => 'Tùy chọn'],
                    ['name' => 'Muối, tiêu đen', 'quantity' => '1', 'unit' => 'ít', 'notes' => ''],
                    ['name' => 'Ngò tây (Parsley)', 'quantity' => '1', 'unit' => 'nhánh', 'notes' => 'Băm nhỏ']
                ],
                'steps' => [
                    'Thấm khô miếng cá hồi. Ướp cá với muối, tiêu và chút vỏ chanh vàng bào nhuyễn trong 5 phút.',
                    'Măng tây rửa sạch, luộc sơ qua nước sôi 1 phút rồi vớt ra ngâm nước đá, để ráo.',
                    'Làm nóng chảo với chút dầu oliu, áp chảo mặt có da cá hồi trước ở lửa lớn 3 phút đến khi da giòn rụm, lật mặt áp chảo thêm 1.5 phút cho cá chín tái bên trong rồi lấy ra.',
                    'Dùng cùng chảo đó (hạ lửa nhỏ), đun chảy phần bơ nhạt, phi thơm tỏi băm. Thêm rượu vang trắng, nước cốt chanh vào đun sôi.',
                    'Đợi sốt hơi sệt lại, cho ngò tây băm vào. Tắt bếp.',
                    'Bày măng tây ra đĩa, đặt fillet cá hồi lên trên, rưới sốt bơ chanh vàng óng lên cá và thưởng thức cùng khoai tây nghiền nếu thích.'
                ]
            ],
            [
                'title' => 'Bò Bít Tết (Beefsteak) Sốt Tiêu Xanh',
                'description' => 'Món bò bít tết hảo hạng với miếng thịt bò Striploin áp chảo đạt độ chín Medium Rare hoàn hảo, thơm nức mùi bơ tỏi và cỏ xạ hương (thyme). Linh hồn vương giả của món ăn nằm ở nước sốt tiêu xanh cay nồng và béo ngậy vị kem.',
                'image' => 'https://images.unsplash.com/photo-1546241072-48010ad28c2c?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 8, // Món chính
                'cooking_time' => 20,
                'difficulty' => 'hard',
                'total_calories' => 650,
                'total_protein' => 45,
                'total_carbs' => 12,
                'total_fat' => 45,
                'ingredients' => [
                    ['name' => 'Thịt bò thăn ngoại (Striploin)', 'quantity' => '300', 'unit' => 'g', 'notes' => 'Dày 2.5cm'],
                    ['name' => 'Muối hạt, tiêu đen', 'quantity' => '1', 'unit' => 'ít', 'notes' => ''],
                    ['name' => 'Tỏi nguyên củ', 'quantity' => '1', 'unit' => 'củ', 'notes' => 'Cắt ngang đít củ'],
                    ['name' => 'Cỏ xạ hương tươi (Thyme)', 'quantity' => '4', 'unit' => 'nhánh', 'notes' => ''],
                    ['name' => 'Bơ lạt', 'quantity' => '50', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Tiêu xanh nguyên hạt', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => 'Đập dập nhẹ'],
                    ['name' => 'Nước dùng bò (Beef stock)', 'quantity' => '100', 'unit' => 'ml', 'notes' => ''],
                    ['name' => 'Kem béo (Whipping cream)', 'quantity' => '50', 'unit' => 'ml', 'notes' => '']
                ],
                'steps' => [
                    'Lấy thịt bò khỏi tủ lạnh, thấm thật khô. Rắc đều muối hạt và tiêu đen xay thoa đều hai mặt thịt trước khi chiên 2 phút.',
                    'Làm chảo gang thật nóng (bốc khói). Đổ xíu dầu ăn, đặt miếng thịt bò vào áp chảo lửa cực lớn, không di chuyển trong 1.5 phút để tạo lớp crust vàng ruộm.',
                    'Lật mặt thịt. Cho tỏi cắt đôi, bơ lạt, nhánh thyme vào chảo. Khi bơ sủi bọt, nghiêng chảo, dùng thìa múc bơ nóng cháy tưới liên tục lên thịt bò (basting) trong 1.5 phút.',
                    'Gắp thịt ra thớt, để thịt nghỉ (rest) 5-7 phút cho nước ngọt hút ngược lại vào thớ thịt.',
                    'Trong lúc đợi, nấu sốt tiêu: Dùng chảo vừa chiên, cho hạt tiêu xanh vào phi thơm, đổ nước dùng bò đun cạn đi phân nửa. Thêm whipping cream đun lửa nhỏ đến khi sánh mịn thì tắt bếp.',
                    'Thái thịt bò thành lát mỏng (chín hồng bên trong). Bày lên đĩa và rưới ngập sốt tiêu xanh nóng hổi.'
                ]
            ],
            [
                'title' => 'Chè Trôi Nước Ngũ Sắc',
                'description' => 'Món chè truyền thống được nâng tầm thành một tác phẩm nghệ thuật với 5 màu sắc hoàn toàn từ thiên nhiên: gấc, lá cẩm, lá dứa, nghệ và khoai môn. Lớp vỏ dẻo quánh bọc nhân đậu xanh bùi bùi, tắm trong nồi nước cốt dừa nồng nàn vị gừng tươi.',
                'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 9, // Tráng miệng
                'cooking_time' => 90,
                'difficulty' => 'medium',
                'total_calories' => 410,
                'total_protein' => 8,
                'total_carbs' => 75,
                'total_fat' => 15,
                'ingredients' => [
                    ['name' => 'Bột nếp ngon', 'quantity' => '500', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Đậu xanh cà vỏ', 'quantity' => '200', 'unit' => 'g', 'notes' => 'Ngâm mềm'],
                    ['name' => 'Đường thốt nốt', 'quantity' => '250', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Nước cốt dừa', 'quantity' => '300', 'unit' => 'ml', 'notes' => ''],
                    ['name' => 'Gừng tươi', 'quantity' => '50', 'unit' => 'g', 'notes' => 'Thái sợi'],
                    ['name' => 'Nước lá dứa, gấc, lá cẩm', 'quantity' => '3', 'unit' => 'chén', 'notes' => 'Làm màu tự nhiên'],
                    ['name' => 'Mè rang (Vừng)', 'quantity' => '30', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Hành tím', 'quantity' => '2', 'unit' => 'củ', 'notes' => 'Làm nhân']
                ],
                'steps' => [
                    'Làm nhân: Đậu xanh hấp chín mềm, tán nhuyễn. Phi thơm hành tím với dầu ăn, trút đậu xanh dừa nạo và đường xào đến khi nhân tróc chảo dính viên không vỡ. Nặn thành các viên tròn nhỏ.',
                    'Làm vỏ: Chia bột nếp làm 5 âu. Lần lượt nhồi bột với các loại nước màu đun nóng già (gấc đỏ, dứa xanh, cẩm tím...) cho đến khi bột thành khối dẻo không dính tay.',
                    'Nặn chè: Lấy lượng bột vừa đủ, ấn dẹt, đặt viên nhân đậu xanh vào giữa rồi vo tròn kín mít.',
                    'Luộc chè: Thả các viên trôi nước vào nồi nước đang sôi. Khi chè nổi lên mặt nước và vỏ bóng láng nghĩa là đã chín. Vớt ngay vào thau nước lạnh múc ra rổ.',
                    'Nấu canh đường: Đun 1 lít nước với đường thốt nốt và gừng sợi. Nước sôi thơm thì thả viên chè đã luộc vào, đun lửa nhỏ liu riu thêm 5 phút cho chè thấm đường.',
                    'Múc chè ra bát, chan nước cốt dừa đun lá nếp lên trên và rắc xíu hạt mè rang trắng.'
                ]
            ],
            [
                'title' => 'Cà Ri Gà Kiểu Thái (Thai Green Curry)',
                'description' => 'Đánh thức mọi giác quan với món cà ri xanh kiểu Thái chuẩn vị siêu bắt cơm. Nước dùng là sự pha trộn diệu kỳ của cốt dừa béo ngậy bùng nổ cùng cốt lá chanh kaffir, sả, riềng và vị cay xé lưỡi từ ớt xanh.',
                'image' => 'https://images.unsplash.com/photo-1455619411412-d5e810614868?q=80&w=1000&auto=format&fit=crop',

                'category_id' => 8, // Món chính
                'cooking_time' => 40,
                'difficulty' => 'medium',
                'total_calories' => 520,
                'total_protein' => 28,
                'total_carbs' => 22,
                'total_fat' => 38,
                'ingredients' => [
                    ['name' => 'Đùi gà rút xương', 'quantity' => '400', 'unit' => 'g', 'notes' => 'Thái miếng vuông'],
                    ['name' => 'Cà tím Thái hoặc VN', 'quantity' => '200', 'unit' => 'g', 'notes' => 'Cắt khối nhâm nước'],
                    ['name' => 'Đậu que', 'quantity' => '100', 'unit' => 'g', 'notes' => 'Cắt khúc'],
                    ['name' => 'Sốt cà ri xanh Thái', 'quantity' => '3', 'unit' => 'muỗng canh', 'notes' => 'Mua sẵn'],
                    ['name' => 'Nước cốt dừa đặc', 'quantity' => '400', 'unit' => 'ml', 'notes' => ''],
                    ['name' => 'Nước dùng gà', 'quantity' => '200', 'unit' => 'ml', 'notes' => ''],
                    ['name' => 'Đường thốt nốt', 'quantity' => '1.5', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Nước mắm', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Lá chanh Kaffir, Húng quế', 'quantity' => '1', 'unit' => 'ít', 'notes' => '']
                ],
                'steps' => [
                    'Bắt chảo nóng, múc lớp cream béo dầy trên bề mặt lon nước cốt dừa đun nóng phi thơm đến khi ra chút dầu. Cho sốt cà ri xanh vào rang thật thơm.',
                    'Đổ ức gà thái mỏng vào xào săn với lớp sốt cay để thịt thấm đẫm mùi hương viền trong ngoài.',
                    'Châm thêm nước dùng gà và phần cốt dừa còn lại vào nồi. Nêm đường thốt nốt nấu chảy và nước mắm, khuấy nhẹ cho vị hòa quyện.',
                    'Cho cà tím, đậu que và lá chanh Kaffir xé nhỏ vào hầm tiếp khoảng 7 phút đến khi rau củ mềm nhưng chưa bị nát nhừ rã.',
                    'Tắt bếp, vo tròn lá húng quế thả vào cùng vài lát ớt sừng đỏ trang trí trên cùng.',
                    'Múc ra tô sâu, thưởng thức nóng với cơn gạo lài (Jasmine rice) bốc khói siêu lôi cuốn.'
                ]
            ],
            [
                'title' => 'Bún Chả Hà Nội Nướng Than Hoa',
                'description' => 'Đậm đà phong vị Bắc Bộ với những miếng chả băm và chả miếng được ướp sả ớt, nướng xèo xèo trên lửa than hoa hồng rực. Quyện cùng bát nước mắm chua ngọt ấm nóng suýt xoa ăn kèm rau sống và bún tươi dai dẻo.',
                'image' => 'https://images.unsplash.com/photo-1622312674312-d81a942defff?auto=format&fit=crop&q=80&w=900',
                'category_id' => 6, // Mì Bún Phở
                'cooking_time' => 60,
                'difficulty' => 'medium',
                'total_calories' => 580,
                'total_protein' => 32,
                'total_carbs' => 65,
                'total_fat' => 20,
                'ingredients' => [
                    ['name' => 'Thịt nạc vai heo', 'quantity' => '400', 'unit' => 'g', 'notes' => 'Xay nhỏ'],
                    ['name' => 'Thịt ba rọi heo', 'quantity' => '400', 'unit' => 'g', 'notes' => 'Thái lát mỏng'],
                    ['name' => 'Bún tươi', 'quantity' => '1', 'unit' => 'kg', 'notes' => ''],
                    ['name' => 'Sả băm', 'quantity' => '4', 'unit' => 'muỗng', 'notes' => 'Lấy nước cốt'],
                    ['name' => 'Hành tím, tỏi băm', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Nước hàng (nước màu)', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Nước mắm', 'quantity' => '4', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Đu đủ dưa chuột xanh', 'quantity' => '200', 'unit' => 'g', 'notes' => 'Làm đồ chua'],
                    ['name' => 'Rau sống (Xà lách, tía tô..)', 'quantity' => '1', 'unit' => 'rổ', 'notes' => '']
                ],
                'steps' => [
                    'Pha sốt ướp chả: Trộn đều nước cốt sả, hành tỏi băm, tiêu đen, đường, nước mắm, nước hàng và dầu hào. Chia sốt ướp làm đôi.',
                    'Ướp chả: Trộn 1 nửa sốt với thịt nạc vai xay, vo thành từng viên tròn dẹt dẹt. Trộn nửa sốt còn lại với thịt ba chỉ thái lát mỏng. Ướp bọc kín trong tủ lạnh ít nhất 2 giờ thấm vị.',
                    'Làm đồ chua: Đu đủ, cà rốt tỉa hoa mỏng, bóp muối rửa sạch rồi ngâm với dấm, đường, xíu muối pha chua chua ngọt ngọt.',
                    'Nướng chả: Kẹp thịt lên vỉ than hoa quạt hồng, lật liên tục đều tay để chả chín vàng xém xém dậy mùi sả mà không bị cháy đen đắng chát. Thỉnh thoảng rưới chút mỡ lên mặt chả khỏi khô.',
                    'Pha nước chấm ấm nóng: Đun tỉ lệ 4 nước lọc, 1 mắm cốt, 1 đường, 1 giấm thành dung dịch ấm tản mạn. Đổ ra bát, thêm tỏi ớt băm.',
                    'Bày bún ra đĩa lớn kèm rổ rau sống. Múc thịt nướng ra bát gốm, cho đồ chua vào và chan ngập nước mắm ấm. Vừa thổi vừa ăn ngon bá cháy.'
                ]
            ],
            [
                'title' => 'Bánh Mì Chảo Thập Cẩm Pate',
                'description' => 'Tuổi thơ mỗi cuối tuần luôn khao khát chảo gang xì xèo bò bít tết băm, xúc xích dai dai, trứng ốp la đào vàng mọng, trét kèm lớp pate gan mịn mượt đẫm ngậy chấm một miếng bánh mì giòn tan hổi.',
                'image' => 'https://images.unsplash.com/photo-1627308595229-7830f5c92135?auto=format&fit=crop&q=80&w=900',
                'category_id' => 3, // Khai vị / Ăn sáng
                'cooking_time' => 15,
                'difficulty' => 'easy',
                'total_calories' => 650,
                'total_protein' => 25,
                'total_carbs' => 50,
                'total_fat' => 35,
                'ingredients' => [
                    ['name' => 'Bánh mì baguette', 'quantity' => '2', 'unit' => 'ổ', 'notes' => ''],
                    ['name' => 'Trứng gà', 'quantity' => '2', 'unit' => 'quả', 'notes' => ''],
                    ['name' => 'Pate gan heo', 'quantity' => '80', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Xúc xích xông khói', 'quantity' => '2', 'unit' => 'cây', 'notes' => 'Khía xéo'],
                    ['name' => 'Bò rọi thái lát dập', 'quantity' => '100', 'unit' => 'g', 'notes' => 'Tẩm xíu mắm đường mỏng'],
                    ['name' => 'Hành tây', 'quantity' => '0.5', 'unit' => 'củ', 'notes' => ''],
                    ['name' => 'Bơ phết', 'quantity' => '1', 'unit' => 'muỗng', 'notes' => ''],
                    ['name' => 'Cà chua, ngò rí', 'quantity' => '1', 'unit' => 'ít', 'notes' => '']
                ],
                'steps' => [
                    'Làm chảo gang lên bếp lửa lớn, cho bơ phết vào tan chảy sôi lên kêu xèo lạch cạch sướng lỗ tai.',
                    'Thả xúc xích khía vào đảo một lúc cho da bung hoa đều lên. Cho tiếp hành tây thái sợi và miếng thịt bò vào chỉ chừng 30 giây đến khi bò tái đổi màu tươm nước áp ra.',
                    'Đập trực tiếp 2 quả trứng gà vào góc trống trong chảo đun ốp la lên mắt ngà ngà chưa tới để lòng đỏ chảy.',
                    'Dùng thìa múc nhanh lượng pate béo để vào góc trống còn lại. Cắt mỏng thêm miếng cà chua thả góc chảo cho nước sốt hòa vào nhau.',
                    'Đợi mặt trứng viền xém giòn, rắc hành ngò lên trên cùng xíu tiêu đen và tương ớt/tương cà xịt tròn xung quanh. Nhấc nguyên chảo xuống đệm gỗ kê bàn nóng!',
                    'Cầm ổ bánh mì xé thành đôi, quẹt cái lòng đỏ trứng vàng óng pha với pate tủy chấm rã vào rã vào miệng cực mê.'
                ]
            ],
            [
                'title' => 'Tacos Gà Nướng Mexico',
                'description' => 'Món Tacos giòn tan cuộn trọn vẹn gia vị ướp Mexican đầy hoang dã. Gà xé đậm đà cay nồng hòa cùng gừng và sốt Salsa chu chua tươi mát, phủ thật nhiều ngò gai và bơ xắt cục hạt lựu béo bùi.',
                'image' => 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?auto=format&fit=crop&q=80&w=900',
                'category_id' => 8, // Món chính
                'cooking_time' => 35,
                'difficulty' => 'easy',
                'total_calories' => 450,
                'total_protein' => 28,
                'total_carbs' => 35,
                'total_fat' => 20,
                'ingredients' => [
                    ['name' => 'Bánh Tacos cứng (Taco shells)', 'quantity' => '6', 'unit' => 'vỏ', 'notes' => ''],
                    ['name' => 'Ức hoặc đùi gà xay', 'quantity' => '300', 'unit' => 'g', 'notes' => ''],
                    ['name' => 'Bột gia vị Taco', 'quantity' => '1', 'unit' => 'gói', 'notes' => 'Mua siêu thị'],
                    ['name' => 'Cà chua chín', 'quantity' => '3', 'unit' => 'quả', 'notes' => 'Băm nhuyễn làm Salsa'],
                    ['name' => 'Hành tây đỏ', 'quantity' => '1', 'unit' => 'củ', 'notes' => 'Băm nhỏ'],
                    ['name' => 'Quả bơ (Avocado)', 'quantity' => '1', 'unit' => 'quả', 'notes' => 'Cắt khối nhỏ'],
                    ['name' => 'Rau mùi (Coriander)', 'quantity' => '1', 'unit' => 'mớ', 'notes' => ''],
                    ['name' => 'Chanh xanh (Lime)', 'quantity' => '2', 'unit' => 'quả', 'notes' => '']
                ],
                'steps' => [
                    'Làm nhân gà: Phi chút tỏi với dầu, đổ thịt gà xay vào xào lăn. Khi gà tiết nước và chuyển màu, rắc bột gia vị Taco cùng chút xíu nước. Đun lửa nhỏ đến khi sốt rút keo lại bao phủ miễng gà khô khô thơm thơm.',
                    'Làm sốt Salsa (Pico de Gallo): Trộn đều cà chua băm, hành tây đỏ băm, rau mùi, một xíu muối, tiêu đỏ và vắt đẫm nước cốt 1 quả chanh xanh. Xóc đều để tủ lạnh dịu vị.',
                    'Làm Tacos: Lấy bột Tacos nướng giòn trên chảo hoặc lò. Lấy vỏ gập hình chiếc móng ngựa.',
                    'Gắn nhân: Xúc lớp gà Mexico bỏ vào ruột Taco đầu tiên, rồi rải bơ xắt hạt lựu dẻo mịn bọc lên.',
                    'Múc dồi dào thìa salsa cà chua trải phủ kín phần kem dư thừa. Rắc rau mùi xanh thơm tươi cuối cùng. Ăn kèm sốt chua hoặc tabasco rụm!'
                ]
            ],
            [
                'title' => 'Ếch Om Chuối Đậu',
                'description' => 'Món nhậu gắn liền với ẩm thực đồng quê miền Bắc, nồi ếch om dẻo sánh thơm lựng thứ mùi đặc trưng từ tía tô lá lốt, miếng ếch dai chắc thịt hòa quyện cùng chuối xanh bùi dẻo quánh tơi, mẻ chua thanh nhẹ ngon rạo rực.',
                'image' => 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?auto=format&fit=crop&q=80&w=900',
                'category_id' => 8, // Món chính
                'cooking_time' => 50,
                'difficulty' => 'medium',
                'total_calories' => 450,
                'total_protein' => 38,
                'total_carbs' => 45,
                'total_fat' => 12,
                'ingredients' => [
                    ['name' => 'Ếch đồng ngon', 'quantity' => '500', 'unit' => 'g', 'notes' => 'Làm sạch, chặt nhỏ'],
                    ['name' => 'Chuối xanh', 'quantity' => '4', 'unit' => 'quả', 'notes' => 'Tước vỏ thái xé'],
                    ['name' => 'Đậu phụ chiên', 'quantity' => '2', 'unit' => 'miếng', 'notes' => 'Cắt khối vuông'],
                    ['name' => 'Thịt ba vị heo', 'quantity' => '200', 'unit' => 'g', 'notes' => 'Thái mỏng rang cháy cạnh'],
                    ['name' => 'Mẻ chua', 'quantity' => '2', 'unit' => 'muỗng', 'notes' => 'Lọc lấy nước cốt'],
                    ['name' => 'Nghệ tươi', 'quantity' => '1', 'unit' => 'nhánh', 'notes' => 'Giã vắt cốt vàng'],
                    ['name' => 'Tía tô, Lá lốt, Hành hoa', 'quantity' => '1', 'unit' => 'nắm', 'notes' => 'Thái mỏng'],
                    ['name' => 'Hành tím', 'quantity' => '2', 'unit' => 'củ', 'notes' => '']
                ],
                'steps' => [
                    'Sơ chế ếch ướp với chút xíu mắm, hạt tiêu đen và một nửa nước cốt nghệ tươi trong 15p.',
                    'Chuối xanh ngâm nước dấm loãng cho k thâm gỉ. Rửa tráng đem luộc qua hoặc chiên chiên vàng giòn ở mặt tùy thích để dai vỏ.',
                    'Bật chảo chiên đậu phụ xong cất góc, tiện tay rang cháy cạnh thịt ba rọi heo tươm ra chút mỡ béo bùi cho vào rổ.',
                    'Dùng lại cái chảo phi thơm hành khô, trút thịt ếch xào lăn trên ngọn lửa bùng săn lại tươm dậy mùi thơm vàng nghệ.',
                    'Cho tất cả thịt lợn, tôm đậu, hạt ếch, chuối thái miếng vào xào chung nồi. Đổ nước mẻ dầm chua hòa nhập đổ săm sắp.',
                    'Hầm nhỏ lửa vung kĩ 15 phút. Nêm bột ngọt, nếm chua vừa đủ. Rưới cuối cùng tía tô lá lốt lên là tỏa khói sôi xừng xực múc ra bát canh bún.'
                ]
            ],
        ];

        $imported = 0;
        foreach ($recipes as $item) {
            $baseSlug = Str::slug($item['title']);
            $slug     = $baseSlug . '-' . Str::random(4);

            $recipe = Recipe::create([
                'user_id'        => $adminUser->id,
                'category_id'    => $item['category_id'],
                'title'          => $item['title'],
                'slug'           => Str::lower($slug),
                'description'    => $item['description'],
                'cooking_time'   => $item['cooking_time'],
                'difficulty'     => $item['difficulty'],
                'total_calories' => $item['total_calories'],
                'total_protein'  => $item['total_protein'],
                'total_carbs'    => $item['total_carbs'],
                'total_fat'      => $item['total_fat'],
                'image'          => $item['image'], // using unsplash links directly
                'view_count'     => rand(100, 3000),
                'is_featured'    => (rand(1, 3) === 1),
                'status'         => 'published',
            ]);

            foreach ($item['ingredients'] as $ing) {
                // Find or create the ingredient
                $ingredientModel = \App\Models\Ingredient::firstOrCreate(
                    ['name' => $ing['name']],
                    [
                        'slug' => \Illuminate\Support\Str::slug($ing['name'] . '-' . \Illuminate\Support\Str::random(4)),
                        'unit' => $ing['unit'] ?: 'g'
                    ]
                );
                
                DB::table('recipe_ingredients')->insert([
                    'recipe_id'  => $recipe->id,
                    'ingredient_id' => $ingredientModel->id,
                    'quantity'   => floatval(preg_replace('/[^0-9.]/', '', strval($ing['quantity']))) ?: 1.0,
                    'notes'      => $ing['notes'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($item['steps'] as $idx => $stepInst) {
                DB::table('recipe_steps')->insert([
                    'recipe_id'   => $recipe->id,
                    'step_number' => $idx + 1,
                    'description' => $stepInst,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
            $imported++;
        }

        // --- SEED ARTICLES (TẠP CHÍ) ---
        Article::truncate();
        $articles = [
            [
                'title' => 'Bí quyết nấu món Phở Bò truyền thống đậm đà',
                'slug' => Str::slug('Bí quyết nấu món Phở Bò truyền thống đậm đà'),
                'excerpt' => 'Khám phá những bí quyết gia truyền để có nồi nước dùng phở trong veo, ngọt thanh và thơm nồng nàn.',
                'content' => 'Nội dung chi tiết về cách nấu phở...',
                'thumbnail' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?w=800',
                'tag' => 'Bí quyết, Truyền thống',
                'is_active' => true,
                'view_count' => rand(100, 500),
                'user_id' => $adminUser->id
            ],
            [
                'title' => 'Top 10 thực phẩm tốt nhất cho sức khỏe mùa hè',
                'slug' => Str::slug('Top 10 thực phẩm tốt nhất cho sức khỏe mùa hè'),
                'excerpt' => 'Mùa hè nắng nóng, hãy bổ sung ngay những thực phẩm này để giữ cơ thể luôn tươi trẻ và tràn đầy năng lượng.',
                'thumbnail' => 'https://images.unsplash.com/photo-1490818387583-1baba5e638af?w=800',
                'content' => 'Nội dung chi tiết về thực phẩm mùa hè...',
                'tag' => 'Sức khỏe, Mùa hè',
                'is_active' => true,
                'view_count' => rand(100, 500),
                'user_id' => $adminUser->id
            ],
            [
                'title' => 'Xu hướng ẩm thực xanh và bền vững năm 2026',
                'slug' => Str::slug('Xu hướng ẩm thực xanh và bền vững năm 2026'),
                'excerpt' => 'Ẩm thực không chỉ là ăn ngon mà còn là trách nhiệm với môi trường. Cùng điểm qua những xu hướng mới nhất.',
                'thumbnail' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800',
                'content' => 'Nội dung chi tiết về ẩm thực xanh...',
                'tag' => 'Xu hướng, Sống xanh',
                'is_active' => true,
                'view_count' => rand(100, 500),
                'user_id' => $adminUser->id
            ]
        ];

        foreach ($articles as $art) {
            Article::create($art);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("Seeded {$imported} authentic recipes and 3 articles successfully.");
    }
}
