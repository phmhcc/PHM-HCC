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
use Illuminate\Support\Facades\DB;
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
            ->assertSeeText('Add another video')
            ->assertSeeText('Sunday Morning Service')
            ->assertSeeText('Sunday Afternoon Service')
            ->assertSeeText('Tuesday Teaching Service')
            ->assertSeeText('Friday Prayer Service')
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
        $this->put(route('dashboard.site-content.social-links.update', $newLink), [
            'url' => '',
        ])->assertRedirect();
        $this->assertDatabaseHas('social_links', ['id' => $newLink->id, 'url' => '']);
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('aria-label="Church Website"', false);
    }

    public function test_admin_can_add_and_remove_multiple_home_page_videos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $youtubeLink = SocialLink::query()->where('name', 'YouTube')->firstOrFail();
        $youtubeLink->update(['url' => 'https://youtube.com/@church-channel']);

        $this->actingAs($admin)
            ->put(route('dashboard.site-content.video.update'), [
                'videos' => [
                    [
                        'url' => 'https://youtu.be/abcdefghijk',
                        'title' => 'Sunday Morning Service',
                        'date' => '2025-05-10',
                    ],
                    [
                        'url' => 'https://youtu.be/lmnopqrstuv',
                        'title' => 'Sunday Afternoon Service',
                        'date' => '2025-05-11',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://www.youtube.com/watch?v=abcdefghijk"', false)
            ->assertSee('src="https://img.youtube.com/vi/abcdefghijk/hqdefault.jpg"', false)
            ->assertSee('href="https://www.youtube.com/watch?v=lmnopqrstuv"', false)
            ->assertSeeText('Sunday Morning Service')
            ->assertSeeText('Sunday Afternoon Service')
            ->assertSeeText('May 10, 2025')
            ->assertSeeText('May 11, 2025')
            ->assertSeeText('YouTube')
            ->assertSeeText('View all videos')
            ->assertDontSeeText('Watch Video')
            ->assertSee('href="https://youtube.com/@church-channel"', false);

        $this->get(route('videos'))
            ->assertOk()
            ->assertSeeText('Videos')
            ->assertSeeText('Sunday Morning Service')
            ->assertSeeText('Sunday Afternoon Service');

        app()->setLocale('sw');
        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Ibada ya Jumapili Asubuhi')
            ->assertSeeText('Ibada ya Jumapili Mchana')
            ->assertSeeText('Tazama video zote');

        $this->put(route('dashboard.site-content.video.update'), [
            'videos' => [[
                'id' => json_decode(DB::table('site_settings')->where('key', 'home.videos')->value('value'), true)[0]['id'],
                'url' => 'https://youtu.be/abcdefghijk',
                'title' => 'Sunday Morning Service',
                'date' => '2025-05-10',
            ]],
        ])->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://www.youtube.com/watch?v=abcdefghijk"', false)
            ->assertDontSee('href="https://www.youtube.com/watch?v=lmnopqrstuv"', false);
        $this->get(route('videos'))
            ->assertOk()
            ->assertSeeText('Sunday Morning Service')
            ->assertDontSeeText('Sunday Afternoon Service');

        $youtubeLink->update(['url' => '']);
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSeeText('View all videos');

        $this->actingAs($admin)->put(route('dashboard.site-content.video.update'), [
            'videos' => [],
        ])->assertRedirect();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('<section class="home-video"', false);
        $this->get(route('videos'))
            ->assertOk()
            ->assertSeeText('Kwa sasa hakuna video zinazopatikana.');
    }

    public function test_home_video_rejects_urls_from_unsupported_hosts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('dashboard.site-content.index'))
            ->put(route('dashboard.site-content.video.update'), [
                'videos' => [[
                    'url' => 'https://example.com/video',
                    'title' => 'Sunday Morning Service',
                    'date' => '2025-05-10',
                ]],
            ])
            ->assertRedirect(route('dashboard.site-content.index'))
            ->assertSessionHasErrors('videos.0.url');
    }

    public function test_home_video_title_must_be_a_predefined_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('dashboard.site-content.index'))
            ->put(route('dashboard.site-content.video.update'), [
                'videos' => [[
                    'url' => 'https://youtu.be/abcdefghijk',
                    'title' => 'Custom title',
                    'date' => '2025-05-10',
                ]],
            ])
            ->assertRedirect(route('dashboard.site-content.index'))
            ->assertSessionHasErrors('videos.0.title');
    }

    public function test_home_video_dashboard_controls_and_save_status_are_translated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        app()->setLocale('sw');

        $this->actingAs($admin)
            ->get(route('dashboard.site-content.index'))
            ->assertOk()
            ->assertSeeText('Chagua faili')
            ->assertSeeText('Hakuna faili lililochaguliwa')
            ->assertSeeText('Ongeza video nyingine');

        $this->put(route('dashboard.site-content.video.update'), [
            'videos' => [[
                'url' => 'https://youtu.be/abcdefghijk',
                'title' => 'Sunday Morning Service',
                'date' => '2025-05-10',
            ]],
        ])->assertRedirect()
            ->assertSessionHas('status', 'Video za ukurasa wa mwanzo zimesasishwa.');

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Tazama video zote');
    }

    public function test_public_home_page_keeps_showing_a_legacy_video_setting(): void
    {
        DB::table('site_settings')->insert([
            ['key' => 'home.video_url', 'value' => 'https://youtu.be/abcdefghijk', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home.video_title', 'value' => 'Tuesday Teaching Service', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home.video_date', 'value' => '2025-05-10', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'home.video_thumbnail', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://www.youtube.com/watch?v=abcdefghijk"', false)
            ->assertSeeText('Tuesday Teaching Service')
            ->assertSee('href="'.route('videos').'"', false);
    }

    public function test_admin_can_upload_and_remove_the_home_video_thumbnail(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->put(route('dashboard.site-content.video.update'), [
            'videos' => [[
                'url' => 'https://vimeo.com/123456789',
                'title' => 'Friday Prayer Service',
                'date' => '2025-05-10',
                'thumbnail' => UploadedFile::fake()->image('conference.jpg'),
            ]],
        ])->assertRedirect();

        $videos = json_decode(DB::table('site_settings')->where('key', 'home.videos')->value('value'), true);
        $thumbnailPath = $videos[0]['thumbnail'];
        Storage::disk('public')->assertExists($thumbnailPath);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Friday Prayer Service')
            ->assertSeeText('Vimeo')
            ->assertSee(Storage::disk('public')->url($thumbnailPath), false);

        $this->put(route('dashboard.site-content.video.update'), [
            'videos' => [],
        ])->assertRedirect();

        Storage::disk('public')->assertMissing($thumbnailPath);
    }
}
