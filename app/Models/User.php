<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /** Primary key configuration for string type */
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /** Mass Assignable Attributes */
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'role',
        'photo',
        'password',
    ];

    /** Attributes hidden from serialization */
    protected $hidden = [
        'password',
    ];

    /** Get Password for Laravel Authentication */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /** Relationship: Orders placed by this User */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    /** Check if User is an Admin */
    public function isAdmin(): bool {
        return isset($this->role) && strtolower($this->role) === 'admin';
    }

    /** Check if User has a specific Role */
    public function hasRole(string $role): bool {
        return isset($this->role) && $this->role === $role;
    }

    /** Send Password Reset Notification */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
