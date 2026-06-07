<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartSearchTest extends TestCase
{
    public function test_smart_search_without_ingredients_returns_ai_recommendations()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $response = $this->actingAs($user)->get(route('recipes.smart-search'));

        $response->assertStatus(200);
        $response->assertViewHas('isAiRecommended', true);
        $response->assertViewHas('results');
        
        $results = $response->original->getData()['results'];
        $this->assertNotNull($results);
    }

    public function test_smart_search_with_ingredients_returns_matches()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $response = $this->actingAs($user)->get(route('recipes.smart-search', [
            'ingredients' => 'thịt bò'
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('isAiRecommended', false);
        $response->assertViewHas('results');
    }
}
