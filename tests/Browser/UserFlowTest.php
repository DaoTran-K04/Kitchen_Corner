<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserFlowTest extends DuskTestCase
{
    /**
     * Test User Login and Navigation flow.
     */
    public function testUserCanLoginAndNavigate()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertPathIs('/login')
                    ->type('email', 'tester@gmail.com')
                    ->type('password', '123456789')
                    ->press('Đăng Nhập')
                    ->assertPathIs('/')
                    // Chuyển sang trang Công thức
                    ->visit('/cong-thuc')
                    ->assertPathIs('/cong-thuc')
                    // Chuyển sang trang Thử thách
                    ->visit('/thu-thach')
                    ->assertPathIs('/thu-thach')
                    // Chuyển sang trang Hồ sơ cá nhân
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    // Đăng xuất (Gỉa sử có button/link đăng xuất hoặc form)
                    // Nếu dùng button logout trong dropdown, có thể gọi post /logout thay thế
                    ->visit('/logout') // Có thể ko chạy nếu là POST
                    ->pause(1000);
            
            // Xử lý nút đăng xuất thường là POST form
            $browser->visit('/')
                    ->script("document.getElementById('logout-form') ? document.getElementById('logout-form').submit() : window.location.href='/';");
        });
    }
}
