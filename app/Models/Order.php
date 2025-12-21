<?php

// app/Models/Order.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'total',
        'date',
        'pickup_time',
        'note',
        'dining_option',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'orderlist', 'order_id', 'product_id')
            ->withPivot('quantity', 'subtotal');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }
}
