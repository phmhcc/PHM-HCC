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
        $videos = $this->homeVideos();
        $youtubeChannelUrl = SocialLink::query()
            ->whereRaw('LOWER(name) = ?', ['youtube'])
            ->whereNotNull('url')
            ->where('url', '!=', '')
            ->value('url');

        return view('pages.index', compact('announcements', 'videos', 'youtubeChannelUrl'));
    }

    public function videos(): View
    {
        return view('pages.videos', ['videos' => $this->homeVideos()]);
    }

    private function homeVideos(): \Illuminate\Support\Collection
    {
        $settings = DB::table('site_settings')
            ->whereIn('key', ['home.videos', 'home.video_url', 'home.video_title', 'home.video_date', 'home.video_thumbnail'])
            ->pluck('value', 'key');
        $storedVideos = json_decode((string) $settings->get('home.videos', ''), true);
        if (! is_array($storedVideos) && filled($settings->get('home.video_url'))) {
            $storedVideos = [[
                'url' => $settings->get('home.video_url'),
                'title' => $settings->get('home.video_title', ''),
                'date' => $settings->get('home.video_date'),
                'thumbnail' => $settings->get('home.video_thumbnail'),
            ]];
        }

        return collect(is_array($storedVideos) ? $storedVideos : [])
            ->filter(fn ($video) => is_array($video) && filled($video['url'] ?? null))
            ->map(function (array $storedVideo): ?array {
                $video = $this->publicVideoSource($storedVideo['url'] ?? null);
                if ($video === null) {
                    return null;
                }

                $video['title'] = __($storedVideo['title'] ?? '');
                $video['date'] = $storedVideo['date'] ?? null;
                if (filled($storedVideo['thumbnail'] ?? null) && str_starts_with($storedVideo['thumbnail'], 'home-videos/')) {
                    $video['thumbnail'] = Storage::disk('public')->url($storedVideo['thumbnail']);
                }

                return $video;
            })
            ->filter()
            ->values();
    }

    private function publicVideoSource(?string $url): ?array
    {
        if (blank($url)) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        if (preg_match('/\.(mp4)$/i', $path) === 1) {
            return [
                'url' => $url,
                'provider' => 'Video',
                'thumbnail' => asset('Images/church-background.jpg'),
            ];
        }

        $videoId = null;
        if ($host === 'youtu.be') {
            $videoId = trim($path, '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $videoId = $query['v'] ?? null;
            if (! is_string($videoId) && preg_match('#/(?:embed|shorts)/([A-Za-z0-9_-]+)#', $path, $matches)) {
                $videoId = $matches[1];
            }
        } elseif (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)) {
            if (preg_match('#/(?:video/)?([0-9]+)(?:$|/)#', $path, $matches)) {
                return [
                    'url' => $url,
                    'provider' => 'Vimeo',
                    'thumbnail' => asset('Images/church-background.jpg'),
                ];
            }
        }

        if (is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1) {
            return [
                'url' => 'https://www.youtube.com/watch?v='.rawurlencode($videoId),
                'provider' => 'YouTube',
                'thumbnail' => 'https://img.youtube.com/vi/'.rawurlencode($videoId).'/hqdefault.jpg',
            ];
        }

        return null;
    }

    public function events(): View
    {
        Event::markPastEventsAsCompleted();
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
