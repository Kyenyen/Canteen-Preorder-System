<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import for type hinting
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import for type hinting

class Product extends Model
{
    use HasFactory;

    /** Primary key configuration */
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    public $timestamps = false;

    /** Mass Assignable Attributes */
    protected $fillable = [
        'product_id',
        'name',
        'category_id',
        'price',
        'photo',
        'description',
        'is_available',
    ];

    /** Relationship: Category this Product belongs to */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /** Relationship: Orders containing this Product */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orderlist', 'product_id', 'order_id')
            ->withPivot('quantity', 'subtotal');    
    }
}