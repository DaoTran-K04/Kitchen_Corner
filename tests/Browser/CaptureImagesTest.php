<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CaptureImagesTest extends DuskTestCase
{
    /**
     * Capture screenshots for Chapter 5
     */
    public function testCaptureImages(): void
    {
        $this->browse(function (Browser $browser) {
            // Login as tester
            $browser->visit('/login')
                    ->type('email', 'tester@gmail.com')
                    ->type('password', '123456789')
                    ->click('button[type="submit"]')
                    ->pause(2000);

            // Capture Hình 5.7: Dành riêng cho bạn (usually on home or recipes page)
            $browser->visit('/')
                    ->pause(2000)
                    ->screenshot('hinh_5_7');

            // Capture Hình 5.4: Chatbot
            try {
                $browser->visit('/')
                        ->pause(1000)
                        ->click('button[onclick*="toggleChat"]') // Guessing the toggle button
                        ->pause(2000)
                        ->screenshot('hinh_5_4');
            } catch (\Exception $e) {
                $browser->screenshot('hinh_5_4_fallback');
            }

            // Capture Hình 5.5: Macros (Recipe page)
            try {
                $browser->visit('/cong-thuc')
                        ->pause(2000)
                        ->click('a.recipe-link') // Guessing the link class
                        ->pause(3000)
                        ->screenshot('hinh_5_5');
            } catch (\Exception $e) {
                $browser->screenshot('hinh_5_5_fallback');
            }

            // Capture Hình 5.6: Smart Fridge
            $browser->visit('/tim-kiem-nguyen-lieu')
                    ->pause(2000);
            try {
                $browser->type('input[name="ingredients"]', 'trứng, thịt')
                        ->click('button[type="submit"]')
                        ->pause(3000);
            } catch (\Exception $e) {
            }
            $browser->screenshot('hinh_5_6');
            
        });
    }
}
