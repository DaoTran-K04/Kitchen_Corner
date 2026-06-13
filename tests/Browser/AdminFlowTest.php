<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminFlowTest extends DuskTestCase
{
    /**
     * Test Admin Login and Dashboard flow.
     */
    public function testAdminCanLoginAndNavigate()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@gmail.com')
                    ->type('password', '123456789')
                    ->press('Đăng Nhập')
                    ->assertPathIs('/') 
                    
                    ->visit('/admin/dashboard')
                    ->assertPathIs('/admin/dashboard')
                    
                    // Quản lý người dùng
                    ->visit('/admin/users')
                    ->assertPathIs('/admin/users')
                    
                    // Quản lý công thức
                    ->visit('/admin/recipes')
                    ->assertPathIs('/admin/recipes')
                    
                    // Quản lý banner
                    ->visit('/admin/banners')
                    ->assertPathIs('/admin/banners')
                    ->pause(1000);
        });
    }
}
