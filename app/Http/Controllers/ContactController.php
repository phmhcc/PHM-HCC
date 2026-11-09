<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contactMessage = ContactMessage::create($data);
        $notification = new ContactMessageReceived($contactMessage);

        User::query()
            ->whereHas('roleRecord.permissions', fn ($query) => $query->whereIn('permissions.name', [
                'contact_messages.view',
                'contact_messages.*',
                '*',
            ]))
            ->with('roleRecord.permissions')
            ->get()
            ->each(fn (User $user) => $user->notify($notification));

        Mail::send('emails.contact-message', [
            'data' => $data,
        ], function ($mail) use ($data) {
            $mail->to(config('mail.from.address'))
                // ->subject($emailSubject)
                ->replyTo($data['email'], $data['name']);
        });


        return back()->with(
            'success',
            __('Thank you! Your message has been submitted successfully.')
        );
    }
}
