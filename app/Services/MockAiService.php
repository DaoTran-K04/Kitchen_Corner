<?php

namespace App\Services;
// giả lập AI
class MockAiService
{
    /**
     * Bộ từ điển đồng nghĩa (Mock Semantic Database)
     * Giả lập khả năng "hiểu ngữ nghĩa" của AI.
     */
    protected $synonyms = [
        'cơm' => ['cơm', 'gạo', 'gạo tẻ', 'gạo nếp', 'cơm trắng', 'cơm tấm', 'cơm chiên'],
        'gạo' => ['gạo', 'cơm', 'gạo tẻ', 'gạo nếp'],
        'thịt lợn' => ['thịt lợn', 'thịt heo', 'sườn', 'ba chỉ', 'ba rọi', 'thịt nạc', 'giò lợn'],
        'thịt heo' => ['thịt lợn', 'thịt heo', 'sườn', 'ba chỉ', 'ba rọi', 'thịt nạc', 'giò heo'],
        'bò' => ['bò', 'thịt bò', 'bắp bò', 'gân bò', 'thăn bò', 'sườn bò'],
        'thịt bò' => ['bò', 'thịt bò', 'bắp bò', 'gân bò', 'thăn bò', 'sườn bò'],
        'gà' => ['gà', 'thịt gà', 'ức gà', 'đùi gà', 'cánh gà', 'gà ta', 'gà công nghiệp'],
        'thịt gà' => ['gà', 'thịt gà', 'ức gà', 'đùi gà', 'cánh gà', 'gà ta', 'gà công nghiệp'],
        'cá' => ['cá', 'cá lóc', 'cá hồi', 'cá chép', 'cá diêu hồng', 'cá trắm', 'cá ba sa'],
        'rau' => ['rau', 'rau muống', 'rau cải', 'xà lách', 'rau xanh', 'rau mồng tơi', 'rau bắp cải'],
        'mì' => ['mì', 'mì tôm', 'mì gói', 'mì ý', 'spaghetti', 'bún', 'phở'],
        'bún' => ['bún', 'bún tươi', 'bún khô', 'phở', 'mì'],
        'phở' => ['phở', 'bánh phở', 'bún', 'mì'],
        'tôm' => ['tôm', 'tôm sú', 'tôm thẻ', 'tôm hùm', 'tép'],
        'trứng' => ['trứng', 'trứng gà', 'trứng vịt', 'trứng cút', 'hột vịt'],
        'đậu hũ' => ['đậu hũ', 'đậu phụ', 'tàu hủ', 'đậu non'],
        'đậu phụ' => ['đậu hũ', 'đậu phụ', 'tàu hủ', 'đậu non'],
    ];

    /**
     * Mở rộng từ khóa dựa trên từ điển đồng nghĩa.
     */
    public function expandKeywords(array $keywords): array
    {
        $expanded = [];
        foreach ($keywords as $keyword) {
            $keyword = mb_strtolower(trim($keyword));
            
            // Tìm trong từ điển, nếu có thì lấy mảng đồng nghĩa, nếu không thì giữ nguyên từ khóa gốc
            if (isset($this->synonyms[$keyword])) {
                $expanded = array_merge($expanded, $this->synonyms[$keyword]);
            } else {
                // Thử tìm kiếm gần đúng (nếu từ khóa chứa chữ "cơm" thì lấy array "cơm")
                $found = false;
                foreach ($this->synonyms as $key => $values) {
                    if (str_contains($keyword, $key)) {
                        $expanded = array_merge($expanded, $values);
                        $found = true;
                        break; // Lấy nhóm đầu tiên tìm thấy
                    }
                }
                
                if (!$found) {
                    $expanded[] = $keyword;
                }
            }
        }
        
        // Loại bỏ các từ khóa trùng lặp
        return array_unique($expanded);
    }

    /**
     * Giả lập văn bản "Thông tin tổng quan do AI tạo"
     */
    public function generateOverview(string $keywordInput, int $resultCount): ?string
    {
        if (empty(trim($keywordInput))) {
            return null;
        }

        $keyword = mb_strtolower(trim($keywordInput));
        
        $templates = [
            "Các công thức nấu ăn liên quan đến <strong>%s</strong> thường là những món ăn rất hấp dẫn, đậm đà hoặc thanh mát tùy theo cách chế biến. Dưới đây là các công thức tiêu biểu được hệ thống chọn lọc giúp bạn dễ dàng chuẩn bị bữa ăn hoàn hảo.",
            "Nguyên liệu <strong>%s</strong> là một thành phần tuyệt vời để tạo nên những bữa ăn dinh dưỡng. Trí tuệ nhân tạo của chúng tôi đã phân tích và gợi ý cho bạn những cách kết hợp tốt nhất dưới đây.",
            "Khám phá thế giới ẩm thực với <strong>%s</strong>! Đây là nguyên liệu đa năng có thể chế biến thành nhiều món ngon khác nhau. Hãy thử các công thức nổi bật mà AI đã tổng hợp cho bạn.",
            "Dựa trên phân tích xu hướng ẩm thực, các món làm từ <strong>%s</strong> luôn được yêu thích vì sự tiện lợi và ngon miệng. Hãy tham khảo danh sách công thức được gợi ý bên dưới."
        ];

        // Chọn ngẫu nhiên 1 template
        $template = $templates[array_rand($templates)];
        
        $text = sprintf($template, $keyword);
        
        if ($resultCount === 0) {
            $text = "Hệ thống AI hiện tại chưa tìm thấy công thức nào chứa chính xác nguyên liệu <strong>{$keyword}</strong>. Tuy nhiên, AI đang học hỏi và sẽ sớm cập nhật. Bạn hãy thử tìm với các từ khóa phổ biến khác nhé!";
        }

        return $text;
    }

