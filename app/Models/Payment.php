<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /** Primary key configuration */
    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    public $timestamps = false;

    /** Mass Assignable Attributes */
    protected $fillable = [
        'payment_id',
        'order_id',
        'method',
        'refunded',
        'refund_date',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_payment_status',
        'paid_at',
    ];

    /** Attribute Type Casting */
    protected $casts = [
        'paid_at' => 'datetime',
        'refund_date' => 'date',
        'refunded' => 'boolean',
    ];

    /** Relationship: Order associated with this Payment */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
