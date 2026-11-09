<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardContactMessageController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user?->hasPermissionTo('contact_messages.view') ?? false, 403);

        $messages = ContactMessage::query()
            ->latest()
            ->paginate(15);

        return view('dashboards.contact-messages', compact('messages'));
    }
}
