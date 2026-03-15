<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            ['content' => 'Một căn phòng không có sách cũng giống như một cơ thể không có linh hồn.', 'author' => 'Marcus Tullius Cicero', 'source' => null, 'is_active' => true, 'order' => 1],
            ['content' => 'Đọc sách là cuộc trò chuyện với những người thông thái nhất qua nhiều thế kỷ.', 'author' => 'René Descartes', 'source' => null, 'is_active' => true, 'order' => 2],
            ['content' => 'Hôm nay bạn là độc giả, ngày mai bạn sẽ là lãnh đạo.', 'author' => 'Margaret Fuller', 'source' => null, 'is_active' => true, 'order' => 3],
            ['content' => 'Những cuốn sách hay nhất là những cuốn nói với bạn điều bạn đã biết.', 'author' => 'George Orwell', 'source' => '1984', 'is_active' => true, 'order' => 4],
            ['content' => 'Bạn không bao giờ cô đơn khi đang đọc một cuốn sách.', 'author' => 'Susan Wiggs', 'source' => null, 'is_active' => true, 'order' => 5],
            ['content' => 'Cuốn sách duy nhất đáng đọc là cuốn dạy ta cách tự suy nghĩ.', 'author' => 'Ralph Waldo Emerson', 'source' => null, 'is_active' => true, 'order' => 6],
            ['content' => 'Đọc có nghĩa là mượn; suy nghĩ từ những gì đã đọc là trả lại.', 'author' => 'Georg C. Lichtenberg', 'source' => null, 'is_active' => true, 'order' => 7],
            ['content' => 'Sách là người bạn thầm lặng nhất và trung thành nhất.', 'author' => 'Elizabeth Barrett Browning', 'source' => null, 'is_active' => true, 'order' => 8],
        ];

        foreach ($quotes as $quote) {
            \App\Models\Quote::create($quote);
        }
    }
}
