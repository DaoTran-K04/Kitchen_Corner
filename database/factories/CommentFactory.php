<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'recipe_id' => \App\Models\Recipe::factory(),
            'content' => $this->faker->randomElement([
                "Món này ngon quá, cảm ơn admin đã chia sẻ công thức nhé!",
                "Hôm qua mình vừa làm thử theo công thức này, cả nhà khen nức nở luôn.",
                "Công thức rất chi tiết và dễ hiểu. Cảm ơn bạn.",
                "Lưu lại cuối tuần làm cho mấy nhóc tì ăn thử. Nhìn hấp dẫn quá.",
                "Cho mình hỏi thay đường trắng bằng đường phèn có được không ạ?",
                "Màu sắc món ăn đẹp quá, nhìn là muốn ăn ngay.",
                "Mình làm theo nhưng hơi nhạt một chút, lần sau sẽ thêm chút mắm.",
                "Món này ăn với cơm nóng thì tuyệt cú mèo.",
                "Nguyên liệu dễ tìm, cách làm đơn giản, quá tuyệt!",
                "Cảm ơn Góc Bếp, nhờ web mà dạo này tay nghề nấu nướng của mình lên hẳn.",
                "Lần đầu làm thành công mĩ mãn, hạnh phúc quá đi.",
                "Có cách nào bảo quản được lâu hơn không tác giả ơi?",
                "Wow, nhìn hấp dẫn quá đi mất!",
                "Đã thử và thành công, vị ngon khó cưỡng.",
                "Nhìn ngon quá, chắc chắn mình sẽ làm thử vào dịp cuối tuần.",
                "Công thức chuẩn thật sự, vị y như ngoài hàng luôn.",
                "Thèm quá, chiều nay đi làm về phải ghé chợ mua đồ làm ngay mới được.",
                "Món này chống ngán cực kỳ hiệu quả nhé mọi người.",
                "Trang trí đẹp mắt quá, nhìn mâm cơm sang hẳn lên."
            ]),
            // Mặc định: Comment gốc
            'parent_id' => null,
        ];
    }

    public function reply()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => \App\Models\Comment::inRandomOrder()->first()?->id,
            ];
        });
    }
}
