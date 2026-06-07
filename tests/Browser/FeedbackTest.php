<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class FeedbackTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testFeedbackFlow(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Vào trang liên hệ, điền form góp ý (Guest)
            $browser->visit('/lien-he')
                    ->pause(1000)
                    ->type('name', 'Nguyễn Văn Test')
                    ->type('email', 'test@example.com')
                    ->type('subject', 'Giao diện rất đẹp!')
                    ->type('message', 'Tôi rất thích giao diện của website, tuy nhiên mong muốn có thêm tính năng chế độ ban đêm.')
                    ->pause(1000)
                    ->press('Gửi Góp Ý')
                    ->pause(2000)
                    ->assertSee('Cảm ơn bạn đã gửi góp ý');

            // 2. Đăng nhập vào Admin
            $adminUser = User::where('role', 'admin')->first();
            if (!$adminUser) {
                // Nếu chưa có, tạo tạm 1 admin
                $adminUser = User::factory()->create([
                    'role' => 'admin',
                    'email' => 'admin_test_feedback@example.com',
                ]);
            }

            $browser->loginAs($adminUser)
                    ->visit('/admin/feedbacks')
                    ->pause(2000)
                    ->assertSee('Nguyễn Văn Test')
                    ->assertSee('Giao diện rất đẹp!');
            
            // 3. Click vào xem chi tiết (Mô phỏng click vào nút xem ở dòng đầu tiên)
            // Lấy ID góp ý mới nhất
            $latestFeedback = \App\Models\Feedback::latest()->first();
            if ($latestFeedback) {
                $browser->visit('/admin/feedbacks/' . $latestFeedback->id)
                        ->pause(2000)
                        ->assertSee('Nguyễn Văn Test')
                        ->assertSee('Tôi rất thích giao diện của website');
            }

            // Dừng vĩnh viễn để User xem (pause 999999)
            $browser->pause(999999999);
        });
    }
}
