<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'UserId';
    public $incrementing = false;

    protected $fillable = [
        'UserId',
        'Username',
        'Email',
        'Role',
        'Photo',
        'Password',
    ];

    protected $hidden = [
        'Password',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'UserId', 'UserId');
    }

    public function isAdmin(): bool {
        return $this->role === "Admin";
    }

    public function hasRole(string $role): bool {
        return $this->Role === $role;
    }
}
