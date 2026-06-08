<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ingredient;
use App\Services\SpoonacularService;
use Illuminate\Support\Str;

class AddCommonIngredients extends Command
{
    protected $signature = 'ingredients:add-common';
    protected $description = 'Add common missing ingredients to DB and Supabase if they have valid nutrition data';

    public function handle(SpoonacularService $spoonacularService)
    {
        $commonIngredients = [
            'Cá ngừ', 'Mực', 'Tôm sú', 'Bạch tuộc', 'Cá lóc', 'Cá diêu hồng', 'Thịt vịt', 
            'Đậu hũ', 'Sả', 'Ớt sừng', 'Gừng tươi', 'Rau muống', 'Rau mồng tơi', 'Rau dền',
            'Bắp cải', 'Súp lơ trắng', 'Bông cải xanh', 'Đậu que', 'Đậu bắp', 'Cà tím',
            'Hành tím', 'Tỏi băm', 'Nghệ tươi', 'Bột ngọt', 'Hạt nêm', 'Giấm táo', 'Rượu vang đỏ',
            'Sữa đặc', 'Nước cốt dừa', 'Dầu hào', 'Tương ớt', 'Tương đen', 'Mật ong', 'Bơ lạt',
            'Thịt ba chỉ', 'Sườn non', 'Thịt bò phi lê', 'Ức gà', 'Trứng cút', 'Trứng vịt'
        ];

        $this->info("Bắt đầu thêm " . count($commonIngredients) . " nguyên liệu thông dụng...");
        
        $added = 0;
        $skipped = 0;

        foreach ($commonIngredients as $name) {
            // Check if already in Ingredient table
            if (Ingredient::where('name', $name)->exists()) {
                $this->line("- Đã tồn tại: $name");
                continue;
            }

            // Gọi SpoonacularService. Hàm này TỰ ĐỘNG lưu vào SupabaseIngredient (bảng supabase_ingredients)
            // theo yêu cầu "nhớ cập nhật vào supbase giúp tôi".
            $info = $spoonacularService->getNutritionInfo($name);

            // "Nhớ có thông số thì mới cập nhật vào, còn số liệu bằng 0 thì không cần cập nhật"
            if ($info && ($info->calories > 0 || $info->protein > 0 || $info->carbs > 0 || $info->fat > 0)) {
                Ingredient::create([
                    'name' => $name,
                    'slug' => Str::slug($name . '-' . Str::random(4)),
                    'unit' => 'g',
                    'calories_per_unit' => $info->calories,
                    'protein_per_unit' => $info->protein,
                    'carbs_per_unit' => $info->carbs,
                    'fat_per_unit' => $info->fat,
                ]);
                $this->info("✅ Đã thêm: $name");
                $added++;
            } else {
                $this->warn("❌ Bỏ qua (không có thông số): $name");
                $skipped++;
            }
            
            usleep(500000); // 0.5s delay
        }

        $this->info("Hoàn tất! Đã thêm: $added, Bỏ qua: $skipped.");
    }
}
