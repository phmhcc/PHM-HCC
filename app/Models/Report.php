<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'title',
        'activities',
        'achievements',
        'challenges',
        'recommendations',
        'department_id',
        'submitted_by',
        'reporting_period',
        'status',
    ];

    protected $casts = [
        'reporting_period' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
