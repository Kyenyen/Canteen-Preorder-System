<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'ProductId';
    public $incrementing = false;
    protected $fillable = [
        'ProductId',
        'Name',
        'Price',
        'Photo',
        'Description',
        'IsAvailable',
    ];

    public function orderlists()
    {
        return $this->hasMany(Orderlist::class, 'ProductId', 'ProductId');
    }
}