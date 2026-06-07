<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\User;
use App\Models\RecipeStep;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ImportMealsApi extends Command
{
    protected $signature = 'meals:import {--count=50 : Number of recipes to import} {--fresh : Clear existing API recipes before importing}';
    protected $description = 'Import recipes from TheMealDB API into Kitchen Corner';

    // Map TheMealDB categories -> Kitchen Corner category IDs
    private array $categoryMap = [
        'Beef'        => 5,  // Thịt & Gia cầm
        'Chicken'     => 5,  // Thịt & Gia cầm
        'Pork'        => 5,  // Thịt & Gia cầm
        'Lamb'        => 5,  // Thịt & Gia cầm
        'Seafood'     => 8,  // Món chính
        'Pasta'       => 6,  // Mì - Bún - Phở
        'Dessert'     => 9,  // Món tráng miệng
        'Starter'     => 3,  // Món khai vị
        'Breakfast'   => 3,  // Món khai vị
        'Side'        => 8,  // Món chính
        'Vegan'       => 1,  // Món ăn chay
        'Vegetarian'  => 1,  // Món ăn chay
        'Miscellaneous' => 2, // Ẩm thực quốc tế
        'Asian'       => 2,  // Ẩm thực quốc tế
        'Indian'      => 2,  // Ẩm thực quốc tế
        'Mexican'     => 2,  // Ẩm thực quốc tế
        'French'      => 2,  // Ẩm thực quốc tế
        'Italian'     => 2,  // Ẩm thực quốc tế
        'American'    => 2,  // Ẩm thực quốc tế
        'British'     => 2,  // Ẩm thực quốc tế
        'Greek'       => 2,  // Ẩm thực quốc tế
        'Spanish'     => 2,  // Ẩm thực quốc tế
        'Turkish'     => 2,  // Ẩm thực quốc tế
        'Japanese'    => 2,  // Ẩm thực quốc tế
        'Chinese'     => 2,  // Ẩm thực quốc tế
        'Thai'        => 2,  // Ẩm thực quốc tế
        'Filipino'    => 2,  // Ẩm thực quốc tế
        'Vietnamese'  => 7,  // Ẩm thực miền Bắc (Vietnamese cuisine)
        'Canadian'    => 2,
        'Moroccan'    => 2,
        'Russian'     => 2,
        'Polish'      => 2,
        'Tunisian'    => 2,
        'Kenyan'      => 2,
        'Jamaican'    => 2,
        'Dutch'       => 2,
        'Croatian'    => 2,
        'Egyptian'    => 2,
        'Malaysian'   => 2,
        'Portuguese'  => 2,
    ];

    // Difficulty estimation based on number of ingredients
    private function estimateDifficulty(int $ingredientCount): string
    {
        if ($ingredientCount <= 5) return 'easy';
        if ($ingredientCount <= 10) return 'medium';
        return 'hard';
    }

    // Estimate cooking time from instructions word count
    private function estimateCookingTime(string $instructions): int
    {
        $words = str_word_count(strip_tags($instructions));
        if ($words < 100) return 20;
        if ($words < 300) return 45;
        if ($words < 600) return 60;
        return 90;
    }

    public function handle(): int
    {
        $count = (int) $this->option('count');
        $fresh = $this->option('fresh');

        // Get all user IDs to distribute recipes
        $userIds = User::pluck('id')->toArray();
        if (empty($userIds)) {
            $this->error('❌ No users found. Please run db:seed first.');
            return self::FAILURE;
        }

        $this->info("👨‍🍳 Found " . count($userIds) . " authors to distribute recipes to.");

        // Clear existing API-imported recipes if --fresh
        if ($fresh) {
            $this->warn('🗑️  Clearing recipes imported from API...');
            Recipe::where('description', 'like', '%[TheMealDB]%')->forceDelete();
        }

        // Fetch all meal categories from TheMealDB
        $this->info('🌐 Fetching categories from TheMealDB...');
        $categoriesRes = Http::timeout(15)->get('https://www.themealdb.com/api/json/v1/1/categories.php');

        if (!$categoriesRes->successful()) {
            $this->error('❌ Failed to fetch categories from TheMealDB.');
            return self::FAILURE;
        }

        $apiCategories = collect($categoriesRes->json('categories') ?? [])
            ->pluck('strCategory')
            ->shuffle()
            ->values();

        // Initialize translator
        $tr = new GoogleTranslate('vi', 'en');

        $imported = 0;
        $skipped  = 0;
        $bar      = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($apiCategories as $apiCategory) {
            if ($imported >= $count) break;

            // Get meals list for this category
            $listRes = Http::timeout(15)->get('https://www.themealdb.com/api/json/v1/1/filter.php', [
                'c' => $apiCategory,
            ]);
            if (!$listRes->successful()) continue;

            $meals = collect($listRes->json('meals') ?? [])->shuffle()->take(5);

            foreach ($meals as $mealItem) {
                if ($imported >= $count) break;

                $mealId = $mealItem['idMeal'];

                // Check duplicate
                if (Recipe::withTrashed()->where('description', 'like', "%[MealID:{$mealId}]%")->exists()) {
                    $skipped++;
                    continue;
                }

                // Get full meal details
                $detailRes = Http::timeout(15)->get('https://www.themealdb.com/api/json/v1/1/lookup.php', [
                    'i' => $mealId,
                ]);
                if (!$detailRes->successful()) continue;

                $meal = $detailRes->json('meals.0');
                if (!$meal) continue;

                // Extract ingredients (TheMealDB uses strIngredient1..20 + strMeasure1..20)
                $ingredients = [];
                for ($i = 1; $i <= 20; $i++) {
                    $ingName    = trim($meal["strIngredient{$i}"] ?? '');
                    $ingMeasure = trim($meal["strMeasure{$i}"] ?? '');
                    if ($ingName && $ingName !== '') {
                        $ingredients[] = ['name' => $ingName, 'measure' => $ingMeasure];
                    }
                }

                // Split instructions into steps (by newline or ". " for numbered steps)
                $rawInstructions = $meal['strInstructions'] ?? '';
                $stepLines = preg_split('/\r\n|\r|\n|(?<=\.)\s+(?=STEP\s*\d)/i', $rawInstructions);
                $steps = collect($stepLines)
                    ->map(fn($s) => trim(preg_replace('/^(STEP\s*\d+[:\.\-]*\s*)/i', '', $s)))
                    ->filter(fn($s) => strlen($s) > 20)
                    ->values()
                    ->toArray();

                if (empty($steps)) {
                    // Fallback: split by period
                    $steps = collect(explode('.', $rawInstructions))
                        ->map(fn($s) => trim($s))
                        ->filter(fn($s) => strlen($s) > 20)
                        ->values()
                        ->toArray();
                }

                // Map category
                $categoryId = $this->categoryMap[$apiCategory] ?? 2; // default: Ẩm thực quốc tế

                // Generate slug
                $baseSlug = Str::slug($meal['strMeal']);
                $slug = $baseSlug . '-' . Str::random(5);
                while (Recipe::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . Str::random(5);
                }

                $ingCount  = count($ingredients);
                $difficulty = $this->estimateDifficulty($ingCount);
                $cookTime   = $this->estimateCookingTime($rawInstructions);

                DB::beginTransaction();
                try {
                    $translatedTitle = $tr->translate($meal['strMeal']);
                    $originalDesc = ($meal['strArea'] ? "Xuất xứ: {$meal['strArea']}. " : '') .
                            "Công thức thuộc danh mục {$apiCategory}. " .
                            "Nguyên liệu gồm: " . collect($ingredients)->pluck('name')->take(5)->implode(', ') . "..." .
                            " [TheMealDB] [MealID:{$mealId}]";
                    $translatedDesc = $tr->translate($originalDesc);
                    
                    // Ensure the tag remains correctly formatted after translation
                    $translatedDesc = preg_replace('/\[.*?MealDB.*?\]/i', '[TheMealDB]', $translatedDesc);
                    $translatedDesc = preg_replace('/\[.*?Meal.*?ID.*?(\d+).*?\]/i', '[MealID:$1]', $translatedDesc);

                    // Create recipe
                    $recipe = Recipe::create([
                        'user_id'        => $userIds[array_rand($userIds)],
                        'category_id'    => $categoryId,
                        'title'          => $translatedTitle ?: $meal['strMeal'],
                        'slug'           => $slug,
                        'description'    => $translatedDesc ?: $originalDesc,
                        'cooking_time'   => $cookTime,
                        'difficulty'     => $difficulty,
                        'image'          => $meal['strMealThumb'] ?? null,
                        'view_count'     => rand(50, 2500),
                        'is_featured'    => (rand(1, 5) === 1),
                        'status'         => 'published',
                        'total_calories' => rand(250, 750),
                        'total_protein'  => rand(10, 50),
                        'total_carbs'    => rand(20, 80),
                        'total_fat'      => rand(5, 40),
                    ]);

                    // Create steps
                    foreach ($steps as $idx => $stepText) {
                        $translatedStep = $tr->translate($stepText);
                        RecipeStep::create([
                            'recipe_id'   => $recipe->id,
                            'step_number' => $idx + 1,
                            'description' => $translatedStep ?: $stepText,
                        ]);
                    }

                    // Create / attach ingredients
                    foreach ($ingredients as $ing) {
                        $translatedIngName = $tr->translate($ing['name']);
                        $ingredientModel = Ingredient::firstOrCreate(
                            ['name' => $translatedIngName ?: $ing['name']],
                            [
                                'slug' => Str::slug($translatedIngName ?: $ing['name']) . '-' . Str::random(4),
                                'unit' => 'g',
                            ]
                        );

                        // Parse quantity from measure (e.g. "200g", "2 cups", "1 tsp")
                        preg_match('/[\d\.]+/', $ing['measure'], $qtyMatch);
                        $qty = !empty($qtyMatch) ? (float) $qtyMatch[0] : 1.0;
                        
                        $translatedMeasure = $tr->translate($ing['measure']);

                        DB::table('recipe_ingredients')->insertOrIgnore([
                            'recipe_id'     => $recipe->id,
                            'ingredient_id' => $ingredientModel->id,
                            'quantity'      => $qty,
                            'notes'         => $translatedMeasure ?: $ing['measure'],
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }

                    DB::commit();
                    $imported++;
                    $bar->advance();

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->newLine();
                    $this->warn("⚠️  Skipped '{$meal['strMeal']}': " . $e->getMessage());
                    $skipped++;
                }

                // Small delay to be kind to the API
                usleep(200000); // 0.2s
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Done! Imported: {$imported} recipes | Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
