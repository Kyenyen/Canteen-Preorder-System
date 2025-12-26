<?php

// app/Models/Order.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** Primary key configuration */
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    public $timestamps = false;

    /** Mass Assignable Attributes */
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

    /** Relationship: User who placed the Order */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /** Relationship: Products in this Order */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'orderlist', 'order_id', 'product_id')
            ->withPivot('quantity', 'subtotal');
    }

    /** Relationship: Payment for this Order */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }
}
