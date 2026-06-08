<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class UserCollectionTest extends DuskTestCase
{
    /**
     * Test adding recipe to collection.
     */
    public function test_user_can_add_to_collection(): void
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
                    ->storeSource('user_collection_recipes_page')
                    ->click('.recipe-card-item')
                    ->pause(1000)
                    ->storeSource('user_collection_test_source')
                    ->click('#bookmarkBtn')
                    ->pause(1000)
                    ->visit('/profile')
                    ->pause(1000)
                    ->storeSource('user_profile_page')
                    ->assertSee('Bài Đã Lưu');
        });
    }
}
