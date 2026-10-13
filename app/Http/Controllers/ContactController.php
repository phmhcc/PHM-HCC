<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $emailSubject = $data['subject'] ?: 'New message from PHM HCC website';

        $emailBody = "New Contact Form Message\n\n"
            . "Name: {$data['name']}\n"
            . "Email: {$data['email']}\n"
            . "Phone: " . ($data['phone'] ?: 'Not provided') . "\n\n"
            . "Message:\n{$data['message']}";


        Mail::send('emails.contact-message', [
            'data' => $data,
        ], function ($mail) use ($data, $emailSubject) {
            $mail->to(config('mail.from.address'))
                ->subject($emailSubject)
                ->replyTo($data['email'], $data['name']);
        });


        return back()->with(
            'success',
            'Thank you! Your message has been submitted successfully.'
        );
    }
}
