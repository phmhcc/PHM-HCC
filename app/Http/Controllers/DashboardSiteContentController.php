<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class DashboardSiteContentController extends Controller
{
    private const MAX_HOME_VIDEOS = 20;

    private const VIDEO_TITLE_OPTIONS = [
        'Sunday Morning Service',
        'Sunday Afternoon Service',
        'Tuesday Teaching Service',
        'Friday Prayer Service',
    ];

    public function index(): View
    {
        $this->authorizeManagement();

        $categories = GalleryCategory::query()
            ->withCount('images')
            ->orderBy('name')
            ->get();
        $selectedCategoryId = request()->query('gallery_category_id');
        $imagesQuery = GalleryImage::query()
            ->with('category')
            ->latest('id');
        if (is_scalar($selectedCategoryId) && ctype_digit((string) $selectedCategoryId) && (int) $selectedCategoryId > 0) {
            $imagesQuery->where('gallery_category_id', (int) $selectedCategoryId);
        } else {
            $selectedCategoryId = '';
        }
        $images = $imagesQuery->paginate(12, ['*'], 'gallery_page')->withQueryString();
        $socialLinks = SocialLink::query()->orderBy('sort_order')->orderBy('name')->get();
        $settings = DB::table('site_settings')->pluck('value', 'key');
        $homeVideos = $this->homeVideosFromSettings($settings);
        $homeVideoThumbnailById = collect($homeVideos)->pluck('thumbnail', 'id');
        $submittedVideoRows = old('videos', $homeVideos);
        $homeVideoRows = is_array($submittedVideoRows)
            ? array_filter($submittedVideoRows, 'is_array')
            : $homeVideos;
        $homeVideoNextIndex = $homeVideoRows === []
            ? 0
            : max(array_map('intval', array_keys($homeVideoRows))) + 1;
        $videoTitleOptions = self::VIDEO_TITLE_OPTIONS;

        return view('dashboards.site-content.index', compact(
            'categories',
            'images',
            'socialLinks',
            'settings',
            'selectedCategoryId',
            'videoTitleOptions',
            'homeVideoRows',
            'homeVideoNextIndex',
            'homeVideoThumbnailById',
        ));
    }

    public function uploadImages(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'category_id' => ['exclude_if:category_id,other', 'nullable', 'required_without:new_category', 'exists:gallery_categories,id'],
            'new_category' => ['nullable', 'required_without:category_id', 'required_if:category_id,other', 'string', 'max:100'],
            'images' => ['required', 'array', 'min:1', 'max:30'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if (filled($data['new_category'] ?? null)) {
            $slug = Str::slug($data['new_category']);
            if ($slug === '') {
                throw ValidationException::withMessages([
                    'new_category' => __('Enter a category name containing letters or numbers.'),
                ]);
            }

            $category = GalleryCategory::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => trim($data['new_category']), 'icon' => 'fa-images'],
            );
        } else {
            $category = GalleryCategory::findOrFail($data['category_id']);
        }

        foreach ($data['images'] as $image) {
            $path = $image->store('gallery', 'public');
            if (! is_string($path)) {
                throw new RuntimeException('The gallery image could not be saved.');
            }

            GalleryImage::create([
                'gallery_category_id' => $category->id,
                'path' => $path,
                'original_name' => $image->getClientOriginalName(),
            ]);
        }

        return back()->with('status', __('Gallery images added to :category.', ['category' => $category->name]));
    }

    public function deleteImage(GalleryImage $galleryImage): RedirectResponse
    {
        $this->authorizeManagement();

        if (str_starts_with($galleryImage->path, 'gallery/') && ! Storage::disk('public')->delete($galleryImage->path)) {
            throw new RuntimeException('The gallery image file could not be deleted.');
        }

        $galleryImage->delete();

        return back()->with('status', __('Gallery image deleted.'));
    }

    public function deleteCategory(GalleryCategory $galleryCategory): RedirectResponse
    {
        $this->authorizeManagement();
        abort_if($galleryCategory->images()->exists(), 422, __('Move or delete this category’s photos before deleting the category.'));

        $galleryCategory->delete();

        return back()->with('status', __('Gallery category deleted.'));
    }

    public function storeSocialLink(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:social_links,name'],
            'url' => ['required', 'url', 'max:2048'],
        ]);
        $data['icon'] = $this->socialIconForName($data['name']);
        $data['sort_order'] = (SocialLink::max('sort_order') ?? 0) + 1;
        SocialLink::create($data);

        return back()->with('status', __('Social link added.'));
    }

    public function updateSocialLink(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'url' => ['nullable', 'url', 'max:2048'],
        ]);
        $data['url'] = $data['url'] ?? '';
        $socialLink->update($data);

        return back()->with('status', __('Social link updated.'));
    }

    public function updateGiving(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'payment_number' => ['required', 'string', 'max:40', 'regex:/^[0-9+() -]+$/'],
            'payment_name' => ['required', 'string', 'max:120'],
            'additional_methods' => ['nullable', 'array', 'max:10'],
            'additional_methods.*' => ['array'],
            'additional_methods.*.name' => ['nullable', 'string', 'max:100'],
            'additional_methods.*.details' => ['nullable', 'string', 'max:300'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $additionalMethods = collect($data['additional_methods'] ?? [])
            ->map(fn (array $method) => [
                'name' => trim($method['name'] ?? ''),
                'details' => trim($method['details'] ?? ''),
            ])
            ->filter(fn (array $method) => $method['name'] !== '' || $method['details'] !== '')
            ->values();

        foreach ($additionalMethods as $index => $method) {
            if ($method['name'] === '' || $method['details'] === '') {
                throw ValidationException::withMessages([
                    "additional_methods.$index" => __('Enter both a payment method name and its details.'),
                ]);
            }
        }

        $oldImagePath = DB::table('site_settings')->where('key', 'giving.image_path')->value('value');
        $newImagePath = null;
        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('giving', 'public');
            if (! is_string($newImagePath)) {
                throw new RuntimeException('The giving image could not be saved.');
            }
        }

        foreach (['payment_number', 'payment_name'] as $key) {
            $value = $data[$key];
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'giving.'.$key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()],
            );
        }

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'giving.additional_methods'],
            [
                'value' => json_encode($additionalMethods->all(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        if ($newImagePath !== null) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'giving.image_path'],
                ['value' => $newImagePath, 'updated_at' => now(), 'created_at' => now()],
            );

            if (is_string($oldImagePath) && str_starts_with($oldImagePath, 'giving/')) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        return back()->with('status', __('Giving payment details updated.'));
    }

    public function updateHomeVideo(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $settings = DB::table('site_settings')->pluck('value', 'key');
        $existingVideos = $this->homeVideosFromSettings($settings);
        $existingVideosById = collect($existingVideos)->keyBy('id');
        $rules = [
            'videos' => ['nullable', 'array', 'max:'.self::MAX_HOME_VIDEOS],
            'videos.*' => ['required', 'array'],
            'videos.*.id' => ['nullable', 'string', 'max:36'],
            'videos.*.url' => ['nullable', 'url', 'max:2048'],
            'videos.*.title' => ['nullable', Rule::in(self::VIDEO_TITLE_OPTIONS)],
            'videos.*.date' => ['nullable', 'date'],
            'videos.*.thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];

        $submittedVideos = $request->input('videos', []);
        foreach (is_array($submittedVideos) ? $submittedVideos : [] as $index => $video) {
            if (! is_array($video) || blank($video['url'] ?? null)) {
                continue;
            }

            $rules["videos.$index.url"] = ['required', 'url', 'max:2048'];
            $rules["videos.$index.title"] = ['required', Rule::in(self::VIDEO_TITLE_OPTIONS)];
            $rules["videos.$index.date"] = ['required', 'date'];
        }

        $data = $request->validate($rules);
        $preparedVideos = [];
        $seenVideoIds = [];

        foreach ($data['videos'] ?? [] as $index => $video) {
            $videoUrl = trim($video['url'] ?? '');
            if ($videoUrl === '') {
                continue;
            }

            if (! $this->isSupportedVideoUrl($videoUrl)) {
                throw ValidationException::withMessages([
                    "videos.$index.url" => __('Enter a YouTube, Vimeo, or direct MP4 video URL.'),
                ]);
            }

            $videoId = $video['id'] ?? '';
            $existingVideo = $videoId !== '' ? $existingVideosById->get($videoId) : null;
            if ($videoId !== '' && $existingVideo === null) {
                throw ValidationException::withMessages([
                    "videos.$index.id" => __('This video entry is no longer available. Refresh the page and try again.'),
                ]);
            }

            if ($videoId !== '' && in_array($videoId, $seenVideoIds, true)) {
                throw ValidationException::withMessages([
                    "videos.$index.id" => __('A video entry can only appear once.'),
                ]);
            }
            if ($videoId !== '') {
                $seenVideoIds[] = $videoId;
            }

            $preparedVideos[] = [$index, $video, $videoUrl, $videoId, $existingVideo];
        }

        $homeVideos = [];
        $newThumbnailPaths = [];
        foreach ($preparedVideos as [$index, $video, $videoUrl, $videoId, $existingVideo]) {
            $thumbnailPath = $existingVideo['thumbnail'] ?? '';
            $thumbnail = $request->file("videos.$index.thumbnail");
            if ($thumbnail !== null) {
                $thumbnailPath = $thumbnail->store('home-videos', 'public');
                if (! is_string($thumbnailPath)) {
                    foreach ($newThumbnailPaths as $newThumbnailPath) {
                        Storage::disk('public')->delete($newThumbnailPath);
                    }
                    throw new RuntimeException('The home page video thumbnail could not be saved.');
                }
                $newThumbnailPaths[] = $thumbnailPath;
            }

            $homeVideos[] = [
                'id' => $videoId !== '' ? $videoId : (string) Str::uuid(),
                'url' => $videoUrl,
                'title' => $video['title'],
                'date' => $video['date'],
                'thumbnail' => $thumbnailPath,
            ];
        }

        $allThumbnailPaths = collect($existingVideos)
            ->pluck('thumbnail')
            ->merge($newThumbnailPaths)
            ->filter(fn ($path) => is_string($path) && str_starts_with($path, 'home-videos/'))
            ->unique();
        $retainedThumbnailPaths = collect($homeVideos)
            ->pluck('thumbnail')
            ->filter(fn ($path) => is_string($path) && str_starts_with($path, 'home-videos/'));

        DB::transaction(function () use ($homeVideos): void {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'home.videos'],
                ['value' => json_encode($homeVideos, JSON_THROW_ON_ERROR), 'updated_at' => now(), 'created_at' => now()],
            );

            foreach (['home.video_url', 'home.video_title', 'home.video_date', 'home.video_thumbnail'] as $legacyKey) {
                DB::table('site_settings')->updateOrInsert(
                    ['key' => $legacyKey],
                    ['value' => '', 'updated_at' => now(), 'created_at' => now()],
                );
            }
        });

        foreach ($allThumbnailPaths->diff($retainedThumbnailPaths) as $thumbnailPath) {
            if (! Storage::disk('public')->delete($thumbnailPath)) {
                throw new RuntimeException('A removed home page video thumbnail could not be deleted.');
            }
        }

        return back()->with('status', __('Home page video updated.'));
    }

    private function homeVideosFromSettings(Collection $settings): array
    {
        $videos = json_decode((string) $settings->get('home.videos', ''), true);
        if (is_array($videos)) {
            return array_values(array_filter($videos, fn ($video) => is_array($video) && filled($video['url'] ?? null)));
        }

        $legacyUrl = $settings->get('home.video_url');
        if (filled($legacyUrl)) {
            return [[
                'id' => 'legacy',
                'url' => $legacyUrl,
                'title' => $settings->get('home.video_title', ''),
                'date' => $settings->get('home.video_date', ''),
                'thumbnail' => $settings->get('home.video_thumbnail', ''),
            ]];
        }

        return [];
    }

    private function authorizeManagement(): void
    {
        abort_unless(request()->user()?->hasPermissionTo('site_content.manage'), 403);
    }

    private function isSupportedVideoUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        return in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
            'youtu.be',
            'vimeo.com',
            'www.vimeo.com',
            'player.vimeo.com',
        ], true) || preg_match('/\.(mp4)$/i', $path) === 1;
    }

    private function socialIconForName(string $name): string
    {
        return match (Str::lower(trim($name))) {
            'facebook' => 'fa-facebook',
            'youtube' => 'fa-youtube',
            'instagram' => 'fa-instagram',
            'whatsapp' => 'fa-whatsapp',
            'x', 'twitter' => 'fa-x-twitter',
            'tiktok' => 'fa-tiktok',
            'linkedin' => 'fa-linkedin',
            'telegram' => 'fa-telegram',
            default => 'fa-link',
        };
    }
}
