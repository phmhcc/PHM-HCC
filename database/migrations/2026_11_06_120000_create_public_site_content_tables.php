<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 60)->default('fa-images');
            $table->timestamps();
        });

        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path', 500)->unique();
            $table->string('original_name', 255);
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('url', 2048);
            $table->string('icon', 60)->default('fa-link');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value');
            $table->timestamps();
        });

        $categoryImages = [
            'youth-ministry' => [
                'FB_IMG_1784723251611.jpg', 'FB_IMG_1793878005088.jpg', 'FB_IMG_1793877918597.jpg',
                'FB_IMG_1793877769089.jpg', 'FB_IMG_1793877763239.jpg', 'FB_IMG_1791266130812.jpg',
                'FB_IMG_1793876013734.jpg', 'FB_IMG_1793878257011.jpg', 'FB_IMG_1793878268365.jpg',
                'FB_IMG_1793878277471.jpg', 'FB_IMG_1793878309572.jpg', 'FB_IMG_1793878155107.jpg',
            ],
            'children-ministry' => [
                'FB_IMG_1793875648741.jpg', 'FB_IMG_1793878212040.jpg', 'FB_IMG_1793878219438.jpg',
                'FB_IMG_1793878224851.jpg', 'FB_IMG_1793878241759.jpg', 'FB_IMG_1793878245099.jpg',
                'FB_IMG_1793878260614.jpg', 'FB_IMG_1793878272947.jpg', 'FB_IMG_1793878288259.jpg',
                'FB_IMG_1793878293205.jpg', 'FB_IMG_1793878301397.jpg', 'FB_IMG_1793878340092.jpg',
            ],
            'women-ministry' => [
                'FB_IMG_1784723083161.jpg', 'FB_IMG_1793875628971.jpg', 'FB_IMG_1793877779155.jpg',
                'FB_IMG_1793877794516.jpg', 'FB_IMG_1793877794516.jpg', 'FB_IMG_1793877933904.jpg',
                'FB_IMG_1793878014519.jpg', 'FB_IMG_1793878254062.jpg', 'FB_IMG_1793878025321.jpg',
                'FB_IMG_1793878058220.jpg', 'FB_IMG_1793878090169.jpg', 'FB_IMG_1793878101812.jpg',
                'FB_IMG_1793878114081.jpg', 'FB_IMG_1793878116663.jpg', 'FB_IMG_1793878119119.jpg',
                'FB_IMG_1793878126821.jpg', 'FB_IMG_1793878145375.jpg', 'FB_IMG_1793878306239.jpg',
                'FB_IMG_1793878150706.jpg', 'FB_IMG_1793878155107.jpg', 'FB_IMG_1793878158896.jpg',
                'FB_IMG_1793878169674.jpg', 'FB_IMG_1793878177576.jpg', 'FB_IMG_1793878180687.jpg',
                'FB_IMG_1793878194214.jpg', 'FB_IMG_1793878199665.jpg',
            ],
            'men-ministry' => [
                'FB_IMG_1784717227063.jpg', 'FB_IMG_1784722994019.jpg', 'FB_IMG_1784723023935.jpg',
                'FB_IMG_1784723120511.jpg', 'FB_IMG_1793876024320.jpg', 'FB_IMG_1793877899908.jpg',
                'FB_IMG_1793877912759.jpg', 'FB_IMG_1793877922000.jpg', 'FB_IMG_1793877928188.jpg',
                'FB_IMG_1793878028847.jpg', 'FB_IMG_1793878050987.jpg', 'FB_IMG_1793877993887.jpg',
                'FB_IMG_1793878348968.jpg', 'FB_IMG_1793878158896.jpg', 'FB_IMG_1793878137913.jpg',
                'FB_IMG_1793878132336.jpg', 'FB_IMG_1793878092801.jpg', 'FB_IMG_1793877813643.jpg',
            ],
            'worship-praise' => [
                'FB_IMG_1793875637214.jpg', 'FB_IMG_1793876003894.jpg', 'FB_IMG_1793876039359.jpg',
                'FB_IMG_1793876048817.jpg', 'FB_IMG_1793876071395.jpg', 'FB_IMG_1793876082431.jpg',
                'FB_IMG_1793877813643.jpg', 'FB_IMG_1793878283535.jpg', 'FB_IMG_1793875662824.jpg',
                'FB_IMG_1793878188446.jpg',
            ],
            'prayer-ministry' => [
                'FB_IMG_1793876048817.jpg', 'FB_IMG_1793877763239.jpg', 'FB_IMG_1793877779155.jpg',
                'FB_IMG_1793877785148.jpg', 'FB_IMG_1793877806086.jpg', 'FB_IMG_1793877922000.jpg',
                'FB_IMG_1793877925920.jpg',
            ],
            'choir-ministry' => [
                'FB_IMG_1793878327879.jpg', 'FB_IMG_1793878334220.jpg', 'FB_IMG_1793878150706.jpg',
                'FB_IMG_1793878169674.jpg', 'FB_IMG_1793878164489.jpg',
            ],
            'community-outreach' => ['FB_IMG_1793878208435.jpg'],
        ];
        $categoryIds = [];
        $now = now();
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

        foreach ($categoryImages as $slug => $images) {
            $categoryIds[$slug] = DB::table('gallery_categories')->insertGetId([
                'name' => $categoryNames[$slug] ?? Str::of($slug)->replace('-', ' ')->title()->toString(),
                'slug' => $slug,
                'icon' => match ($slug) {
                    'children-ministry' => 'fa-children',
                    'women-ministry' => 'fa-person-dress',
                    'men-ministry' => 'fa-person',
                    'worship-praise' => 'fa-music',
                    'prayer-ministry' => 'fa-hands-praying',
                    'community-outreach' => 'fa-hands-holding-child',
                    default => 'fa-users',
                },
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $imageCategories = [];
        foreach ($categoryImages as $slug => $images) {
            foreach ($images as $filename) {
                $imageCategories[$filename] = $slug;
            }
        }

        $imageFiles = is_dir(public_path('Images')) ? File::files(public_path('Images')) : [];
        foreach ($imageFiles as $image) {
            if (preg_match('/^FB_IMG_.*\.(?:jpe?g|png|webp)$/i', $image->getFilename()) !== 1) {
                continue;
            }

            $filename = $image->getFilename();
            $slug = $imageCategories[$filename] ?? null;
            DB::table('gallery_images')->insertOrIgnore([
                'gallery_category_id' => $slug === null ? null : $categoryIds[$slug],
                'path' => 'Images/'.$filename,
                'original_name' => $filename,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ([
            ['Facebook', 'https://www.facebook.com/share/14kCi4Q2iXW/', 'fa-facebook', 1],
            ['YouTube', 'https://youtube.com/@pastor-happymacxmkomekwa?si=2UKZ1chgQAFkT2FI', 'fa-youtube', 2],
            ['Instagram', 'https://www.instagram.com/pastor_mkomekwa?igsh=eHI1Y3VtbnQ5dTEz', 'fa-instagram', 3],
            ['WhatsApp', 'https://wa.me/255754970421', 'fa-whatsapp', 4],
        ] as [$name, $url, $icon, $sortOrder]) {
            DB::table('social_links')->insert([
                'name' => $name,
                'url' => $url,
                'icon' => $icon,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ([
            'giving.payment_number' => '5061162',
            'giving.payment_name' => 'VODACOM LIPA NAMBA SADAKA HCC',
        ] as $key => $value) {
            DB::table('site_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('gallery_categories');
    }
};
