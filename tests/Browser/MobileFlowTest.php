<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MobileFlowTest extends DuskTestCase
{
    /**
     * Mô phỏng khách vãng lai lướt web trên điện thoại
     */
    public function testGuestMobileFlow()
    {
        $this->browse(function (Browser $browser) {
            // Đảm bảo không bị dính session từ test trước
            $browser->driver->manage()->deleteAllCookies();
            
            $browser->resize(390, 844)
                    ->visit('/')
                    ->assertPathIs('/')
                    ->assertVisible('#mobile-bottom-nav')
                    
                    // Click Công thức trên Bottom Tab Bar
                    ->click('#mobile-bottom-nav a[href*="cong-thuc"]')
                    ->waitForLocation('/cong-thuc', 5)
                    ->assertPathIs('/cong-thuc')
                    
                    // Mở menu Thêm (Hamburger)
                    ->click('#bottom-nav-menu-btn')
                    ->pause(1000)
                    ->assertVisible('#mobile-menu')
                    
                    // Đóng menu đi để không che khuất màn hình
                    ->click('#close-mobile-menu')
                    ->pause(500)
                    
                    // Click Tạp chí trên Bottom Tab Bar
                    ->click('#mobile-bottom-nav a[href*="tap-chi"]')
                    ->waitForLocation('/tap-chi', 5)
                    ->assertPathIs('/tap-chi');
        });
    }

    /**
     * Mô phỏng người dùng đã đăng nhập sử dụng web trên điện thoại
     */
    public function testUserMobileFlow()
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            
            $browser->resize(390, 844)
                    ->visit('/login')
                    ->assertPathIs('/login')
                    ->clear('#email')->type('#email', 'tester@gmail.com')
                    ->clear('#password')->type('#password', '123456789')
                    ->press('Đăng Nhập')
                    
                    // Sau khi đăng nhập, hệ thống sẽ redirect về home
                    ->waitForLocation('/', 5)
                    ->assertPathIs('/')
                    ->assertVisible('#mobile-bottom-nav')
                    
                    // Bấm vào Hồ sơ cá nhân qua menu mở rộng
                    ->click('#bottom-nav-menu-btn')
                    ->pause(1000)
                    ->assertVisible('#mobile-menu')
                    ->clickLink('Hồ sơ') // Tìm đúng text link
                    ->waitForLocation('/profile', 5)
                    ->assertPathIs('/profile');
        });
    }

    /**
     * Mô phỏng Quản trị viên sử dụng điện thoại để vào trang quản trị
     */
    public function testAdminMobileFlow()
    {
        $this->browse(function (Browser $browser) {
            $browser->driver->manage()->deleteAllCookies();
            
            $browser->resize(390, 844)
                    ->visit('/login')
                    ->assertPathIs('/login')
                    ->clear('#email')->type('#email', 'admin@gmail.com')
                    ->clear('#password')->type('#password', '123456789')
                    ->press('Đăng Nhập')
                    ->waitForLocation('/', 5)
                    ->assertPathIs('/') 
                    
                    // Admin vào thẳng trang Dashboard bằng điện thoại
                    ->visit('/admin/dashboard')
                    ->assertPathIs('/admin/dashboard')
                    
                    // Chuyển qua các tab quản lý
                    ->visit('/admin/users')
                    ->assertPathIs('/admin/users')
                    
                    ->visit('/admin/recipes')
                    ->assertPathIs('/admin/recipes')
                    
                    ->visit('/admin/banners')
                    ->assertPathIs('/admin/banners');
        });
    }
}
