<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ingredient;
use App\Services\SpoonacularService;

class SyncIngredientNutrition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingredients:sync-nutrition {--force : Force sync all ingredients to Supabase}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update nutrition data for all ingredients from Spoonacular API';

    /**
     * Execute the console command.
     */
    public function handle(SpoonacularService $spoonacularService)
    {
        $this->info('Bắt đầu đồng bộ dữ liệu dinh dưỡng nguyên liệu...');

        if ($this->option('force')) {
            $ingredients = Ingredient::all();
            $this->warn('Chế độ Force: Đồng bộ tất cả nguyên liệu lên Supabase...');
        } else {
            $ingredients = Ingredient::where('calories_per_unit', 0)->get();
        }

        if ($ingredients->isEmpty()) {
            $this->info('Tất cả nguyên liệu đã có dữ liệu dinh dưỡng.');
            return;
        }

        $bar = $this->output->createProgressBar($ingredients->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($ingredients as $ingredient) {
            // Lấy thông tin dinh dưỡng (hàm này sẽ tự động lưu vào SupabaseIngredient cache)
            $info = $spoonacularService->getNutritionInfo($ingredient->name);

            if ($info && ($info->calories > 0 || $info->protein > 0 || $info->carbs > 0 || $info->fat > 0)) {
                // Info in DB is based on 100g. 
                // We assume Ingredient unit is 'g' or 'ml' and we store per 100g.
                // If the local ingredient has a different default unit (like 'củ', 'trái'), 
                // it might be inaccurate without a conversion table, but we will store the raw 100g stats for now.
                $ingredient->update([
                    'calories_per_unit' => $info->calories,
                    'protein_per_unit' => $info->protein,
                    'carbs_per_unit' => $info->carbs,
                    'fat_per_unit' => $info->fat,
                ]);
                $successCount++;
            } else {
                // Xóa nguyên liệu không có thông số hoặc không tìm thấy trên API
                $ingredient->delete();
                $failCount++;
            }

            $bar->advance();
            
            // Tránh rate limit của Spoonacular (1s delay)
            sleep(1);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Đồng bộ hoàn tất! Thành công: {$successCount}, Thất bại: {$failCount}");
    }
}
