<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminBannerCreateTest extends DuskTestCase
{
    /**
     * Test creating a banner in the admin interface.
     */
    public function test_create_banner(): void
    {
        // Cleanup
        \App\Models\Banner::where('title', 'Banner Test Tự Động')->delete();

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->pause(1000)
                    ->type('email', 'admin@gmail.com')
                    ->pause(1000)
                    ->type('password', '123456789')
                    ->pause(1000)
                    ->keys('input[name="password"]', '{enter}')
                    ->pause(2000)
                    // Chuyển tới trang tạo Banner của Admin
                    ->visit('/admin/banners/create')
                    ->pause(2000)
                    // Điền form
                    ->type('title', 'Banner Test Tự Động')
                    ->pause(1000)
                    ->click('#tab-url')
                    ->waitFor('#image-url', 5)
                    ->pause(1000)
                    ->type('image_url', 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?q=80&w=1000')
                    ->pause(1000)
                    ->type('tag', 'Khuyến mãi Hot')
                    ->pause(1000)
                    ->type('description', 'Đây là banner được tạo tự động bởi AI thông qua Laravel Dusk')
                    ->pause(1000)
                    // Bỏ qua nhập order vì nó dùng custom picker (hidden input)
                    // Bỏ qua is_active vì nó bị ẩn bằng class sr-only (mặc định đã checked rồi)
                    // Bấm nút Lưu
                    ->press('Lưu')
                    ->pause(2000)
                    ->assertSee('Banner Test Tự Động');
        });
    }
}
