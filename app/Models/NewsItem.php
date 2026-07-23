<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = [
        'title', 'source', 'url', 'published_at',
        'sentiment', 'impact', 'summary', 'symbols',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'symbols' => 'array',
    ];
}
