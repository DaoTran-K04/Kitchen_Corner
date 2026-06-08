<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AdminIngredientTest extends DuskTestCase
{
    /**
     * Test the Admin Ingredients UI directly in Chrome.
     */
    public function test_admin_can_manage_ingredients_via_ui(): void
    {
        // Cleanup existing test data from previous failed runs (including soft-deleted ones)
        \App\Models\Ingredient::withTrashed()->where('name', 'Nguyên Liệu Test Bot')->forceDelete();

        $this->browse(function (Browser $browser) {
            // Đảm bảo user admin tồn tại
            $admin = User::where('email', 'admin@gmail.com')->first();
            
            if (!$admin) {
                $admin = User::factory()->create([
                    'email' => 'admin@gmail.com',
                    'password' => bcrypt('123456789'),
                    'role' => 'admin',
                ]);
            }

            // 1. Đăng nhập và vào trang danh sách nguyên liệu
            $browser->visit('/login')
                    ->type('email', 'admin@gmail.com')
                    ->type('password', '123456789')
                    ->press('Đăng Nhập')
                    ->pause(2000)
                    ->visit('/admin/ingredients')
                    ->pause(1000) // Dừng 1 giây để user có thể nhìn thấy
                    ->assertSee('Quản Lý Nguyên Liệu');

            // 2. Click nút "Thêm mới"
            $browser->clickLink('Thêm mới')
                    ->pause(1000)
                    ->assertPathIsNot('/admin/ingredients');

            // 3. Điền form thêm nguyên liệu mới
            $browser->type('name', 'Nguyên Liệu Test Bot')
                    ->type('unit', '100g')
                    ->type('calories_per_unit', '50')
                    ->type('protein_per_unit', '2')
                    ->type('carbs_per_unit', '5')
                    ->type('fat_per_unit', '1')
                    ->pause(1500) // Dừng lại 1.5s để user xem dữ liệu đã điền
                    ->press('Lưu thông tin');

            // 4. Kiểm tra xem đã tạo thành công chưa
            $browser->pause(1000)
                    ->assertSee('Đã thêm nguyên liệu mới thành công!')
                    ->type('search', 'Nguyên Liệu Test Bot')
                    ->keys('input[name=search]', '{enter}')
                    ->pause(1000)
                    ->assertSee('Nguyên Liệu Test Bot');

            // 5. Tìm nguyên liệu vừa tạo để xóa
            // Do trong bảng có nút xóa dùng form, ta sẽ dùng javascript hoặc xpath để click
            // Hoặc có thể tìm theo input/button nằm cùng row
            $browser->pause(2000) // Đợi 2s để user đọc được kết quả
                    ->with('.overflow-x-auto table tbody', function ($table) {
                        // Click nút xóa ở dòng đầu tiên có chứa text 'Nguyên Liệu Test Bot'
                        // Cấu trúc blade: form > button[type=submit][title=Xóa]
                        // $table->press('Xóa'); -> Tuy nhiên confirm dialog của browser sẽ hiện ra
                    });
            
            // Xóa bằng selector
            $browser->script("
                let rows = document.querySelectorAll('tbody tr');
                for(let row of rows) {
                    if(row.innerText.includes('Nguyên Liệu Test Bot')) {
                        let btn = row.querySelector('button[title=\"Xóa\"]');
                        if(btn) btn.click();
                        break;
                    }
                }
            ");
            
            $browser->pause(500)
                    ->acceptDialog()
                    ->pause(1000)
                    ->assertSee('Đã xóa nguyên liệu thành công!')
                    ->assertDontSee('Nguyên Liệu Test Bot');
                    
        });
    }
}
