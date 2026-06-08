<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CommentAndRatingTest extends DuskTestCase
{
    /**
     * Test commenting on a recipe.
     */
    public function test_comment_on_recipe(): void
    {
        $this->browse(function (Browser $browser) {
            $recipe = \App\Models\Recipe::where('status', 'published')->first();
            $browser->visit('/login')
                    ->pause(1000)
                    ->type('email', 'tester@gmail.com')
                    ->type('password', '123456789')
                    ->press('Đăng Nhập')
                    ->pause(2000)
                    ->visit('/cong-thuc')
                    ->pause(1000)
                    ->click('.recipe-card-item')
                    ->pause(2000)
                    ->waitFor('textarea[name="content"]', 10)
                    ->type('textarea[name="content"]', 'Món này rất ngon!')
                    ->press('Gửi bình luận')
                    ->pause(2000)
                    ->assertSee('Món này rất ngon!');
        });
    }
}
