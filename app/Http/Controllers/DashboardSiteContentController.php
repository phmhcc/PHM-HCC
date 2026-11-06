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
use Illuminate\Validation\Rule;
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
        $images = GalleryImage::query()
            ->with('category')
            ->latest('id')
            ->paginate(12, ['*'], 'gallery_page');
        $socialLinks = SocialLink::query()->orderBy('sort_order')->orderBy('name')->get();
        $settings = DB::table('site_settings')->pluck('value', 'key');

        return view('dashboards.site-content.index', compact('categories', 'images', 'socialLinks', 'settings'));
    }

    public function uploadImages(Request $request): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'category_id' => ['nullable', 'required_without:new_category', 'exists:gallery_categories,id'],
            'new_category' => ['nullable', 'required_without:category_id', 'string', 'max:100'],
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
            'icon' => ['required', 'regex:/^fa-[a-z0-9-]+$/'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ]);
        SocialLink::create($data);

        return back()->with('status', __('Social link added.'));
    }

    public function updateSocialLink(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $this->authorizeManagement();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('social_links', 'name')->ignore($socialLink->id)],
            'url' => ['required', 'url', 'max:2048'],
            'icon' => ['required', 'regex:/^fa-[a-z0-9-]+$/'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
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
        ]);

        foreach ($data as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'giving.'.$key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()],
            );
        }

        return back()->with('status', __('Giving payment details updated.'));
    }

    private function authorizeManagement(): void
    {
        abort_unless(request()->user()?->hasPermissionTo('site_content.manage'), 403);
    }
}
