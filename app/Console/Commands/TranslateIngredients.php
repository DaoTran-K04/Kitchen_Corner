<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Str;

class TranslateIngredients extends Command
{
    protected $signature = 'ingredients:translate';
    protected $description = 'Translate English ingredient names to Vietnamese and merge duplicates';

    public function handle()
    {
        $this->info('Bắt đầu dịch tên nguyên liệu...');
        
        $tr = new GoogleTranslate();
        $tr->setTarget('vi');
        
        $ingredients = Ingredient::withTrashed()->get();
        $bar = $this->output->createProgressBar($ingredients->count());
        $bar->start();
        
        $mergedCount = 0;
        $translatedCount = 0;
        
        foreach ($ingredients as $ingredient) {
            $name = $ingredient->name;
            
            // Check if name lacks vietnamese diacritics
            if (!preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/u', strtolower($name))) {
                try {
                    $tr->setSource('en');
                    $translated = $tr->translate($name);
                    
                    // Fix some weird translations
                    if (strtolower($translated) == 'tất cả các mục đích') $translated = 'Gia vị tổng hợp';
                    if (strtolower($translated) == 'tiêu tất cả') $translated = 'Hạt tiêu đen';
                    
                    $translated = mb_strtoupper(mb_substr($translated, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($translated, 1, null, 'UTF-8');
                    
                    if (strtolower($translated) !== strtolower($name)) {
                        
                        $existing = Ingredient::where('name', $translated)->where('id', '!=', $ingredient->id)->first();
                        
                        if ($existing) {
                            // Merge into existing: update recipe_ingredients
                            DB::table('recipe_ingredients')
                                ->where('ingredient_id', $ingredient->id)
                                ->update(['ingredient_id' => $existing->id]);
                            
                            // force Delete the old English ingredient
                            $ingredient->forceDelete(); 
                            $mergedCount++;
                        } else {
                            $ingredient->name = $translated;
                            $ingredient->slug = Str::slug($translated . '-' . Str::random(4));
                            $ingredient->save();
                            $translatedCount++;
                        }
                    }
                    
                } catch (\Exception $e) {
                    // Ignore translate errors
                }
            }
            $bar->advance();
            // Sleep to avoid rate limiting
            usleep(200000); // 200ms
        }
        $bar->finish();
        $this->newLine();
        $this->info("Hoàn tất! Đã dịch {$translatedCount} nguyên liệu, gộp {$mergedCount} nguyên liệu trùng lặp.");
    }
}
