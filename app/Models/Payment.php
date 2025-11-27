<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'PaymentId';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'PaymentId',
        'OrderId',
        'Method',
        'Refunded',
        'RefundDate',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderId', 'OrderId');
    }
}
