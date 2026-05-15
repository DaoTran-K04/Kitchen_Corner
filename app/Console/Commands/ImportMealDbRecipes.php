<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;

class ImportMealDbRecipes extends Command
{
    protected $signature = 'mealdb:import {--clear : Xóa toàn bộ công thức cũ trước khi import}';
    protected $description = 'Import công thức nấu ăn thật với ảnh đẹp từ TheMealDB API';

    // Bản đồ category TheMealDB → category_id Kitchen Corner
    private array $categoryMap = [
        'Beef'       => 8,  // Món chính
        'Chicken'    => 8,  // Món chính
        'Pork'       => 8,  // Món chính
        'Lamb'       => 8,  // Món chính
        'Seafood'    => 8,  // Món chính
        'Vegetarian' => 8,  // Món chính
        'Pasta'      => 6,  // Mì - Bún - Phở
        'Vegan'      => 9,  // Món tráng miệng (salad/vegan)
        'Dessert'    => 9,  // Món tráng miệng
        'Starter'    => 3,  // Món khai vị (map to category 3)
        'Breakfast'  => 3,
        'Side'       => 8,
        'Miscellaneous' => 8,
        'Goat'       => 8,
    ];

    // Dịch tên nguyên liệu sang tiếng Việt (những nguyên liệu phổ biến)
    private array $ingredientTranslations = [
        'chicken breast' => 'ức gà',
        'chicken thighs' => 'đùi gà',
        'chicken' => 'thịt gà',
        'beef' => 'thịt bò',
        'ground beef' => 'thịt bò xay',
        'pork' => 'thịt heo',
        'salmon' => 'cá hồi',
        'shrimp' => 'tôm',
        'rice' => 'gạo',
        'flour' => 'bột mì',
        'sugar' => 'đường',
        'salt' => 'muối',
        'pepper' => 'tiêu',
        'olive oil' => 'dầu ô liu',
        'vegetable oil' => 'dầu ăn',
        'garlic' => 'tỏi',
        'onion' => 'hành tây',
        'tomato' => 'cà chua',
        'egg' => 'trứng',
        'eggs' => 'trứng',
        'butter' => 'bơ',
        'milk' => 'sữa',
        'cheese' => 'phô mai',
        'lemon' => 'chanh vàng',
        'lime' => 'chanh xanh',
        'ginger' => 'gừng',
        'soy sauce' => 'nước tương',
        'fish sauce' => 'nước mắm',
        'coconut milk' => 'nước cốt dừa',
        'spring onions' => 'hành lá',
        'coriander' => 'ngò rí',
        'basil' => 'húng quế',
        'pasta' => 'mì pasta',
        'potato' => 'khoai tây',
        'potatoes' => 'khoai tây',
        'carrot' => 'cà rốt',
        'carrots' => 'cà rốt',
        'mushroom' => 'nấm',
        'spinach' => 'rau bina',
        'cream' => 'kem tươi',
        'breadcrumbs' => 'vụn bánh mì',
        'water' => 'nước',
        'broth' => 'nước dùng',
        'stock' => 'nước lèo',
        'vinegar' => 'giấm',
        'honey' => 'mật ong',
        'paprika' => 'ớt bột paprika',
        'cumin' => 'hạt thì là',
        'turmeric' => 'bột nghệ',
        'cinnamon' => 'quế',
        'bay leaves' => 'lá nguyệt quế',
        'tomato paste' => 'tương cà',
        'yogurt' => 'sữa chua',
        'noodles' => 'mì sợi',
    ];

    public function handle(): int
    {
        $this->info('🍽️  Bắt đầu import công thức từ TheMealDB...');

        // Xóa data cũ nếu có --clear
        if ($this->option('clear')) {
            $this->warn('⚠️  Đang xóa toàn bộ công thức cũ...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Recipe::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->info('✅ Đã xóa xong');
        }

        // Lấy user admin để gán làm tác giả
        $adminUser = User::where('role', 'admin')->first() ?? User::first();
        if (!$adminUser) {
            $this->error('❌ Không tìm thấy user nào trong database!');
            return 1;
        }
        $this->info("👤 Sẽ gán tất cả công thức cho user: {$adminUser->name}");

        // Đảm bảo thư mục lưu ảnh tồn tại
        $storageDir = public_path('assets/recipes');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate('vi', 'en');

        // Khởi tạo công cụ Dịch tự động (Anh -> Việt)
        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate('vi', 'en');

        $totalImported = 0;
        $totalSkipped  = 0;

        // Danh sách category TheMealDB để lấy
        $mealDbCategories = [
            'Beef', 'Chicken', 'Seafood', 'Lamb', 'Pork',
            'Pasta', 'Dessert', 'Vegetarian', 'Vegan', 'Starter'
        ];

        foreach ($mealDbCategories as $cat) {
            $this->info("\n📦 Đang lấy danh mục: {$cat}");

            $listUrl  = "https://www.themealdb.com/api/json/v1/1/filter.php?c={$cat}";
            $listResp = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RESOLVE => ['www.themealdb.com:443:104.21.57.122']]])->timeout(15)->withoutVerifying()->get($listUrl);

