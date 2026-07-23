<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $fillable = ['code', 'name', 'description', 'concepts', 'enabled'];

    protected $casts = [
        'concepts' => 'array',
        'enabled' => 'boolean',
    ];
}
