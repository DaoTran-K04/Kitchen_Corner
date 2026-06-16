<?php

namespace App\Services;

use App\Models\SupabaseIngredient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//crawl và chuẩn hóa N_BASE từ USDA thông qua api này
class SpoonacularService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.spoonacular.com';

    public function __construct()
    {
        $this->apiKey = env('SPOONACULAR_API_KEY');
    }

    public function translateVietnameseToEnglish($vietnameseName)
    {
        $key = strtolower(trim($vietnameseName));
        
        try {
            $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
            $tr->setSource('vi');
            $tr->setTarget('en');
            $englishName = $tr->translate($key);
            
            return strtolower(trim($englishName));
        } catch (\Exception $e) {
            Log::error("Translation failed for {$key}: " . $e->getMessage());
            return $key;
        }
    }

    public function getNutritionInfo($vietnameseName)
    {
        // 1. Dịch sang tiếng Anh
        $englishName = $this->translateVietnameseToEnglish($vietnameseName);

        // 2. Tra cứu Supabase
        $cachedIngredient = SupabaseIngredient::where('vietnamese_name', $vietnameseName)
                                              ->orWhere('english_name', $englishName)
                                              ->first();

        if ($cachedIngredient) {
            return $cachedIngredient;
        }

        // 3. Nếu chưa có, gọi Spoonacular API
        // Endpoint: GET https://api.spoonacular.com/food/ingredients/search?query=...
        $searchResponse = Http::get("{$this->baseUrl}/food/ingredients/search", [
            'query' => $englishName,
            'apiKey' => $this->apiKey,
            'number' => 1
        ]);

        if ($searchResponse->successful() && !empty($searchResponse->json('results'))) {
            $ingredientId = $searchResponse->json('results')[0]['id'];
            $image = $searchResponse->json('results')[0]['image'];

            // Endpoint: GET https://api.spoonacular.com/food/ingredients/{id}/information
            $infoResponse = Http::get("{$this->baseUrl}/food/ingredients/{$ingredientId}/information", [
                'amount' => 100, // Chuẩn hóa về 100g
                'unit' => 'grams',
                'apiKey' => $this->apiKey
            ]);

            if ($infoResponse->successful()) {
                $nutrients = collect($infoResponse->json('nutrition.nutrients'));
                
                $calories = $nutrients->firstWhere('name', 'Calories')['amount'] ?? 0;
                $protein = $nutrients->firstWhere('name', 'Protein')['amount'] ?? 0;
                $carbs = $nutrients->firstWhere('name', 'Carbohydrates')['amount'] ?? 0;
                $fat = $nutrients->firstWhere('name', 'Fat')['amount'] ?? 0;

                // 4. Lưu vào Supabase để dùng lần sau
                $newIngredient = SupabaseIngredient::create([
                    'vietnamese_name' => strtolower(trim($vietnameseName)),
                    'english_name' => $englishName,
                    'calories' => (int) $calories,
                    'protein' => (float) $protein,
                    'carbs' => (float) $carbs,
                    'fat' => (float) $fat,
                    'image_url' => "https://spoonacular.com/cdn/ingredients_100x100/{$image}",
                ]);

                return $newIngredient;
            }
        }

        Log::error("Spoonacular: Could not find ingredient info for {$englishName}");
        return null;
    }
}