            if (!$listResp->successful()) {
                $this->warn("  ⚠️ Không lấy được danh sách {$cat}");
                continue;
            }

            $meals = $listResp->json()['meals'] ?? [];
            $this->info("  → Tìm thấy " . count($meals) . " món");

            // Lấy toàn bộ danh sách, không giới hạn mốc 10 món nữa!

            foreach ($meals as $meal) {
                $detailUrl  = "https://www.themealdb.com/api/json/v1/1/lookup.php?i=" . $meal['idMeal'];
                $detailResp = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RESOLVE => ['www.themealdb.com:443:104.21.57.122']]])->timeout(15)->withoutVerifying()->get($detailUrl);

                if (!$detailResp->successful()) {
                    $totalSkipped++;
                    continue;
                }

                $detail = $detailResp->json()['meals'][0] ?? null;
                if (!$detail) {
                    $totalSkipped++;
                    continue;
                }

                // Kiểm tra đã có chưa
                if (Recipe::where('title', $detail['strMeal'])->exists()) {
                    $this->line("  ⏭  Bỏ qua (đã có): " . $detail['strMeal']);
                    $totalSkipped++;
                    continue;
                }

                // Tải ảnh về local
                $imagePath = $this->downloadImage($detail['strMealThumb'], $detail['idMeal']);

                // Xây dựng nguyên liệu
                $ingredients = $this->buildIngredients($detail);

                // Tính nutrition ước tính
                $ingredientCount = count($ingredients);
                $calories  = rand(250, 650);
                $protein   = rand(15, 45);
                $carbs     = rand(20, 80);
                $fat       = rand(8, 35);

                // Map category
                $categoryId = $this->categoryMap[$cat] ?? 8;

                // Dịch Tên món ăn
                try {
                    $viName = $tr->translate($detail['strMeal']);
                } catch (\Exception $e) {
                    $viName = $detail['strMeal'];
                }

                // Tạo mô tả tiếng Việt
                $description = $this->translateIngredient($meal['strCategory'] ?? '') . ' thơm ngon, mang hương vị đặc trưng TheMealDB.';

                // Tạo slug không trùng
                $baseSlug = Str::slug($viName);
                $slug     = $baseSlug . '-' . Str::upper(Str::random(4));

                $recipe = Recipe::create([
                    'user_id'        => $adminUser->id,
                    'category_id'    => $categoryId,
                    'title'          => $viName,
                    'slug'           => $slug,
                    'description'    => $description,
                    'cooking_time'   => rand(20, 90),
                    'difficulty'     => $this->mapDifficulty($ingredientCount),
                    'total_calories' => $calories,
                    'total_protein'  => $protein,
                    'total_carbs'    => $carbs,
                    'total_fat'      => $fat,
                    'image'          => $imagePath,
                    'view_count'     => rand(10, 500),
                    'is_featured'    => (rand(1, 5) === 1),
                    'is_premium'     => true,
                    'status'         => 'published',
                ]);

                // Lưu nguyên liệu vào bảng recipe_ingredients nếu có
                $this->saveIngredients($recipe, $ingredients);

                // Lưu Các Bước Nấu Ăn
                $instructions = $detail['strInstructions'] ?? '';
                if (!empty($instructions)) {
                    try {
                        $viInstructions = $tr->translate($instructions);
                    } catch (\Exception $e) {
                         $viInstructions = $instructions;
                    }
                    
                    $stepsText = array_filter(array_map('trim', explode("\r\n", $viInstructions)));
                    if (count($stepsText) <= 1) {
                        $stepsText = array_filter(array_map('trim', explode(".", $viInstructions)));
                    }
                    
                    $stepNum = 1;
                    foreach ($stepsText as $stepDesc) {
                        if (strlen($stepDesc) > 5) {
                            \App\Models\RecipeStep::create([
                                'recipe_id' => $recipe->id,
                                'step_number' => $stepNum++,
                                'description' => $stepDesc
                            ]);
                        }
                    }
                }

                $this->line("  ✅ Đã import: " . $viName . " ({$cat})");
                $totalImported++;

                // Nghỉ nhỏ tránh spam API
                usleep(200000); // 200ms
            }
        }

        $this->newLine();
        $this->info("🎉 Hoàn tất!");
        $this->info("   ✅ Đã import: {$totalImported} công thức");
        $this->info("   ⏭  Bỏ qua:    {$totalSkipped} công thức");
        $this->info("   📊 Tổng hiện tại: " . Recipe::count() . " công thức");

        return 0;
    }

    private function downloadImage(string $url, string $id): string
    {
        try {
            $ext      = 'jpg';
            $filename = "mealdb_{$id}.{$ext}";
            $destPath = 'assets/recipes/' . $filename;
            $storagePath = public_path($destPath);
            // Sử dụng asset() để lấy URL linh hoạt thay vì fix cứng IP
            $absoluteHttpUrl = asset($destPath);

            if (file_exists($storagePath)) {
                return $absoluteHttpUrl;
            }


            $response = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RESOLVE => ['www.themealdb.com:443:104.21.57.122']]])->timeout(20)->withoutVerifying()->get($url);
            if ($response->successful()) {
                file_put_contents($storagePath, $response->body());
                return $absoluteHttpUrl;
            }
        } catch (\Exception $e) {
            // Fallback: dùng URL bên ngoài
        }

        // Fallback: dùng URL ảnh gốc (external URL)
        return $url;
    }

    private function buildIngredients(array $detail): array
    {
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $ingredient = trim($detail["strIngredient{$i}"] ?? '');
            $measure    = trim($detail["strMeasure{$i}"] ?? '');
            if ($ingredient !== '') {
                $viName       = $this->translateIngredient($ingredient);
                $ingredients[] = [
                    'name'   => $viName,
                    'amount' => $measure ?: '1 phần',
                ];
            }
        }
        return $ingredients;
    }

    private function translateIngredient(string $en): string
    {
        $lower = strtolower(trim($en));
        foreach ($this->ingredientTranslations as $key => $vi) {
            if (strpos($lower, $key) !== false) {
                return $vi;
            }
        }
        // Trả về tiếng Anh nếu không có bản dịch
        return ucfirst($en);
    }

    private function buildDescription(array $detail, string $category, array $ingredients): string
    {
        $name      = $detail['strMeal'];
        $area      = $detail['strArea'] ?? 'thế giới';
        $ingCount  = count($ingredients);

        $areaViMap = [
            'Vietnamese' => 'Việt Nam', 'Japanese' => 'Nhật Bản', 'Chinese' => 'Trung Hoa',
            'Thai' => 'Thái Lan', 'Italian' => 'Ý', 'American' => 'Mỹ', 'Mexican' => 'Mexico',
            'French' => 'Pháp', 'Indian' => 'Ấn Độ', 'Spanish' => 'Tây Ban Nha',
            'Greek' => 'Hy Lạp', 'Turkish' => 'Thổ Nhĩ Kỳ', 'British' => 'Anh',
            'Korean' => 'Hàn Quốc', 'Moroccan' => 'Ma Rốc', 'Caribbean' => 'Caribbean',
            'Unknown' => 'nhiều nơi', 'International' => 'quốc tế',
        ];
        $areaVi = $areaViMap[$area] ?? $area;

        $categoryViMap = [
            'Beef' => 'thịt bò', 'Chicken' => 'gà', 'Seafood' => 'hải sản',
            'Lamb' => 'thịt cừu', 'Pork' => 'thịt heo', 'Pasta' => 'pasta',
            'Dessert' => 'tráng miệng', 'Vegetarian' => 'chay', 'Vegan' => 'thuần chay',
            'Starter' => 'khai vị',
        ];
        $catVi = $categoryViMap[$category] ?? $category;

        return "{$name} là một món {$catVi} nổi tiếng đến từ ẩm thực {$areaVi}. "
             . "Món ăn này được chế biến từ {$ingCount} nguyên liệu tươi ngon, "
             . "mang đến hương vị đặc trưng và hấp dẫn cho người thưởng thức. "
             . "Với cách chế biến đơn giản nhưng tinh tế, {$name} là lựa chọn hoàn hảo cho "
             . "bữa ăn gia đình mỗi ngày. Hãy thử ngay công thức này và trổ tài nấu nướng nhé!";
    }

    private function mapDifficulty(int $ingredientCount): string
    {
        if ($ingredientCount <= 5) return 'easy';
        if ($ingredientCount <= 10) return 'medium';
        return 'hard';
    }

    private function saveIngredients(Recipe $recipe, array $ingredients): void
    {
        // Kiểm tra xem bảng recipe_ingredients có tồn tại không
        if (!\Schema::hasTable('recipe_ingredients')) return;

        foreach ($ingredients as $ing) {
            try {
                DB::table('recipe_ingredients')->insert([
                    'recipe_id'  => $recipe->id,
                    'name'       => $ing['name'],
                    'amount'     => $ing['amount'],
                    'unit'       => '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Bảng không có column phù hợp, bỏ qua
            }
        }
    }
}
