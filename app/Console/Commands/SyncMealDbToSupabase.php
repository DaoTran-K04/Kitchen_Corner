<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SupabaseRecipe;

class SyncMealDbToSupabase extends Command
{
    protected $signature = 'mealdb:sync-supabase';
    protected $description = 'Sync raw data from TheMealDB directly to Supabase PostgreSQL Data Lake';

    public function handle()
    {
        $this->info('Bắt đầu đồng bộ dữ liệu gốc từ TheMealDB lên Supabase Data Lake...');

        $categories = ['Beef', 'Chicken', 'Seafood', 'Lamb', 'Pork', 'Pasta', 'Dessert', 'Vegetarian', 'Vegan', 'Starter'];
        $totalSynced = 0;

        foreach ($categories as $cat) {
            $this->info("Đang lấy danh mục: {$cat}");
            
            $listUrl = "https://www.themealdb.com/api/json/v1/1/filter.php?c={$cat}";
            $listResp = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RESOLVE => ['www.themealdb.com:443:104.21.57.122']]])->timeout(15)->withoutVerifying()->get($listUrl);

            if (!$listResp->successful()) continue;

            $meals = $listResp->json()['meals'] ?? [];
            
            foreach ($meals as $meal) {
                // Check if exists
                if (SupabaseRecipe::where('meal_id', $meal['idMeal'])->exists()) {
                    continue;
                }

                $detailUrl = "https://www.themealdb.com/api/json/v1/1/lookup.php?i=" . $meal['idMeal'];
                $detailResp = Http::withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RESOLVE => ['www.themealdb.com:443:104.21.57.122']]])->timeout(15)->withoutVerifying()->get($detailUrl);

                if (!$detailResp->successful()) continue;

                $detail = $detailResp->json()['meals'][0] ?? null;
                if (!$detail) continue;

                // Build ingredients json
                $ingredients = [];
                for ($i = 1; $i <= 20; $i++) {
                    $ing = trim($detail["strIngredient{$i}"] ?? '');
                    $measure = trim($detail["strMeasure{$i}"] ?? '');
                    if ($ing !== '') {
                        $ingredients[] = ['name' => $ing, 'amount' => $measure];
                    }
                }

                try {
                    SupabaseRecipe::create([
                        'meal_id' => $detail['idMeal'],
                        'name' => $detail['strMeal'],
                        'category' => $detail['strCategory'],
                        'area' => $detail['strArea'],
                        'instructions' => $detail['strInstructions'],
                        'image_url' => $detail['strMealThumb'],
                        'youtube_url' => $detail['strYoutube'],
                        'ingredients_json' => $ingredients,
                    ]);
                    $totalSynced++;
                    $this->line(" Đã đồng bộ lên Supabase: " . $detail['strMeal']);
                } catch (\Exception $e) {
                    $this->error(" Lỗi lưu Supabase: " . $e->getMessage());
                }

                usleep(200000);
            }
        }

        $this->info("Đồng bộ hoàn tất! Đã lưu {$totalSynced} công thức vào Supabase Data Lake.");
    }
}
