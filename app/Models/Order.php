<?php

// app/Models/Order.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'OrderId';
    public $incrementing = false;

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

    public function orderlists()
    {
        return $this->hasMany(OrderList::class, 'OrderId', 'OrderId');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'OrderId', 'OrderId');
    }
}
