<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserFlowTest extends DuskTestCase
{
    /**
     * Test a complete user flow.
     */
    public function test_user_flow(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Vào trang chủ
            $browser->visit('/')
                ->pause(2000)
                ->assertPathIs('/');
                
            // 2. Chuyển sang trang Đăng nhập
            $browser->visit('/login')
                ->pause(2000)
                ->assertPathIs('/login')
                ->type('email', 'nhanthien.071972@gmail.com')
                ->pause(1000)
                ->type('password', 'Hoangdao_20004')
                ->pause(1000)
                ->keys('input[name="password"]', '{enter}')
                ->pause(10000); // Wait 10 seconds for user to see
        });
    }
}
