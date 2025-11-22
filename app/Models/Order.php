<?php

// app/Models/Order.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Added payment fields to fillable
    protected $fillable = ['user_id', 'pickup_time', 'total_amount', 'status', 'payment_status', 'payment_method'];

    public function items() { return $this->hasMany(OrderItem::class); }
    public function user() { return $this->belongsTo(User::class); }
}
