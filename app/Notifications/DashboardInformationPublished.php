<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DashboardInformationPublished extends Notification
{
    use Queueable;

    private const ITEM_TYPES = ['event', 'duty', 'meeting', 'department'];

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly string $titleTranslation,
        private readonly array $titleParameters,
        private readonly string $messageTranslation,
        private readonly string $url,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'title_translation' => $this->titleTranslation,
            'title_parameters' => $this->titleParameters,
            'message_translation' => $this->messageTranslation,
            'url' => $this->url,
        ];
    }

    public static function localizedTitle(array $data): string
    {
        $title = $data['title'] ?? __('Church notification');
        $translation = $data['title_translation'] ?? null;
        $parameters = $data['title_parameters'] ?? [];

        if ($translation === null) {
            foreach (self::ITEM_TYPES as $itemType) {
                $prefix = 'New '.$itemType.': ';
                if (str_starts_with($title, $prefix)) {
                    $translation = 'New '.$itemType.': :title';
                    $parameters = ['title' => substr($title, strlen($prefix))];
                    break;
                }
            }
        }

        if ($translation === null) {
            return $title;
        }

        if (array_key_exists('title', $parameters)) {
            $parameters['title'] = __($parameters['title']);
        }

        return __($translation, $parameters);
    }

    public static function localizedMessage(array $data): string
    {
        $translation = $data['message_translation'] ?? null;
        $message = $data['message'] ?? '';

        if ($translation !== null) {
            return __($translation, $data['message_parameters'] ?? []);
        }

        return $message === 'An administrator added this to the church dashboard.'
            ? __($message)
            : $message;
    }
}