    /**
     * Giả lập tìm kiếm công thức từ các nguồn ngoài (Google Search Mock)
     */
    public function fetchExternalResults(string $keyword): array
    {
        if (empty(trim($keyword))) {
            return [];
        }

        $keyword = mb_strtolower(trim($keyword));
        $ucFirstKeyword = mb_convert_case($keyword, MB_CASE_TITLE, "UTF-8");

        // Mã hóa từ khóa để đưa vào URL
        $encodedKeyword = urlencode($keyword);
        $encodedYoutubeKeyword = urlencode("cách nấu " . $keyword);

        // Lấy 10 ảnh thực tế từ các công thức có sẵn trong Database để làm fallback (đảm bảo 100% không bị chặn bởi mạng)
        $foodImages = \Illuminate\Support\Facades\Cache::remember('mock_food_images', 3600, function () {
            $images = \App\Models\Recipe::whereNotNull('image')->inRandomOrder()->limit(10)->pluck('image')->toArray();
            // Nếu DB chưa có đủ ảnh, cung cấp 1 ảnh nội bộ làm dự phòng cuối cùng
            if (empty($images)) {
                $images = ['https://foodish-api.com/images/burger/burger87.jpg'];
            }
            return $images;
        });

        // Nhân bản mảng lên nếu ít hơn 10 ảnh để đủ số lượng xáo trộn
        while (count($foodImages) < 10) {
            $foodImages = array_merge($foodImages, $foodImages);
        }
        shuffle($foodImages);

        // Tập hợp dữ liệu nguồn đa dạng (Chỉ chọn các trang chuyên nấu ăn và search chuẩn)
        // Tập hợp dữ liệu nguồn cố định: YouTube, Google, TikTok
        $sourcesPool = [
            [
                'title' => "Top 10 Cách Chế Biến {$ucFirstKeyword} Siêu Ngon",
                'snippet' => "Bạn đã chán ngấy với cách nấu thông thường? Thử ngay 10 công thức biến tấu {$keyword} lạ miệng, đảm bảo ăn là ghiền.",
                'source' => 'youtube.com',
                'url' => "https://www.youtube.com/results?search_query={$encodedYoutubeKeyword}",
                'thumbnail' => $this->scrapeThumbnail('youtube', $encodedKeyword, $foodImages[0]),
                'favicon' => 'https://www.google.com/s2/favicons?domain=youtube.com',
            ],
            [
                'title' => "Tuyệt chiêu nấu {$ucFirstKeyword} chuẩn vị Google",
                'snippet' => "Xem ngay các kết quả hàng đầu trên Google về cách chế biến {$keyword} ngon nhất được chọn lọc kỹ càng.",
                'source' => 'google.com',
                'url' => "https://www.google.com/search?q={$encodedYoutubeKeyword}",
                'thumbnail' => $foodImages[1],
                'favicon' => 'https://www.google.com/s2/favicons?domain=google.com',
            ],
            [
                'title' => "Review các công thức {$ucFirstKeyword} hot nhất Tiktok",
                'snippet' => "Cùng điểm lại những cách làm {$keyword} đã gây bão mạng xã hội thời gian qua. Đơn giản, dễ làm tại nhà.",
                'source' => 'tiktok.com',
                'url' => "https://www.tiktok.com/search?q={$encodedYoutubeKeyword}",
                'thumbnail' => $foodImages[2],
                'favicon' => 'https://www.google.com/s2/favicons?domain=tiktok.com',
            ]
        ];

        return $sourcesPool;
    }

    /**
     * Web Crawler Bot: Cào ảnh thumbnail thực tế từ các nguồn bên ngoài
     */
    private function scrapeThumbnail($source, $encodedKeyword, $fallbackImage)
    {
        return \Illuminate\Support\Facades\Cache::remember('mock_img_' . $source . '_' . md5($encodedKeyword), 3600*24, function () use ($source, $encodedKeyword, $fallbackImage) {
            try {
                $options = [
                    "http" => [
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                        "timeout" => 3
                    ]
                ];
                $context = stream_context_create($options);
                
                if ($source === 'cookpad') {
                    $html = @file_get_contents("https://cookpad.com/vn/tim-kiem/{$encodedKeyword}", false, $context);
                    if ($html && preg_match('/<img[^>]+src=["\'](https:\/\/img-global\.cpcdn\.com\/recipes\/[^"\']+)["\']/', $html, $matches)) {
                        return $matches[1];
                    }
                } elseif ($source === 'youtube') {
                    $html = @file_get_contents("https://www.youtube.com/results?search_query=cách+nấu+{$encodedKeyword}", false, $context);
                    if ($html && preg_match('/(https:\/\/i\.ytimg\.com\/vi\/[^\/]+\/hqdefault\.jpg)/', $html, $matches)) {
                        return $matches[1];
                    }
                }
            } catch (\Exception $e) {
                // Ignore errors and return fallback
            }
            return $fallbackImage;
        });
    }
}

