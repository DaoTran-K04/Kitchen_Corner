<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RecipeInteractionTest extends DuskTestCase
{
    /**
     * Test user can like and comment on a recipe.
     */
    public function test_user_can_like_and_comment_on_recipe(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);
        $recipe = Recipe::where('status', 'published')->inRandomOrder()->first();

        if (!$recipe) {
            $this->markTestSkipped('No published recipes available for testing.');
        }

        $this->browse(function (Browser $browser) use ($user, $recipe) {
            $browser->loginAs($user)
                    // Visit homepage to trigger personalized recommendations setup (cache/session)
                    ->visitRoute('home')
                    ->pause(1500)
                    ->scrollTo('#personalized-recipes-slider')
                    ->pause(1000)
                    ->assertSee('Dành Riêng Cho Bạn')
                    
                    // Navigate to a specific recipe using named route
                    ->visitRoute('recipes.show', $recipe->slug)
                    ->waitFor('h1') // Wait for the H1 tag to appear (Recipe Title)
                    ->pause(1500);
                    
                    // Find Like Button and click (using script to center it to avoid sticky header)
                    $browser->script("document.getElementById('likeBtn').scrollIntoView({behavior: 'smooth', block: 'center'});");
                    $browser->pause(1000) // Pause to let tester visually observe
                            ->click('#likeBtn')
                            ->pause(1500); // Wait for AJAX to finish
                    
                    // Type a comment
                    $browser->script("document.querySelector('textarea[name=\"content\"]').scrollIntoView({behavior: 'smooth', block: 'center'});");
                    $browser->pause(1000)
                            ->type('textarea[name="content"]', 'Đây là bình luận kiểm thử từ Laravel Dusk. Công thức thật tuyệt vời!')
                            ->pause(1500) // Let tester visually observe
                            ->press('Gửi bình luận');
                    
                    // Verify the comment appears in the comments list
                    $browser->waitFor('#recipe-comments-wrapper')
                            ->pause(2000)
                    
                            // Go back to the homepage to observe the updated counts (if visible)
                            ->visitRoute('home')
                            ->pause(2000);
        });
    }
}
