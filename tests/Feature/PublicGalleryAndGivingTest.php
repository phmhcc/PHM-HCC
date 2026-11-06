<?php

namespace Tests\Feature;

use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGalleryAndGivingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $categoryNames = [
            'youth-ministry' => 'Youth Ministry',
            'children-ministry' => 'Children Ministry',
            'women-ministry' => 'Women Ministry',
            'men-ministry' => 'Men Ministry',
            'worship-praise' => 'Worship & Praise',
            'prayer-ministry' => 'Prayer Ministry',
            'choir-ministry' => 'Choir Ministry',
            'community-outreach' => 'Community Outreach',
        ];

        foreach ($categoryNames as $slug => $name) {
            $category = GalleryCategory::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'icon' => 'fa-users'],
            );
            GalleryImage::create([
                'gallery_category_id' => $category->id,
                'path' => 'test/'.$slug.'.jpg',
                'original_name' => $slug.'.jpg',
            ]);
        }
    }

    public function test_public_gallery_has_category_filters_and_download_links(): void
    {
        $youthImage = GalleryImage::query()
            ->whereHas('category', fn ($query) => $query->where('slug', 'youth-ministry'))
            ->firstOrFail();
        $response = $this->get(route('gallery'))
            ->assertOk()
            ->assertSeeText('Youth Ministry')
            ->assertSeeText('Children')
            ->assertSeeText('Women')
            ->assertSeeText('Men Ministry')
            ->assertSeeText('Worship & Praise')
            ->assertSeeText('Prayer Ministry')
            ->assertSeeText('Choir')
            ->assertSeeText('Community Outreach')
            ->assertSee('data-gallery-category="prayer-ministry"', false)
            ->assertSee('data-view-category="prayer-ministry"', false)
            ->assertSeeText('View Photos')
            ->assertSeeText('View All Photos')
            ->assertSee('class="gallery-ministry-card"', false)
            ->assertSee('data-view-category="community-outreach"', false)
            ->assertSee('id="gallery-collection" hidden', false)
            ->assertSee('data-gallery-category="all"', false)
            ->assertSee('href="'.route('gallery.image', ['galleryImage' => $youthImage, 'download' => 1]).'"', false)
            ->assertSee('src="'.route('gallery.image', $youthImage).'"', false);

        $images = GalleryImage::query()->with('category')->get();
        self::assertNotEmpty($images);
        self::assertSame(count($images), substr_count($response->getContent(), 'class="gallery-download"'));
        self::assertSame(count($images), substr_count($response->getContent(), 'class="gallery-image-card"'));

        preg_match_all(
            '/<article class="gallery-ministry-card" data-category="([^"]+)" data-photo-count="(\d+)">/',
            $response->getContent(),
            $categoryCards,
            PREG_SET_ORDER,
        );
        self::assertCount(8, $categoryCards);
        $categoryPhotoTotal = 0;

        foreach ($categoryCards as $categoryCard) {
            $category = $categoryCard[1];
            $reportedCount = (int) $categoryCard[2];
            $actualCount = substr_count($response->getContent(), 'data-gallery-card data-category="'.$category.'"');

            self::assertSame($actualCount, $reportedCount, "{$category} count must match its displayed photos.");
            self::assertStringContainsString(
                'data-gallery-category="'.$category.'" data-photo-count="'.$actualCount.'"',
                $response->getContent(),
            );
            $categoryPhotoTotal += $reportedCount;
        }

        preg_match_all(
            '/class="gallery-image-card" data-gallery-card data-category=""/',
            $response->getContent(),
            $uncategorizedCards,
        );
        self::assertSame(count($images), $categoryPhotoTotal + count($uncategorizedCards[0]));
        self::assertSame(count($images), substr_count($response->getContent(), 'class="gallery-image-card"'));

        $emptyCategory = GalleryCategory::query()->create([
            'name' => 'Hidden Empty Category',
            'slug' => 'hidden-empty-category',
        ]);
        $this->get(route('gallery'))
            ->assertOk()
            ->assertDontSee('data-category="'.$emptyCategory->slug.'"', false);
    }

    public function test_giving_page_is_public_and_links_to_verified_church_contact_details(): void
    {
        $this->get(route('giving'))
            ->assertOk()
            ->assertSee('<nav class="navbar">', false)
            ->assertSee('<footer class="site-footer">', false)
            ->assertSeeText('We are')
            ->assertSeeText('generous')
            ->assertSeeText('5061162')
            ->assertSeeText('VODACOM LIPA NAMBA SADAKA HCC')
            ->assertSeeText('Give in person')
            ->assertSeeText('Mbezi-Magari Saba, Dar es Salaam, Tanzania')
            ->assertSeeText('contact the church using the official number below')
            ->assertSee('class="giving-photo-link" href="'.asset('Images/FB_IMG_1793878208435.jpg').'" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('href="tel:+255754970421"', false)
            ->assertSee('href="https://wa.me/255754970421"', false)
            ->assertDontSeeText('Selcom');
    }

    public function test_giving_page_translates_its_public_content_into_swahili(): void
    {
        $this->withSession(['locale' => 'sw'])
            ->get(route('giving'))
            ->assertOk()
            ->assertSeeText('Sisi ni')
            ->assertSeeText('wakarimu')
            ->assertSeeText('Kutoa')
            ->assertSeeText('Vodacom M-Pesa')
            ->assertSeeText('5061162')
            ->assertSeeText('Toa kanisani');
    }

    public function test_gallery_category_cards_are_translated_into_swahili(): void
    {
        $this->withSession(['locale' => 'sw'])
            ->get(route('gallery'))
            ->assertOk()
            ->assertSeeText('Tazama Picha')
            ->assertSeeText('Tazama Picha Zote')
            ->assertSeeText('Huduma kwa Jamii')
            ->assertSeeText('Huduma ya Maombi');
    }
}
