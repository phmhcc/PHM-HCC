<?php

namespace Tests\Feature;

use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\SocialLink;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardSiteContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
    }

    public function test_website_content_management_is_restricted_to_users_with_site_content_permission(): void
    {
        $this->get(route('dashboard.site-content.index'))->assertRedirect(route('login'));

        $deacon = User::factory()->create(['role' => 'deacon']);
        $this->actingAs($deacon)
            ->get(route('dashboard.site-content.index'))
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('dashboard.site-content.index'))
            ->assertOk()
            ->assertSeeText('Website Content')
            ->assertSeeText('Add gallery photos')
            ->assertSeeText('Giving payment details')
            ->assertSeeText('Social links')
            ->assertSee('href="'.route('dashboard.site-content.index').'"', false);
    }

    public function test_admin_can_upload_photos_to_a_new_category_and_the_public_gallery_updates(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('dashboard.site-content.gallery.upload'), [
                'new_category' => 'Community Media',
                'images' => [
                    UploadedFile::fake()->createWithContent(
                        'community-one.png',
                        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j6QAAAABJRU5ErkJggg=='),
                    ),
                    UploadedFile::fake()->createWithContent(
                        'community-two.png',
                        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+j6QAAAABJRU5ErkJggg=='),
                    ),
                ],
            ])
            ->assertRedirect();

        $category = GalleryCategory::query()->where('slug', 'community-media')->firstOrFail();
        $this->assertSame(2, $category->images()->count());
        $images = $category->images()->get();

        foreach ($images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }

        $this->get(route('gallery'))
            ->assertOk()
            ->assertSee('data-category="community-media" data-photo-count="2"', false);
        $this->get(route('gallery.image', $images->first()))->assertOk();
        $this->get(route('gallery.image', ['galleryImage' => $images->first(), 'download' => 1]))
            ->assertOk()
            ->assertHeader('content-disposition');

        $emptyCategory = GalleryCategory::query()->create([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
        ]);
        $this->get(route('gallery'))->assertDontSee('data-category="'.$emptyCategory->slug.'"', false);
    }

    public function test_admin_can_delete_gallery_photos_and_empty_categories(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $category = GalleryCategory::query()->create([
            'name' => 'Temporary Category',
            'slug' => 'temporary-category',
        ]);
        $imagePath = 'gallery/temporary.png';
        Storage::disk('public')->put($imagePath, 'image-data');
        $image = GalleryImage::create([
            'gallery_category_id' => $category->id,
            'path' => $imagePath,
            'original_name' => 'temporary.png',
        ]);

        $this->actingAs($admin)
            ->delete(route('dashboard.site-content.gallery.categories.delete', $category))
            ->assertStatus(422);

        $this->delete(route('dashboard.site-content.gallery.images.delete', $image))
            ->assertRedirect();
        Storage::disk('public')->assertMissing($imagePath);
        $this->assertDatabaseMissing('gallery_images', ['id' => $image->id]);

        $this->delete(route('dashboard.site-content.gallery.categories.delete', $category))
            ->assertRedirect();
        $this->assertDatabaseMissing('gallery_categories', ['id' => $category->id]);
    }

    public function test_admin_updates_giving_details_and_social_links_shown_on_public_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $socialLink = SocialLink::query()->where('name', 'Facebook')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('dashboard.site-content.giving.update'), [
                'payment_number' => '1234567',
                'payment_name' => 'NEW CHURCH GIVING ACCOUNT',
            ])
            ->assertRedirect();

        $this->put(route('dashboard.site-content.social-links.update', $socialLink), [
            'name' => 'Church Facebook',
            'url' => 'https://facebook.com/example-church',
            'icon' => 'fa-facebook',
            'sort_order' => 1,
        ])->assertRedirect();

        $this->get(route('giving'))
            ->assertOk()
            ->assertSeeText('1234567')
            ->assertSeeText('NEW CHURCH GIVING ACCOUNT');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="Church Facebook"', false)
            ->assertSee('href="https://facebook.com/example-church"', false);

        $this->post(route('dashboard.site-content.social-links.store'), [
            'name' => 'Church Website',
            'url' => 'https://example.org',
            'icon' => 'fa-link',
            'sort_order' => 5,
        ])->assertRedirect();

        $newLink = SocialLink::query()->where('name', 'Church Website')->firstOrFail();
        $this->delete(route('dashboard.site-content.social-links.delete', $newLink))->assertRedirect();
        $this->assertDatabaseMissing('social_links', ['id' => $newLink->id]);
    }
}
