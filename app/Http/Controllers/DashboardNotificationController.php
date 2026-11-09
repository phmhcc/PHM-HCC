<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Notifications\DatabaseNotification;

class DashboardNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        return view('dashboards.notifications', compact('notifications'));
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $item = $request->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        return back()->with('status', __('Notification marked as read.'));
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        /** @var DatabaseNotification $item */
        $item = $request->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        $url = $item->data['url'] ?? null;
        if (! is_string($url)) {
            $translation = $item->data['title_translation'] ?? null;
            foreach ([
                'event' => 'events',
                'duty' => 'duties',
                'meeting' => 'meetings',
                'department' => 'departments',
            ] as $type => $resource) {
                if ($translation === 'New '.$type.': :title'
                    || str_starts_with((string) ($item->data['title'] ?? ''), 'New '.$type.': ')) {
                    $url = route('dashboard.resource.index', $resource, false);
                    break;
                }
            }
        }

        $path = parse_url(is_string($url) ? $url : '', PHP_URL_PATH);
        if (! is_string($path) || ! str_starts_with($path, '/dashboard/')) {
            return redirect()->route('dashboard.notifications');
        }

        return redirect($path);
    }
}
