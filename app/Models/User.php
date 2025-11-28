<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'username',
        'email',
        'role',
        'photo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // Return password for Laravel auth (column is 'Password' in DB)
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function isAdmin(): bool {
        return isset($this->role) && $this->role === 'Admin';
    }

    public function hasRole(string $role): bool {
        return isset($this->role) && $this->role === $role;
    }
}
