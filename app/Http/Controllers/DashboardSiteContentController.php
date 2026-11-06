<?php

namespace App\Http\Controllers;

use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class DashboardSiteContentController extends Controller
{
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

        return view('dashboards.site-content.index', compact('categories', 'images', 'socialLinks', 'settings', 'selectedCategoryId'));
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
            'url' => ['required', 'url', 'max:2048'],
        ]);
        $socialLink->update($data);

        return back()->with('status', __('Social link updated.'));
    }

    public function deleteSocialLink(SocialLink $socialLink): RedirectResponse
    {
        $this->authorizeManagement();
        $socialLink->delete();

        return back()->with('status', __('Social link deleted.'));
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

    private function authorizeManagement(): void
    {
        abort_unless(request()->user()?->hasPermissionTo('site_content.manage'), 403);
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
