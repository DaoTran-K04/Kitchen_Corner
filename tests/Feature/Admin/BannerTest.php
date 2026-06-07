<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_banner_list()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.banners.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.banners.index');
    }

    public function test_admin_can_view_banner_create_form()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.banners.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.banners.create');
    }

    public function test_admin_can_create_banner_with_image_url()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Test Banner with URL',
            'image_url' => 'https://example.com/image.jpg',
            'is_active' => true,
            'order' => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('banners', [
            'title' => 'Test Banner with URL',
            'image' => 'https://example.com/image.jpg',
            'is_active' => 1,
            'order' => 1,
        ]);
    }

    public function test_admin_can_create_banner_with_file_upload()
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('banner.jpg');

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Test Banner with File',
            'image' => $file,
            'is_active' => true,
            'order' => 2,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $response->assertSessionHas('success');

        $banner = Banner::where('title', 'Test Banner with File')->first();
        $this->assertNotNull($banner);
        $this->assertTrue(str_starts_with($banner->image, 'banners/'));
        
        Storage::disk('public')->assertExists($banner->image);
    }

    public function test_regular_user_cannot_create_banner()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('admin.banners.store'), [
            'title' => 'Hacker Banner',
            'image_url' => 'https://example.com/hacker.jpg',
        ]);

        // Assuming middleware 'admin' redirects or throws 403. Check web.php for admin group
        // If it redirects to login or 403
        $response->assertStatus(403); // Or 302 depending on the implementation
    }
}
