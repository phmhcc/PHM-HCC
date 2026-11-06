<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon'];

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }
}
