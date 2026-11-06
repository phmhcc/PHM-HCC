<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use App\Models\SocialLink;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PublicPageController extends Controller
{
    public function home(): View
    {
        $announcements = Announcement::query()
            ->latest('published_at')
            ->latest('id')
            ->take(5)
            ->get();

        return view('pages.index', compact('announcements'));
    }

    public function events(): View
    {
        $events = Event::query()
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pages.events', compact('events'));
    }

    public function gallery(): View
    {
        $categories = GalleryCategory::query()
            ->whereHas('images')
            ->with(['images' => fn ($query) => $query->orderBy('id')])
            ->withCount('images')
            ->orderBy('name')
            ->get();
        $galleryImages = GalleryImage::query()
            ->with('category')
            ->orderBy('id')
            ->get();

        return view('pages.gallery', compact('categories', 'galleryImages'));
    }

    public function galleryImage(GalleryImage $galleryImage, \Illuminate\Http\Request $request): Response
    {
        if (str_starts_with($galleryImage->path, 'Images/')) {
            $path = public_path($galleryImage->path);
            abort_unless(is_file($path), 404);

            return $request->boolean('download')
                ? response()->download($path, $galleryImage->original_name)
                : response()->file($path);
        }

        abort_unless(Storage::disk('public')->exists($galleryImage->path), 404);

        return Storage::disk('public')->response(
            $galleryImage->path,
            $galleryImage->original_name,
            [],
            $request->boolean('download') ? 'attachment' : 'inline',
        );
    }

    public function giving(): View
    {
        $settings = DB::table('site_settings')->pluck('value', 'key');
        $additionalPaymentMethods = json_decode($settings->get('giving.additional_methods', '[]'), true);

        return view('pages.giving', [
            'paymentNumber' => $settings->get('giving.payment_number', '5061162'),
            'paymentName' => $settings->get('giving.payment_name', 'VODACOM LIPA NAMBA SADAKA HCC'),
            'additionalPaymentMethods' => is_array($additionalPaymentMethods) ? $additionalPaymentMethods : [],
            'givingImageUrl' => filled($settings->get('giving.image_path'))
                ? Storage::disk('public')->url($settings->get('giving.image_path'))
                : asset('Images/FB_IMG_1793878208435.jpg'),
        ]);
    }
}
