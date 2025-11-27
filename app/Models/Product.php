<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'ProductId';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'ProductId',
        'Name',
        'Price',
        'Photo',
        'Description',
        'IsAvailable',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orderlist', 'ProductId', 'OrderId')
            ->withPivot('Quantity', 'Subtotal');    
    }
}