<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'page_type',
        'session_id',
        'ip_hash',
        'user_agent',
        'visitable_id',
        'visitable_type',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    public function scopeInPeriod($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->where('visited_at', '>=', $from);
        }
        if ($to) {
            $query->where('visited_at', '<=', $to);
        }
        return $query;
    }
}
