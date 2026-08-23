<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'site:')) {
            return asset('images/' . ltrim(substr($this->image, 5), '/'));
        }

        return asset('storage/' . $this->image);
    }

    public function hasManagedImage(): bool
    {
        return filled($this->image) && !str_starts_with($this->image, 'site:');
    }
}
