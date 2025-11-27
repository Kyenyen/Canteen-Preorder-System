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
    public $timestamps = false;

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

    // Return password for Laravel auth (column is 'Password' in DB)
    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'UserId', 'UserId');
    }

    public function isAdmin(): bool {
        return isset($this->Role) && $this->Role === 'Admin';
    }

    public function hasRole(string $role): bool {
        return isset($this->Role) && $this->Role === $role;
    }
}
