<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'name',
        'category',
        'price',
        'photo',
        'description',
        'is_available',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orderlist', 'product_id', 'order_id')
            ->withPivot('quantity', 'subtotal');    
    }
}