<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'venue',
        'department_id',
        'created_by',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Event $event): void {
            if ($event->status === 'scheduled' && $event->hasEnded()) {
                $event->status = 'completed';
            }
        });
    }

    public static function markPastEventsAsCompleted(): int
    {
        $now = now();

        return static::query()
            ->where('status', 'scheduled')
            ->where(function ($query) use ($now): void {
                $query->where(function ($query) use ($now): void {
                    $query->whereNotNull('end_date')
                        ->where('end_date', '<=', $now);
                })->orWhere(function ($query) use ($now): void {
                    $query->whereNull('end_date')
                        ->where('start_date', '<=', $now);
                });
            })
            ->update([
                'status' => 'completed',
                'updated_at' => $now,
            ]);
    }

    private function hasEnded(): bool
    {
        $end = $this->end_date ?? $this->start_date;

        return $end !== null && $end->lessThanOrEqualTo(now());
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
