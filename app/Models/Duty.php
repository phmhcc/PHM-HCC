<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Duty extends Model
{
    protected $fillable = [
        'title',
        'description',
        'duty_date',
        'start_time',
        'end_time',
        'department_id',
        'assigned_to',
        'created_by',
        'status',
    ];

    protected $casts = [
        'duty_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
