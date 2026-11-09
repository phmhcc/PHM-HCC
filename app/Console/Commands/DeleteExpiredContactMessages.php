<?php

namespace App\Console\Commands;

use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteExpiredContactMessages extends Command
{
    protected $signature = 'contact-messages:prune';

    protected $description = 'Delete contact messages and notifications older than one month';

    public function handle(): int
    {
        $cutoff = now()->subMonth();
        $expiredIds = ContactMessage::query()
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        if ($expiredIds->isNotEmpty()) {
            ContactMessage::query()->whereIn('id', $expiredIds)->delete();
        }

        $expiredNotifications = DB::table('notifications')
            ->where('type', ContactMessageReceived::class)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$expiredIds->count()} contact messages and {$expiredNotifications} related notifications.");

        return self::SUCCESS;
    }
}
