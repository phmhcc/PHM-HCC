<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use Illuminate\View\View;

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
}
