<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;

class IngredientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_ingredients_index()
    {
        Ingredient::create([
            'name' => 'Thịt bò',
            'slug' => 'thit-bo',
            'unit' => '100g',
            'calories_per_unit' => 250,
            'protein_per_unit' => 26,
            'carbs_per_unit' => 0,
            'fat_per_unit' => 15,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.ingredients.index'));

        $response->assertStatus(200);
        $response->assertSee('Thịt bò');
        $response->assertSee('250');
    }

    public function test_admin_can_create_an_ingredient()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.ingredients.store'), [
            'name' => 'Cà chua',
            'unit' => '1 quả',
            'calories_per_unit' => 22,
            'protein_per_unit' => 1,
            'carbs_per_unit' => 5,
            'fat_per_unit' => 0,
        ]);

        $response->assertRedirect(route('admin.ingredients.index'));
        $this->assertDatabaseHas('ingredients', [
            'name' => 'Cà chua',
            'calories_per_unit' => 22,
        ]);
    }

    public function test_admin_can_update_an_ingredient()
    {
        $ingredient = Ingredient::create([
            'name' => 'Trứng gà',
            'slug' => 'trung-ga',
            'unit' => '1 quả',
            'calories_per_unit' => 70,
            'protein_per_unit' => 6,
            'carbs_per_unit' => 0,
            'fat_per_unit' => 5,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.ingredients.update', $ingredient->id), [
            'name' => 'Trứng vịt',
            'unit' => '1 quả',
            'calories_per_unit' => 130,
            'protein_per_unit' => 9,
            'carbs_per_unit' => 1,
            'fat_per_unit' => 9,
        ]);

        $response->assertRedirect(route('admin.ingredients.index'));
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'name' => 'Trứng vịt',
            'calories_per_unit' => 130,
        ]);
    }

    public function test_admin_can_delete_an_ingredient()
    {
        $ingredient = Ingredient::create([
            'name' => 'Hành tây',
            'slug' => 'hanh-tay',
            'unit' => '100g',
            'calories_per_unit' => 40,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.ingredients.destroy', $ingredient->id));

        $response->assertRedirect(route('admin.ingredients.index'));
        
        // Soft delete assertion
        $this->assertSoftDeleted('ingredients', [
            'id' => $ingredient->id,
        ]);
    }

    public function test_admin_can_search_ingredients()
    {
        Ingredient::create(['name' => 'Cà rốt', 'slug' => 'ca-rot', 'unit' => '100g', 'calories_per_unit' => 41]);
        Ingredient::create(['name' => 'Khoai tây', 'slug' => 'khoai-tay', 'unit' => '100g', 'calories_per_unit' => 77]);

        $response = $this->actingAs($this->admin)->get(route('admin.ingredients.index', ['search' => 'Khoai']));

        $response->assertStatus(200);
        $response->assertSee('Khoai tây');
        $response->assertDontSee('Cà rốt');
    }
}
