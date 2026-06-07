<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Recipe;

class UserJourneyTest extends DuskTestCase
{
    public function test_user_journey(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Truy cập Trang chủ
            $browser->visit('http://127.0.0.1:8000/')
                    ->pause(2000)
                    ->screenshot('journey_1_home');
            
            // 2. Chuyển hướng sang trang Đăng Nhập
            $browser->visit('http://127.0.0.1:8000/login')
                    ->pause(2000)
                    ->screenshot('journey_2_login');
            
            // 3. Chuyển hướng sang trang Đăng Ký
            $browser->visit('http://127.0.0.1:8000/register')
                    ->pause(2000)
                    ->screenshot('journey_3_register');

            // 4. Xem chi tiết một Công thức bất kỳ
            $recipe = Recipe::where('status', 'published')->first();
            if ($recipe) {
                $browser->visit('http://127.0.0.1:8000/recipes/' . $recipe->slug)
                        ->pause(2000)
                        ->script('window.scrollTo({top: 800, behavior: "instant"});');
                $browser->pause(1000)
                        ->screenshot('journey_4_recipe_detail');
            }
            
            // 5. Khám phá trang Blog/Tạp chí
            $browser->visit('http://127.0.0.1:8000/tap-chi')
                    ->pause(2000)
                    ->screenshot('journey_5_articles');
        });
    }
}
