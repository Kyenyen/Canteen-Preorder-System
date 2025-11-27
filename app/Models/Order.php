<?php

// app/Models/Order.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'OrderId';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'OrderId',
        'UserId',
        'Status',
        'Total',
        'Date',
        'PickupTime',
        'DiningOption',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserId', 'UserId');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'orderlist', 'OrderId', 'ProductId')
            ->withPivot('Quantity', 'Subtotal');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'OrderId', 'OrderId');
    }
}
