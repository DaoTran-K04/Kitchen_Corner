<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginFlowTest extends DuskTestCase
{
    /**
     * Test the login flow visually.
     */
    public function test_login_flow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(2000)
                    ->visit('/login')
                    ->pause(2000)
                    ->type('email', 'nhanthien.071972@gmail.com')
                    ->pause(1000)
                    ->type('password', 'Hoangdao_20004')
                    ->pause(1500)
                    ->keys('input[name="password"]', '{enter}')
                    ->pause(600000); // Tạm dừng 10 phút để bạn tự xem kết quả
        });
    }
}
