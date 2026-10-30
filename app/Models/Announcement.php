<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'message', 'published_at', 'created_by'];

    protected $casts = ['published_at' => 'datetime'];
}
