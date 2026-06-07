<?php

namespace App\Services\AI;

class AiIntentService
{
    /**
     * Phân loại intent của câu nói dựa trên rules (regex)
     * Các intent:
     * - recipe.recommend: Gợi ý món chung chung
     * - recipe.ingredient: Tìm món theo nguyên liệu
     * - recipe.diet: Tìm món ăn kiêng, healthy
     * - recipe.difficulty: Tìm món dễ nấu, nhanh
     * - recipe.review: Xem review món ăn
     * - site.help: Hỏi về trang web
     * - review.help: Cách viết review
     * - irrelevant: Câu hỏi không liên quan
     * - chat: Câu chat bình thường nếu không match (xin chào, cảm ơn, v.v.)
     */
    public function detectIntent(string $message): string
    {
        $msg = mb_strtolower(trim($message));

        // 1. Irrelevant
        if (preg_match('/(giải toán|giải phương trình|lập trình|code giúp|viết code|dịch|tiếng anh|toán học|lịch sử thế giới)/ui', $msg)) {
            return 'irrelevant';
        }

        // 2. Site help
        if (preg_match('/(web này có gì|trang web này|chức năng web|giới thiệu về|góc bếp là gì)/ui', $msg)) {
            return 'site.help';
        }

        // 3. Review help
        if (preg_match('/(cách viết review|viết đánh giá|hướng dẫn review|làm sao để đánh giá|review thế nào cho hay)/ui', $msg)) {
            return 'review.help';
        }

        // 4. Recipe Ingredient
        if (preg_match('/(tôi có|trong tủ lạnh có|còn.*thì nấu gì|nguyên liệu.*nấu gì|mua.*nấu món gì|trứng|thịt bò|thịt lợn|thịt gà|cà chua|rau)/ui', $msg) 
            && preg_match('/(nấu gì|làm món gì|thì nấu|món nào)/ui', $msg)) {
            return 'recipe.ingredient';
        }

        // 5. Recipe Diet
        if (preg_match('/(healthy|ít calo|ăn kiêng|giảm cân|ít dầu|ít béo|eat clean|keto|thuần chay|ăn chay)/ui', $msg)) {
            return 'recipe.diet';
        }

        // 6. Recipe Difficulty
        if (preg_match('/(dễ nấu|dễ làm|đơn giản|nhanh chóng|ít thời gian|ít nguyên liệu|sinh viên|lười nấu)/ui', $msg)) {
            return 'recipe.difficulty';
        }

        // 7. Recipe Recommend (chung chung)
        if (preg_match('/(gợi ý|ăn gì|món nào ngon|nấu gì ngon|món mới|món hot|món ăn tối|đề xuất món|hợp khẩu vị)/ui', $msg)) {
            return 'recipe.recommend';
        }

        // 8. Recipe detail / Search specific (e.g. Cách nấu phở bò, cho tôi công thức gà kho)
        if (preg_match('/(cách nấu|cách làm|công thức|hướng dẫn nấu)/ui', $msg)) {
            return 'recipe.detail';
        }

        // 9. Greeting / General
        if (preg_match('/(xin chào|hello|hi|chào bạn|chào|cảm ơn|thanks)/ui', $msg)) {
            return 'chat.greeting';
        }

        // Default to intent inference by AI if not matched locally, but we map to general recipe.search locally
        return 'recipe.search';
    }
}
