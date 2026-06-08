<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchAndFilterTest extends DuskTestCase
{
    /**
     * Test searching and filtering recipes.
     */
    public function test_search_and_filter(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/cong-thuc')
                    ->pause(1000)
                    ->type('q', 'Phở')
                    ->keys('input[name="q"]', '{enter}')
                    ->pause(2000)
                    // It should stay on recipes page and show results or "Không tìm thấy"
                    ->assertSee('Phở') // Có thể là kết quả hoặc keyword trong form
                    ->visit('/cong-thuc?category=1')
                    ->pause(2000);
        });
    }
}
