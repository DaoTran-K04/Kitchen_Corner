<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class RegisterFlowTest extends DuskTestCase
{
    /**
     * Test the registration flow.
     */
    public function test_register_flow(): void
    {
        // Cleanup existing test data
        User::where('email', 'newuser@gmail.com')->delete();

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(1000)
                    ->pause(1000)
                    ->type('name', 'New Test User')
                    ->type('email', 'newuser@gmail.com')
                    ->type('password', '123456789')
                    ->type('password_confirmation', '123456789')
                    ->press('Đăng Ký')
                    ->pause(3000)
                    ->screenshot('register_failed_debug');
        });
    }
}
