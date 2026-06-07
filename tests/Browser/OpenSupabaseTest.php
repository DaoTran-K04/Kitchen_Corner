<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OpenSupabaseTest extends DuskTestCase
{
    public function test_open_supabase(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://supabase.com/dashboard/projects')
                    ->pause(600000); // 10 phút
        });
    }
}
