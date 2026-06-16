<?php

namespace App\Services\AI;

use App\Models\Recipe;
use App\Models\Ingredient;

class AiContextService
{
    /**
     * Get DB context based on intent and user message
     */
    public function getContext(string $intent, string $message): array
    {
        $context = [];
        $msg = mb_strtolower(trim($message));

        switch ($intent) {
            case 'recipe.recommend':
                $recipes = Recipe::where('status', 'published')
                    ->orderByDesc('view_count')
                    ->limit(5)
                    ->get(['id', 'title', 'cooking_time', 'difficulty', 'image', 'slug']);
                
                $context['suggested_recipes'] = $recipes->each->append('thumbnail');
                $context['message'] = "Đây là một số món ăn nổi bật và được yêu thích nhất hiện nay.";
                break;

            case 'recipe.diet':
                // Tìm món ít calo (< 400), ít fat, hoặc có title chứa từ khóa
                $recipes = Recipe::where('status', 'published')
                    ->where(function($q) {
                        $q->where('total_calories', '<', 400)
                          ->orWhere('title', 'like', '%healthy%')
                          ->orWhere('title', 'like', '%salad%')
                          ->orWhere('title', 'like', '%chay%');
                    })
                    ->limit(5)
                    ->get(['id', 'title', 'cooking_time', 'difficulty', 'total_calories', 'image', 'slug']);
                $context['suggested_recipes'] = $recipes->each->append('thumbnail');
                $context['message'] = "Gợi ý cho bạn một số món ăn healthy, ít calo và phù hợp với chế độ ăn kiêng.";
                break;

            case 'recipe.difficulty':
                // Tìm món dễ nấu, thời gian < 30 phút
                $recipes = Recipe::where('status', 'published')
                    ->where('difficulty', 'easy')
                    ->where('cooking_time', '<=', 30)
                    ->limit(5)
                    ->get(['id', 'title', 'cooking_time', 'difficulty', 'image', 'slug']);
                $context['suggested_recipes'] = $recipes->each->append('thumbnail');
                $context['message'] = "Đây là những món rất dễ nấu và tiết kiệm thời gian, phù hợp cho người bận rộn hoặc sinh viên.";
                break;

            case 'recipe.ingredient':
                // Detect ingredients from message
                $ingredients = Ingredient::all();
                $foundIngredients = [];
                foreach ($ingredients as $ing) {
                    if (mb_strpos($msg, mb_strtolower($ing->name)) !== false) {
                        $foundIngredients[] = $ing->id;
                    }
                }

                if (!empty($foundIngredients)) {
                    $recipes = Recipe::where('status', 'published')
                        ->whereHas('ingredients', function($q) use ($foundIngredients) {
                            $q->whereIn('ingredient_id', $foundIngredients);
                        })
                        ->limit(5)
                        ->get(['id', 'title', 'cooking_time', 'difficulty', 'image', 'slug']);
                    
                    $context['suggested_recipes'] = $recipes->each->append('thumbnail');
                    $context['message'] = "Dựa trên nguyên liệu bạn có, tôi tìm thấy những công thức này.";
                } else {
                    $context['message'] = "Tôi không nhận diện được nguyên liệu cụ thể trong câu hỏi, nhưng bạn có thể tham khảo mục Tìm kiếm theo Tủ lạnh web.";
                }
                break;

            case 'recipe.search':
            case 'recipe.detail':
                // Loại bỏ từ khóa thừa
                $cleanMsg = $msg;
                $remove = ['cách làm', 'hướng dẫn nấu', 'hướng dẫn', 'làm sao để', 'chỉ tôi làm', 'nấu món', 'món', 'tôi muốn', 'cách nấu', 'công thức'];
                foreach ($remove as $phrase) {
                    $cleanMsg = trim(preg_replace('/\b' . preg_quote($phrase, '/') . '\b/u', '', $cleanMsg));
                }
                
                if (mb_strlen($cleanMsg) > 2) {
                    $recipes = Recipe::where('status', 'published')
                        ->where(function($q) use ($cleanMsg) {
                            $lowerCleanMsg = mb_strtolower($cleanMsg, 'UTF-8');
                            $q->whereRaw('LOWER(title) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerCleanMsg}%"])
                              ->orWhereRaw('LOWER(description) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerCleanMsg}%"])
                              ->orWhereHas('category', function($cat) use ($lowerCleanMsg) {
                                  $cat->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerCleanMsg}%"]);
                              })
                              ->orWhereHas('ingredients', function($ing) use ($lowerCleanMsg) {
                                  $ing->whereRaw('LOWER(name) COLLATE utf8mb4_bin LIKE ?', ["%{$lowerCleanMsg}%"]);
                              });
                        })
                        ->limit(3)
                        ->get(['id', 'title', 'cooking_time', 'difficulty', 'image', 'slug', 'description']);
                    
                    if ($recipes->count() > 0) {
                        $context['suggested_recipes'] = $recipes->each->append('thumbnail');
                        $context['message'] = "Tôi tìm thấy công thức bạn cần đây:";
                    }
                }
                break;

            case 'site.help':
                $context['message'] = "Góc Bếp là nền tảng chia sẻ công thức nấu ăn của người Việt. Tại đây bạn có thể khám phá hàng ngàn công thức, tự tính toán dinh dưỡng, dùng AI gợi ý món từ nguyên liệu có sẵn, và kết nối với cộng đồng đầu bếp đam mê.";
                break;

            case 'review.help':
                $context['message'] = "Để viết một review món ăn hay, bạn nên:\n1. Tả lại hương vị chân thực (ngọt, mặn, độ giòn, độ mềm).\n2. Chia sẻ mẹo bạn đã áp dụng thành công (ví dụ: thêm chút chanh cho thơm).\n3. Đính kèm hình ảnh thành phẩm của bạn.\n4. Đánh giá độ khó thực tế so với công thức.";
                break;

            case 'irrelevant':
                $context['message'] = "Xin lỗi, tôi là trợ lý AI chuyên về ẩm thực và nấu ăn của Góc Bếp. Tôi không thể hỗ trợ các vấn đề ngoài chuyên môn như giải toán, lập trình hay các chủ đề khác.";
                break;
        }

        return $context;
    }
}
