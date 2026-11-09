<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class CompletePastEvents extends Command
{
    protected $signature = 'events:complete-past';

    protected $description = 'Mark events that have ended as completed';

    public function handle(): int
    {
        $count = Event::markPastEventsAsCompleted();

        $this->info("Marked {$count} past events as completed.");

        return self::SUCCESS;
    }
}
