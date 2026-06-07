<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LazyLoadTest extends DuskTestCase
{
    public function test_lazy_loading_and_user_interaction(): void
    {
        $this->browse(function (Browser $browser) {
            // Mở trang chủ
            $browser->visit('http://127.0.0.1:8000/')
                    ->pause(3000)
                    ->screenshot('step1_top');
            
            // Cuộn xuống phần công thức thịnh hành
            $browser->script('window.scrollTo({top: 800, behavior: "instant"});');
            $browser->pause(2000)
                    ->screenshot('step2_scroll_800');

            // Cuộn xuống danh sách hôm nay nấu gì
            $browser->script('window.scrollTo({top: 2000, behavior: "instant"});');
            $browser->pause(2000)
                    ->screenshot('step3_scroll_2000');
            
            // Cuộn xuống phần tác giả
            $browser->script('window.scrollTo({top: 3500, behavior: "instant"});');
            $browser->pause(2000)
                    ->screenshot('step4_scroll_3500');
        });
    }
}
