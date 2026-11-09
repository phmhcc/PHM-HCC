<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\View\View;

class DashboardContactMessageController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermissionTo('contact_messages.view'), 403);

        $messages = ContactMessage::query()
            ->latest()
            ->paginate(15);

        return view('dashboards.contact-messages', compact('messages'));
    }
}
