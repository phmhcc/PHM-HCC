<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactMessageReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly ContactMessage $contactMessage)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New contact message',
            'message' => 'New message from '.$this->contactMessage->name,
            'title_translation' => 'New contact message',
            'message_translation' => 'New message from :name',
            'message_parameters' => ['name' => $this->contactMessage->name],
            'contact_message_id' => $this->contactMessage->id,
            'url' => route('dashboard.contact-messages.index', [], false),
        ];
    }
}
