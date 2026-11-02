<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->latest('published_at')
            ->latest('id')
            ->paginate(10);

        return view('pages.announcements', compact('announcements'));
    }
}
