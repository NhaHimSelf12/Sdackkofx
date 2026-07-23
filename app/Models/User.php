<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'timezone',
        'account_balance', 'default_risk_pct', 'google_id', 'avatar'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_balance' => 'float',
            'default_risk_pct' => 'float',
        ];
    }

    public function journals()
    {
        return $this->hasMany(TradeJournal::class);
    }

    public function watchlistMarkets()
    {
        return $this->belongsToMany(Market::class, 'watchlists')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
